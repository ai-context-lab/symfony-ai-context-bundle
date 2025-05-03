<?php

namespace AiContextBundle\Tests\fixtures\Service;

class DummyService
{
    public function sayHello(string $name = 'world'): string
    {
        return "Hello $name";
    }
}
