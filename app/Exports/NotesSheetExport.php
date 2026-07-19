<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class NotesSheetExport implements FromArray, WithTitle, WithStyles, ShouldAutoSize
{
    public function array(): array
    {
        return [
            ['How to use this file'],
            [''],
            ['1. "Products" tab', 'Your current product list. Safe to review or export as a backup.'],
            ['2. "Categories" tab', 'Lookup table of every category_id and its name. Use this to fill the category_id column correctly.'],
            [''],
            ['Adding new products'],
            ['- Go to the "Products" tab.'],
            ['- Add a new row below the existing data.'],
            ['- Fill in: name, price, stock, category_id (see "Categories" tab), status, description, add_to_pos.'],
            ['- Leave "id", "sku", "category_name", and "created_at" blank for new rows — these are auto-generated.'],
            [''],
            ['Column rules'],
            ['name', 'Required. Text.'],
            ['price', 'Required. Number, e.g. 12.50'],
            ['stock', 'Required. Whole number, e.g. 20'],
            ['status', 'Optional. Must be "active" or "inactive". Leave blank to default to active.'],
            ['category_id', 'Required. Must match an ID from the "Categories" tab. Example: category_id = 1 → name = អីគេ'],
            ['description', 'Optional. Free text.'],
            ['add_to_pos', 'Optional. Enter "Yes" or "No".'],
            [''],
            ['Example row'],
            ['name', 'price', 'stock', 'status', 'category_id', 'description', 'add_to_pos'],
            ['Sample Product', '9.99', '15', 'active', '1', 'Example description text', 'Yes'],
            [''],
            ['Once your file is ready, upload it using the "Import Excel" button on the Products page.'],
        ];
    }

    public function title(): string
    {
        return 'Notes';
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A6')->getFont()->setBold(true);
        $sheet->getStyle('A12')->getFont()->setBold(true);
        $sheet->getStyle('A21')->getFont()->setBold(true);
        $sheet->getStyle('A22:G22')->getFont()->setBold(true);
        $sheet->getStyle('A23:G23')->getFont()->setItalic(true);

        $sheet->getStyle('A1:G23')
            ->getAlignment()
            ->setVertical(Alignment::VERTICAL_TOP)
            ->setWrapText(true);

        return [];
    }
}