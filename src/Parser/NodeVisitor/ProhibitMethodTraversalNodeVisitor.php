<?php

declare(strict_types = 1);
namespace Fantestic\CestManager\Parser\NodeVisitor;

use PhpParser\NodeVisitorAbstract;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeTraverser;

/**
 * A NodeVisitor which prevents traversal of any methods encountered.
 * This can increase AST-traversal performance significantly.
 * 
 * @author Gerald Baumeister <gerald.b@whosonlocation.com>
 * @package Fantestic
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
class ProhibitMethodTraversalNodeVisitor extends NodeVisitorAbstract
{
    public function enterNode(Node $node) :?int
    {
        if ($node instanceof ClassMethod) {
            return NodeTraverser::DONT_TRAVERSE_CURRENT_AND_CHILDREN;
        }
        return null;
    }
}
