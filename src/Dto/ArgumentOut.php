<?php

declare(strict_types = 1);
namespace Fantestic\CestManager\Dto;

use Fantestic\CestManager\Contract\ArgumentInterface;
use Fantestic\CestManager\Exception\UnprocessableScenarioException;
use Fantestic\CestManager\Unparser\PrettyPrinter;
use PhpParser\Node;
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


    public function __construct(string $value, string $type)
    {
        $this->value = $value;
        $this->type = $type;
    }


    public static function fromPhpParserNode(Node $node) :ArgumentOut
    {
        $type = self::getTypeFromNode($node);
        $value = self::getValueFromNode($node);
        return new self($value, $type);
    }


    private static function getValueFromNode(Node $node) :string
    {
        $prettyPrinter = new PrettyPrinter();
        return $prettyPrinter->prettyPrintExpr($node);
    }


    /**
     * 
     * @param Node $node 
     * @return string 
     * @throws UnprocessableScenarioException 
     */
    private static function getTypeFromNode(Node $node) :string
    {
            if ($node instanceof String_) {
                return 'string';
            } else {
                throw new UnprocessableScenarioException(
                    sprintf('Argument of type "%s" cannot be processed by Fantestic.', get_class($node))
                );
            }
    }


    public function getValue(): string
    {
        return $this->value;
    }


    public function getType() :string
    {
        return $this->type;
    }
}