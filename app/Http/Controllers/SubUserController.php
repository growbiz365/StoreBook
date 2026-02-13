<?php

namespace App\Http\Controllers;

use App\Mail\SubUserCreated;
use App\Models\Branch;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Business;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class SubUserController extends Controller
{

    /**
     * Show the list of sub-users (belonging to the parent).
     */
    public function index(Request $request)
    {
        $title = 'Manage Sub Users';

        // Get the currently authenticated parent user
        $parentUser = auth()->user();

        // Apply search filters if provided
        $search = $request->input('search');

        // Fetch the sub-users with the applied search filters
        $users = $parentUser->subUsers()
            ->when($search, function ($query, $search) {
                return $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('username', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('created_at', 'desc') // Optional: Sort by created date (latest first)
            ->paginate(10); // Pagination for 10 items per page

        return view('subusers.index', compact('users', 'title'));
    }

    /**
     * Show the form to create a sub-user.
     */
    public function create()
    {
        $title = 'Create Sub User';
        $parentUser = auth()->user();

        // Get roles that the parent user can assign (including their own roles and created roles)
        $assignableRoles = $parentUser->getAssignableRoles();

        // Get businesses associated with the parent user
        $parentbusinesses = $parentUser->businesses;
        $assignedBusinesses = $parentbusinesses->pluck('id')->toArray(); // Add this line

        return view('subusers.create', compact('parentUser', 'assignableRoles', 'parentbusinesses', 'assignedBusinesses', 'title'));
    }


    /**
     * Store the sub-user.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'username' => 'required|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'businesses' => 'nullable|array',  // Ensure Businesses are provided
            'businesses.*' => 'exists:businesses,id',  // Ensure each Businesses exists
            'roles' => 'nullable|array', // Ensure roles are passed
            'roles.*' => 'exists:roles,id', // Ensure each role exists
        ]);

        if ($validator->fails()) {
            return redirect()->route('subusers.create')->withErrors($validator)->withInput();
        }

        // Create new subuser
        $subUser = new User();
        $subUser->name = $request->name;
        $subUser->username = $request->username;
        $subUser->email = $request->email;
        $subUser->password = Hash::make($request->password);
        $subUser->parent_id = auth()->user()->id; // Assign the parent user ID
        $subUser->save();

        // Assign branches to the sub-user
        if ($request->has('businesses')) {
            $subUser->businesses()->sync($request->businesses);
        }

        // Assign roles to the sub-user if any
        if ($request->has('roles')) {
            $roles = array_map('intval', $request->roles); // Convert to integers
            $subUser->roles()->sync($roles); // Assign roles
        }

        // Get the assigned business and roles
        $businesses = Business::whereIn('id', $request->businesses)->get();
        $roles = Role::whereIn('id', $request->roles)->get(); // Fetch roles instead of permissions

        $parentUser = auth()->user();

       
        return redirect()->route('subusers.index')->with('success', 'Sub-user created successfully');
    }


    /**
     * Edit a sub-user's details.
     */
    public function edit($id)
    {
        // Fetch the sub-user to be edited
        $subUser = User::findOrFail($id);

        $parentUser = auth()->user();

        // Get roles that the parent user can assign (including their own roles and created roles)
        $assignableRoles = $parentUser->getAssignableRoles();

        // Get assigned roles and branches of the sub-user
        $assignedRoles = $subUser->roles->pluck('id')->toArray();
        $assignedBusinesses = $subUser->businesses->pluck('id')->toArray(); // Add this line
        $businesses = $parentUser->businesses;

        return view('subusers.edit', compact(
            'subUser',
            'parentUser',
            'assignableRoles',
            'assignedRoles',
            'assignedBusinesses',
            'businesses'
        ));
    }

    /**
     * Update a sub-user's details.
     */
    public function update(Request $request, $id)
    {
        $subUser = User::findOrFail($id);

        // Validate the request
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'username' => 'required|unique:users,username,' . $id,
            'email' => 'required|email|unique:users,email,' . $id,
            'roles' => 'nullable|array', // For roles instead of permissions
            'roles.*' => 'exists:roles,id', // Ensure roles exist
            'businesses' => 'nullable|array',
            'businesses.*' => 'exists:businesses,id',
        ]);

        if ($validator->fails()) {
            return redirect()->route('subusers.edit', $id)->withErrors($validator)->withInput();
        }

        // Update sub-user details
        $subUser->name = $request->name;
        $subUser->username = $request->username;
        $subUser->email = $request->email;
        $subUser->save();

        // Sync the roles and branches
        if ($request->has('roles')) {
            $roles = array_map('intval', $request->roles); // Convert to integers
            $subUser->roles()->sync($roles); // Sync roles instead of permissions
        }

        if ($request->has('businesses')) {
            $subUser->businesses()->sync($request->businesses);
        }

        return redirect()->route('subusers.index')->with('success', 'Sub User updated successfully');
    }

    /**
     * Suspend the specified sub-user.
     */
    public function suspend(Request $request, $id)
    {
        $subUser = User::findOrFail($id);
        $parentUser = auth()->user();
        
        // Ensure the sub-user belongs to the current parent
        if ($subUser->parent_id !== $parentUser->id) {
            return redirect()->route('subusers.index')->with('error', 'You can only suspend your own sub-users.');
        }
        
        // Prevent suspending super admins
        if ($subUser->isSuperAdmin()) {
            return redirect()->route('subusers.index')->with('error', 'Cannot suspend Super Admin users.');
        }
        
        $reason = $request->input('reason', 'No reason provided');
        $subUser->suspend($reason);
        
        return redirect()->route('subusers.index')->with('success', 'Sub-user suspended successfully.');
    }

    /**
     * Unsuspend the specified sub-user.
     */
    public function unsuspend($id)
    {
        $subUser = User::findOrFail($id);
        $parentUser = auth()->user();
        
        // Ensure the sub-user belongs to the current parent
        if ($subUser->parent_id !== $parentUser->id) {
            return redirect()->route('subusers.index')->with('error', 'You can only unsuspend your own sub-users.');
        }
        
        $subUser->unsuspend();
        
        return redirect()->route('subusers.index')->with('success', 'Sub-user unsuspended successfully.');
    }

    /**
     * Delete a sub-user.
     */
    public function destroy($id)
    {
        $subUser = User::findOrFail($id);
        $parentUser = auth()->user();
        
        // Ensure the sub-user belongs to the current parent
        if ($subUser->parent_id !== $parentUser->id) {
            return redirect()->route('subusers.index')->with('error', 'You can only delete your own sub-users.');
        }
        
        // Prevent deleting super admins
        if ($subUser->isSuperAdmin()) {
            return redirect()->route('subusers.index')->with('error', 'Cannot delete Super Admin users.');
        }
        
        $subUser->delete();
        return redirect()->route('subusers.index')->with('success', 'Sub-user deleted successfully');
    }
}
