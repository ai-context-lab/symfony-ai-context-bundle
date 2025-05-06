<?php

namespace AiContextBundle\Tests\fixtures\Event;
use Symfony\Contracts\EventDispatcher\Event;

class MyEvent extends Event
{
    public function __construct(
        private int $userId,
        private string $email,
    ) {}

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
