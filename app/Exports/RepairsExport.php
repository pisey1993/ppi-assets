<?php

namespace App\Exports;

use App\Models\Repair;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RepairsExport implements FromCollection, WithHeadings
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Repair::query();

        // Apply filters same as in controller
        if (!empty($this->filters['repair_date_from'])) {
            $query->whereDate('repair_date', '>=', $this->filters['repair_date_from']);
        }
        if (!empty($this->filters['repair_date_to'])) {
            $query->whereDate('repair_date', '<=', $this->filters['repair_date_to']);
        }
        if (!empty($this->filters['issue'])) {
            $query->where('issue', 'like', '%'.$this->filters['issue'].'%');
        }
        if (!empty($this->filters['repair_cost_min'])) {
            $query->where('repair_cost', '>=', $this->filters['repair_cost_min']);
        }
        if (!empty($this->filters['repair_cost_max'])) {
            $query->where('repair_cost', '<=', $this->filters['repair_cost_max']);
        }
        if (!empty($this->filters['status'])) {
            $query->where('status', 'like', '%'.$this->filters['status'].'%');
        }
        if (!empty($this->filters['vendor'])) {
            $query->where('vendor', 'like', '%'.$this->filters['vendor'].'%');
        }
        if (!empty($this->filters['remarks'])) {
            $query->where('remarks', 'like', '%'.$this->filters['remarks'].'%');
        }

        return $query->orderBy('repair_date', 'desc')->get([
            'repair_date',
            'issue',
            'status',
            'repair_cost',
            'vendor',
            'remarks'
        ]);
    }

    public function headings(): array
    {
        return [
            'Repair Date',
            'Issue',
            'Status',
            'Repair Cost',
            'Vendor',
            'Remarks',
        ];
    }
}
