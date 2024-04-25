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

    public function peekNext(string $character): bool
    {
        return $character === mb_substr($this->contents, 0, 1);
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
}
