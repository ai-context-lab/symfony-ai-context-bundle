<?php

namespace AiContextBundle\Tests\fixtures\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

class DummyController extends AbstractController
{
    #[Route('/test', name: 'test_index', methods: ['GET'])]
    public function index(): void {}
}
