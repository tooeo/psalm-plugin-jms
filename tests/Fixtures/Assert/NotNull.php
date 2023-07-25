<?php

namespace Tooeo\PsalmPluginJms\Tests\Fixtures\Assert;

#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY)]
class NotNull
{
    public function __construct()
    {
    }
}