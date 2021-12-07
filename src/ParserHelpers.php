<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser;

use Collecthor\DataInterfaces\VariableInterface;
use Collecthor\SurveyjsParser\Parsers\CommentParser;
use InvalidArgumentException;

trait ParserHelpers
{
    /**
     * @param array<string, mixed> $config
     * @param SurveyConfiguration $surveyConfiguration
     * @return array<string, string>
     */
    private function extractTitles(array $config, SurveyConfiguration $surveyConfiguration): array
    {
        $defaults = [];
        foreach ($surveyConfiguration->locales as $locale) {
            if (isset($config['name']) && is_string($config['name'])) {
                $defaults[$locale] = $config['name'];
            }
        }
        return $this->extractLocalizedTexts($config, $surveyConfiguration, 'title', $defaults);
    }

    /**
     * Parse the comment variable part of a question
     * @param non-empty-array<string, mixed> $config
     * @param SurveyConfiguration $surveyConfiguration
     * @return iterable<VariableInterface>
     */
    private function parseComments(array $config, SurveyConfiguration $surveyConfiguration): iterable
    {
        $parser = new CommentParser();
        yield from $parser->parse($this, $config, $surveyConfiguration);
    }

    /**
     * @param array<string, mixed> $config
     */
    private function extractValueName(array $config): string
    {
        $result = $config['valueName'] ?? $config['name'] ?? null;
        if (!is_string($result)) {
            throw new \InvalidArgumentException('The valueName or name key must be set');
        }
        return $result;
    }

    /**
     * @param non-empty-string $field
     * @param array<string, mixed> $config
     * @param array<string, string> $defaults
     * @return array<string, string>
     */
    private function extractLocalizedTexts(array $config, SurveyConfiguration $surveyConfiguration, string $field = 'text', array $defaults = []): array
    {
        if (!isset($config[$field])) {
            return $defaults;
        }

        if (is_string($config[$field])) {
            $result = [];
            foreach ($surveyConfiguration->locales as $locale) {
                $result[$locale] = $config[$field];
            }
            return $result;
        }

        if (is_array($config[$field])) {
            $result = [];
            foreach ($surveyConfiguration->locales as $locale) {
                if (isset($config[$field][$locale]) && !is_array($config[$field][$locale])) {
                    $result[$locale] = (string) $config[$field][$locale];
                }
            }
            return $result;
        }

        throw new InvalidArgumentException("Invalid format: " . print_r($config[$field], true));
    }

    /**
     * @param array<string, mixed> $config
     * @param string $key
     * @return array<mixed>
     */
    private function extractArray(array $config, string $key): array
    {
        if (!isset($config[$key])) {
            return [];
        }
        if (!is_array($config[$key])) {
            throw new InvalidArgumentException("Expected to find an array at key $key, inside: " . print_r($config, true));
        }

        return $config[$key];
    }

    /**
     * @param array<mixed> $config
     */
    private function extractName(array $config): string
    {
        if (!isset($config['name']) || !is_string($config['name'])) {
            throw new InvalidArgumentException("Expected to find a string at key `name`, inside: " . print_r($config, true));
        }
        return $config['name'];
    }
}
