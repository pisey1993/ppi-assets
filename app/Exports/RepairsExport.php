<?php

namespace App\Exports;

use App\Models\Repair;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RepairsExport implements FromCollection, WithHeadings
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Repair::query();

        if (!empty($this->filters['repair_date_from'])) {
            $query->whereDate('repair_date', '>=', $this->filters['repair_date_from']);
        }

        if (!empty($this->filters['repair_date_to'])) {
            $query->whereDate('repair_date', '<=', $this->filters['repair_date_to']);
        }

        // Add other filters similarly...

        return $query->get([
            'id',
            'asset_id',
            'repair_date',
            'issue',
            'repair_cost',
            'status',
            'vendor',
            'remarks',
            'created_at',
            'updated_at',
        ]);
    }

    public function headings(): array
    {
        return [
            'ID',
            'Asset ID',
            'Repair Date',
            'Issue',
            'Repair Cost',
            'Status',
            'Vendor',
            'Remarks',
            'Created At',
            'Updated At',
        ];
    }
}
