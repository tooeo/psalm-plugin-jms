<?php

namespace Tooeo\PsalmPluginJms\Helpers;

/**
 * @psalm-suppress UnusedClass
 */
final class CheckClassExistsHelper
{
    /**
     * @var array|true[]
     */
    private static array $ignoredTypes = [
        'array' => true,
        'string' => true,
        'float' => true,
        'int' => true,
        'integer' => true,
        'bool' => true,
        'boolean' => true,
        'double' => true,
        'number' => true,
    ];


    public static function findGroup(string $comment): string
    {
        $matched = [];
        if (preg_match('#(array|enum)<(.*)>#i', $comment, $matched)) {
            return self::findGroup($matched[2]);
        }

        if (preg_match('#(.*)<.*>#i', $comment, $matchedClass)) {
            $comment = $matchedClass[1];
        }
        $comment = explode(',', $comment)[1] ?? $comment;
        $matchedClass = [];


        return trim($comment);
    }

    public static function parseClass(string $comment): ?string
    {
        $matched = [];
        $patterns = [
            '#@JMS\\\Type\(name=["\']{0,1}(.*?)["\']{0,1}\)#i',
            '#@JMS\\\Type\(["\']{0,1}(.*?)["\']{0,1}\)#i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $comment, $matched)) {
                $type = self::findGroup($matched[1] ?? '');

                if (isset(self::$ignoredTypes[strtolower($type)])) {
                    return null;
                }

                return $type;
            }
        }

        return null;
    }

    public static function isClassExists(string $class, array $uses, string $namespace): bool
    {
        $class = explode('::', $class)[0];
        foreach ($uses as $flipped => $use) {
            if ($flipped === strtolower($class)) {
                $class = $use;
                break;
            }
        }
        foreach ($uses as $flipped => $use) {
            if (preg_match("#^$flipped#i", $class)) {
                $class = preg_replace("#$flipped#i", $use, $class);
                break;
            }
        }

        return preg_match('#interface$#i', $class)
            ? interface_exists($class) || interface_exists($namespace.'\\'.$class)
            : class_exists($class) || class_exists($namespace.'\\'.$class);
    }

    public static function addSuppressed(string $comment, array &$suppressed): void
    {
        $matched = [];
        preg_match_all('#@psalm-suppress\s+(.*)#i', $comment, $matched);

        foreach ($matched[1] ?? [] as $item) {
            $suppressed[] = $item;
        }
    }

    public static function addIgnoredType(string $type): void
    {
        self::$ignoredTypes[strtolower($type)] = true;
    }

    public static function removeIgnoredType(string $type): void
    {
        unset(self::$ignoredTypes[strtolower($type)]);
    }

    public static function getIgnoredType(): array
    {
        return array_keys(self::$ignoredTypes);
    }
}