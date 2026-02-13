<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Illuminate\Routing\Attributes\Middleware;

class PermissionController extends Controller
{
    /**
     * Display a listing of the permissions.
     */    
   // #[Middleware('permission:view permissions')]
    public function index(Request $request)
    {
        $title = 'Manage Permissions'; // Set the title for the index page
   
        $search = $request->input('search');

        $permissions = Permission::when($search, function ($query, $search) {
                return $query->where('name', 'like', '%' . $search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('permissions.index', ['permissions' => $permissions,'title' => $title]);
    }

    /**
     * Show the form for creating a new permission.
     */
 //   #[Middleware('permission:create permissions')]
    public function create()
    {
        $title = 'Create Permission'; // Set the title for the create page
        return view('permissions.create',compact('title'));
    }

    /**
     * Store a newly created permission in storage.
     */
  //  #[Middleware('permission:create permissions')]
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:permissions|min:3',
        ]);

        if ($validator->fails()) {
            return redirect()->route('permissions.create')
                ->withErrors($validator)
                ->withInput();
        }

        Permission::create(['name' => $request->name, 'guard_name' => 'web']);
        
        // Clear permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        return redirect()->route('permissions.index')->with('success', 'Permission added successfully');
    }

    /**
     * Show the form for editing the specified permission.
     */
    //#[Middleware('permission:edit permissions')]
    public function edit($id)
    {
        $title = 'Edit Permission'; // Set the title for the create page
        $permission = Permission::findOrFail($id);
        return view('permissions.edit', ['permission' => $permission,'title'=>$title]);
    }

    /**
     * Update the specified permission in storage.
     */
   // #[Middleware('permission:edit permissions')]
    public function update(Request $request, $id)
    {
        $permission = Permission::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:permissions,name,' . $id,
        ]);

        if ($validator->fails()) {
            return redirect()->route('permissions.edit', $id)
                ->withErrors($validator)
                ->withInput();
        }

        $permission->update(['name' => $request->name]);
        
        // Clear permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        return redirect()->route('permissions.index')->with('success', 'Permission updated successfully');
    }

    /**
     * Remove the specified permission from storage.
     */
 //   #[Middleware('permission:delete permissions')]
    public function destroy($id)
    {
        $permission = Permission::find($id);

        if (!$permission) {
            return redirect()->route('permissions.index')->with('error', 'Permission not found');
        }

        $permission->delete();
        
        // Clear permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        return redirect()->route('permissions.index')->with('success', 'Permission deleted successfully');
    }
}
