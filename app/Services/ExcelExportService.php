<?php

namespace App\Services;

use Maatwebsite\Excel\Facades\Excel;

class ExcelExportService
{
    private static $instance;

    private function __construct()
    {
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function exportProducts($productsExport)
    {
        return Excel::download($productsExport, 'products.xlsx');
    }

    public function exportOrders($ordersExport)
    {
        return Excel::download($ordersExport, 'orders.xlsx');
    }
}
