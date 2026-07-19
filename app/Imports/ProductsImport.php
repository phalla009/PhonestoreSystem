<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;

class ProductsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    /**
     * Clean up each row BEFORE validation runs.
     * This fixes common Excel issues: extra spaces, wrong case,
     * category_id stored as text/float instead of int, etc.
     */
    public function prepareForValidation($data, $index)
    {
        // Trim all string values and collapse empty strings to null
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $value = trim($value);
                $data[$key] = $value === '' ? null : $value;
            }
        }

        // Normalize status to lowercase (Active -> active, INACTIVE -> inactive)
        if (isset($data['status'])) {
            $data['status'] = strtolower($data['status']);
        }

        // category_id sometimes comes in as "2.0" or " 2 " from Excel — force to int
        if (isset($data['category_id']) && $data['category_id'] !== null) {
            $data['category_id'] = (int) $data['category_id'];
        }

        // add_to_pos sometimes comes as "Yes"/"No"/"TRUE"/"1" — normalize to 0/1
        if (isset($data['add_to_pos'])) {
            $data['add_to_pos'] = in_array(strtolower((string) $data['add_to_pos']), ['1', 'yes', 'true'], true) ? 1 : 0;
        }

        return $data;
    }

    public function model(array $row)
    {
        $product = Product::create([
            'name'        => $row['name'],
            'price'       => $row['price'],
            'stock'       => $row['stock'],
            'status'      => $row['status'] ?? 'active',
            'description' => $row['description'] ?? null,
            'category_id' => $row['category_id'],
            'add_to_pos'  => $row['add_to_pos'] ?? 0,
        ]);

        $product->update([
            'sku' => 'kr' . $product->created_at->format('Ymd') . str_pad($product->id, 2, '0', STR_PAD_LEFT),
        ]);

        return $product;
    }

    public function rules(): array
    {
        return [
            'name'        => 'required|string',
            'price'       => 'required|numeric',
            'stock'       => 'required|integer',
            'status'      => 'nullable|in:active,inactive',
            'category_id' => 'required|integer|exists:categories,id',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'category_id.required' => 'Category is missing or blank in this row — check the category_id column.',
            'category_id.exists'   => 'This category_id does not match any existing category.',
            'status.in'            => 'Status must be either "active" or "inactive" (case-insensitive is fine now).',
        ];
    }
}