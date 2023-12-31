<?php

namespace Tooeo\PsalmPluginJms\Tests\Helpers;

use PHPUnit\Framework\TestCase;
use Tooeo\PsalmPluginJms\Helpers\CheckClassExistsHelper;

class CheckClassExistsHelperTest extends TestCase
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
        CheckClassExistsHelper::addIgnoredType('MixedType');
        CheckClassExistsHelper::addIgnoredType('ArrayOrString');
        $result = CheckClassExistsHelper::parseClass($comment);

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
                '@JMS\Type(array<string, array<string>>);',
                null,
            ],
            [
                '@JMS\Type(array<string, array<string, JmsDto>>);',
                'JmsDto',
            ],
            [
                '@JMS\Type("MixedType")',
                null,
            ],
            [
                '@JMS\Type("ArrayOrString")',
                null,
            ],
            [
                '@JMS\Type(DateTime<\'Y-m-d\'>)',
                'DateTime',
            ],
            [
                '@JMS\Type("DateTime<\'d.m.Y H:i:s\', \'UTC\'>")',
                'DateTime',
            ],
            [
                '@JMS\Type(name="DateTime<\'d.m.Y H:i:s\', \'UTC\'>")',
                'DateTime',
            ],
            [
                '@JMS\Type(name="string")',
                null,
            ],
            [
                '@JMS\Type(name=array<enum<JmsDto::class>>)',
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

        $result = CheckClassExistsHelper::isClassExists($class, $uses, $namespace);

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

    /**
     * @dataProvider  findGroupDataProvider
     * @return void
     */
    public function testFindGroup(string $content, string $expected)
    {
        $result = CheckClassExistsHelper::findGroup($content);

        $this->assertEquals($expected, $result);
    }

    public function findGroupDataProvider()
    {
        return [
            ['Class', 'Class'],
            ['array<Class>', 'Class'],
            ['array<enum<string>>', 'string'],
            ['array<enum<string, int>>', 'int'],
            ['array<enum<string, Class>>', 'Class'],
            ['array<enum<string, Class::class>>', 'Class::class'],
            ['array<enum<string, array<enum<array<enum<string, Class::class>>>>>>', 'Class::class'],
        ];
    }
}
