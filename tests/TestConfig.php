<?php

declare(strict_types=1);

namespace Tooeo\PsalmPluginJms\Tests;

use Tooeo\PsalmPluginJms\Plugin;
use const DIRECTORY_SEPARATOR;
use function getcwd;
use Psalm\Config;
use Psalm\Internal\IncludeCollector;

/**
 * borrowed from psalm
 */
class TestConfig extends Config
{
    private static ?Config\ProjectFileFilter $cached_project_files = null;

    /**
     * @psalm-suppress PossiblyNullPropertyAssignmentValue because cache_directory isn't strictly nullable
     */
    public function __construct()
    {
        parent::__construct();
        $this->addPluginClass(Plugin::class);

        $this->throw_exception = false;
        $this->use_docblock_types = true;
        $this->level = 1;
        $this->cache_directory = null;

        $this->base_dir = getcwd() . DIRECTORY_SEPARATOR;

        if (!self::$cached_project_files) {
            self::$cached_project_files = Config\ProjectFileFilter::loadFromXMLElement(
                new \SimpleXMLElement($this->getContents()),
                $this->base_dir,
                true
            );
        }

        $this->project_files = self::$cached_project_files;
        $this->setIncludeCollector(new IncludeCollector());

        $this->collectPredefinedConstants();
        $this->collectPredefinedFunctions();
    }

    protected function getContents() : string
    {
        return '<?xml version="1.0"?>
<psalm>
    <projectFiles>
        <directory name="src/tests" />
    </projectFiles>
    <plugins>
        <pluginClass class="Sfp\Psalm\TypedLocalVariablePlugin\Plugin" />
    </plugins>
</psalm>';
    }

    /**
     * @return false
     */
    public function getComposerFilePathForClassLike(string $fq_classlike_name): bool
    {
        return false;
    }

    public function getProjectDirectories(): array
    {
        return [];
    }
}