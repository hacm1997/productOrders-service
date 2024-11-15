<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Product::all(['id', 'name', 'description', 'price', 'stock', 'created_at']);
    }

    public function headings(): array
    {
        return ['ID', 'Name', 'Description', 'Price', 'Stock', 'Created At'];
    }
}
