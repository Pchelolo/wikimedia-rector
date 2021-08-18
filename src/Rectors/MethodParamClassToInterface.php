<?php

namespace Wikimedia\Rector\Rectors;

use InvalidArgumentException;
use PhpParser\Node;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\NullableType;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Reflection\ParameterReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Type\MixedType;
use PHPStan\Type\NullType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\UnionType;
use Rector\Core\Contract\Rector\ConfigurableRectorInterface;
use Rector\Core\Exception\ShouldNotHappenException;
use Rector\Core\NodeAnalyzer\ParamAnalyzer;
use Rector\Core\Rector\AbstractRector;
use Rector\Core\Reflection\ReflectionResolver;
use Rector\PHPStanStaticTypeMapper\ValueObject\TypeKind;
use Rector\TypeDeclaration\TypeInferer\PropertyTypeInferer;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class MethodParamClassToInterface extends AbstractRector implements ConfigurableRectorInterface {

    public const REPLACE_CLASS = 'class';

    public const REPLACE_WITH_INTERFACE = 'interface';

    /** @var string */
    private $replaceClass;

    /** @var string */
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
        ReflectionResolver $reflectionResolver,
        PropertyTypeInferer $propertyTypeInferer
    ) {
        $this->paramAnalyzer = $paramAnalyzer;
        $this->reflectionProvider = $reflectionProvider;
        $this->reflectionResolver = $reflectionResolver;
    }

    public function configure( array $configuration ): void {
        if ( !array_key_exists( self::REPLACE_CLASS, $configuration ) ) {
            throw new InvalidArgumentException( __CLASS__ . ' REPLACE_CLASS config is required' );
        }
        $this->replaceClass = $configuration[self::REPLACE_CLASS];

        if ( !array_key_exists( self::REPLACE_WITH_INTERFACE, $configuration ) ) {
            throw new InvalidArgumentException( __CLASS__ . ' REPLACE_WITH_INTERFACE config is required' );
        }
        $this->replaceWithInterface = $configuration[self::REPLACE_WITH_INTERFACE];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Replace class parameter typehints with interface where possible',
            [
            new CodeSample(
<<<'CODE_SAMPLE'
class SomeClass
{
  public function method( TestClass $param ) {
    $param->interfaceMethod();
  }
}
CODE_SAMPLE
                ,
<<<'CODE_SAMPLE'
class SomeClass
{
  public function method( TestInterface $param ) {
    $param->interfaceMethod();
  }
}
CODE_SAMPLE
            ) ] );
    }

    public function getNodeTypes(): array {
        return [ ClassMethod::class ];
    }

    /**
     * @param ClassMethod $method
     */
    public function refactor( Node $method ) {
        $paramsToRefactor = $this->getParamsOfReplacedClass( $method );
        if ( !$paramsToRefactor ) {
            return null;
        }
        $modified = false;
        foreach ( $paramsToRefactor as $param ) {
            if ( $this->callsMethodsNotInInterface( $method, $param ) ) {
                continue;
            }
            if ( $this->passedToMethodsExpectingClass( $method, $this->getName( $param ) ) ) {
                continue;
            }
            if ( $this->assignedToClassTypedProperty( $method, $this->getName( $param ) ) ) {
                continue;
            }
            // Can be refactored!
            $this->refactorParamDocBlock( $param, $method );
            $this->refactorParamTypeHint( $param );
            $modified = true;
        }
        return $modified ? $method : null;
    }

    /**
     * @param Node $method
     * @return Param[]
     */
    private function getParamsOfReplacedClass( Node $method ) {
        $result = [];
        foreach ( $method->getParams() as $param ) {
            if ( $this->isObjectType( $param, new ObjectType( $this->replaceClass ) ) ) {
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
        $replaceWithInterface = $this->reflectionProvider->getClass( $this->replaceWithInterface );
        foreach ( $callsOnParam as $call ) {
            if ( !$replaceWithInterface->hasMethod( $this->getName( $call->name ) ) ) {
                return true;
            }
        }
        return false;
    }

    private function passedToMethodsExpectingClass( ClassMethod $method, string $paramName ) {
        foreach ( $this->betterNodeFinder->findInstancesOf(
            $method,
            [ Node\Expr\MethodCall::class, Node\Expr\New_::class, Node\Expr\StaticCall::class ] ) as $call
        ) {
            $paramPositions = [];
            foreach ( $call->args as $index => $arg ) {
                if ( $this->isName( $arg->value, $paramName ) ) {
                    $paramPositions[] = $index;
                }
            }
            if ( !$paramPositions ) {
                continue;
            }
            if ( $call instanceof Node\Expr\MethodCall ) {
                $methodCalledOn = $this->reflectionResolver->resolveMethodReflectionFromMethodCall( $call );
            } elseif ( $call instanceof Node\Expr\New_ ) {
                $methodCalledOn = $this->reflectionResolver->resolveMethodReflectionFromNew( $call );
            } elseif ( $call instanceof Node\Expr\StaticCall ) {
                $methodCalledOn = $this->reflectionResolver->resolveMethodReflectionFromStaticCall( $call );
            } else {
                throw new ShouldNotHappenException();
            }
            if ( !$methodCalledOn ) {
                // Could not reflect the types. Skip this one.
                return true;
            }
            $parameters = ParametersAcceptorSelector::selectSingle( $methodCalledOn->getVariants() );
            foreach ( $parameters->getParameters() as $index => $param ) {
                /** @var ParameterReflection $param */
                if ( in_array( $index, $paramPositions ) &&
                    !$param->getType()->isSuperTypeOf( new ObjectType( $this->replaceWithInterface ) )->yes()
                ) {
                    return true;
                }
            }
        }
        return false;
    }

    private function assignedToClassTypedProperty( ClassMethod $method, string $paramName ) {
        $assignments = $this->betterNodeFinder->find(
            $method,
            function ( Node $node ) use ( $paramName ) {
                return $node instanceof Node\Expr\Assign &&
                    $node->expr instanceof Node\Expr\Variable &&
                    $this->isName( $node->expr, $paramName );
            }
        );

        if ( !$assignments ) {
            return false;
        }

        // TODO: skip if the property is typehinted with interface somehow.
        // ReflectionResolver::resolveFromPropertyFetch
        foreach ( $assignments as $assign ) {
            if ( $assign->var instanceof Node\Expr\PropertyFetch ) {
                return true;
            }
        }
        return false;
    }

    private function refactorParamTypeHint( Param $param ): void {
        $fullyQualified = new FullyQualified(
            $this->reflectionProvider->getClassName( $this->replaceWithInterface )
        );
        if ( $this->paramAnalyzer->isNullable($param) ) {
            $param->type = new NullableType( $fullyQualified );
            return;
        }
        $param->type = $fullyQualified;
    }

    private function refactorParamDocBlock( Param $param, ClassMethod $classMethod ) : void {
        $paramName = $this->getName( $param->var );
        if ( $paramName === null ) {
            throw new ShouldNotHappenException();
        }
        $phpDocInfo = $this->phpDocInfoFactory->createFromNode( $classMethod );
        if ( !$phpDocInfo ) {
            return;
        }

        $paramTagValueNode = $phpDocInfo->getParamTagValueByName( $paramName );
        if ( !$paramTagValueNode ) {
            return;
        }

        $type = new ObjectType( $this->reflectionProvider->getClassName( $this->replaceWithInterface ) );
        if ( $this->paramAnalyzer->isNullable( $param ) ) {
            $type = new UnionType( [ $type, new NullType() ] );
        }
        $paramTagValueNode->type = $this->staticTypeMapper
            ->mapPHPStanTypeToPHPStanPhpDocTypeNode( $type, TypeKind::PARAM() );;
    }
}