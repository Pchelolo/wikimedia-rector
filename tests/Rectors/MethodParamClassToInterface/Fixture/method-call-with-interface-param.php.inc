<?php /** @noinspection ALL */

namespace Wikimedia\Rector\Tests\Rectors\MethodParamClassToInterface\Fixture;

use Wikimedia\Rector\Tests\Rectors\MethodParamClassToInterface\Classes\TestClass;
use Wikimedia\Rector\Tests\Rectors\MethodParamClassToInterface\Classes\TestInterface;

class MethodCallWithInterfaceParam {

    /**
     * @param TestClass $param
     */
    public function test( TestClass $param ) {
        $this->method( $param );
    }

    private function method( TestInterface $param ) {
        $param->interfaceMethod();
    }
}

?>
-----
<?php /** @noinspection ALL */

namespace Wikimedia\Rector\Tests\Rectors\MethodParamClassToInterface\Fixture;

use Wikimedia\Rector\Tests\Rectors\MethodParamClassToInterface\Classes\TestClass;
use Wikimedia\Rector\Tests\Rectors\MethodParamClassToInterface\Classes\TestInterface;

class MethodCallWithInterfaceParam {

    /**
     * @param TestInterface $param
     */
    public function test( TestInterface $param ) {
        $this->method( $param );
    }

    private function method( TestInterface $param ) {
        $param->interfaceMethod();
    }
}

?>
