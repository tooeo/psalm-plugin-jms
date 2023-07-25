<?php

namespace Tooeo\PsalmPluginJms\Tests\Fixtures\Assert;

#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY)]
class Type
{
    public function __construct(mixed $type, string $message = null)
    {
    }
}