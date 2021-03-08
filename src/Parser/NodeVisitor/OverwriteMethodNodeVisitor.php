<?php

declare(strict_types = 1);

namespace Fantestic\CestManager\Parser\NodeVisitor;

use PhpParser\NodeVisitorAbstract;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeTraverser;

/**
 * Overwrites the method of a ast
 * 
 * @author Gerald Baumeister <gerald.b@whosonlocation.com>
 * @package Fantestic
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
class OverwriteMethodNodeVisitor extends NodeVisitorAbstract
{
    private string $methodName;
    /**
     * @var Node[]
     */
    private array $methodBody;

    /**
     * @var string $methodName
     * @var Node[] $stmts
     */
    public function __construct(string $methodName, array $methodBody)
    {
        $this->methodName = $methodName;
        $this->methodBody = $methodBody;
    }

    public function leaveNode(Node $node) :?int
    {
        if ($node instanceof ClassMethod && $node->name === $this->methodName) {
            $node->stmts = $this->methodBody;
            return NodeTraverser::STOP_TRAVERSAL;
        }
        return null;
    }
}
