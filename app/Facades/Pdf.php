<?php

namespace App\Facades;

use App\Support\Pdf as PdfService;

class Pdf extends Facade
{
    protected static function getFacadeAccessor()
    {
        return PdfService::class;
    }
}
