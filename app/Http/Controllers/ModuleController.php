<?php

namespace App\Http\Controllers;

use App\Models\Module;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    // Show the list of modules
    public function index(Request $request)
    {
        $query = $request->input('search'); // The search term provided by the user

        // Start a query on the Module model
        $modules = Module::query();

        if ($query) {
            // Filter the query by name, guard_name, and code using "like"
            $modules->where(function($queryBuilder) use ($query) {
                $queryBuilder->where('name', 'like', '%' . $query . '%')
                             ->orWhere('guard_name', 'like', '%' . $query . '%')
                             ->orWhere('code', 'like', '%' . $query . '%');
            });
        }

        // Paginate the results, 10 per page
        $modules = $modules->paginate(10);

        return view('modules.index', compact('modules'));
    }

    // Show the form to create a new module
    public function create()
    {
        return view('modules.create');
    }

    // Store a new module
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'guard_name' => 'nullable|string|max:255',
            'code' => 'required|string|max:100',
        ]);

        $request['guard_name'] = 'web';

        Module::create($request->all());

        return redirect()->route('modules.index')->with('success', 'Module created successfully');
    }

    // Show the form to edit a module
    public function edit(Module $module)
    {
        return view('modules.edit', compact('module'));
    }

    // Update the module
    public function update(Request $request, Module $module)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'guard_name' => 'nullable|string|max:255',
            'code' => 'required|string|max:100',
        ]);

        $module->update($request->all());

        return redirect()->route('modules.index')->with('success', 'Module updated successfully');
    }

    // Delete the module
    public function destroy(Module $module)
    {
        $module->delete();

        return redirect()->route('modules.index')->with('success', 'Module deleted successfully');
    }
}
