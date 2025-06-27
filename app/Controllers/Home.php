<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        return view('website/header')
            . view('website/index' )
            . view('website/footer');

    }
}
