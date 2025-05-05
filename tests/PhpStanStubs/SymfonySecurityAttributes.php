<?php

namespace Symfony\Component\Security\Http\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class IsGranted
{
    public function __construct(
        public string $attribute,
        public mixed $subject = null
    ) {}
}
