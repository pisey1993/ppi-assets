<?php

namespace App\Http\Controllers;

use App\Models\AssetTransfer;
use App\Models\Assets; // Corrected to plural 'Assets'
use App\Models\User;
use App\Models\Locations;
use Inertia\Inertia;
use Illuminate\Http\Request; // <-- ADD THIS LINE

class AssetTransferController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transfers = AssetTransfer::with(['fromUser', 'assignedUser', 'fromLocation', 'toLocation'])
            ->orderBy('transfer_date', 'desc')
            ->get()
            ->map(function ($transfer) {
                return [
                    'id' => $transfer->id,
                    'asset_id' => $transfer->asset_id,
                    'transfer_date' => $transfer->transfer_date,
                    'from_location_name' => optional($transfer->fromLocation)->name,
                    'to_location_name' => optional($transfer->toLocation)->name,
                    // Ensure 'assignedUser' is correctly mapped if 'toUser' or 'fromUser' is the source
                    // In your Vue code, it looks for 'to_user_id' or 'from_user_id'
                    'assigned_user_name' => optional($transfer->toUser)->name ?? optional($transfer->fromUser)->name,
                    'user_status' => $transfer->user_status,
                    'reason' => $transfer->reason, // Changed from 'notes' to 'reason' to match schema
                ];
            });

        $users = User::select('id', 'name')->get();
        $locations = Locations::select('id', 'name')->get();

        return Inertia::render('Asset/TransferHistory', [
            'transfers' => $transfers,
            'users' => $users,
            'locations' => $locations,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'asset_id' => 'required|integer',
            'from_user_id' => 'nullable|integer',
            'to_user_id' => 'nullable|integer',
            // Changed validation from 'string' to 'integer' to match Vue's `loc.id`
            'from_location' => 'nullable|integer',
            // Changed validation from 'string' to 'integer' to match Vue's `loc.id`
            'to_location' => 'nullable|integer',
            'transfer_date' => 'required|date',
            'approved_by' => 'nullable|string|max:255',
            'reason' => 'nullable|string',
            'user_status' => 'nullable|string|max:100',
        ]);

        AssetTransfer::create($request->all());

        return redirect()->back()->with('success', 'Asset transfer recorded successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AssetTransfer $assetTransfer)
    {
        $request->validate([
            'asset_id' => 'required|integer',
            'from_user_id' => 'nullable|integer',
            'to_user_id' => 'nullable|integer',
            // Changed validation from 'string' to 'integer' to match Vue's `loc.id`
            'from_location' => 'nullable|integer',
            // Changed validation from 'string' to 'integer' to match Vue's `loc.id`
            'to_location' => 'nullable|integer',
            'transfer_date' => 'required|date',
            'approved_by' => 'nullable|string|max:255',
            'reason' => 'nullable|string',
            'user_status' => 'nullable|string|max:100',
        ]);

        $assetTransfer->update($request->all());

        return redirect()->back()->with('success', 'Asset transfer updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AssetTransfer $assetTransfer)
    {
        $assetTransfer->delete();

        return redirect()->back()->with('success', 'Asset transfer deleted.');
    }

    /**
     * Print the specified resource.
     */
    public function print(AssetTransfer $transfer)
    {
        return Inertia::render('AssetTransfers/Print', [
            'transfer' => $transfer->load(['asset', 'fromUser', 'toUser']),
        ]);
    }
}
