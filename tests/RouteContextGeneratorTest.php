<?php

namespace AiContextBundle\Tests\Generator;

use AiContextBundle\Generator\RouteContextGenerator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;

class RouteContextGeneratorTest extends TestCase
{
    public function testGenerateReturnsExpectedRoutes()
    {
        $routeCollection = new RouteCollection();

        $route = new Route('/test/path', ['_controller' => 'App\\Controller\\TestController::index'], [], ['_locale' => 'en'], '', [], ['GET']);
        $routeCollection->add('test_route', $route);

        $routerMock = $this->createMock(RouterInterface::class);
        $routerMock->method('getRouteCollection')->willReturn($routeCollection);

        $generator = new RouteContextGenerator($routerMock);
        $context = $generator->generate();

        $this->assertIsArray($context);
        $this->assertCount(1, $context);

        $routeData = $context[0];
        $this->assertSame('test_route', $routeData['name']);
        $this->assertSame('/test/path', $routeData['path']);
        $this->assertSame(['GET'], $routeData['methods']);
        $this->assertSame('App\\Controller\\TestController::index', $routeData['controller']);
//        $this->assertSame(['_locale' => 'en'], $routeData['requirements']);
    }
}
