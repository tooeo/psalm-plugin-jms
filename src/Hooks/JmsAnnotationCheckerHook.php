<?php

namespace Tooeo\PsalmPluginJms\Hooks;

use Psalm\CodeLocation;
use Psalm\Issue\UndefinedDocblockClass;
use Psalm\IssueBuffer;
use Psalm\Plugin\EventHandler\AfterClassLikeAnalysisInterface;
use Psalm\Plugin\EventHandler\Event\AfterClassLikeAnalysisEvent;

/**
 * @psalm-suppress UnusedClass
 */
class JmsAnnotationCheckerHook implements AfterClassLikeAnalysisInterface
{
    protected const ERROR_MESSAGE = 'Class %s does not exists';
    /**
     * @var array|true[]
     */
    protected static array $ignoredTypes = [
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

    public static function afterStatementAnalysis(AfterClassLikeAnalysisEvent $event)
    {
        foreach ($event->getStmt()->getProperties() as $property) {
            foreach ($property->getComments() as $comment) {
                if ($class = self::parseClass($comment->getText())) {
                    if (!self::isClassExists(
                        $class,
                        $event->getClasslikeStorage()->aliases->uses,
                        $event->getClasslikeStorage()->aliases->namespace
                    )) {
                        $suppressed = self::getSuppressed(
                            $comment->getText(),
                            $event->getClasslikeStorage()->suppressed_issues
                        );
                        IssueBuffer::maybeAdd(
                            new UndefinedDocblockClass(
                                sprintf(self::ERROR_MESSAGE, $class),
                                new CodeLocation(
                                    $event->getStatementsSource()->getSource(),
                                    $event->getStmt(),
                                    null,
                                    true,
                                    null,
                                    $class
                                ),
                                $class
                            ),
                            $suppressed,
                            true
                        );
                    }
                }
            }
        }
    }

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
        $pattern = '#@JMS\\\Type\(["\']{0,1}(.*?)["\']{0,1}\)#i';
        if (preg_match($pattern, $comment, $matched)) {
            $type = self::findGroup($matched[1] ?? '');

            if (isset(self::$ignoredTypes[strtolower($type)])) {
                return null;
            }

            return $type;
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

    private static function getSuppressed(string $comment, array $suppressed = []): array
    {
        $matched = [];
        preg_match_all('#@psalm-suppress\s+(.*)#i', $comment, $matched);

        foreach ($matched[1] ?? [] as $item) {
            $suppressed[] = $item;
        }

        return $suppressed;
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
