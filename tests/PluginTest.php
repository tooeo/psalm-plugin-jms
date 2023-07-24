<?php

namespace Tooeo\PsalmPluginJms\Tests;

use Psalm\Config;
use Psalm\Context;
use Psalm\Internal\Provider\FakeFileProvider;
use Psalm\Internal\RuntimeCaches;
use Psalm\IssueBuffer;
use SimpleXMLElement;
use Tooeo\PsalmPluginJms\Hooks\JmsAnnotationCheckerHook;
use Tooeo\PsalmPluginJms\Plugin;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psalm\Plugin\RegistrationInterface;

class PluginTest extends TestCase
{
    use ProphecyTrait;
    /**
     * @var ObjectProphecy<RegistrationInterface>
     */
    private $registration;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->registration = $this->prophesize(RegistrationInterface::class);
        RuntimeCaches::clearAll();
        $this->fileProvider = new FakeFileProvider();

        parent::setUp();
    }

    /**
     * @test
     * @return void
     */
    public function hasEntryPoint()
    {
        $this->expectNotToPerformAssertions();
        $plugin = new Plugin();
        $plugin($this->registration->reveal(), null);
    }

    /**
     * @test
     * @return void
     */
    public function acceptsConfig()
    {
        $plugin = new Plugin();
        $plugin($this->registration->reveal(), new SimpleXMLElement(<<<XML
          <pluginClass class="Tooeo\PsalmPluginJms\Plugin">
            <ignoringTypes>
                        <ignored>testAdd</ignored>
                        <ignored remove="true">integer</ignored>
            </ignoringTypes>
          </pluginClass>
XML
 ));

        $ignored = JmsAnnotationCheckerHook::getIgnoredType();
        $this->assertTrue(in_array('testadd', $ignored), 'testAdd');
        $this->assertTrue(!in_array('integer', $ignored), 'integer');
    }


    public function testPluginMain(): void
    {
        $this->addFile(
            __METHOD__,
            file_get_contents(__DIR__.'/Fixtures/SomeTestFile.php')
        );
        $this->analyzeFile(__METHOD__, new Context());
        $this->assertSame(4, IssueBuffer::getErrorCount());

        $errors = $this->getErrors(__METHOD__);
        $errorsToCheck = [
            'Class AssertW\JmsDto does not exists',
            'Class FixturesAlias\JmsDto> does not exists',
            'Class \Api\User\Dto\CurrencyError does not exists',
            'Class \Api\User\Dto\CurrencyError::class does not exists',
        ];

        foreach ($errorsToCheck as $error) {
            $this->assertTrue(isset($errors[$error]), $error);
        }
    }

    protected function makeConfig(): Config
    {
        $config = new TestConfig();
        $config->setCustomErrorLevel('MissingConstructor', 'suppress');

        return $config;
    }

    private function getErrors(string $method): array
    {
        $errors = [];
        foreach (IssueBuffer::getIssuesData()[$method] ?? [] as $issuesDatum) {
            $errors[$issuesDatum->message] = $issuesDatum->message;
        }

        return $errors;
    }
}
