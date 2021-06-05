<?php

namespace Wikimedia\Rector\Rectors;

use PhpParser\Node;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Wikimedia\Rector\Services\DeprecationParser;

final class RemoveDeprecatedClassesRector extends AbstractRector {

    /** @var DeprecationParser */
    private $deprecationParser;

    public function __construct(
        DeprecationParser $deprecationParser
    ) {
        $this->deprecationParser = $deprecationParser;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition( 'Remove deprecated classes', [
            new CodeSample(<<<'CODE_SAMPLE'
/**
 * @deprecated since 1.36
 */
class SomeClass
{
}
CODE_SAMPLE
                , <<<'CODE_SAMPLE'
CODE_SAMPLE
            ) ] );
    }

    public function getNodeTypes(): array {
        return [ Node\Stmt\Class_::class ];
    }

    public function refactor( Node $node ) {
        if ( $this->deprecationParser->isClassDeprecated( $node ) ) {
            $this->removeNode($node);
        }
        return null;
    }
}