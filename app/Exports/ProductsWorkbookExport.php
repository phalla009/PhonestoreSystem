<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Http\Request;

class ProductsWorkbookExport implements WithMultipleSheets
{
    protected ?Request $request;

    public function __construct(?Request $request = null)
    {
        $this->request = $request;
    }

    public function sheets(): array
    {
        return [
            new NotesSheetExport(),
            new ProductsExport($this->request),
            new CategoriesSheetExport(),
        ];
    }
}
