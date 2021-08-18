<?php

namespace Wikimedia\Rector\Rectors\MediaWiki1_37;

use PhpParser\Node;
use PHPStan\Type\ObjectType;
use Rector\Core\Contract\Rector\ConfigurableRectorInterface;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class MoveIDatabaseMethodsToResultWrapperRector extends AbstractRector implements ConfigurableRectorInterface {

    public const DATABASE_CLASS = 'class';

    private $databaseClass = '\Wikimedia\Rdbms\IDatabase';

    public function configure( array $configuration ): void {
        if ( array_key_exists( self::DATABASE_CLASS, $configuration ) ) {
            $this->databaseClass = $configuration[self::DATABASE_CLASS];
        }
    }


    public function getRuleDefinition(): RuleDefinition {
        return new RuleDefinition(
            'Replace class parameter typehints with interface where possible',
            [
                new CodeSample(
<<<'CODE_SAMPLE'
$db->fetchObject( $result );
CODE_SAMPLE
                    ,
<<<'CODE_SAMPLE'
$result->fetchObject();
CODE_SAMPLE
                ) ] );
    }

    public function getNodeTypes(): array {
        return [ Node\Expr\MethodCall::class ];
    }

    /**
     * @param Node\Expr\MethodCall $node
     * @return Node|Node[]|void|null
     */
    public function refactor( Node $node ) {
        if ( !$this->isObjectType( $node->var, new ObjectType( $this->databaseClass ) ) ) {
            return null;
        }
        switch ( $this->getName( $node->name ) ) {
            case 'fetchObject':
            case 'fetchRow':
            case 'numRows': // TODO: handle possibility $res is null
                return $this->replaceFetch( $node );
                break;
            case 'freeResult': // TODO: Use IResultWrapper::free()
            case 'dataSeek': // TODO: IResultWrapper::seek()
            default:
                return null;
        }
    }

    private function replaceFetch( Node\Expr\MethodCall $call ): Node {
        $call->var = $call->args[0]->value;
        $call->args = [];
        return $call;
    }
}