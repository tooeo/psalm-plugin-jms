<?php

namespace Tooeo\PsalmPluginJms;

use SimpleXMLElement;
use Psalm\Plugin\PluginEntryPointInterface;
use Psalm\Plugin\RegistrationInterface;
use Tooeo\PsalmPluginJms\Hooks\JmsAnnotationCheckerHook;

class Plugin implements PluginEntryPointInterface
{
    /** @return void */
    public function __invoke(RegistrationInterface $psalm, ?SimpleXMLElement $config = null): void
    {
        require_once __DIR__.'/Hooks/JmsAnnotationCheckerHook.php';
        $psalm->registerHooksFromClass(JmsAnnotationCheckerHook::class);
    }
}