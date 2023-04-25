<?php
declare(strict_types=1);

use Rector\CodeQuality\Rector\Class_\InlineConstructorDefaultToPropertyRector;
use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\Php53;
use Rector\Php54;
use Rector\Php55;
use Rector\Php70;
use Rector\Php71;
use Rector\Php73;
use Rector\Php74;
use Rector\Php80;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/config',
        __DIR__ . '/module',
        __DIR__ . '/public',
    ]);
    $rectorConfig->fileExtensions([
        'php',
        'phtml'
    ]);

    // register a single rule
    $rectorConfig->rule(InlineConstructorDefaultToPropertyRector::class);

    // define sets of rules
    $rectorConfig->sets([
        //LevelSetList::UP_TO_PHP_82,
        SetList::CODE_QUALITY,
    ]);

    $rectorConfig->skip([
        Php71\Rector\FuncCall\RemoveExtraParametersRector::class,
        #
        Php73\Rector\FuncCall\JsonThrowOnErrorRector::class,
        #
        Php74\Rector\Closure\ClosureToArrowFunctionRector::class,
        #
        Php80\Rector\Switch_\ChangeSwitchToMatchRector::class,
        #
        Rector\CodeQuality\Rector\If_\ExplicitBoolCompareRector::class,
        Php71\Rector\FuncCall\CountOnNullRector::class,
        
        
        #Rector\CodeQuality\Rector\If_\ShortenElseIfRector::class,
        #Rector\CodeQuality\Rector\If_\CombineIfRector::class,
        
        Rector\CodeQuality\Rector\Ternary\SwitchNegatedTernaryRector::class,
        //Rector\CodeQuality\Rector\If_\SimplifyIfElseToTernaryRector::class,
        #\Rector\CodeQuality\Rector\Ternary\UnnecessaryTernaryExpressionRector::class,
        
        //Rector\CodeQuality\Rector\NotEqual\CommonNotEqualRector::class,
        
    ]);
};
