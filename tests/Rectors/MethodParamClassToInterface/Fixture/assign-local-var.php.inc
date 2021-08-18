<?php /** @noinspection ALL */

namespace Wikimedia\Rector\Tests\Rectors\MethodParamClassToInterface\Fixture;

use Wikimedia\Rector\Tests\Rectors\MethodParamClassToInterface\Classes\TestClass;

class AssignToLocalVar {

    /**
     * @param TestClass $param
     */
    public function test( TestClass $param ) {
        $property = $param;
    }
}

?>
-----
<?php /** @noinspection ALL */

namespace Wikimedia\Rector\Tests\Rectors\MethodParamClassToInterface\Fixture;

use Wikimedia\Rector\Tests\Rectors\MethodParamClassToInterface\Classes\TestInterface;
use Wikimedia\Rector\Tests\Rectors\MethodParamClassToInterface\Classes\TestClass;

class AssignToLocalVar {

    /**
     * @param TestInterface $param
     */
    public function test( TestInterface $param ) {
        $property = $param;
    }
}

?>