<?php

declare(strict_types = 1);
namespace Fantestic\CestManager\Dto;

use Fantestic\CestManager\Contract\ArgumentInterface;
use Fantestic\CestManager\Exception\UnprocessableScenarioException;
use Fantestic\CestManager\Unparser\PrettyPrinter;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Scalar\String_;

/**
 * 
 * 
 * @package Fantestic/CestManager
 * @author Gerald Baumeister <gerald@fantestic.io>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
class ArgumentOut implements ArgumentInterface
{
    protected mixed $value;
    protected string $type;
    protected ?string $expression;


    public function __construct(mixed $value, string $type, string $expression = null)
    {
        $this->value = $value;
        $this->type = $type;
        $this->expression = $expression;
    }


    public static function fromPhpParserNode(Node $node) :ArgumentOut
    {
        $type = self::getTypeFromNode($node);
        $value = self::getValueFromNode($node);
        $expr = self::getExpressionFromNode($node);
        return new self($value, $type, $expr);
    }


    public function getValue(): string
    {
        return $this->value;
    }


    public function getType() :string
    {
        return $this->type;
    }


    public function getExpression() :string
    {
        return $this->expression;
    }


    public function getParameterName() :?string
    {
        return $this->parameterName;
    }


    private static function getValueFromNode(Arg $node) :mixed
    {
        if ($node->value instanceof String_) {
            return $node->value->value;
        } else {
            throw new UnprocessableScenarioException(
                sprintf('Argument of type "%s" cannot be processed by Fantestic.', get_class($node))
            );
        }
    }


    private static function getExpressionFromNode(Arg $node) :string
    {
        $prettyPrinter = new PrettyPrinter();
        return $prettyPrinter->prettyPrintExpr($node->value);
    }


    /**
     * 
     * @param Node $node 
     * @return string 
     * @throws UnprocessableScenarioException 
     */
    private static function getTypeFromNode(Node $node) :string
    {
        if ($node->value instanceof String_) {
            return 'string';
        } else {
            throw new UnprocessableScenarioException(
                sprintf('Argument of type "%s" cannot be processed by Fantestic.', get_class($node))
            );
        }
    }
}
