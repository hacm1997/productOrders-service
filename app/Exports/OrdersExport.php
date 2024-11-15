<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OrdersExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Order::all(['id', 'user_id', 'product_id', 'status', 'total', 'created_at']);
    }

    public function headings(): array
    {
        return ['ID', 'User ID', 'Product ID', 'Status', 'Total', 'Created At'];
    }
}
