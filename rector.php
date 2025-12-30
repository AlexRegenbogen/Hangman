<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Class_\InlineConstructorDefaultToPropertyRector;
use Rector\CodeQuality\Rector\FuncCall\SingleInArrayToCompareRector;
use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\ClassMethod\RemoveUselessParamTagRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveUselessReturnTagRector;
use Rector\DeadCode\Rector\Property\RemoveUselessVarTagRector;
use Rector\Php83\Rector\ClassMethod\AddOverrideAttributeToOverriddenMethodsRector;
use Rector\Privatization\Rector\Class_\FinalizeTestCaseClassRector;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\TypeDeclaration\Rector\Class_\ReturnTypeFromStrictTernaryRector;
use Rector\TypeDeclaration\Rector\ClassMethod\AddMethodCallBasedStrictParamTypeRector;
use Rector\TypeDeclaration\Rector\ClassMethod\AddParamTypeBasedOnPHPUnitDataProviderRector;
use Rector\TypeDeclaration\Rector\ClassMethod\AddVoidReturnTypeWhereNoReturnRector;
use Rector\TypeDeclaration\Rector\ClassMethod\NumericReturnTypeFromStrictReturnsRector;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnNeverTypeRector;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnTypeFromStrictNativeCallRector;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnTypeFromStrictNewArrayRector;
use RectorLaravel\Set\LaravelSetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__.'/app',
        __DIR__.'/public',
        __DIR__.'/resources/lang',
        __DIR__.'/resources/views',
        __DIR__.'/tests/**',
    ]);
    $rectorConfig->skip([
        __DIR__.'/bootstrap',
    ]);

    $rectorConfig->rules([
        AddMethodCallBasedStrictParamTypeRector::class,
        AddParamTypeBasedOnPHPUnitDataProviderRector::class,
        FinalizeTestCaseClassRector::class,
        InlineConstructorDefaultToPropertyRector::class,
        RemoveUselessParamTagRector::class,
        RemoveUselessReturnTagRector::class,
        RemoveUselessVarTagRector::class,
        ReturnTypeFromStrictNativeCallRector::class,
        ReturnTypeFromStrictTernaryRector::class,
        AddOverrideAttributeToOverriddenMethodsRector::class,
        ReturnTypeFromStrictNewArrayRector::class,
        NumericReturnTypeFromStrictReturnsRector::class,
        AddVoidReturnTypeWhereNoReturnRector::class,
        SingleInArrayToCompareRector::class,
    ]);
    $rectorConfig->skip([
        ReturnNeverTypeRector::class, // Throws off testing with PHPunit.
    ]);

    $rectorConfig->importNames();
    $rectorConfig->importShortClasses(false);

    $rectorConfig->parallel(360, 4, 20);

    // define sets of rules
    $rectorConfig->sets([
        SetList::DEAD_CODE,
        SetList::EARLY_RETURN,
        SetList::TYPE_DECLARATION,
        SetList::CODE_QUALITY,
        SetList::CODING_STYLE,
        LevelSetList::UP_TO_PHP_83,
        LaravelSetList::LARAVEL_120,
    ]);
};
