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

    public static function parseClass(string $comment): ?string
    {
        $matched = [];
        $ignored = [
            'array' => true,
            'string' => true,
            'float' => true,
            'int' => true,
            'integer' => true,
            'bool' => true,
            'boolean' => true,
            'double' => true,
            'number' => true,
            'MixedType' => true,
            'ArrayOrString' => true,
        ];
        $regexps = [
            'enum<([^<>,\'"]+)>',
            'array<enum<([^<>,\'"]+)>>',
            'array<[^<>,\'"]+,\s*enum<([^<>,\'"]+)>>',
            'array<([^<>,\'"]+)>',
            'array<[^<>,\'"]+,\s*([^<>,\'"]+)>',
            '([^\'"]+)',
        ];
        foreach ($regexps as $regexp) {
            $pattern = '#@JMS\\\Type\(["\']{0,1}' . $regexp . '["\']{0,1}\)#i';
            if (preg_match($pattern, $comment, $matched)) {
                if (isset($ignored[strtolower($matched[1] ?? '')])) {
                    return null;
                }

                return $matched[1] ?? null;
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

    private static function getSuppressed(string $comment, array $suppressed = []): array
    {
        $matched = [];
        preg_match_all('#@psalm-suppress\s+(.*)#i', $comment, $matched);

        foreach ($matched[1] ?? [] as $item) {
            $suppressed[] = $item;
        }

        return $suppressed;
    }
}
