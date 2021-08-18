<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Wikimedia\Rector\Rectors\MethodParamClassToInterface;
use Wikimedia\Rector\Tests\Rectors\MethodParamClassToInterface\Classes\TestClass;
use Wikimedia\Rector\Tests\Rectors\MethodParamClassToInterface\Classes\TestInterface;

return static function (
    ContainerConfigurator $containerConfigurator
): void {
    $containerConfigurator->services()
        ->set( MethodParamClassToInterface::class )
        ->call( 'configure', [[
            MethodParamClassToInterface::REPLACE_CLASS => TestClass::class,
            MethodParamClassToInterface::REPLACE_WITH_INTERFACE => TestInterface::class,
        ]] );
};