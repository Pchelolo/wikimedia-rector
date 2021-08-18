<?php /** @noinspection ALL */

namespace Wikimedia\Rector\Tests\Rectors\MethodParamClassToInterface\Fixture;

use Wikimedia\Rector\Tests\Rectors\MethodParamClassToInterface\Classes\TestClass;

class MethodCallInterface {
    /**
     * @param TestClass $param
     */
    public function test( TestClass $param ) {
        $param->interfaceMethod();
    }
}

?>
-----
<?php /** @noinspection ALL */

namespace Wikimedia\Rector\Tests\Rectors\MethodParamClassToInterface\Fixture;

use Wikimedia\Rector\Tests\Rectors\MethodParamClassToInterface\Classes\TestInterface;
use Wikimedia\Rector\Tests\Rectors\MethodParamClassToInterface\Classes\TestClass;

class MethodCallInterface {
    /**
     * @param TestInterface $param
     */
    public function test( TestInterface $param ) {
        $param->interfaceMethod();
    }
}

?>