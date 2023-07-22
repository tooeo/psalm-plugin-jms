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
                    if (!self::isClassExists($class, $event->getClasslikeStorage()->aliases->uses)) {
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
        ];
        $regexps = [
            '#@JMS\\\Type\(["\']{0,1}array<([^<>,\'"]+)>["\']{0,1}\)#i',
            '#@JMS\\\Type\(["\']{0,1}array<[^<>,\'"]+,\s*([^<>,\'"]+)>["\']{0,1}\)#i',
            '#@JMS\\\Type\(["\']{0,1}([^\'"]+)["\']{0,1}\)#i',
        ];
        foreach ($regexps as $regexp) {
            if (preg_match($regexp, $comment, $matched)) {
                if (isset($ignored[$matched[1] ?? null])) {
                    return null;
                }

                return $matched[1] ?? null;
            }
        }

        return null;
    }

    public static function isClassExists(string $class, array $uses = []): bool
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

        return class_exists($class);
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
