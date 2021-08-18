<?php /** @noinspection ALL */

namespace Wikimedia\Rector\Tests\Rectors\MethodParamClassToInterface\Fixture;

use Wikimedia\Rector\Tests\Rectors\MethodParamClassToInterface\Classes\TestClass;

class StaticCallWithClassParam {

    /**
     * @param TestClass $param
     */
    public function test( TestClass $param ) {
        self::method( $param );
    }

    private static function method( TestClass $param ) {
        $param->notInterfaceMethod();
    }
}

?>
