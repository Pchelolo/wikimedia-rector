<?php

namespace Wikimedia\Rector\Rectors;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\NullableType;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\Expression;
use Rector\BetterPhpDocParser\PhpDocManipulator\PhpDocTypeChanger;
use Rector\Core\NodeAnalyzer\ParamAnalyzer;
use Rector\Core\Rector\AbstractRector;
use Rector\NodeNameResolver\NodeNameResolver;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class UserToUserIndetityRector extends AbstractRector {

    /**
     * @var PhpDocTypeChanger
     */
    private $phpDocTypeChanger;

    /**
     * @var ParamAnalyzer
     */
    private $paramAnalyzer;

    public function __construct(PhpDocTypeChanger $phpDocTypeChanger, ParamAnalyzer $paramAnalyzer)
    {
        $this->phpDocTypeChanger = $phpDocTypeChanger;
        $this->paramAnalyzer = $paramAnalyzer;
    }

    public function getRuleDefinition(): \Symplify\RuleDocGenerator\ValueObject\RuleDefinition
    {
        return new RuleDefinition( 'Remove deprecated properties', [
            new CodeSample( 'TODO', 'TODO' ) ] );
    }

    public function getNodeTypes(): array {
        return [\PhpParser\Node\Stmt\ClassMethod::class];
    }

    public function refactor(Node $node)
    {
        if ( !$this->hasUserParam( $node ) ){
            return null;
        }
        if ( !$this->isUserIdentityEnough( $node ) ) {
            return null;
        }
        return null;
/*
        $isModifiedNode = \false;
        foreach ($node->getParams() as $param) {
            if (!$this->isObjectType($param, new \PHPStan\Type\ObjectType('User'))) {
                continue;
            }
            $this->refactorParamTypeHint($param);
            $this->refactorParamDocBlock($param, $node);
            $this->refactorMethodCalls($param, $node);
            $isModifiedNode = \true;
        }
        if (!$isModifiedNode) {
            return null;
        }
        return $node;*/
    }

    private function refactorParamTypeHint(Param $param) : void
    {
        $fullyQualified = new FullyQualified('Interface1');
        if ($this->paramAnalyzer->isNullable($param)) {
            $param->type = new NullableType($fullyQualified);
            return;
        }
        $param->type = $fullyQualified;
    }

    private function hasUserParam( Node $node ) {
        foreach ($node->getParams() as $param) {
            if ($this->isObjectType($param, new \PHPStan\Type\ObjectType('Class1'))) {
                return true;
            }
        }
        return false;
    }

    private function isUserIdentityEnough( Node $node ) {
        foreach ( $node->stmts as $statement ) {
            echo get_class( $statement );
            echo "\n";
            if ( $statement instanceof Expression && $statement->expr instanceof FuncCall ) {
                $funcCall = $statement->expr;
                echo $this->nodeNameResolver->getName( $statement->expr );
                $func = new \ReflectionFunction( $this->nodeNameResolver->getName( $statement->expr ) );
                array_map( function( \ReflectionParameter $param ) use ( $func ) {
                    echo 'AAA' . $param->getType();
                }, $func->getParameters() );
            }
        }
    }
}