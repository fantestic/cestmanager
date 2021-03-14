<?php

declare(strict_types = 1);

namespace Fantestic\CestManager\Parser\NodeVisitor;

use PhpParser\NodeVisitorAbstract;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;

/**
 * Extracts all methods from a ClassNode.
 * 
 * @author Gerald Baumeister <gerald.b@whosonlocation.com>
 * @package Fantestic
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
class FindMethodsNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var ClassMethod[]
     */
    private array $methodNodes = [];

    public function leaveNode(Node $node) :?int
    {
        if ($node instanceof ClassMethod) {
            $this->methodNodes[$node->name->name] = $node;
        }
        return null;
    }

    /**
     * @return ClassMethod[]
     */
    public function getMethodNodes() :array
    {
        return $this->methodNodes;
    }

    /**
     * @return string[]
     */
    public function getMethodNames() :array
    {
        return array_keys($this->methodNodes);
    }
}
