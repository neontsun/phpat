<?php declare(strict_types=1);

namespace PHPat\Rule\Extractor\Relation\DocComment\ClassScope;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\ShouldNotHappenException;

trait PropertyTagExtractor
{
    public function getNodeType(): string
    {
        return InClassNode::class;
    }

    /**
     * @return array<int, mixed>
     * @throws ShouldNotHappenException
     */
    protected function extractNodeClassNames(Node $node, Scope $scope): array
    {
        if ($this->configuration->ignoreDocComments()) {
            return [];
        }

        if (!$scope->isInClass()) {
            return [];
        }

        $docComment = $node->getDocComment();
        if ($docComment === null) {
            return [];
        }

        $classReflection = $scope->getClassReflection();
        $traitReflection = $scope->getTraitReflection();
        $functionReflection = $scope->getFunction();

        $resolvedPhpDoc = $this->fileTypeMapper->getResolvedPhpDoc(
            $scope->getFile(),
            $classReflection->getName(),
            $traitReflection ? $traitReflection->getName() : null,
            $functionReflection ? $functionReflection->getName() : null,
            $docComment->getText()
        );

        $names = [];
        foreach ($resolvedPhpDoc->getPropertyTags() as $tag) {
            if ($tag->isReadable()) {
                array_push($names, ...$tag->getReadableType()->getReferencedClasses());
            }
            if ($tag->isWritable()) {
                array_push($names, ...$tag->getWritableType()->getReferencedClasses());
            }
        }

        return array_unique($names);
    }
}
