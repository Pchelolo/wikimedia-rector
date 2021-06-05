<?php

namespace Wikimedia\Rector\Rectors;

use PhpParser\Node;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Wikimedia\Rector\Services\DeprecationParser;

final class RemoveDeprecatedPropertiesRector extends AbstractRector {

    /** @var DeprecationParser */
    private $deprecationParser;

    public function __construct(
        DeprecationParser $deprecationParser
    ) {
        $this->deprecationParser = $deprecationParser;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition( 'Remove deprecated properties', [
            new CodeSample(<<<'CODE_SAMPLE'
class SomeClass
{
	/**
	 * @deprecated since 1.36
	 */
	private $variable;
}
CODE_SAMPLE
                , <<<'CODE_SAMPLE'
class SomeClass
{
}
CODE_SAMPLE
            ) ] );
    }

    public function getNodeTypes(): array {
        return [ Node\Stmt\Property::class ];
    }

    public function refactor( Node $node ) {
        if ( $this->deprecationParser->isPropertyDeprecated( $node ) ) {
            $this->removeNode($node);
        }
        return null;
    }
}