<?php

declare(strict_types = 1);

namespace Fantestic\CestManager\Parser\NodeVisitor;

use PhpParser\NodeVisitorAbstract;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeTraverser;

/**
 * Adds a method into the Class-Ast
 * 
 * @author Gerald Baumeister <gerald.b@whosonlocation.com>
 * @package Fantestic
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
class AddMethodNodeVisitor extends NodeVisitorAbstract
{
    public function __construct(
        private ClassMethod $methodAst,
        private bool $stopTraversal = true
    ) { }

    public function leaveNode(Node $node) :?int
    {
        if ($node instanceof Class_) {
            $node->stmts[] = $this->methodAst;
            if (true === $this->stopTraversal) {
                return NodeTraverser::STOP_TRAVERSAL;
            }
        }
        return null;
    }
}
