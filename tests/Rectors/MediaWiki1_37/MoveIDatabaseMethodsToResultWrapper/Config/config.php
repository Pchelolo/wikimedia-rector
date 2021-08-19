<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Wikimedia\Rector\Rectors\MediaWiki1_37\MoveIDatabaseMethodsToResultWrapperRector;
use Wikimedia\Rector\Tests\Rectors\MediaWiki1_37\MoveIDatabaseMethodsToResultWrapper\Classes\TestIDatabase;

return static function (
    ContainerConfigurator $containerConfigurator
): void {
    $containerConfigurator->services()
        ->set( MoveIDatabaseMethodsToResultWrapperRector::class )
        ->call( 'configure', [[
            MoveIDatabaseMethodsToResultWrapperRector::DATABASE_CLASS => TestIDatabase::class,
        ]] );
};