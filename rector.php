<?php

use Rector\Core\Configuration\Option;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Wikimedia\Rector\Rectors\MethodParamClassToInterface;
use Wikimedia\Rector\Rectors\RemoveDeprecatedClassesRector;
use Wikimedia\Rector\Rectors\RemoveDeprecatedMethodsRector;
use Wikimedia\Rector\Rectors\RemoveDeprecatedPropertiesRector;
use Wikimedia\Rector\Services\DeprecationParser;

return static function ( ContainerConfigurator $containerConfigurator ) : void {
	$parameters = $containerConfigurator->parameters();
	$parameters->set( Option::BOOTSTRAP_FILES, [
	    'includes/AutoLoader.php',
        'vendor/autoload.php'
    ] );
	$parameters->set( Option::AUTOLOAD_PATHS, [
	    //__DIR__ . '/test_code',
        'includes/',
        'extensions/Echo/includes/',
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
            MethodParamClassToInterface::REPLACE_CLASS => 'User',
            MethodParamClassToInterface::REPLACE_WITH_INTERFACE => '\MediaWiki\User\UserIdentity',
        ]] );
};
