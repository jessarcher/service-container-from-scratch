<?php

namespace App\Http\Controllers;

use App\Support\Pdf;

class HomeController
{
    public function __construct(protected Pdf $pdf) {}

    public function index()
    {
        return $this->pdf->generate('hello');
    }
}
