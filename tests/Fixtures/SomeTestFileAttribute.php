<?php

namespace Tooeo\PsalmPluginJms\Tests\Fixtures;

use \Tooeo\PsalmPluginJms\Tests\Fixtures\JmsDto;
use Tooeo\PsalmPluginJms\Tests\Fixtures as FixturesAlias;
use Tooeo\PsalmPluginJms\Tests\Fixtures\Assert as AssertW;


class SomeTestFileAttribute
{
    /**
     * @psalm-suppress MissingConstructor
     */
    #[Assert\Type('\Tooeo\PsalmPluginJms\Tests\Fixtures\JmsDto')]
    public string $good;

    /**
     * @psalm-suppress MissingConstructor
     */
    #[Assert\Type('SameNamespace::class')]
    public string $goodSameNamespace;
    /**
     * @psalm-suppress MissingConstructor
     */
    #[Assert\Type('array<AssertW\JmsDto>')]
    public string $goodArray;

    /**
     * @psalm-suppress MissingConstructor
     */
    #[Assert\Type('FixturesAlias\JmsDto')]
    public string $goodFixturesAliasJmsDto;

    /**
     * @psalm-suppress MissingConstructor
     */
    #[Assert\Type('\Tooeo\PsalmPluginJms\Tests\Fixtures\JmsDto::class')]
    public string $goodClass;

    /**
     * @psalm-suppress MissingConstructor
     */
    #[Assert\Type('\Api\User\Dto\CurrencyError')]
    public string $error;

    /**
     * @psalm-suppress MissingConstructor
     */
    #[Assert\Type('array<\Api\User\Dto\CurrencyError>')]
    public string $errorArray;


    /**
     * @psalm-suppress MissingConstructor
     */
    #[Assert\Type('\Api\User\Dto\CurrencyError::class')]
    public string $errorClass;

    /**
     * @psalm-suppress MissingConstructor
     */
    #[Assert\Type('FixturesAlias\JmsDto>')]
    public string $errorClassWrongClass;

}
