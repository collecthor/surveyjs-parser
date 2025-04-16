<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Traits;

use Collecthor\SurveyjsParser\Values\JavascriptFunction;
use function iter\map;

trait GetExtractor
{
    /**
     * @var list<string>
     */
    private readonly array $dataPath;

    public function getExtractor(): JavascriptFunction
    {
        $index = \iter\join('?.', map(fn (string $p) => json_encode([$p], JSON_THROW_ON_ERROR), $this->dataPath));
        return new JavascriptFunction(<<<JS
            (data) => data{$index} ?? null
        JS);
    }
}
