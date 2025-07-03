<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Repair;

class RepairController extends Controller
{
    // Get repairs for asset - returns JSON
    public function index($assetId)
    {
        $repairs = Repair::where('asset_id', $assetId)
            ->orderByDesc('repair_date')
            ->get();

        return response()->json($repairs);
    }

    // Store new repair - expects JSON data, returns JSON
    public function store(Request $request, $assetId)
    {
        $validated = $request->validate([
            'repair_date' => 'required|date',
            'issue' => 'nullable|string|max:255',
            'repair_cost' => 'nullable|numeric',
            'status' => 'nullable|string|max:50',
            'vendor' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
        ]);

        $validated['asset_id'] = $assetId;

        $repair = Repair::create($validated);

        return response()->json($repair, 201);
    }

    // Update repair by id
    public function update(Request $request, $repairId)
    {
        $repair = Repair::findOrFail($repairId);

        $validated = $request->validate([
            'repair_date' => 'required|date',
            'issue' => 'nullable|string|max:255',
            'repair_cost' => 'nullable|numeric',
            'status' => 'nullable|string|max:50',
            'vendor' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
        ]);

        $repair->update($validated);

        return response()->json($repair);
    }

    // Delete repair by id
    public function destroy($repairId)
    {
        Repair::destroy($repairId);

        return response()->json(['message' => 'Repair deleted successfully.']);
    }
}
