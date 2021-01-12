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

        $controller = $container->make(HomeController::class);

        $response = $controller->index();

        $this->assertEquals('mocked pdf response', $response);
    }
}
