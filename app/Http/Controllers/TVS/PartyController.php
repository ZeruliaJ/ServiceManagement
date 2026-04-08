<?php

namespace App\Http\Controllers\TVS;

use App\Http\Controllers\Controller;
use App\Models\TVS\Party;
use App\Models\TVS\PartyType;
use Illuminate\Http\Request;

class PartyController extends Controller
{
    /**
     * Create new party (customer/institution)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'party_type_id' => 'required|exists:party_types,id',
            'code' => 'required|unique:parties',
            'name' => 'required|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'tax_id' => 'nullable|string',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'region' => 'nullable|string',
            'district' => 'nullable|string',
            'town' => 'nullable|string',
        ]);

        $party = Party::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Party created successfully',
            'data' => $party
        ], 201);
    }

    /**
     * Get party details
     */
    public function show(Party $party)
    {
        return response()->json([
            'success' => true,
            'data' => $party->load(['partyType', 'ownerMappings', 'customerLifetimeValues'])
        ]);
    }

    /**
     * Update party
     */
    public function update(Request $request, Party $party)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'tax_id' => 'nullable|string',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'region' => 'nullable|string',
            'district' => 'nullable|string',
            'town' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $party->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Party updated',
            'data' => $party
        ]);
    }

    /**
     * List all parties with filters
     */
    public function index(Request $request)
    {
        $query = Party::query();

        if ($request->has('party_type_id')) {
            $query->where('party_type_id', $request->party_type_id);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('code', 'like', "%$search%")
                  ->orWhere('phone', 'like', "%$search%");
            });
        }

        $parties = $query->with('partyType')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $parties
        ]);
    }

    /**
     * Get party types
     */
    public function getPartyTypes()
    {
        return response()->json([
            'success' => true,
            'data' => PartyType::all()
        ]);
    }
}
