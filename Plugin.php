<?php

namespace Tooeo\PsalmPluginJms;

use SimpleXMLElement;
use Psalm\Plugin\PluginEntryPointInterface;
use Psalm\Plugin\RegistrationInterface;
use Tooeo\PsalmPluginJms\Hooks\JmsAnnotationCheckerHook;

/**
 * @psalm-suppress UnusedClass
 */
class Plugin implements PluginEntryPointInterface
{
    /** @return void */
    public function __invoke(RegistrationInterface $psalm, ?SimpleXMLElement $config = null): void
    {
        require_once __DIR__.'/src/Hooks/JmsAnnotationCheckerHook.php';
        $psalm->registerHooksFromClass(JmsAnnotationCheckerHook::class);

        if ($config === null) {
            return;
        }

        if (!$config->typies instanceof SimpleXMLElement) {
            return;
        }

        foreach ($config->plugins ?? [] as $plugin) {
            $class = (string)$plugin->pluginClass->attributes()['class'];
            if ($class === self::class) {
                foreach ($plugin->pluginClass->ignoringTypes ?? [] as $toIgnore) {
                    foreach ($toIgnore ?? [] as $ignored) {
                        $toRemove = (string) $ignored->attributes()['remove'] ?? 'false';
                        if($toRemove === 'true') {
                            JmsAnnotationCheckerHook::removeIgnoredType((string)$ignored);
                        } else {
                            JmsAnnotationCheckerHook::addIgnoredType((string)$ignored);
                        }
                    }
                }
            }
        }
    }
}
