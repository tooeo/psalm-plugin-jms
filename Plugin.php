<?php

namespace Tooeo\PsalmPluginJms;

use SimpleXMLElement;
use Psalm\Plugin\PluginEntryPointInterface;
use Psalm\Plugin\RegistrationInterface;
use Tooeo\PsalmPluginJms\Helpers\CheckClassExistsHelper;
use Tooeo\PsalmPluginJms\Hooks\JmsAnnotationCheckerHook;

/**
 * @psalm-suppress UnusedClass
 */
class Plugin implements PluginEntryPointInterface
{
    /** @return void */
    public function __invoke(RegistrationInterface $psalm, ?SimpleXMLElement $config = null): void
    {
        require_once __DIR__ . '/src/Hooks/JmsAnnotationCheckerHook.php';
        $psalm->registerHooksFromClass(JmsAnnotationCheckerHook::class);

        if ($config === null) {
            return;
        }

        foreach ($config->ignoringTypes ?? [] as $toIgnore) {
            foreach ($toIgnore ?? [] as $ignored) {
                $toRemove = (string) $ignored->attributes()['remove'] ?? 'false';
                if ($toRemove === 'true') {
                    CheckClassExistsHelper::removeIgnoredType((string)$ignored);
                } else {
                    CheckClassExistsHelper::addIgnoredType((string)$ignored);
                }
            }
        }
    }
}
