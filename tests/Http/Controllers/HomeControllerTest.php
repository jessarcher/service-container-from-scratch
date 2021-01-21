<?php

namespace Tests\Http\Controllers;

use App\Container\Container;
use App\Http\Controllers\HomeController;
use App\Support\Pdf;
use Mockery;
use Tests\TestCase;

class HomeControllerTest extends TestCase
{
    public function test_it_can_swap_dependencies()
    {
        $mockPdf = Mockery::mock(Pdf::class);
        $mockPdf->shouldReceive('generate')->andReturn('mocked pdf response');
        $this->swap(Pdf::class, $mockPdf);

        $container = Container::getInstance();

        $response = $container->make(HomeController::class)->index();

        $this->assertEquals('mocked pdf response', $response);
    }

    public function test_it_can_mock_via_the_facade()
    {
        $this->markTestSkipped();

        \App\Facades\Pdf::shouldReceive('generate')->andReturn('mocked pdf response');

        $container = Container::getInstance();

        $response = $container->make(HomeController::class)->index();

        $this->assertEquals('mocked pdf response', $response);

        $response = $container->make(HomeController::class)->index();

        $this->assertEquals('mocked pdf response', $response);
    }
}
