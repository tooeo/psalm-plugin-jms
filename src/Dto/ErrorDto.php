<?php

namespace Tooeo\PsalmPluginJms\Dto;

class ErrorDto
{
    protected bool $isFixable = true;

    public function __construct(
        protected string $class,
        protected array $suppressed = [])
    {
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * @return array
     */
    public function getSuppressed(): array
    {
        return $this->suppressed;
    }

    public function isFixable()
    {
        return $this->isFixable;
    }
}
