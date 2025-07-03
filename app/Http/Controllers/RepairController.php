<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Repair;

class RepairController extends Controller
{
    // Return all repairs (you can limit or paginate as needed)
    public function index($assetId)
    {
        $repairs = Repair::where('asset_id', $assetId)
            ->orderByDesc('repair_date')
            ->get();

        return view('repairs.index', compact('repairs'));
    }

    // Store new repair with validation
    public function store(Request $request)
    {
        $data = $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'repair_date' => 'required|date',
            'issue' => 'nullable|string|max:255',
            'repair_cost' => 'nullable|numeric',
            'status' => 'nullable|string|max:50',
            'vendor' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
        ]);

        $repair = Repair::create($data);

        // Return JSON for AJAX requests or redirect for web
        if ($request->wantsJson()) {
            return response()->json(['message' => 'Repair added!', 'repair' => $repair], 201);
        }

        return redirect()->back()->with('success', 'Repair added!');
    }

    // Update repair by id with validation
    public function update(Request $request, $id)
    {
        $repair = Repair::findOrFail($id);

        $data = $request->validate([
            'repair_date' => 'required|date',
            'issue' => 'nullable|string|max:255',
            'repair_cost' => 'nullable|numeric',
            'status' => 'nullable|string|max:50',
            'vendor' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
        ]);

        $repair->update($data);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Repair updated!', 'repair' => $repair]);
        }

        return redirect()->back()->with('success', 'Repair updated!');
    }

    // Delete repair by id
    public function destroy(Request $request, $id)
    {
        Repair::destroy($id);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Repair deleted!']);
        }

        return redirect()->back()->with('success', 'Repair deleted!');
    }

    // Get repairs by asset id (for your Vue component)
    public function getByAsset($assetId)
    {
        $repairs = Repair::where('asset_id', $assetId)->orderByDesc('repair_date')->get();

        return response()->json($repairs);
    }
}
