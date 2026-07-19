<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Http\Request;

class ProductsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
{
    protected ?Request $request;

    public function __construct(?Request $request = null)
    {
        $this->request = $request;
    }

    /**
     * Pull the same filtered data set as the products index page,
     * so "Export" respects whatever search/category filter is active.
     */
    public function collection(): Collection
    {
        $query = Product::with(['category']);

        if ($this->request) {
            if ($this->request->search) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->request->search . '%')
                      ->orWhere('sku', 'like', '%' . $this->request->search . '%');
                });
            }

            if ($this->request->has('category_id') && $this->request->category_id != '') {
                $query->where('category_id', $this->request->category_id);
            }
        }

        return $query->get();
    }

    /**
     * IMPORTANT: headings must match the import column keys exactly
     * (lowercase, underscored) so this exported file can be re-uploaded
     * through the Import Excel button without any editing.
     */
    public function headings(): array
    {
        return [
            'id',
            'sku',
            'name',
            'category_id',
            'category_name',
            'price',
            'stock',
            'status',
            'add_to_pos',
            'description',
            'created_at',
        ];
    }

    public function map($product): array
    {
        return [
            $product->id,
            $product->sku,
            $product->name,
            $product->category_id,
            $product->category->name ?? 'N/A',
            number_format($product->price, 2),
            $product->stock,
            $product->status,
            $product->add_to_pos ? 'Yes' : 'No',
            $product->description,
            $product->created_at?->timezone('Asia/Phnom_Penh')->format('d M, Y h:i A'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return 'Products';
    }
}
