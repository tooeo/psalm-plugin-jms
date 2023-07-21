<?php

namespace Tooeo\PsalmPluginJms\Tests\Fixtures;

use \Tooeo\PsalmPluginJms\Tests\Fixtures\JmsDto;
use Tooeo\PsalmPluginJms\Tests\Fixtures as FixturesAlias;
use Tooeo\PsalmPluginJms\Tests\Fixtures\Assert as AssertW;

class SomeTestFile
{
    /**
     * @JMS\Type("\Tooeo\PsalmPluginJms\Tests\Fixtures\JmsDto");
     */
    public string $good;
    /**
     * @JMS\Type("array<AssertW\JmsDto>");
     */
    public string $goodArray;

    /**
     * @JMS\Type("FixturesAlias\JmsDto");
     */
    public string $goodFixturesAliasJmsDto;

    /**
     * @JMS\Type(\Tooeo\PsalmPluginJms\Tests\Fixtures\JmsDto::class);
     */
    public string $goodClass;

    /**
     * @JMS\Type("\Api\User\Dto\CurrencyError");
     */
    public string $error;

    /**
     * @JMS\Type(array<\Api\User\Dto\CurrencyError>);
     */
    public string $errorArray;


    /**
     * @JMS\Type(\Api\User\Dto\CurrencyError::class);
     */
    public string $errorClass;

    /**
     * @JMS\Type(FixturesAlias\JmsDto>);
     */
    public string $errorClassWrongClass;

}
