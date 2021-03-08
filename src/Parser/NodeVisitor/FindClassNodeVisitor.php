<?php

declare(strict_types = 1);
namespace Fantestic\CestManager\Parser\NodeVisitor;

use PhpParser\NodeVisitorAbstract;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\NodeTraverser;

/**
 * Searches for a Class inside an AST
 * 
 * @author Gerald Baumeister <gerald.b@whosonlocation.com>
 * @package Fantestic\CestManager
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
class FindClassNodeVisitor extends NodeVisitorAbstract
{
    private ?Node $classNode = null;


    public function leaveNode(Node $node) :?int
    {
        if ($node instanceof Class_) {
            $this->classNode = $node;
            return NodeTraverser::STOP_TRAVERSAL;
        }
        return null;
    }


    public function classWasFound() :bool
    {
        return !is_null($this->methodNode);
    }


    public function getMethodNode() :?Class_
    {
        return $this->classNode;
    }
}
