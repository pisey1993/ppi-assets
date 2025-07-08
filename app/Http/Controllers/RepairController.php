<?php

namespace App\Http\Controllers;

use App\Models\Repair;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RepairsExport;

class RepairController extends Controller
{
    /**
     * Export filtered repairs to Excel.
     */
    public function export(Request $request)
    {
        $filters = $request->only([
            'repair_date_from', 'repair_date_to', 'issue', 'repair_cost_min', 'repair_cost_max',
            'status', 'vendor', 'remarks',
        ]);

        $fileName = 'repairs_report_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new RepairsExport($filters), $fileName);
    }

    /**
     * Show repairs report with filters.
     */
    public function report(Request $request)
    {
        $query = Repair::query();

        // Search filters
        $query->when($request->repair_date_from, fn($q) =>
        $q->whereDate('repair_date', '>=', $request->repair_date_from)
        );

        $query->when($request->repair_date_to, fn($q) =>
        $q->whereDate('repair_date', '<=', $request->repair_date_to)
        );

        $query->when($request->issue, fn($q) =>
        $q->where('issue', 'like', '%' . $request->issue . '%')
        );

        $query->when($request->repair_cost_min, fn($q) =>
        $q->where('repair_cost', '>=', $request->repair_cost_min)
        );

        $query->when($request->repair_cost_max, fn($q) =>
        $q->where('repair_cost', '<=', $request->repair_cost_max)
        );

        $query->when($request->status, fn($q) =>
        $q->where('status', 'like', '%' . $request->status . '%')
        );

        $query->when($request->vendor, fn($q) =>
        $q->where('vendor', 'like', '%' . $request->vendor . '%')
        );

        $query->when($request->remarks, fn($q) =>
        $q->where('remarks', 'like', '%' . $request->remarks . '%')
        );

        $repairs = $query->orderBy('repair_date', 'desc')->paginate(15)->withQueryString();

        return view('repairs-report', [
            'repairs' => $repairs,
            'filters' => $request->only([
                'repair_date_from', 'repair_date_to', 'issue', 'repair_cost_min', 'repair_cost_max',
                'status', 'vendor', 'remarks',
            ]),
        ]);
    }

    /**
     * Display repairs list for a given asset using Inertia.
     */
    public function index($assetId)
    {
        $repairs = Repair::where('asset_id', $assetId)->get();

        return Inertia::render('Assets/Edit', [
            'repairs' => $repairs,
            'assetId' => (int) $assetId,
        ]);
    }

    /**
     * Store a new repair.
     */
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

        return redirect()->route('repairs.index', ['assetId' => $assetId])
            ->with('success', 'Repair added successfully.');
    }

    /**
     * Update an existing repair.
     */
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

    /**
     * Delete a repair.
     */
    public function destroy($repairId)
    {
        Repair::destroy($repairId);

        return redirect()->back()->with('success', 'Repair deleted successfully.');
    }
}
