<?php

namespace PHPat\Rule\Assertion\ShouldNotConstruct;

use PHPat\Rule\Assertion\Assertion;
use PHPat\Statement\Builder\StatementBuilderFactory;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\RuleErrorBuilder;

abstract class ShouldNotConstruct extends Assertion
{
    public function __construct(
        StatementBuilderFactory $statementBuilderFactory,
        ReflectionProvider $reflectionProvider
    ) {
        parent::__construct(__CLASS__, $statementBuilderFactory, $reflectionProvider);
    }

    protected function applyValidation(ClassReflection $subject, array $targets, array $nodes): array
    {
        $errors = [];
        foreach ($targets as $target) {
            foreach ($nodes as $node) {
                if ($target->matches($this->reflectionProvider->getClass($node))) {
                    $errors[] = RuleErrorBuilder::message($this->getMessage($subject->getName(), $node))->build();
                }
            }
        }

        return $errors;
    }

    protected function getMessage(string $subject, string $target): string
    {
        return sprintf('%s should not construct %s', $subject, $target);
    }
}
