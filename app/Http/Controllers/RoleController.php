<?php
namespace App\Http\Controllers;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Role;

class RoleController extends Controller
{
    public function index(request $request)
    {
        $title = 'Manage Roles'; // Set the title for the index page
        $search = $request->input('search');
        $user = auth()->user();

        // Base query with eager loading to avoid N+1 when showing business info
        // We load creator and their businesses, since roles are created per business by a user
        $query = Role::with(['creator.businesses'])
            // Check if there is a search query and filter the roles accordingly
            ->when($search, function ($query, $search) {
                return $query->where('name', 'LIKE', '%' . $search . '%');
            });

        // If user is not Super Admin, only show roles they can manage
        if (!$user->isSuperAdmin()) {
            $query->where(function ($q) use ($user) {
                $q->where('created_by', $user->id) // Roles created by user
                  ->orWhereIn('id', $user->roles->pluck('id')); // Roles assigned to user
            });
        }

        $roles = $query->orderBy('name', 'asc')
            ->paginate(10)
            ->withQueryString(); // Preserve query string in pagination links

        return view('roles.index', compact('roles','title'));
    }

    public function create()
    {
        $title = 'Create Role'; // Set the title for the create page
        $user = auth()->user();
        
        // Check if user can create roles
        if (!$user->canCreateRoles()) {
            return redirect()->route('roles.index')->with('error', 'You do not have permission to create roles.');
        }
        
        // Get permissions that the user can assign (inherited permissions)
        $assignablePermissions = $user->getAssignablePermissions();
        
        return view('roles.create', compact('assignablePermissions', 'title'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        
        // Check if user can create roles
        if (!$user->canCreateRoles()) {
            return redirect()->route('roles.index')->with('error', 'You do not have permission to create roles.');
        }

        // Validate the input
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:roles|min:3',
            'permission' => 'required|array|min:1'
        ]);

        if ($validator->passes()) {
            // Validate that user can assign all selected permissions
            $assignablePermissionNames = $user->getAssignablePermissionNames();
            $selectedPermissions = Permission::whereIn('id', $request->permission)->pluck('name')->toArray();
            
            foreach ($selectedPermissions as $permissionName) {
                if (!in_array($permissionName, $assignablePermissionNames)) {
                    return redirect()->route('roles.create')
                        ->withErrors(['permission' => 'You do not have permission to assign: ' . $permissionName])
                        ->withInput();
                }
            }

            // Create the new role with created_by field
            $role = Role::create([
                'name' => $request->name,
                'created_by' => $user->id
            ]);

            if (!empty($request->permission)) {
                // Attach selected permissions to the role
                foreach ($request->permission as $permissionId) {
                    $permission = Permission::find($permissionId);
                    if ($permission && $user->canAssignPermission($permission->name)) {
                        $role->givePermissionTo($permission);
                    }
                }
            }

            // Redirect with success message
            return redirect()->route('roles.index')->with('success', 'Role Added successfully');
        } else {
            // Redirect with errors and input data
            return redirect()->route('roles.create')->withErrors($validator)->withInput();
        }
    }

    public function edit($id)
    {
        $title = 'Edit Role'; // Set the title for the create page
        $user = auth()->user();
        
        // Find the role by ID
        $role = Role::findOrFail($id);
        
        // Check if user can edit this role
        if (!$user->canEditRole($role)) {
            return redirect()->route('roles.index')->with('error', 'You do not have permission to edit this role.');
        }
        
        $haspermission = $role->permissions->pluck('name');
        
        // Get permissions that the user can assign (inherited permissions)
        $assignablePermissions = $user->getAssignablePermissions();
        
        // Prepare selected permission IDs for Alpine.js
        $selectedPermissionIds = $haspermission->map(function($permissionName) use ($assignablePermissions) {
            $permission = $assignablePermissions->first(function($p) use ($permissionName) {
                return $p->name === $permissionName;
            });
            return $permission ? $permission->id : null;
        })->filter()->values()->toArray();
        
        return view('roles.edit', [
            'role' => $role,
            'assignablePermissions' => $assignablePermissions,
            'haspermission' => $haspermission,
            'selectedPermissionIds' => $selectedPermissionIds,
            'title' => $title,
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        
        // Find the role by ID
        $role = Role::findOrFail($id);
        
        // Check if user can edit this role
        if (!$user->canEditRole($role)) {
            return redirect()->route('roles.index')->with('error', 'You do not have permission to edit this role.');
        }

        // Validate the input
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:roles,name,' . $id . ',id',
            'permission' => 'required|array|min:1' // Ensure permissions are selected
        ]);

        if ($validator->passes()) {
            // Validate that user can assign all selected permissions
            $assignablePermissionNames = $user->getAssignablePermissionNames();
            $selectedPermissions = Permission::whereIn('id', $request->permission)->pluck('name')->toArray();
            
            foreach ($selectedPermissions as $permissionName) {
                if (!in_array($permissionName, $assignablePermissionNames)) {
                    return redirect()->route('roles.edit', $id)
                        ->withErrors(['permission' => 'You do not have permission to assign: ' . $permissionName])
                        ->withInput();
                }
            }

            // Update the role name
            $role->name = $request->name;
            $role->save();

            // Sync permissions with the role
            if (!empty($request->permission)) {
                $permissionNames = Permission::whereIn('id', $request->permission)->pluck('name')->toArray();
                $role->syncPermissions($permissionNames);
            } else {
                // If no permissions are selected, remove all permissions
                $role->syncPermissions([]);
            }

            // Redirect with success message
            return redirect()->route('roles.index')->with('success', 'Role Updated successfully');
        } else {
            // Redirect with errors and input data
            return redirect()->route('roles.edit', $id)->withErrors($validator)->withInput();
        }
    }

    public function destroy(Request $request, $id)
    {
        $user = auth()->user();
        
        // Find the role by ID
        $role = Role::findOrFail($id);
        
        // Check if user can delete this role
        if (!$user->isSuperAdmin() && $role->created_by != $user->id) {
            return redirect()->route('roles.index')->with('error', 'You can only delete roles you created.');
        }
        
        $role->delete();
        return redirect()->route('roles.index')->with('success', 'Role deleted successfully');
    }
}