<?php

declare(strict_types = 1);
namespace Fantestic\CestManager\Parser\NodeVisitor;

use PhpParser\NodeVisitorAbstract;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeTraverser;

/**
 * Removes a method from an ast
 * 
 * @author Gerald Baumeister <gerald.b@whosonlocation.com>
 * @package Fantestic
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
class RemoveMethodNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var string $methodName
     * @var Node[] $stmts
     */
    public function __construct(
      private string $methodName
    ) {}


    public function leaveNode(Node $node) :?int
    {
        if ($node instanceof ClassMethod && $node->name->name === $this->methodName) {
            return NodeTraverser::REMOVE_NODE;
        }
        return null;
    }
}
