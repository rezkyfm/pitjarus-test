<?php

namespace App\Models;

use CodeIgniter\Model;

class ReportProduct extends Model
{
    protected $table = 'report_product';
    protected $primaryKey = 'report_id';

    protected $allowedFields = ['store_id', 'product_id', 'compliance', 'tanggal'];

    public function filterByAreaAndDate(array $storeArea, $dateFrom, $dateTo)
    {
        
        return $this->select('SUM(report_product.compliance)/ COUNT(report_product.report_id)*100 as value, store_area.area_name, product_brand.brand_name') 
            ->join('store', 'store.store_id = report_product.store_id')
            ->join('store_area', 'store.area_id = store_area.area_id')
            ->join('product', 'product.product_id = report_product.product_id')
            ->join('product_brand', 'product_brand.brand_id = product.brand_id')
            ->whereIn('store_area.area_id', $storeArea)
            ->where('DATE(tanggal) >=',$dateFrom)
            ->where('DATE(tanggal) <=',$dateTo)
            ->groupBy('store_area.area_name')
            ->groupBy('product_brand.brand_name')
            ->find();
    }
}