<?php

namespace App\Http\Controllers;

use App\Models\Assets;
use App\Models\Categories;
use App\Models\User;
use App\Models\Locations;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\AssetTransfer;


class AssetsController extends Controller
{
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


    public function edit($id)
    {
        $asset = Assets::with(['category', 'transfers', 'repairs', 'assignedUser', 'currentLocation'])->findOrFail($id);

        $next = Assets::where('id', '>', $id)->orderBy('id')->first();
        $previous = Assets::where('id', '<', $id)->orderByDesc('id')->first();

        return Inertia::render('Assets/Edit', [
            'asset' => $asset,
            'repairs' => $asset->repairs,
            'users' => User::all(),
            'locations' => Locations::all(),
            'categories' => Categories::all(),
            'transfers' => $asset->transfers,
            'next_id' => $next?->id,
            'previous_id' => $previous?->id,
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
