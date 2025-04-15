<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Helpers;

use Collecthor\SurveyjsParser\Expressions\ArrayNode;
use Collecthor\SurveyjsParser\Expressions\BinaryOperatorNode;
use Collecthor\SurveyjsParser\Expressions\Buffer;
use Collecthor\SurveyjsParser\Expressions\FunctionNode;
use Collecthor\SurveyjsParser\Expressions\Node;
use Collecthor\SurveyjsParser\Expressions\Operator;
use Collecthor\SurveyjsParser\Expressions\UnaryOperatorNode;
use Collecthor\SurveyjsParser\Expressions\UnescapedStringNode;
use Collecthor\SurveyjsParser\Expressions\ValueNode;
use Collecthor\SurveyjsParser\Expressions\VariableNode;

class ExpressionParser
{
    private int $depth = 0;
    /**
    Expression := Number | quoted-string
                  | FunctionName "(" ExpressionList ")" | Variable | BracketedExpression
                  | Expression BinaryOp Expression

    Number = "-" PositiveNumber | PositiveNumber
    PositiveNumber = "\d+" ("." \d+)?
    Variable = "{" VariableName "}"
    BracketedExpression: = "(" Expression ")"
    FunctionName = "[a-Z][_a-Z0-9]*"
    ExpressionList = Expression ("," Expression)*
    VariableName = "[a-Z][_a-Z0-9.]*"
     */

    private function parseNumber(Buffer $buffer): ValueNode
    {
        $value = $buffer->readRegex('/^(-?\d+(\.\d+)?)/');
        return new ValueNode(str_contains($value, '.') ? (float) $value : (int) $value);
    }

    private function parseString(Buffer $buffer): ValueNode
    {
        $quote = $buffer->readChar("'", "\"");
        $result = "";
        while ($buffer->peekChar() !== $quote) {
            $char = $buffer->readChar();
            // Weak escaping by SurveyJS...
            if ($char === "\\") {
                $next = $buffer->readChar();
                if ($next !== $quote) {
                    $result .= $char;
                }
                $result .= $next;
            } else {
                $result .= $char;
            }
        }
        $buffer->readChar($quote);
        return new ValueNode($result);
    }
    private function parseBracketedExpression(Buffer $buffer): Node
    {
        $buffer->readChar("(");
        $result = $this->parseExpression($buffer);
        $buffer->readChar(")");
        return $result;
    }

    private function parseVariable(Buffer $buffer): Node
    {
        $buffer->readChar('{');
        $buffer->consumeWhitespace();
        $variableName = $buffer->readRegex("/^([\pL][\ \-_.\pL\d]*)/u");
        $index = $this->parseIndex($buffer);
        $buffer->readChar('}');
        return new VariableNode($variableName, ...$index);
    }

    /**
     * @param Buffer $buffer
     * @return list<string>
     */
    private function parseIndex(Buffer $buffer): array
    {
        $indexes = [];
        while ($buffer->peekNext('[')) {
            $buffer->readChar('[');
            $indexes[] = $buffer->readRegex("/^((?:[a-zA-Z][a-zA-Z_0-9.]*)|\d+)/");
            $buffer->readChar(']');
        }
        return $indexes;
    }
    private function parseArray(Buffer $buffer): Node
    {
        $data = [];
        $buffer->readChar('[');
        $buffer->consumeWhitespace();
        if (!$buffer->peekNext(']')) {
            $data[] = $this->parseExpression($buffer);
            while ($buffer->peekNext(',')) {
                $buffer->readChar(',');
                $data[] = $this->parseExpression($buffer);
            }
        }

        $buffer->readChar(']');

        return new ArrayNode(...$data);
    }

    private function parseInternal(Buffer $buffer): Node
    {
        $buffer->consumeWhitespace();
        $character = $buffer->peekChar();
        $result = match (substr($character, 0, 1)) {
            "{" => $this->parseVariable($buffer),
            "(" => $this->parseBracketedExpression($buffer),
            "-", "0", "1", "2", "3", "4", "5", "6", "7", "8", "9" => $this->parseNumber($buffer),
            "'", "\"" => $this->parseString($buffer),
            "[" => $this->parseArray($buffer),
            default => $this->parseFunction($buffer)
        };
        $buffer->consumeWhitespace();
        return $result;
    }

    /**
     * Parse one or more expressions separated by operators.
     * @param Buffer $buffer
     * @return Node
     */
    private function parseExpression(Buffer $buffer): Node
    {
        $this->depth++;
        $operands = [];
        $operators = [];
        $operands[] = $this->parseInternal($buffer);
        while (null !== $operator = $buffer->readOptionalOperator()) {
            // Special case for unary operators.
            if ($operator->isUnary()) {
                $operands[] = new UnaryOperatorNode($operator, array_pop($operands));
                $buffer->consumeWhitespace();
                continue;
            }
            $operators[] = $operator;
            $operands[] = $this->parseInternal($buffer);
        }

        $this->depth--;
        return $this->resolveOperators($operands, $operators);
    }

    /**
     * @param list<Node> $operands
     * @param list<Operator> $operators
     * @return Node
     */
    private function resolveOperators(array $operands, array $operators): Node
    {
        if (count($operands) - 1 !== count($operators)) {
            throw new \Exception('There should be 1 more operand than operators');
        }

        if (count($operands) === 1) {
            return $operands[0];
        }
        if (count($operands) === 2) {
            return new BinaryOperatorNode($operators[0], $operands[0], $operands[1]);
        }

        if ($operators[0]->hasPrecendenceOver($operators[1])) {
            return new BinaryOperatorNode(
                $operators[1],
                new BinaryOperatorNode($operators[0], $operands[0], $operands[1]),
                $this->resolveOperators(array_slice($operands, 2), array_slice($operators, 2))
            );
        } else {
            return new BinaryOperatorNode($operators[0], $operands[0], $this->resolveOperators(array_slice($operands, 1), array_slice($operators, 1)));
        }
    }
    public function parse(string $expression): Node
    {
        $buffer = new Buffer($expression);
        try {
            $result = $this->parseExpression($buffer);
        } catch (\Throwable $t) {
            $this->depth = 0;
            throw $t;
        }

        if ($buffer->peekChar() !== "") {
            throw new \Exception("Buffer not empty after parsing expression: " . $buffer);
        }
        if ($this->depth !== 0) {
            throw new \Exception("Depth not 0 after parsing expression: $buffer");
        }
        return $result;
    }

    private function parseFunction(Buffer $buffer): Node
    {
        $functionName = $buffer->readRegex("/^([a-zA-Z][a-zA-Z_0-9]*)/");

        $arguments = [];
        $buffer->consumeWhitespace();
        // Special exception for notempty prefix
        if ($functionName === 'notempty') {
            $operand = $this->parseExpression($buffer);
            return new UnaryOperatorNode(Operator::NotEmpty, $operand);
        }

        if (!$buffer->peekNext('(')) {
            // Unescaped string?!
            return new UnescapedStringNode($functionName);
        }
        $buffer->readChar('(');
        while ($buffer->peekChar() !== ")") {
            $buffer->consumeWhitespace();
            $arguments[] = $this->parseExpression($buffer);
            $buffer->consumeWhitespace();
            if ($buffer->peekChar() === ',') {
                $buffer->readChar(',');
            }
        }
        $buffer->readChar(')');

        return new FunctionNode($functionName, ...$arguments);
    }
}
