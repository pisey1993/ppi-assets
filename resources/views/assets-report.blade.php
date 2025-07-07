<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Asset Report</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    @vite(['resources/css/app.css', 'resources/js/app.js']) <!-- Tailwind CSS and optional JS -->
</head>
<body class="bg-gray-100 text-gray-900">

<div class="max-w-7xl mx-auto p-6 min-h-screen">
    <h1 class="text-3xl font-bold mb-6">Asset Report</h1>

    <!-- Filter Form -->
    <form method="GET" action="{{ route('assets.report') }}" class="grid grid-cols-1 md:grid-cols-6 gap-4 mb-6">
        <input
            type="text"
            name="name"
            value="{{ $filters['name'] ?? '' }}"
            class="p-2 border rounded"
            placeholder="Search by Name"
        />

        <select name="status" class="p-2 border rounded">
            <option value="">All Statuses</option>
            @foreach($statuses as $status)
                <option value="{{ $status }}" {{ ($filters['status'] ?? '') == $status ? 'selected' : '' }}>
                    {{ ucfirst($status) }}
                </option>
            @endforeach
        </select>

        <select name="department" class="p-2 border rounded">
            <option value="">All Locations</option>
            @foreach($departments as $dept)
                <option value="{{ $dept }}" {{ ($filters['department'] ?? '') == $dept ? 'selected' : '' }}>
                    {{ $dept }}
                </option>
            @endforeach
        </select>

        <input
            type="date"
            name="purchase_date_from"
            value="{{ $filters['purchase_date_from'] ?? '' }}"
            class="p-2 border rounded"
            placeholder="Purchase Date From"
        />
        <input
            type="date"
            name="purchase_date_to"
            value="{{ $filters['purchase_date_to'] ?? '' }}"
            class="p-2 border rounded"
            placeholder="Purchase Date To"
        />

        <select name="purchase_age" class="p-2 border rounded">
            <option value="">All Ages</option>
            <option value="under_1" {{ ($filters['purchase_age'] ?? '') == 'under_1' ? 'selected' : '' }}>Under 1 Year</option>
            <option value="under_3" {{ ($filters['purchase_age'] ?? '') == 'under_3' ? 'selected' : '' }}>Under 3 Years</option>
            <option value="under_5" {{ ($filters['purchase_age'] ?? '') == 'under_5' ? 'selected' : '' }}>Under 5 Years</option>
            <option value="3" {{ ($filters['purchase_age'] ?? '') == '3' ? 'selected' : '' }}>Over 3 Years</option>
            <option value="5" {{ ($filters['purchase_age'] ?? '') == '5' ? 'selected' : '' }}>Over 5 Years</option>
            <option value="10" {{ ($filters['purchase_age'] ?? '') == '10' ? 'selected' : '' }}>Over 10 Years</option>
        </select>

        <div class="flex items-center space-x-2 col-span-2 md:col-span-1">
            <input
                type="checkbox"
                name="show_all"
                value="1"
                id="show_all"
                {{ !empty($filters['show_all']) ? 'checked' : '' }}
            />
            <label for="show_all" class="text-sm select-none">Show All</label>
        </div>

        <div class="col-span-2 md:col-span-1 flex space-x-2">
            <button
                type="submit"
                class="w-full px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
            >
                Apply Filter
            </button>

            <a
                href="{{ route('assets.report.export', request()->query()) }}"
                class="w-full px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-center"
            >
                Export Excel
            </a>
        </div>
    </form>

    <!-- Asset Table -->
    <div class="overflow-x-auto bg-white shadow rounded">
        <table class="min-w-full text-sm border border-gray-200">
            <thead class="bg-gray-100 text-left">
            <tr>
                <th class="p-2 border">Asset Code</th>
                <th class="p-2 border">Name</th>
                <th class="p-2 border">Purchase Date</th>
                <th class="p-2 border">Status</th>
                <th class="p-2 border">Assigned To</th>
                <th class="p-2 border">Location</th>
            </tr>
            </thead>
            <tbody>
            @forelse($assets as $asset)
                <tr class="hover:bg-gray-50">
                    <td class="p-2 border">{{ $asset->asset_code }}</td>
                    <td class="p-2 border">{{ $asset->name }}</td>
                    <td class="p-2 border">{{ $asset->purchase_date }}</td>
                    <td class="p-2 border">{{ ucfirst($asset->status) }}</td>
                    <td class="p-2 border">{{ $asset->assigned_user->name ?? '—' }}</td>
                    <td class="p-2 border">{{ $asset->current_location->name ?? '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center p-4">No assets found.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if(!request()->show_all && $assets instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="mt-6">
            {{ $assets->links() }}
        </div>
    @endif
</div>

</body>
</html>
