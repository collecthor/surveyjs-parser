<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Helpers;

use Collecthor\SurveyjsParser\Interfaces\ValueOptionInterface;
use Collecthor\SurveyjsParser\Values\DontKnowValueOption;
use Collecthor\SurveyjsParser\Values\IntegerValueOption;
use Collecthor\SurveyjsParser\Values\NoneValueOption;
use Collecthor\SurveyjsParser\Values\OtherValueOption;
use Collecthor\SurveyjsParser\Values\RefuseValueOption;
use Collecthor\SurveyjsParser\Values\StringValueOption;
use InvalidArgumentException;

use function iter\all;

/**
 * @param array<mixed> $config
 */
function extractOptionalString(array $config, string $key): string|null
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
function extractOptionalInteger(array $config, string $key): int|null
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
 * @param array<mixed> $config
 * @return string
 * @throws InvalidArgumentException
 */
function extractOptionalName(array $config): string
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
 * @param array<string|int, mixed> $config
 */
function extractValueName(array $config): string
{
    $result = $config['valueName'] ?? $config['name'] ?? null;
    if (!is_string($result)) {
        throw new \InvalidArgumentException('The valueName or name key must be set');
    }
    return $result;
}


/**
 * @param array<mixed> $config
 * @return array<mixed>|null
 */
function extractOptionalArray(array $config, string $key): null|array
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
 * We use a mixed type here; since we're parsing user data.
 * We expect / hope for a list, but might get anything.
 * @return list<ValueOptionInterface>
 */
function extractChoices(mixed $choices): array
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
            $displayValues = extractLocalizedTexts($choice, 'text', ['default' => (string) $value]);
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
 * @param non-empty-string $field
 * @param array<mixed> $config
 * @param array<string, string> $defaults
 * @return array<string, string>
 */
function extractLocalizedTexts(array $config, string $field = 'text', array $defaults = []): array
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
 */
function extractBoolean(array $config, bool $default, string ...$keys): bool
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
function extractOptionalBoolean(array $config, string $key): bool|null
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
function showNoneItem(array $config): bool
{
    return extractBoolean($config, false, 'showNoneItem', 'hasNone');
}

/**
 * @param array<mixed> $config
 */
function showOtherItem(array $config): bool
{
    return extractBoolean($config, false, 'showOtherItem', 'hasOther');
}

/**
 * @param array<mixed> $config
 */
function showCommentArea(array $config): bool
{
    return extractBoolean($config, false, 'showCommentArea', 'hasComment');
}

/**
 * @param array<mixed> $config
 */
function showRefuseItem(array $config): bool
{
    return extractBoolean($config, false, 'showRefuseItem');
}

/**
 * @param array<mixed> $config
 */
function showDontKnowItem(array $config): bool
{
    return extractBoolean($config, false, 'showDontKnowItem');
}

/**
 * @param array<string|int, mixed> $questionConfig
 * @return list<ValueOptionInterface>
 */
function generateChoices(array $questionConfig): array
{
    $choices = extractChoices(extractOptionalArray($questionConfig, 'choices'));
    if (showNoneItem($questionConfig)) {
        $choices[] = new NoneValueOption(extractLocalizedTexts($questionConfig, 'noneText'));
    }
    if (showOtherItem($questionConfig)) {
        $choices[] = new OtherValueOption(extractLocalizedTexts($questionConfig, 'otherText'));
    }
    if (showRefuseItem($questionConfig)) {
        $choices[] = new RefuseValueOption(extractLocalizedTexts($questionConfig, 'refuseText'));
    }
    if (showDontKnowItem($questionConfig)) {
        $choices[] = new DontKnowValueOption(extractLocalizedTexts($questionConfig, 'dontKnowText'));
    }
    return $choices;
}


/**
 * @param array<mixed> $config
 * @return string
 * @throws InvalidArgumentException
 */
function extractName(array $config): string
{
    if (!isset($config['name']) || !is_string($config['name'])) {
        throw new InvalidArgumentException("Expected to find a string at key `name`, inside: " . print_r($config, true));
    }
    return $config['name'];
}



/**
 * Concat a combination of localized strings and normal ones
 * @param array<string, string> $titles
 * @param array<string, string>|string $variables
 * @return array<string, string>
 */
function arrayFormat(array $titles, array|string ...$variables): array
{
    $locales = [];
    foreach ([$titles, ...$variables] as $stringDictionary) {
        if (is_array($stringDictionary)) {
            foreach (array_keys($stringDictionary) as $locale) {
                $locales[$locale] = true;
            }
        }
    }
    $result = [];
    foreach (array_keys($locales) as $locale) {
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


/**
 * @param array<string|int, mixed> $config
 * @return array<string, string>
 */
function extractTitles(array $config): array
{
    return extractLocalizedTexts($config, 'title', [
        'default' => extractOptionalName($config)
    ]);
}


/**
 * @template T
 * @param list<object> $items
 * @param class-string<T> $class
 * @return bool
 * @phpstan-assert-if-true list<T> $items
 */
function allInstanceOf(array $items, string $class): bool
{
    return all(static fn (object $option) => $option instanceof $class, $items);
}
