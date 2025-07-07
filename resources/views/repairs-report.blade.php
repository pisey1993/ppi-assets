<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Repair Report</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    {{-- Using a CDN for demonstration purposes. In a real project, you'd use @vite. --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Optional: Extend Tailwind theme for custom colors if needed
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            '50': '#eff6ff',
                            '100': '#dbeafe',
                            '200': '#bfdbfe',
                            '300': '#93c5fd',
                            '400': '#60a5fa',
                            '500': '#3b82f6',
                            '600': '#2563eb',
                            '700': '#1d4ed8',
                            '800': '#1e40af',
                            '900': '#1e3a8a',
                        },
                        green: {
                            '600': '#16a34a',
                            '700': '#15803d',
                        },
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-slate-100 font-sans">

<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">

    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold tracking-tight text-slate-900">Repair Report</h1>
        <p class="mt-1 text-sm text-slate-600">Browse, filter, and review repair records.</p>
    </div>

    <!-- Filter Form Card -->
    <div class="bg-white p-6 rounded-lg shadow-sm mb-8">
        <form method="GET" action="{{ route('repairs.report') }}">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

                <!-- Date Range -->
                <div>
                    <label for="repair_date_from" class="block text-sm font-medium text-slate-700">Repair Date From</label>
                    <input type="date" id="repair_date_from" name="repair_date_from" value="{{ $filters['repair_date_from'] ?? '' }}" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" />
                </div>
                <div>
                    <label for="repair_date_to" class="block text-sm font-medium text-slate-700">To</label>
                    <input type="date" id="repair_date_to" name="repair_date_to" value="{{ $filters['repair_date_to'] ?? '' }}" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" />
                </div>

                <!-- Cost Range -->
                <div>
                    <label for="repair_cost_min" class="block text-sm font-medium text-slate-700">Min Cost</label>
                    <input type="number" step="0.01" id="repair_cost_min" name="repair_cost_min" value="{{ $filters['repair_cost_min'] ?? '' }}" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" placeholder="e.g., 50.00" />
                </div>
                <div>
                    <label for="repair_cost_max" class="block text-sm font-medium text-slate-700">Max Cost</label>
                    <input type="number" step="0.01" id="repair_cost_max" name="repair_cost_max" value="{{ $filters['repair_cost_max'] ?? '' }}" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" placeholder="e.g., 500.00" />
                </div>

                <!-- Text Searches -->
                <div>
                    <label for="issue" class="block text-sm font-medium text-slate-700">Issue</label>
                    <input type="text" id="issue" name="issue" value="{{ $filters['issue'] ?? '' }}" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" placeholder="Screen replacement..." />
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-slate-700">Status</label>
                    <input type="text" id="status" name="status" value="{{ $filters['status'] ?? '' }}" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" placeholder="Completed, Pending..." />
                </div>
                <div>
                    <label for="vendor" class="block text-sm font-medium text-slate-700">Vendor</label>
                    <input type="text" id="vendor" name="vendor" value="{{ $filters['vendor'] ?? '' }}" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" placeholder="Tech Solutions Inc." />
                </div>
                <div>
                    <label for="remarks" class="block text-sm font-medium text-slate-700">Remarks</label>
                    <input type="text" id="remarks" name="remarks" value="{{ $filters['remarks'] ?? '' }}" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" placeholder="Search remarks..." />
                </div>
            </div>

            <!-- Form Actions -->
            <div class="mt-6 flex items-center justify-end gap-x-4">

                <a href="{{ route('repairs.report') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-800">
                    Reset
                </a>

                <button type="submit" class="inline-flex items-center gap-x-2 rounded-md bg-primary-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                        <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                    </svg>
                    Apply Filters
                </button>

                {{-- Export Button --}}
                <a href="{{ route('repairs.report.export', request()->query()) }}"
                   class="inline-flex items-center gap-x-2 rounded-md bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-600"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1M12 12v8m4-4H8m4-4v-4m-4 4v-4" />
                    </svg>
                    Export Excel
                </a>

            </div>
        </form>
    </div>

    <!-- Results Table Card -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50">
                <tr>
                    <th scope="col" class="py-3.5 px-6 text-left font-semibold text-slate-600">Repair Date</th>
                    <th scope="col" class="py-3.5 px-6 text-left font-semibold text-slate-600">Issue</th>
                    <th scope="col" class="py-3.5 px-6 text-left font-semibold text-slate-600">Status</th>
                    <th scope="col" class="py-3.5 px-6 text-right font-semibold text-slate-600">Cost</th>
                    <th scope="col" class="py-3.5 px-6 text-left font-semibold text-slate-600">Vendor</th>
                    <th scope="col" class="py-3.5 px-6 text-left font-semibold text-slate-600">Remarks</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                @forelse($repairs as $repair)
                    <tr class="hover:bg-slate-50">
                        <td class="whitespace-nowrap px-6 py-4 text-slate-700">{{ \Carbon\Carbon::parse($repair->repair_date)->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-slate-800 font-medium">{{ $repair->issue ?? '—' }}</td>
                        <td class="whitespace-nowrap px-6 py-4">
                            @php
                                // Example logic for status badges. Customize this as needed.
                                $status = strtolower($repair->status ?? '');
                                $badgeClass = 'bg-slate-100 text-slate-800'; // Default
                                if (in_array($status, ['completed', 'finished'])) {
                                    $badgeClass = 'bg-green-100 text-green-800';
                                } elseif (in_array($status, ['in progress', 'pending'])) {
                                    $badgeClass = 'bg-yellow-100 text-yellow-800';
                                } elseif (in_array($status, ['cancelled', 'failed'])) {
                                    $badgeClass = 'bg-red-100 text-red-800';
                                }
                            @endphp
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $badgeClass }}">
                                {{ $repair->status ?? 'Unknown' }}
                            </span>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-right text-slate-700 font-mono">{{ $repair->repair_cost !== null ? '$' . number_format($repair->repair_cost, 2) : '—' }}</td>
                        <td class="whitespace-nowrap px-6 py-4 text-slate-700">{{ $repair->vendor ?? '—' }}</td>
                        <td class="px-6 py-4 text-slate-600 max-w-xs truncate">{{ $repair->remarks ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="text-center py-16 px-6">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mx-auto h-12 w-12 text-slate-400">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                                </svg>
                                <h3 class="mt-2 text-lg font-semibold text-slate-800">No Repairs Found</h3>
                                <p class="mt-1 text-sm text-slate-500">Try adjusting your search or filter criteria.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{-- Laravel's default pagination is already styled well with Tailwind --}}
        {{ $repairs->appends(request()->query())->links() }}
    </div>

</div>

</body>
</html>
