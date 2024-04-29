<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Expressions;

class Buffer
{
    public function __construct(private string $contents)
    {
    }

    public function consumeWhitespace(): void
    {
        $this->contents = ltrim($this->contents);
    }

    public function readChar(string ...$validCharacters): string
    {
        $result = $this->peekChar(...$validCharacters);
        if ($result === "") {
            throw new \Exception('Failed to read character, buffer is empty');
        }
        $this->contents = mb_substr($this->contents, 1);
        return $result;
    }


    public function peekChar(string ...$validCharacters): string
    {
        $result = mb_substr($this->contents, 0, 1);
        if ($validCharacters !== [] && $result !== "" && !in_array($result, $validCharacters, true)) {
            throw new \Exception("Expected character in " . implode(", ", $validCharacters) . " got $result");
        }

        return $result;
    }


    public function readOptionalOperator(): Operator | null
    {
        foreach (Operator::cases() as $operator) {
            if (str_starts_with($this->contents, $operator->value)) {
                $this->contents = mb_substr($this->contents, mb_strlen($operator->value));
                return $operator;
            }
        }
        return null;
    }
    public function peekNext(string $character): bool
    {
        return str_starts_with($this->contents, $character);
    }

    public function readRegex(string $regex): string
    {
        if (preg_match($regex, $this->contents, $matches) === 1) {
            $this->contents = substr($this->contents, strlen($matches[0]));
            return $matches[1];
        } else {
            throw new \Exception("Expected match of ($regex) in buffer: {$this->contents}");
        }
    }

    public function __toString(): string
    {
        return "Buffer {$this->contents}";
    }
}
