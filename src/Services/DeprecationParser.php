<?php

namespace Wikimedia\Rector\Services;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Stmt\Expression;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfoFactory;
use Rector\NodeNameResolver\NodeNameResolver;

class DeprecationParser {

    const REMOVE_SOFT_DEPRECATED = 'remove_soft_deprecated';

    const REMOVE_UP_TO_VERSION = 'remove_up_to_version';

    /** @var bool */
    private $removeSoftDeprecated = false;

    /** @var ?string */
    private $removeUpToVersion = null;

    /** @var PhpDocInfoFactory */
    private $phpDocInfoFactory;

    /** @var NodeNameResolver */
    private $nodeNameResolver;

    public function __construct(
        PhpDocInfoFactory $phpDocInfoFactory,
        NodeNameResolver $nodeNameResolver
    ) {
        $this->phpDocInfoFactory = $phpDocInfoFactory;
        $this->nodeNameResolver = $nodeNameResolver;
    }

    private function isSoftDeprecated( Node $node ) {
        $docComment = $this->phpDocInfoFactory->createFromNodeOrEmpty( $node );
        $deprecated = $docComment->getTagsByName( 'deprecated' );
        if ( $deprecated ) {
            return true; // TODO: support version
        }
        return false;
    }

    public function isClassDeprecated( Node $node ) {
        return $this->removeSoftDeprecated && $this->isSoftDeprecated( $node );
    }

    public function isPropertyDeprecated( Node $node ) {
        return $this->removeSoftDeprecated && $this->isSoftDeprecated( $node );
    }

    public function isMethodDeprecated( Node $node ) {
        if ( $this->removeSoftDeprecated && $this->isSoftDeprecated( $node ) ) {
            return true;
        }

        if ( $node->stmts ) {
            // Try to remove only when wfDeprecated is first statement.
            if ( $node->stmts &&
                $node->stmts[0] instanceof Expression &&
                $node->stmts[0]->expr instanceof FuncCall &&
                $this->nodeNameResolver->getName( $node->stmts[0]->expr ) === 'wfDeprecated'
            ) {
                if ( !$this->removeUpToVersion ) {
                    return true;
                }

                $deprecationExprArgs = $node->stmts[0]->expr->args;
                if ( count( $deprecationExprArgs ) < 2 ) {
                    // Don't know the version, skip. TODO: log
                    return false;
                }

                $deprecatedVersion = $deprecationExprArgs[1]->value->value;
                if ( !$this->removeUpToVersion ||
                    version_compare( $deprecatedVersion, $this->removeUpToVersion, '<' )
                ) {
                    return true;
                }
            }
        }

        return false;
    }

    public function configure( array $configuration ) : void {
        $this->removeSoftDeprecated = array_key_exists(
            self::REMOVE_SOFT_DEPRECATED,
            $configuration
        );
        $this->removeUpToVersion = $configuration[self::REMOVE_UP_TO_VERSION] ?? null;
    }
}