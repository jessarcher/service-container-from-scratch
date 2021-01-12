<?php

namespace Tests\Facades;

use App\Facades\Pdf;
use PHPUnit\Framework\TestCase;

class PdfTest extends TestCase
{
    public function test_it_proxies_static_calls()
    {
        $pdf = Pdf::generate('thing');

        $this->assertEquals('pdf version of thing', $pdf);
    }

    public function test_it_can_be_mocked()
    {
        Pdf::shouldReceive('generate')->andReturn('mocked response');

        $pdf = Pdf::generate('something');

        $this->assertEquals('mocked response', $pdf);
    }
}
