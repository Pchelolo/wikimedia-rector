<?php

use Rector\Core\Configuration\Option;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Wikimedia\Rector\Rectors\MethodParamClassToInterface;
use Wikimedia\Rector\Tests\Rectors\MethodParamClassToInterface\Classes\TestClass;
use Wikimedia\Rector\Tests\Rectors\MethodParamClassToInterface\Classes\TestInterface;

return static function (
    ContainerConfigurator $containerConfigurator
): void {
    $parameters = $containerConfigurator->parameters();
    $parameters->set( Option::AUTO_IMPORT_NAMES, true );
    $containerConfigurator->services()
        ->set( MethodParamClassToInterface::class )
        ->call( 'configure', [[
            MethodParamClassToInterface::REPLACE_CLASS => TestClass::class,
            MethodParamClassToInterface::REPLACE_WITH_INTERFACE => TestInterface::class,
        ]] );
};