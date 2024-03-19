<?php

declare(strict_types=1);

// ecs.php
use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->parallel();

    // Paths
    $ecsConfig->paths([
        __DIR__ . '/src', __DIR__ . '/tests', __DIR__ . '/ecs.php'



    ]);
    // A. full sets
    $ecsConfig->import(SetList::PSR_12);


    // B. standalone rule
    //    $services->set(ArraySyntaxFixer::class)
    //        ->call('configure', [[
    //            'syntax' => 'short',
    //        ]]);
    $ecsConfig->rule(NoUnusedImportsFixer::class);
};
