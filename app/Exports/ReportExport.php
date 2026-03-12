<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class ReportExport implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    protected $results;
    protected $type;

    public function __construct($results, $type)
    {
        $this->results = $results;
        $this->type    = $type;
    }

    public function collection()
    {
        return $this->results->map(function ($row) {
            return match ($this->type) {
                'sales' => [
                    'Date'      => $row->date,
                    'Total USD' => number_format($row->total ?? 0, 2),
                    'Total KHR' => number_format(($row->total ?? 0) * 4000, 0),
                ],
                'financial' => [
                    'Date'         => $row->date,
                    'Sold Qty'     => $row->sold_qty ?? 0,
                    'Revenue USD'  => number_format($row->revenue ?? 0, 2),
                ],
                'inventory' => [
                    'Product'   => $row->name,
                    'Quantity'  => $row->quantity ?? $row->stock ?? 0,
                    'Price USD' => number_format($row->price ?? 0, 2),
                    'Total USD' => number_format(($row->quantity ?? $row->stock ?? 0) * ($row->price ?? 0), 2),
                ],
                'customer' => [
                    'Name'        => $row->name,
                    'Gender'      => ucfirst($row->gender),
                    'Total Qty'   => $row->total_qty ?? 0,
                    'Total Price' => number_format($row->total_price ?? 0, 2),
                    'Joined'      => $row->created_at,
                ],
                default => (array) $row,
            };
        });
    }

    public function headings(): array
    {
        return match ($this->type) {
            'sales'     => ['Date', 'Total (USD)', 'Total (KHR)'],
            'financial' => ['Date', 'Sold Quantity', 'Revenue (USD)'],
            'inventory' => ['Product', 'Quantity', 'Price (USD)', 'Total (USD)'],
            'customer'  => ['Name', 'Gender', 'Total Qty', 'Total Price', 'Joined'],
            default     => [],
        };
    }

    public function title(): string
    {
        return ucfirst($this->type) . ' Report';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF1A73E8']],
            ],
        ];
    }
}