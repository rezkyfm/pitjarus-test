<?php

namespace App\Controllers;

use App\Models\StoreArea;
use App\Models\Store;
use App\Models\ReportProduct;

class Home extends BaseController
{
    public function index(): string
    {
        $storeArea = new StoreArea();
        $data['storeAreas'] = $storeArea->findAll();
        return view('index', $data);
    }

    public function filter()
    {
        $request = service('request');
        $formData = $request->getPost();

        $ReportProduct = new ReportProduct();
        $responseData = $ReportProduct->filterByAreaAndDate($formData['storearea'], $formData['datefrom'], $formData['dateto']);

        return $this->response->setJSON($responseData);
    }
}
