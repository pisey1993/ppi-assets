<?php

namespace App\Http\Controllers;

use App\Models\Assets;
use App\Models\Categories;
use App\Models\User;
use App\Models\Locations;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\AssetTransfer;
use SimpleSoftwareIO\QrCode\Facades\QrCode;



class AssetsController extends Controller
{
    public function print(Assets $asset)
    {
        return Inertia::render('Assets/PrintAsset', [
            'asset' => $asset,
            'category' => $asset->category,
            'assigned_user' => $asset->assignedToUser,
            'location' => $asset->currentLocation,
        ]);
    }


    public function report(Request $request)
    {
        $query = Assets::with(['category', 'assignedUser', 'currentLocation'])
            ->when($request->name, fn($q) =>
            $q->where('name', 'like', "%{$request->name}%")
            )
            ->when($request->status, fn($q) =>
            $q->where('status', $request->status)
            )
            ->when($request->department, function ($q) use ($request) {
                $q->whereHas('currentLocation', function ($subQuery) use ($request) {
                    $subQuery->where('name', $request->department);
                });
            })
            ->when($request->purchase_date_from, fn($q) =>
            $q->whereDate('purchase_date', '>=', $request->purchase_date_from)
            )
            ->when($request->purchase_date_to, fn($q) =>
            $q->whereDate('purchase_date', '<=', $request->purchase_date_to)
            )
            ->when($request->purchase_age, function ($q, $age) {
                if (in_array($age, ['under_1', 'under_3', 'under_5'])) {
                    $years = (int) str_replace('under_', '', $age);
                    $cutoff = now()->subYears($years)->toDateString();
                    $q->where('purchase_date', '>=', $cutoff); // Under X years
                } else {
                    $cutoff = now()->subYears((int) $age)->toDateString();
                    $q->where('purchase_date', '<=', $cutoff); // Over X years
                }
            })
            ->orderBy('purchase_date', 'asc');

        $assets = $request->show_all
            ? $query->get()
            : $query->paginate(15)->withQueryString();

        return view('assets-report', [
            'assets' => $assets,
            'filters' => $request->only([
                'name', 'status', 'department',
                'purchase_date_from', 'purchase_date_to', 'purchase_age', 'show_all',
            ]),
            'statuses' => ['In Stock', 'Using', 'Repair', 'Broken'],
            'departments' => Locations::select('name')->distinct()->pluck('name'),
        ]);
    }






    public function index(Request $request)
    {
        $assets = Assets::with(['category', 'assignedUser'])
            ->when($request->search, fn($q) =>
            $q->where('asset_code', 'like', "%{$request->search}%")
                ->orWhere('name', 'like', "%{$request->search}%")
            )
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Assets/Index', [
            'assets' => $assets,
            'filters' => $request->only('search'),
            'categories' => Categories::all(),
            'users' => User::all(),
            'appUrl' => config('app.url'), // ←✅ Add this line
        ]);
    }





    public function create()
    {
        return Inertia::render('Assets/Create', [
            'users' => User::select('id', 'name')->get(),
            'locations' => Locations::select('id', 'name')->get(),
            'categories' => Categories::select('id', 'category_name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'asset_code'            => 'required|string|max:255',
            'name'                  => 'required|string',
            'category'              => 'nullable|integer|exists:categories,id',
            'model'                 => 'nullable|string',
            'serial_number'         => 'nullable|string',
            'vendor'                => 'nullable|string',
            'purchase_date'         => 'nullable|date',
            'purchase_cost'         => 'nullable|numeric',
            'warranty_expiry'       => 'nullable|date',
            'status'                => 'nullable|in:available,assigned,repair,retired',
            'current_location_id'   => 'nullable|integer|exists:locations,id',
            'assigned_to_user_id'   => 'nullable|integer|exists:users,id',
            'notes'                 => 'nullable|string',
        ]);

        $asset = Assets::create($validated);

        return redirect()
            ->route('assets.edit', $asset->id)
            ->with('flash', [
                'message' => 'Asset created successfully!',
                'type'    => 'success',
            ]);
    }


    protected function getNextAssetId($currentId)
    {
        return Assets::where('id', '>', $currentId)
            ->orderBy('id', 'asc')
            ->value('id');
    }

    protected function getPreviousAssetId($currentId)
    {
        return Assets::where('id', '<', $currentId)
            ->orderBy('id', 'desc')
            ->value('id');
    }

    public function edit($id)
    {
        $asset = Assets::with(['category', 'transfers', 'repairs', 'assignedUser', 'currentLocation'])->findOrFail($id);

        return Inertia::render('Assets/Edit', [
            'asset' => $asset,
            'repairs' => $asset->repairs,
            'users' => User::all(),
            'locations' => Locations::all(),
            'categories' => Categories::all(),
            'transfers' => $asset->transfers,
            'next_id' => $this->getNextAssetId($id),
            'previous_id' => $this->getPreviousAssetId($id),
            'routes' => [
                'asset_edit' => url('/assets'),
            ],
        ]);
    }



    public function update(Request $request, Assets $asset)
    {
        $validated = $request->validate([
            'asset_code' => 'required|string|max:255',
            'name' => 'required|string',
            'category' => 'nullable|integer|exists:categories,id',
            'model' => 'nullable|string',
            'serial_number' => 'nullable|string',
            'vendor' => 'nullable|string',
            'purchase_date' => 'nullable|date',
            'purchase_cost' => 'nullable|numeric',
            'warranty_expiry' => 'nullable|date',
            'status' => 'nullable|string',
            'current_location_id' => 'nullable|integer|exists:locations,id',
            'assigned_to_user_id' => 'nullable|integer|exists:users,id',
            'notes' => 'nullable|string',
        ]);

        $asset->update($validated);

        return redirect()->route('assets.edit', $asset->id)->with('flash', [
            'message' => 'Asset updated successfully!',
            'type'    => 'success',
        ]);
    }


    public function destroy(Assets $asset)
    {
        $asset->delete();
        return redirect()->route('assets.index')->with('success', 'Asset deleted successfully.');
    }
}
