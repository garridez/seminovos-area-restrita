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
        //__DIR__ . '/node_modules',
        __DIR__ . '/public',
    ]);

    // register a single rule
    $rectorConfig->rule(InlineConstructorDefaultToPropertyRector::class);

    // define sets of rules
    $rectorConfig->sets([
        LevelSetList::UP_TO_PHP_74
    ]);

    $rectorConfig->skip([
        #
        //Php53\Rector\Ternary\TernaryToElvisRector::class,
        #
        //Php54\Rector\Array_\LongArrayToShortArrayRector::class,
        #
        //Php55\Rector\Class_\ClassConstantToSelfClassRector::class,
        #
        #Php70\Rector\Ternary\TernaryToNullCoalescingRector::class,
        #
        Php71\Rector\ClassConst\PublicConstantVisibilityRector::class,
        Php71\Rector\FuncCall\CountOnNullRector::class,
        Php71\Rector\FuncCall\RemoveExtraParametersRector::class,
        Php71\Rector\List_\ListToArrayDestructRector::class,
        #
        Php73\Rector\FuncCall\ArrayKeyFirstLastRector::class,
        Php73\Rector\FuncCall\JsonThrowOnErrorRector::class,
        Php73\Rector\FuncCall\StringifyStrNeedlesRector::class,
        #
        Php74\Rector\Assign\NullCoalescingOperatorRector::class,
        Php74\Rector\Closure\ClosureToArrowFunctionRector::class,
        Php74\Rector\LNumber\AddLiteralSeparatorToNumberRector::class,
        #
        Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector::class,
        Php80\Rector\FunctionLike\MixedTypeRector::class,
        Php80\Rector\FunctionLike\UnionTypesRector::class,
        Php80\Rector\Identical\StrEndsWithRector::class,
        Php80\Rector\Identical\StrStartsWithRector::class,
        Php80\Rector\NotIdentical\StrContainsRector::class,
        Php80\Rector\Switch_\ChangeSwitchToMatchRector::class,
        #
        Rector\TypeDeclaration\Rector\Property\TypedPropertyFromAssignsRector::class,
    ]);
};
