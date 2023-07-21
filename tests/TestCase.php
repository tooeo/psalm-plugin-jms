<?php

namespace Tooeo\PsalmPluginJms\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Psalm\Config;
use Psalm\Context;
use Psalm\Internal\Analyzer\FileAnalyzer;
use Psalm\Internal\Analyzer\ProjectAnalyzer;
use Psalm\Internal\Provider\FakeFileProvider;
use Psalm\Internal\Provider\ParserCacheProvider;
use Psalm\Internal\Provider\Providers;
use Psalm\Internal\RuntimeCaches;
use Psalm\IssueBuffer;

class TestCase extends BaseTestCase
{

    /** @var ProjectAnalyzer */
    protected $projectAnalyzer;

    /** @var FakeFileProvider */
    protected $fileProvider;

    public static function setUpBeforeClass(): void
    {
        ini_set('memory_limit', '-1');

        if (!defined('PSALM_VERSION')) {
            define('PSALM_VERSION', '2.0.0');
        }

        if (!defined('PHP_PARSER_VERSION')) {
            define('PHP_PARSER_VERSION', '4.0.0');
        }

        parent::setUpBeforeClass();
    }

    protected function makeConfig(): Config
    {
        return new TestConfig();
    }

    public function setUp(): void
    {
        parent::setUp();

        RuntimeCaches::clearAll();

        $this->fileProvider = new FakeFileProvider();

        $config = $this->makeConfig();

        $providers = new Providers(
            $this->fileProvider,
            $this->createMock(ParserCacheProvider::class)
        );

        $this->projectAnalyzer = new ProjectAnalyzer(
            $config,
            $providers
        );

        $this->projectAnalyzer->setPhpVersion('7.4', 's');
        $config->initializePlugins($this->projectAnalyzer);
    }

    public function tearDown(): void
    {
        RuntimeCaches::clearAll();
    }

    public function addFile(string $filePath, string $contents): void
    {
        $this->fileProvider->registerFile($filePath, $contents);
        $this->projectAnalyzer->getCodebase()->scanner->addFileToShallowScan($filePath);
    }

    public function analyzeFile(
        string $file_path,
        Context $context,
        bool $track_unused_suppressions = true,
        bool $taint_flow_tracking = false
    ): void {
        $codebase = $this->projectAnalyzer->getCodebase();

        if ($taint_flow_tracking) {
            $this->projectAnalyzer->trackTaintedInputs();
        }

        $codebase->addFilesToAnalyze([$file_path => $file_path]);

        $codebase->scanFiles();

        $codebase->config->visitStubFiles($codebase);

        if ($codebase->alter_code) {
            $this->projectAnalyzer->interpretRefactors();
        }

        $this->projectAnalyzer->trackUnusedSuppressions();

        $file_analyzer = new FileAnalyzer(
            $this->projectAnalyzer,
            $file_path,
            $codebase->config->shortenFileName($file_path)
        );
        $file_analyzer->analyze($context);

        if ($codebase->taint_flow_graph) {
            $codebase->taint_flow_graph->connectSinksAndSources();
        }

        if ($track_unused_suppressions) {
            IssueBuffer::processUnusedSuppressions($codebase->file_provider);
        }
    }
}
