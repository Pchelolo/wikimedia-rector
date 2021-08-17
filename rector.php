<?php

use Rector\Core\Configuration\Option;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Wikimedia\Rector\Rectors\MethodParamClassToInterface;

return static function ( ContainerConfigurator $containerConfigurator ) : void {
	$parameters = $containerConfigurator->parameters();
	$parameters->set( Option::AUTOLOAD_PATHS, [
	    __DIR__ . '/test_code',
	] );
	$services = $containerConfigurator->services();

	/*$services
        ->set( DeprecationParser::class )
        ->call( 'configure', [[
            DeprecationParser::REMOVE_SOFT_DEPRECATED => true,
        ]] )
        ->autowire();
	$services->set( RemoveDeprecatedClassesRector::class );
	$services->set( RemoveDeprecatedMethodsRector::class );
	$services->set( RemoveDeprecatedPropertiesRector::class );*/

    $services
        ->set( MethodParamClassToInterface::class )
        ->call( 'configure', [[
            MethodParamClassToInterface::REPLACE_CLASS => '\Wikimedia\Rector\TestCode\Class1',
            MethodParamClassToInterface::REPLACE_WITH_INTERFACE => '\Wikimedia\Rector\TestCode\Interface1',
        ]] );
};
