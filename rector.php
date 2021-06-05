<?php

use Rector\Core\Configuration\Option;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Wikimedia\Rector\Rectors\RemoveDeprecatedClassesRector;
use Wikimedia\Rector\Rectors\RemoveDeprecatedMethodsRector;
use Wikimedia\Rector\Rectors\RemoveDeprecatedPropertiesRector;
use Wikimedia\Rector\Services\DeprecationParser;

return static function ( ContainerConfigurator $containerConfigurator ) : void {
	$parameters = $containerConfigurator->parameters();
	$parameters->set ( Option::AUTOLOAD_PATHS, [
	    'includes/',
	] );
	$services = $containerConfigurator->services();
	$services
        ->set( DeprecationParser::class )
        ->call( 'configure', [[
            DeprecationParser::REMOVE_SOFT_DEPRECATED => true,
        ]] )
        ->autowire();
	$services->set( RemoveDeprecatedClassesRector::class );
	$services->set( RemoveDeprecatedMethodsRector::class );
	$services->set( RemoveDeprecatedPropertiesRector::class );
};
