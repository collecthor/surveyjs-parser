<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser;

use Collecthor\SurveyjsParser\Interfaces\ValueOptionInterface;
use Collecthor\SurveyjsParser\Values\DontKnowValueOption;
use Collecthor\SurveyjsParser\Values\IntegerValueOption;
use Collecthor\SurveyjsParser\Values\NoneValueOption;
use Collecthor\SurveyjsParser\Values\OtherValueOption;
use Collecthor\SurveyjsParser\Values\RefuseValueOption;
use Collecthor\SurveyjsParser\Values\StringValueOption;
use Collecthor\SurveyjsParser\Variables\OpenTextVariable;
use InvalidArgumentException;

trait ParserHelpers
{
    /**
     * @phpstan-param non-empty-array<mixed> $questionConfig
     * @param list<string> $dataPrefix
     * @return iterable<OpenTextVariable>
     */
    private function parseCommentField(array $questionConfig, SurveyConfiguration $surveyConfiguration, array $dataPrefix): iterable
    {
        if ($this->extractOptionalBoolean($questionConfig, 'hasOther') ?? $this->extractOptionalBoolean($questionConfig, 'showOtherItem') ?? false) {
            $defaultPostfix = "Other";
            $postfixField = "otherText";
        } elseif ($this->extractOptionalBoolean($questionConfig, 'hasComment') ?? $this->extractOptionalBoolean($questionConfig, 'showCommentArea') ?? false) {
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
            $titles[$locale] = $title . " - " . ($postfixes[$locale] ?? $postfixes['default']);
        }


        $name = implode('.', [...$dataPrefix, $this->extractName($questionConfig), 'comment']);
        $dataPath = [...$dataPrefix, $this->extractValueName($questionConfig) . $surveyConfiguration->commentPostfix];

        yield new OpenTextVariable(name: $name, dataPath: $dataPath, titles: $titles, rawConfiguration: $questionConfig);
    }

    /**
     * @param array<string|int, mixed> $config
     * @return array<string, string>
     */
    private function extractTitles(array $config): array
    {
        return $this->extractLocalizedTexts($config, 'title', [
            'default' => $this->extractOptionalName($config)
        ]);
    }

    /**
     * @param array<string|int, mixed> $config
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
     * @param array<mixed> $config
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
            $result = $defaults;
            foreach ($config[$field] as $locale => $data) {
                if (is_string($locale) && (is_string($data) || is_int($data))) {
                    $result[$locale] = (string) $data;
                }
            }
            return $result;
        }

        throw new InvalidArgumentException("Invalid format: " . print_r($config[$field], true));
    }

    /**
     * @param array<mixed> $config
     * @return array<mixed>|null
     */
    private function extractOptionalArray(array $config, string $key): null|array
    {
        if (!isset($config[$key])) {
            return null;
        }
        if (!is_array($config[$key])) {
            throw new InvalidArgumentException("Expected to find an array at key $key, inside: " . print_r($config, true));
        }

        return $config[$key];
    }
    /**
     * @param array<mixed> $config
     * @return string
     * @throws InvalidArgumentException
     */
    private function extractName(array $config): string
    {
        if (!isset($config['name']) || !is_string($config['name'])) {
            throw new InvalidArgumentException("Expected to find a string at key `name`, inside: " . print_r($config, true));
        }
        return $config['name'];
    }

    /**
     * @param array<mixed> $config
     * @return string
     * @throws InvalidArgumentException
     */
    private function extractOptionalName(array $config): string
    {
        if (isset($config['name']) && is_string($config['name'])) {
            return $config['name'];
        } elseif (isset($config['name']) && !is_string($config['name'])) {
            throw new InvalidArgumentException("Expected to find a string at key `name`, inside: " . print_r($config, true));
        } elseif (!isset($config['name']) && isset($config['title']) && is_scalar($config['title'])) {
            return (string) $config['title'];
        }
        return "";
    }

    /**
     * @param array<mixed> $config
     */
    private function showNoneItem(array $config): bool
    {
        return $this->extractBoolean($config, false, 'showNoneItem', 'hasNone');
    }

    /**
     * @param array<mixed> $config
     */
    private function showOtherItem(array $config): bool
    {
        return $this->extractBoolean($config, false, 'showOtherItem', 'hasOther');
    }

    /**
     * @param array<mixed> $config
     */
    private function showRefuseItem(array $config): bool
    {
        return $this->extractBoolean($config, false, 'showRefuseItem');
    }

    /**
     * @param array<mixed> $config
     */
    private function showDontKnowItem(array $config): bool
    {
        return $this->extractBoolean($config, false, 'showDontKnowItem');
    }

    /**
     * @param array<mixed> $config
     */
    private function extractBoolean(array $config, bool $default, string ...$keys): bool
    {
        foreach ($keys as $key) {
            if (is_bool($config[$key] ?? null)) {
                return $config[$key];
            }
        }

        return $default;
    }

    /**
     * @param array<mixed> $config
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
     * @param array<mixed> $config
     */
    private function extractOptionalString(array $config, string $key): string|null
    {
        if (!isset($config[$key])) {
            return null;
        }
        if (is_string($config[$key])) {
            return $config[$key];
        }

        throw new InvalidArgumentException("Key $key in array is expected to be string or null, got: " . print_r($config, true));
    }

    /**
     * @param array<mixed> $config
     */
    private function extractOptionalInteger(array $config, string $key): int|null
    {
        if (!isset($config[$key])) {
            return null;
        }

        if (is_int($config[$key])) {
            return $config[$key];
        }
        throw new InvalidArgumentException("Key $key in array is expected to be integer or null, got: " . print_r($config, true));
    }

    /**
     * @param array<string|int, mixed> $questionConfig
     * @return list<ValueOptionInterface>
     */
    private function generateChoices(array $questionConfig): array
    {
        $choices = $this->extractChoices($this->extractOptionalArray($questionConfig, 'choices'));
        if ($this->showNoneItem($questionConfig)) {
            $choices[] = new NoneValueOption($this->extractLocalizedTexts($questionConfig, 'noneText'));
        }
        if ($this->showOtherItem($questionConfig)) {
            $choices[] = new OtherValueOption($this->extractLocalizedTexts($questionConfig, 'otherText'));
        }
        if ($this->showRefuseItem($questionConfig)) {
            $choices[] = new RefuseValueOption($this->extractLocalizedTexts($questionConfig, 'refuseText'));
        }
        if ($this->showDontKnowItem($questionConfig)) {
            $choices[] = new DontKnowValueOption($this->extractLocalizedTexts($questionConfig, 'dontKnowText'));
        }
        return $choices;
    }

    /**
     * We use a mixed type here; since we're parsing user data.
     * We expect / hope for a list, but might get anything.
     * @return list<ValueOptionInterface>
     */
    private function extractChoices(mixed $choices): array
    {
        if ($choices === null) {
            return [];
        } elseif (!is_array($choices) || !array_is_list($choices)) {
            throw new \InvalidArgumentException("Choices must be a list");
        }
        $result = [];
        foreach ($choices as $choice) {
            if (is_array($choice) && isset($choice['value'])) {
                $value = $choice['value'];
                if (!is_scalar($value)) {
                    throw new \InvalidArgumentException('Values must be scalar, got: ' . print_r($choice, true));
                }
                $displayValues = $this->extractLocalizedTexts($choice, 'text', ['default' => (string) $value]);
            } elseif (is_int($choice) || (is_string($choice) && ctype_digit($choice))) {
                $value = (int)$choice;
                $displayValues = [];
            } elseif (is_string($choice)) {
                $value = $choice;
                $displayValues = [];
            } elseif ($choice === []) {
                continue;
            } else {
                throw new \InvalidArgumentException("Each choice must be a string or int or an array with keys value and text");
            }

            if (is_int($value)) {
                $result[] = new IntegerValueOption($value, $displayValues);
            } else {
                $result[] = new StringValueOption((string) $value, $displayValues);
            }
        }

        // Make sure that if 1 option is a string value option, all options are string value options.

        return $result;
    }

    /**
     * Concat a combination of localized strings and normal ones
     * @param array<string, string> $titles
     * @param array<string, string>|string $variables
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
                    $result[$locale] .= $variable[$locale] ?? $variable['default'] ?? $variable[array_keys($variable)[0]];
                } else {
                    $result[$locale] .= $variable;
                }
            }
        }
        return $result;
    }
}
