<?php

namespace App\Controllers;

class TestController extends BaseController
{
    public function materialRequests()
    {
        $data = [
            'title' => 'Material Requests - Test',
            'materialRequests' => [],
            'projects' => [],
            'users' => [],
            'filters' => []
        ];

        return view('procurement/material_requests/index', $data);
    }
}
