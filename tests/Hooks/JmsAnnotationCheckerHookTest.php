<?php

namespace Tooeo\PsalmPluginJms\Tests\Hooks;

use PHPUnit\Framework\TestCase;
use Psalm\Aliases;
use Tooeo\PsalmPluginJms\Hooks\JmsAnnotationCheckerHook;

class JmsAnnotationCheckerHookTest extends TestCase
{

    /**
     *
     * @dataProvider parseClassDataProvider
     * @param string      $comment
     * @param string|null $expected
     * @return void
     */
    public function testParseClass(string $comment, ?string $expected)
    {
        $comment = <<<CODE
   /**
     * $comment
     */
CODE;
        $result = JmsAnnotationCheckerHook::parseClass($comment);

        $this->assertEquals($expected, $result);
    }


    public function parseClassDataProvider()
    {
        return [
            [
                '@JMS\Type("Tooeo\PsalmPluginJms\Tests\Fixtures\JmsDto");',
                'Tooeo\PsalmPluginJms\Tests\Fixtures\JmsDto',
            ],
            [
                '@JMS\Type(\'Tooeo\PsalmPluginJms\Tests\Fixtures\JmsDto\');',
                'Tooeo\PsalmPluginJms\Tests\Fixtures\JmsDto',
            ],
            [
                '@JMS\Type(\'JmsDto::class\');',
                'JmsDto::class',
            ],
            [
                '@JMS\Type(\'JmsDto::class\');',
                'JmsDto::class',
            ],
            [
                '@JMS\Type("JmsDto::class");',
                'JmsDto::class',
            ],
            [
                '@JMS\Type("array<Tooeo\PsalmPluginJms\Tests\Fixtures\JmsDto>");',
                'Tooeo\PsalmPluginJms\Tests\Fixtures\JmsDto',
            ],
            [
                '@JMS\Type(\'array<Tooeo\PsalmPluginJms\Tests\Fixtures\JmsDto>\');',
                'Tooeo\PsalmPluginJms\Tests\Fixtures\JmsDto',
            ],
            [
                '@JMS\Type(array<Tooeo\PsalmPluginJms\Tests\Fixtures\JmsDto>);',
                'Tooeo\PsalmPluginJms\Tests\Fixtures\JmsDto',
            ],
            [
                '@JMS\Type(array<JmsDto::class>);',
                'JmsDto::class',
            ],
            [
                '@JMS\Type(array<Api\User\Dto\CurrencyError>);',
                'Api\User\Dto\CurrencyError',
            ],
            [
                '@JMS\Type(array<Api\User\Dto\CurrencyError);',
                'array<Api\User\Dto\CurrencyError',
            ],
            [
                '@JMS\Type(array<string>);',
                null,
            ],
            [
                '@JMS\Type(string);',
                null,
            ],
            [
                '@JMS\Type(int);',
                null,
            ],
            [
                '@JMS\Type(bool);',
                null,
            ],
            [
                '@JMS\Type(boolean);',
                null,
            ],
            [
                '@JMS\Type(float);',
                null,
            ],
            [
                '@JMS\Type(array<string, string>);',
                null,
            ],
            [
                '@JMS\Type(array<string, int>);',
                null,
            ],
            [
                '@JMS\Type(array<int,integer>);',
                null,
            ],
            [
                '@JMS\Type("array<string,Tooeo\PsalmPluginJms\Tests\Fixtures\JmsDto>");',
                'Tooeo\PsalmPluginJms\Tests\Fixtures\JmsDto',
            ],
            [
                '@JMS\Type("array<int, Tooeo\PsalmPluginJms\Tests\Fixtures\JmsDto>");',
                'Tooeo\PsalmPluginJms\Tests\Fixtures\JmsDto',
            ],
            [
                '@JMS\Type("array<int, JmsDto::class>");',
                'JmsDto::class',
            ],
            [
                '@JMS\Type("Enum<JmsDto::class>");',
                'JmsDto::class',
            ],
            [
                '@JMS\Type("array<enum<JmsDto::class>>");',
                'JmsDto::class',
            ],
            [
                '@JMS\Type("array<string, enum<JmsDto::class>>");',
                'JmsDto::class',
            ],
        ];
    }

    /**
     * @dataProvider isClassExistsProvider
     * @param string $class
     * @param bool   $expected
     * @return void
     */
    public function testIsClassExists(string $class, bool $expected)
    {
        $uses = [
            'jmsdto' => 'Tooeo\PsalmPluginJms\Tests\Fixtures\JmsDto',
            'assertw' => 'Tooeo\PsalmPluginJms\Tests\Fixtures\Assert',
            'jmsdtoalias' => 'Tooeo\PsalmPluginJms\Tests\Fixtures\JmsDto',
            'fixturesalias' => 'Tooeo\PsalmPluginJms\Tests\Fixtures',
        ];

        $namespace = 'Tooeo\PsalmPluginJms\Tests\Fixtures';

        $result = JmsAnnotationCheckerHook::isClassExists($class, $uses, $namespace);

        $this->assertEquals($expected, $result);
    }

    public function isClassExistsProvider()
    {
        return [
            ['Tooeo\PsalmPluginJms\Tests\Fixtures\JmsDto', true],
            ['JmsDtoAlias', true],
            ['JmsDto', true],
            ['Tooeo\PsalmPluginJms\Tests\Fixtures\JmsDto::class', true],
            ['FixturesAlias\JmsDto::class', true],
            ['SameNamespace::class', true],
            ['JmsDtoInterface', true],
            ['JmsDtoWrongInterface', false],
            ['SameNamespace::class', true],
        ];
    }
}
