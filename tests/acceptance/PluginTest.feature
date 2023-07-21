Feature: basics
  In order to test my plugin
  As a plugin developer
  I need to have tests

  Background:
    Given I have the following config
      """
      <?xml version="1.0"?>
      <psalm totallyTyped="true">
        <projectFiles>
          <directory name="."/>
        </projectFiles>
        <plugins>
          <pluginClass class="Tooeo\PsalmPluginJms\Plugin" />
        </plugins>
      </psalm>
      """
  Scenario: run without errors
    Given I have the following code
      """
<?php

namespace Tooeo\PsalmPluginJms\Tests\Fixtures;

use \Tooeo\PsalmPluginJms\Tests\Fixtures\JmsDto;
use Tooeo\PsalmPluginJms\Tests\Fixtures as FixturesAlias;
use Tooeo\PsalmPluginJms\Tests\Fixtures\Assert as AssertW;

class SomeTestFile
{
    /**
     * @JMS\Type('\Tooeo\PsalmPluginJms\Tests\Fixtures\JmsDto');
     * @psalm-suppress PropertyNotSetInConstructor
     */
    public string $good;
    /**
     * @JMS\Type('array<JmsDto>');
     * @psalm-suppress PropertyNotSetInConstructor
     */
    public string $goodArray;

    /**
     * @JMS\Type('FixturesAlias\JmsDto');
     * @psalm-suppress PropertyNotSetInConstructor
     */
    public string $goodFixturesAliasJmsDto;

    /**
     * @JMS\Type(\Tooeo\PsalmPluginJms\Tests\Fixtures\JmsDto::class);
     * @psalm-suppress PropertyNotSetInConstructor
     */
    public string $goodClass;
}
      """
    When I run Psalm
    Then I see no errors

  Scenario: run with errors
    Given I have the following code
      """
<?php

namespace Tooeo\PsalmPluginJms\Tests\Fixtures;

use \Tooeo\PsalmPluginJms\Tests\Fixtures\JmsDto;
use Tooeo\PsalmPluginJms\Tests\Fixtures as FixturesAlias;
use Tooeo\PsalmPluginJms\Tests\Fixtures\Assert as AssertW;

class SomeTestFile
{
    /**
     * @JMS\Type('\Tooeo\PsalmPluginJms\Tests\Fixtures\JmsDto');
     * @psalm-suppress PropertyNotSetInConstructor
     */
    public string $good;

    /**
     * @JMS\Type('array<FixturesAlias\JmsDto>');
     * @psalm-suppress PropertyNotSetInConstructor
     */
    public string $goodArray;

    /**
     * @JMS\Type('array<AssertW\JmsDto>');
     * @psalm-suppress PropertyNotSetInConstructor
     */
    public string $errorArray;

    /**
     * @JMS\Type('FixturesAlias\JmsDto');
     * @psalm-suppress PropertyNotSetInConstructor
     */
    public string $goodFixturesAliasJmsDto;

    /**
     * @JMS\Type(\Tooeo\PsalmPluginJms\Tests\Fixtures\JmsDto::class);
     * @psalm-suppress PropertyNotSetInConstructor
     */
    public string $goodClass;

    /**
     * @JMS\Type('\Api\User\Dto\CurrencyError');
     * @psalm-suppress PropertyNotSetInConstructor
     */
    public string $error;

    /**
     * @JMS\Type(array<\Api\User\Dto\CurrencyError>);
     * @psalm-suppress PropertyNotSetInConstructor
     */
    public string $errorArray;


    /**
     * @JMS\Type(\Api\User\Dto\CurrencyError::class);
     * @psalm-suppress PropertyNotSetInConstructor
     */
    public string $errorClass;

    /**
     * @JMS\Type(FixturesAlias\JmsDto>);
     * @psalm-suppress PropertyNotSetInConstructor
     */
    public string $errorClassWrongClass;

}
      """
    When I run Psalm
    Then I see these errors
      | Type                 | Message                                                                             |
      | PropertyTypeCoercion | Class AssertW\JmsDto does not exists                                                |
      | PropertyTypeCoercion | Class \Api\User\Dto\CurrencyError does not exists                                   |
      | PropertyTypeCoercion | Class \Api\User\Dto\CurrencyError::class does not exists                            |
      | PropertyTypeCoercion | Class FixturesAlias\JmsDto> does not exists                                         |
    And I see no other errors

