<?php

namespace Wikimedia\Rector\Rectors;

use PhpParser\Node;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Wikimedia\Rector\Services\DeprecationParser;

final class RemoveDeprecatedMethodsRector extends AbstractRector {

    /** @var DeprecationParser */
    private $deprecationParser;

	public function __construct(
	    DeprecationParser $deprecationParser
    ) {
	    $this->deprecationParser = $deprecationParser;
    }

    public function getRuleDefinition(): RuleDefinition
	{
		return new RuleDefinition( 'Remove deprecated methods', [
			new CodeSample(<<<'CODE_SAMPLE'
class SomeClass
{
	/**
	 * @deprecated since 1.36
	 */
	public function test()
	{
	}
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
		return [ Node\Stmt\ClassMethod::class ];
	}

	public function refactor( Node $node ) {
	    if ( $this->deprecationParser->isMethodDeprecated( $node ) ) {
            $this->removeNode($node);
        }
		return null;
	}
}