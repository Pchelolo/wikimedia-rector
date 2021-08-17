<?php

namespace Wikimedia\Rector\Rectors;

use InvalidArgumentException;
use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\NullableType;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\ScopeFactory;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ParameterReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Reflection\ParametersAcceptorWithPhpDocs;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Type\ObjectType;
use PHPStan\Type\TypeWithClassName;
use Rector\Core\Contract\Rector\ConfigurableRectorInterface;
use Rector\Core\NodeAnalyzer\ParamAnalyzer;
use Rector\Core\Rector\AbstractRector;
use Rector\Core\Reflection\ReflectionResolver;
use Rector\StaticTypeMapper\Naming\NameScopeFactory;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class MethodParamClassToInterface extends AbstractRector implements ConfigurableRectorInterface {

    public const REPLACE_CLASS = 'class';

    public const REPLACE_WITH_INTERFACE = 'interface';

    /** @var ClassReflection */
    private $replaceClass;

    /** @var ClassReflection */
    private $replaceWithInterface;

    /** @var ParamAnalyzer */
    private $paramAnalyzer;

    /** @var ReflectionProvider */
    private $reflectionProvider;

    /** @var ReflectionResolver */
    private $reflectionResolver;

    public function __construct(
        ParamAnalyzer $paramAnalyzer,
        ReflectionProvider $reflectionProvider,
        ReflectionResolver $reflectionResolver
    ) {
        $this->paramAnalyzer = $paramAnalyzer;
        $this->reflectionProvider = $reflectionProvider;
        $this->reflectionResolver = $reflectionResolver;
    }

    public function configure( array $configuration ): void {
        if ( !array_key_exists( self::REPLACE_CLASS, $configuration ) ) {
            throw new InvalidArgumentException( __CLASS__ . ' REPLACE_CLASS config is required' );
        }
        $this->replaceClass = $this->reflectionProvider->getClass( $configuration[self::REPLACE_CLASS] );

        if ( !array_key_exists( self::REPLACE_WITH_INTERFACE, $configuration ) ) {
            throw new InvalidArgumentException( __CLASS__ . ' REPLACE_WITH_INTERFACE config is required' );
        }
        $this->replaceWithInterface = $this->reflectionProvider->getClass( $configuration[self::REPLACE_WITH_INTERFACE] );
    }

    public function getRuleDefinition(): \Symplify\RuleDocGenerator\ValueObject\RuleDefinition
    {
        // TODO:
        return new RuleDefinition( 'Remove deprecated properties', [
            new CodeSample( 'TODO', 'TODO' ) ] );
    }

    public function getNodeTypes(): array {
        return [ ClassMethod::class ];
    }

    public function refactor( Node $method ) {
        $paramsToRefactor = $this->getParamsOfReplacedClass( $method );
        if ( !$paramsToRefactor ) {
            return null;
        }
        foreach ( $paramsToRefactor as $param ) {
            if ( $this->callsMethodsNotInInterface( $method, $param ) ) {
                continue;
            }
            if ( $this->passedToMethodsExpectingClass( $method, $this->getName( $param ) ) ) {
                continue;
            }
            // Can be refactored!
            $this->refactorParamTypeHint( $param );
        }
    }

    /**
     * @param Node $method
     * @return Param[]
     */
    private function getParamsOfReplacedClass( Node $method ) {
        $result = [];
        foreach ( $method->getParams() as $param ) {
            if ( $this->isObjectType( $param, new ObjectType( $this->replaceClass->getName() ) ) ) {
                $result[] = $param;
            }
        }
        return $result;
    }

    private function callsMethodsNotInInterface( Node $method, Param $param ) {
        $callsOnParam = $this->betterNodeFinder->find( $method, function ( Node $node ) use ( $param ) {
            return $node instanceof Node\Expr\MethodCall
                && $this->nodeNameResolver->areNamesEqual( $node->var, $param );
        } );
        foreach ( $callsOnParam as $call ) {
            if ( !$this->replaceWithInterface->hasMethod( $this->getName( $call->name ) ) ) {
                return false;
            }
        }
        return true;
    }

    private function passedToMethodsExpectingClass( Node $method, string $paramName ) {
        foreach ( $this->betterNodeFinder->findInstancesOf( $method, [ Node\Expr\MethodCall::class ] ) as $call ) {
            $paramPositions = [];
            foreach ( $call->args as $index => $arg ) {
                if ( $this->isName( $arg->value, $paramName ) ) {
                    $paramPositions[] = $index;
                }
            }
            if ( !$paramPositions ) {
                continue;
            }
            $methodCalledOn = $this->reflectionResolver->resolveMethodReflectionFromMethodCall( $call );
            $parameters = ParametersAcceptorSelector::selectSingle( $methodCalledOn->getVariants() );
            foreach ( $parameters->getParameters() as $index => $param ) {
                /** @var ParameterReflection $param */
                if ( in_array( $index, $paramPositions ) &&
                    !$param->getType()->equals( new ObjectType( $this->replaceClass->getName() ) )
                ) {
                    return false;
                }
            }
        }
        return true;
    }

    private function refactorParamTypeHint(Param $param) : void {
        $fullyQualified = new FullyQualified( $this->replaceWithInterface->getName() );
        if ($this->paramAnalyzer->isNullable($param)) {
            $param->type = new NullableType( $fullyQualified );
            return;
        }
        $param->type = $fullyQualified;
    }
}