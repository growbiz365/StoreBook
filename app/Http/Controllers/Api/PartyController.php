<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Party;
use Illuminate\Http\Request;

class PartyController extends Controller
{
    /**
     * Get all parties with pagination
     */
    public function index(Request $request)
    {
        try {
            $query = Party::where('business_id', session('active_business'))
                ->where('status', 1); // Only active parties

            $parties = $query->latest()
                ->paginate($request->get('limit', 15));

            return response()->json([
                'data' => $parties->items(),
                'meta' => [
                    'current_page' => $parties->currentPage(),
                    'last_page' => $parties->lastPage(),
                    'per_page' => $parties->perPage(),
                    'total' => $parties->total(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Failed to fetch parties: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search parties by name, email, or phone
     */
    public function search(Request $request)
    {
        try {
            $searchTerm = $request->get('q', '');
            $limit = $request->get('limit', 15);
            $page = $request->get('page', 1);

            $query = Party::where('business_id', session('active_business'))
                ->where('status', 1); // Only active parties

            if (!empty($searchTerm)) {
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('name', 'like', "%{$searchTerm}%")
                        ->orWhere('phone_no', 'like', "%{$searchTerm}%")
                        ->orWhere('cnic', 'like', "%{$searchTerm}%")
                        ->orWhere('ntn', 'like', "%{$searchTerm}%");
                });
            }

            $parties = $query->latest()
                ->paginate($limit, ['*'], 'page', $page);

            return response()->json([
                'data' => $parties->items(),
                'meta' => [
                    'current_page' => $parties->currentPage(),
                    'last_page' => $parties->lastPage(),
                    'per_page' => $parties->perPage(),
                    'total' => $parties->total(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Failed to search parties: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a specific party by ID
     */
    public function show(Party $party)
    {
        try {
            // Ensure the party belongs to the active business
            if ($party->business_id !== session('active_business')) {
                return response()->json([
                    'error' => true,
                    'message' => 'Party not found'
                ], 404);
            }

            return response()->json($party);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Failed to fetch party: ' . $e->getMessage()
            ], 500);
        }
    }
}
