<?php

namespace App\Http\Controllers;

use App\Mail\UserCreated;
use App\Models\Branch;
use App\Models\Business;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;


class UserController extends Controller
{

    public function index(Request $request)
    {
        $title = 'Manage Users'; // Set the title for the index page
        $search = $request->input('search');

        $users = User::whereNull('parent_id') // Only show parent accounts
            ->when($search, function ($query, $search) {
                return $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')
                          ->orWhere('email', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('users.index', ['users' => $users, 'title' => $title]);
    }
    /* 	public function show(User $user)
        {
            // Return a view or JSON with user details
            return view('users.show', compact('user'));
        }
     */
    public function create()
    {
        $title = 'Create User'; // Set the title for the create page
        $roles = Role::orderBy('name', 'asc')->get();
        $businesses = Business::all();

        return view('users.create', compact('roles', 'title', 'businesses'));
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'username' => 'required|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'businesses' => 'nullable|array',  // Ensure branches are provided
            'businesses.*' => 'exists:businesses,id',  // Ensure each branch exists
            'role' => 'nullable|array',  // Ensure role is an array if provided
            'role.*' => 'exists:roles,id', // Ensure each role exists
        ]);

        if ($validator->fails()) {
            return redirect()->route('users.create')->withErrors($validator)->withInput();
        }

        // Create new user
        $user = new User();
        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();

        // Assign roles to the user if any
        if ($request->has('role')) {
            $user->roles()->sync($request->role);
            
            // Automatically sync businesses based on role
            $user->syncBusinessesForRole();
            
            // If not Super Admin, assign selected businesses
            if (!$user->isSuperAdmin()) {
                $user->businesses()->sync($request->businesses);
            }
        } else {
            // Assign businesses to the user if no roles are assigned
            $user->businesses()->sync($request->businesses);
        }

       
        return redirect()->route('users.index')->with('success', 'User Added successfully');
    }



    /**
     * Display the specified resource.
     */
    public function edit(string $id)
    {
        $title = 'Edit User'; // Set the title for the create page
        $user = User::findOrFail($id);
        $roles = Role::orderBy('name', 'asc')->get();
        $hasRoles = $user->roles->pluck('id');
        $businesses = Business::all();

        return view('users.edit', compact('user', 'roles', 'hasRoles', 'title','businesses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Validation
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'username' => 'required|unique:users,username,' . $id,
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'nullable|array', // Ensure role selection is an array
            'role.*' => 'exists:roles,id', // Ensure each role exists
            'businesses' => 'nullable|array',  // Ensure branches are provided
            'businesses.*' => 'exists:businesses,id',  // Ensure each branch exists
        ]);

        if ($validator->fails()) {
            return redirect()->route('users.edit', $id)->withErrors($validator)->withInput();
        }

        // Update user details
        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->save();

        // Sync roles for the user
        if ($request->has('role')) {
            $user->roles()->sync($request->role);
            
            // Automatically sync businesses based on role
            $user->syncBusinessesForRole();
            
            // If not Super Admin, assign selected businesses
            if (!$user->isSuperAdmin()) {
                $user->businesses()->sync($request->input('businesses', []));
            }
        } else {
            // Always sync businesses, even if none are selected
            $user->businesses()->sync($request->input('businesses', []));
        }

        return redirect()->route('users.index')->with('success', 'User updated successfully');
    }


    /**
     * Suspend the specified user.
     */
    public function suspend(Request $request, string $id)
    {
        $user = User::findOrFail($id);
        
        // Prevent suspending super admins
        if ($user->isSuperAdmin()) {
            return redirect()->route('users.index')->with('error', 'Cannot suspend Super Admin users.');
        }
        
        // Prevent self-suspension
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')->with('error', 'You cannot suspend yourself.');
        }
        
        $reason = $request->input('reason', 'No reason provided');
        $user->suspend($reason);
        
        return redirect()->route('users.index')->with('success', 'User suspended successfully.');
    }

    /**
     * Unsuspend the specified user.
     */
    public function unsuspend(string $id)
    {
        $user = User::findOrFail($id);
        $user->unsuspend();
        
        return redirect()->route('users.index')->with('success', 'User unsuspended successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        
        // Prevent deleting super admins
        if ($user->isSuperAdmin()) {
            return redirect()->route('users.index')->with('error', 'Cannot delete Super Admin users.');
        }
        
        // Prevent self-deletion
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')->with('error', 'You cannot delete yourself.');
        }
        
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully');
    }

    public function search(Request $request)
    {
        // Fetch the query parameter from the request
        $query = $request->input('query');
        $id = $request->input('id');

        if ($id) {
            $user = User::find($id);
            if ($user) {
                return response()->json($user);
            } else {
                return response()->json([], 404);  // Return empty if not found
            }
        }

        if ($query) {
            $users = User::where('name', 'like', "%$query%")
                ->orWhere('email', 'like', "%$query%")
                ->limit(10)
                ->get(['id', 'name', 'email']);

            return response()->json($users);
        }

        // Return empty if no query is provided
        return response()->json([]);
    }
}
