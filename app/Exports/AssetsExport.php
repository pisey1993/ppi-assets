<?php

namespace App\Exports;

use App\Models\Assets;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AssetsExport implements FromCollection, WithHeadings
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Assets::with(['assignedUser', 'currentLocation'])
            ->when($this->filters['name'] ?? null, fn($q) =>
            $q->where('name', 'like', '%' . $this->filters['name'] . '%')
            )
            ->when($this->filters['status'] ?? null, fn($q) =>
            $q->where('status', $this->filters['status'])
            )
            ->when($this->filters['department'] ?? null, function ($q) {
                $q->whereHas('currentLocation', fn($subQuery) =>
                $subQuery->where('name', $this->filters['department'])
                );
            })
            ->when($this->filters['purchase_date_from'] ?? null, fn($q) =>
            $q->whereDate('purchase_date', '>=', $this->filters['purchase_date_from'])
            )
            ->when($this->filters['purchase_date_to'] ?? null, fn($q) =>
            $q->whereDate('purchase_date', '<=', $this->filters['purchase_date_to'])
            )
            ->when($this->filters['purchase_age'] ?? null, function ($q, $age) {
                if (in_array($age, ['under_1', 'under_3', 'under_5'])) {
                    $years = (int) str_replace('under_', '', $age);
                    $cutoff = now()->subYears($years)->toDateString();
                    $q->where('purchase_date', '>=', $cutoff); // Under X years
                } else {
                    $cutoff = now()->subYears((int) $age)->toDateString();
                    $q->where('purchase_date', '<=', $cutoff); // Over X years
                }
            })
            ->orderBy('purchase_date', 'asc')
            ->get();

        // Map to export format
        return $query->map(fn($asset) => [
            'Asset Code'   => $asset->asset_code,
            'Name'         => $asset->name,
            'Purchase Date'=> $asset->purchase_date,
            'Status'       => $asset->status,
            'Assigned To'  => $asset->assignedUser->name ?? '—',
            'Location'     => $asset->currentLocation->name ?? '—',
        ]);
    }

    public function headings(): array
    {
        return [
            'Asset Code',
            'Name',
            'Purchase Date',
            'Status',
            'Assigned To',
            'Location',
        ];
    }
}
