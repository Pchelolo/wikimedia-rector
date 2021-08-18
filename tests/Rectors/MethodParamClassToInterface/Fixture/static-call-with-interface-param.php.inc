<?php /** @noinspection ALL */

namespace Wikimedia\Rector\Tests\Rectors\MethodParamClassToInterface\Fixture;

use Wikimedia\Rector\Tests\Rectors\MethodParamClassToInterface\Classes\TestClass;
use Wikimedia\Rector\Tests\Rectors\MethodParamClassToInterface\Classes\TestInterface;

class StaticCallWithInterfaceParam {

    /**
     * @param TestClass $param
     */
    public function test( TestClass $param ) {
        self::method( $param );
    }

    private static function method( TestInterface $param ) {
        $param->interfaceMethod();
    }
}

?>
-----
<?php /** @noinspection ALL */

namespace Wikimedia\Rector\Tests\Rectors\MethodParamClassToInterface\Fixture;

use Wikimedia\Rector\Tests\Rectors\MethodParamClassToInterface\Classes\TestClass;
use Wikimedia\Rector\Tests\Rectors\MethodParamClassToInterface\Classes\TestInterface;

class StaticCallWithInterfaceParam {

    /**
     * @param TestInterface $param
     */
    public function test( TestInterface $param ) {
        self::method( $param );
    }

    private static function method( TestInterface $param ) {
        $param->interfaceMethod();
    }
}

?>
