<?php

namespace App\Http\Controllers;

use App\Models\Repair;
use App\Models\User;
use App\Models\Location;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RepairController extends Controller
{
    // Display repairs for a given asset using Inertia
    public function index($assetId)
    {
        $repairs = Repair::where('asset_id', $assetId)
            ->orderByDesc('repair_date')
            ->get();

        // You can fetch additional data if needed
        $users = User::all(); // example
        $locations = Location::all(); // example

        return Inertia::render('Asset/Repairs', [
            'repairs' => $repairs,
            'assetId' => $assetId,
            'users' => $users,
            'locations' => $locations,
        ]);
    }

    // Store new repair - Inertia-compatible
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

        Repair::create($validated);

        return redirect()->back()->with('success', 'Repair added successfully.');
    }

    // Update existing repair
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

        return redirect()->back()->with('success', 'Repair updated successfully.');
    }

    // Delete repair
    public function destroy($repairId)
    {
        Repair::destroy($repairId);

        return redirect()->back()->with('success', 'Repair deleted successfully.');
    }
}
