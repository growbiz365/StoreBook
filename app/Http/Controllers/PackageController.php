<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\Currency;
use App\Models\Module;
use App\Models\Permission;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index(Request $request)
    {
        $packages = Package::query();

        // Search functionality for package name
        if ($request->has('search')) {
            $packages = $packages->where('package_name', 'like', '%' . $request->search . '%');
        }

        $packages = $packages->paginate(10);

        return view('packages.index', compact('packages'));
    }

    public function create()
    {
        $currencies = Currency::all(); // Get all currencies for the dropdown
        return view('packages.create', compact('currencies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'package_name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'currency_id' => 'required|exists:currencies,id',
            'duration_months' => 'required|integer',
        ]);

        Package::create($request->all());

        return redirect()->route('packages.index')->with('success', 'Package created successfully');
    }

    public function show(Package $package)
    {
        return view('packages.show', compact('package'));
    }

    public function edit(Package $package)
    {
        $currencies = Currency::all();
        return view('packages.edit', compact('package', 'currencies'));
    }

    public function update(Request $request, Package $package)
    {
        $request->validate([
            'package_name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'currency_id' => 'required|exists:currencies,id',
            'duration_months' => 'required|integer',
        ]);

        $package->update($request->all());

        return redirect()->route('packages.index')->with('success', 'Package updated successfully');
    }

    public function destroy(Package $package)
    {
        $package->delete();

        return redirect()->route('packages.index')->with('success', 'Package deleted successfully');
    }

    // Show the modules assigned to a package
    public function modules(Package $package)
    {
        // Get all available modules
        $modules = Permission::all();

        // Get modules already assigned to this package
        $assignedModules = $package->modules;

        return view('packages.modules', compact('package', 'modules', 'assignedModules'));
    }

    // Store the assigned modules to the package
    public function storeModules(Request $request, Package $package)
    {
        $request->validate([
            'modules' => 'nullable|array', // Changed from 'required' to 'nullable'
            'modules.*' => 'exists:permissions,id',
        ]);

        // Get the modules array or empty array if no modules selected
        $modules = $request->input('modules', []);

        // Sync the modules (this will remove unselected modules)
        $package->modules()->sync($modules);

        return redirect()->route('packages.modules', $package->id)->with('success', 'Modules updated successfully');
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $packageId = $request->input('id');

        if ($packageId) {
            // If a specific ID is passed, return that package
            $package = Package::find($packageId);
            return response()->json([
                'id' => $package->id,
                'name' => $package->package_name,
            ]);
        } elseif ($query) {
            // If query is provided, search packages based on name
            $packages = Package::where('package_name', 'like', '%' . $query . '%')
                ->get(['id', 'package_name']);

            // Rename package_name to name in the response
            $packages = $packages->map(function ($package) {
                return [
                    'id' => $package->id,
                    'name' => $package->package_name,
                ];
            });

            return response()->json($packages);
        }

        return response()->json([]);
    }
}
