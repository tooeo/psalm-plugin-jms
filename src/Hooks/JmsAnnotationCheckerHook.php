<?php

namespace Tooeo\PsalmPluginJms\Hooks;

use PhpParser\Node\Stmt\Property;
use Psalm\CodeLocation;
use Psalm\Issue\UndefinedDocblockClass;
use Psalm\IssueBuffer;
use Psalm\Plugin\EventHandler\AfterClassLikeAnalysisInterface;
use Psalm\Plugin\EventHandler\Event\AfterClassLikeAnalysisEvent;
use Psalm\Storage\ClassLikeStorage;
use Tooeo\PsalmPluginJms\Dto\ErrorDto;
use Tooeo\PsalmPluginJms\Helpers\CheckClassExistsHelper;

/**
 * @psalm-suppress UnusedClass
 */
class JmsAnnotationCheckerHook implements AfterClassLikeAnalysisInterface
{
    protected const ERROR_MESSAGE = 'Class %s does not exists';

    public static function afterStatementAnalysis(AfterClassLikeAnalysisEvent $event)
    {
        foreach ($event->getStmt()->getProperties() as $property) {
            if ($error = self::checkAttributes($property, $event->getClasslikeStorage())) {
                self::addError($error, $event);
            }
            if ($error = self::checkComment($property, $event->getClasslikeStorage())) {
                self::addError($error, $event);
            }
        }
    }

    private static function checkComment(Property $property, ClassLikeStorage $classLikeStorage): ?ErrorDto
    {
        foreach ($property->getComments() as $comment) {
            if ($class = CheckClassExistsHelper::parseClass($comment->getText())) {
                if (
                    !CheckClassExistsHelper::isClassExists(
                        $class,
                        $classLikeStorage->aliases->uses,
                        $classLikeStorage->aliases->namespace
                    )
                ) {
                    $suppressed = self::getSuppressed($property, $classLikeStorage->suppressed_issues);

                    return new ErrorDto($class, $suppressed);
                }
            }
        }

        return null;
    }

    private static function checkAttributes(Property $property, ClassLikeStorage $classLikeStorage): ?ErrorDto
    {
        foreach ($property->attrGroups as $attrGroup) {
            foreach ($attrGroup->attrs as $attribute) {
                $lastElement = null;
                if ($parts = $attribute->name->getParts()) {
                    $lastElement = end($parts);
                }

                if ($lastElement !== 'Type') {
                    continue;
                }
                foreach ($attribute->args as $arg) {
                    if (
                        null !== $arg->name &&
                        $arg->name->name !== 'type' ||
                        property_exists($arg->value, 'name')
                    ) {
                        continue;
                    }

                    $class = CheckClassExistsHelper::findGroup($arg->value?->value);
                    if (CheckClassExistsHelper::checkInIgnore($class)) {
                        continue;
                    }
                    if (
                        !CheckClassExistsHelper::isClassExists(
                            $class,
                            $classLikeStorage->aliases->uses ?? [],
                            $classLikeStorage->aliases->namespace ?? ''
                        )
                    ) {
                        $suppressed = self::getSuppressed($property, $classLikeStorage->suppressed_issues);

                        return new ErrorDto($class, $suppressed);
                    }
                }
            }
        }

        return null;
    }

    private static function addError(ErrorDto $error, AfterClassLikeAnalysisEvent $event)
    {
        IssueBuffer::maybeAdd(
            new UndefinedDocblockClass(
                sprintf(self::ERROR_MESSAGE, $error->getClass()),
                new CodeLocation(
                    $event->getStatementsSource()->getSource(),
                    $event->getStmt(),
                    null,
                    true,
                    null,
                    $error->getClass()
                ),
                $error->getClass()
            ),
            $error->getSuppressed(),
            $error->isFixable()
        );
    }

    private static function getSuppressed(Property $property, array $suppressedIssues): array
    {
//        foreach ($property->getComments() as $comment) {
//            CheckClassExistsHelper::addSuppressed(
//                $comment->getText(),
//                $suppressedIssues
//            );
//        }

        return $suppressedIssues;
    }
}
