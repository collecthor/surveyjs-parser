<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser;

use Collecthor\DataInterfaces\ValueOptionInterface;
use Collecthor\DataInterfaces\VariableInterface;
use Collecthor\SurveyjsParser\Values\IntegerValueOption;
use Collecthor\SurveyjsParser\Values\StringValueOption;
use Collecthor\SurveyjsParser\Variables\OpenTextVariable;
use InvalidArgumentException;

trait ParserHelpers
{
    /**
     * @phpstan-param non-empty-array<string, mixed> $questionConfig
     * @param array<string> $dataPrefix
     * @return iterable<VariableInterface>
     */
    private function parseCommentField(array $questionConfig, SurveyConfiguration $surveyConfiguration, array $dataPrefix): iterable
    {
        if ($this->extractOptionalBoolean($questionConfig, 'hasOther') ?? false) {
            $defaultPostfix = "Other";
            $postfixField = "otherText";
        } elseif ($this->extractOptionalBoolean($questionConfig, 'hasComment') ?? false) {
            $defaultPostfix = "Other (describe)";
            $postfixField = "commentText";
        } else {
            return;
        }

        $defaultPostfixes = [
            'default' => $defaultPostfix,
        ];

        $postfixes = $this->extractLocalizedTexts($questionConfig, $postfixField, $defaultPostfixes);

        $titles = [];
        foreach ($this->extractTitles($questionConfig) as $locale => $title) {
            $titles[$locale] = $title . " - " . $postfixes[$locale];
        }


        $name = implode('.', [...$dataPrefix, $this->extractName($questionConfig), 'comment']);
        $dataPath = [...$dataPrefix, $this->extractValueName($questionConfig) . $surveyConfiguration->commentPostfix];

        yield new OpenTextVariable($name, $titles, $dataPath, $questionConfig);
    }

    /**
     * @param array<string, mixed> $config
     * @return array<string, string>
     */
    private function extractTitles(array $config): array
    {
        return $this->extractLocalizedTexts($config, 'title', [
            'default' => $this->extractName($config)
        ]);
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
    private function extractLocalizedTexts(array $config, string $field = 'text', array $defaults = []): array
    {
        if (!isset($config[$field])) {
            return $defaults;
        }

        if (is_string($config[$field])) {
            return [
                'default' => $config[$field],
            ];
        }

        if (is_array($config[$field])) {
            $result = [];
            foreach ($config[$field] as $locale => $data) {
                if (!is_array($data)) {
                    $result[$locale] = (string) $data;
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
     * @param string $locale
     * @return string
     * @throws InvalidArgumentException
     */
    private function extractName(array $config, string $locale = 'default'): string
    {
        if (!isset($config['name']) || !is_string($config['name'])) {
            throw new InvalidArgumentException("Expected to find a string at key `name`, inside: " . print_r($config, true));
        }
        if (is_array($config['name'])) {
            if (isset($config['name'][$locale]) && is_string($config['name'][$locale])) {
                return $config['name'][$locale];
            }
        }
        return $config['name'];
    }

    /**
     * @param array<string, mixed> $config
     * @param string $key
     * @return bool|null
     */
    private function extractOptionalBoolean(array $config, string $key): bool|null
    {
        if (!isset($config[$key])) {
            return null;
        }
        if (is_bool($config[$key])) {
            return $config[$key];
        }

        throw new InvalidArgumentException("Key $key in array is expected to be boolean or null, got: " . print_r($config, true));
    }

    /**
     * @param array<mixed>  $choices
     * @return non-empty-list<ValueOptionInterface>
     */
    private function extractChoices(array $choices): array
    {
        if (!array_is_list($choices) || $choices === []) {
            throw new \InvalidArgumentException("Choices must be a non empty list");
        }
        /** @var ValueOptionInterface[] $result */
        $result = [];
        foreach ($choices as $choice) {
            if (is_array($choice) && isset($choice['value'], $choice['text'])) {
                $value = $choice['value'];
                $displayValues = $this->extractLocalizedTexts($choice);
            } elseif (is_string($choice) || is_int($choice)) {
                $value = $choice;
                $displayValues = [];
            } else {
                throw new \InvalidArgumentException("Each choice must be a string or int or an array with keys value and text");
            }

            if (is_int($value)) {
                $result[] = new IntegerValueOption($value, $displayValues);
            } elseif (is_scalar($value)) {
                $result[] = new StringValueOption((string) $value, $displayValues);
            } else {
                throw new \InvalidArgumentException('Values must be scalar, got: ' . print_r($choice, true));
            }
        }

        return $result;
    }

    /**
     * Concat a combination of localized strings and normal ones
     * @param array<string> $titles
     * @param (array<string, string>|string)[] $variables
     * @return array<string, string>
     */
    private function arrayFormat(array $titles, array|string ...$variables): array
    {
        $locales = array_keys($titles);

        $result = [];
        foreach ($locales as $locale) {
            $result[$locale] = '';
            foreach ([$titles, ...$variables] as $variable) {
                if (is_array($variable)) {
                    $result[$locale] .= $variable[$locale] ?? $variable['default'];
                } else {
                    $result[$locale] .= $variable;
                }
            }
        }
        return $result;
    }
}
