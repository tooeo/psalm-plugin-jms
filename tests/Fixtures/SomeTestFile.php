<?php

namespace Tooeo\PsalmPluginJms\Tests\Fixtures;

use \Tooeo\PsalmPluginJms\Tests\Fixtures\JmsDto;
use Tooeo\PsalmPluginJms\Tests\Fixtures as FixturesAlias;
use Tooeo\PsalmPluginJms\Tests\Fixtures\Assert as AssertW;


class SomeTestFile
{
//    /**
//     * @JMS\Type('\Tooeo\PsalmPluginJms\Tests\Fixtures\JmsDto');
//     * @psalm-suppress MissingConstructor
//     */
//    #[Assert\Type('numeric')]
//    public string $good;
//

    #[Assert\Type(type: 'JmsDto', message: 'dddd')]
    #[Assert\SerializedName('amount')]
    public string $good2;

    /**
     * @JMS\Type(SameNamespace::class);
     * @psalm-suppress MissingConstructor
     */
    public string $goodSameNamespace;
    /**
     * @JMS\Type('array<AssertW\JmsDto>');
     * @psalm-suppress MissingConstructor
     */
    public string $goodArray;

    /**
     * @JMS\Type('FixturesAlias\JmsDto');
     * @psalm-suppress MissingConstructor
     */
    public string $goodFixturesAliasJmsDto;

    /**
     * @JMS\Type(\Tooeo\PsalmPluginJms\Tests\Fixtures\JmsDto::class);
     * @psalm-suppress MissingConstructor
     */
    public string $goodClass;

    /**
     * @JMS\Type('\Api\User\Dto\CurrencyError');
     * @psalm-suppress MissingConstructor
     */
    public string $error;

    /**
     * @JMS\Type(array<\Api\User\Dto\CurrencyError>);
     * @psalm-suppress MissingConstructor
     */
    public string $errorArray;


    /**
     * @JMS\Type(\Api\User\Dto\CurrencyError::class);
     * @psalm-suppress MissingConstructor
     */
    public string $errorClass;

    /**
     * @JMS\Type(FixturesAlias\JmsDto>);
     * @psalm-suppress MissingConstructor
     */
    public string $errorClassWrongClass;

}
