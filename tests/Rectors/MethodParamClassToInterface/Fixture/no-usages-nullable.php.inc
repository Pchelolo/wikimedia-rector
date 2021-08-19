<?php /** @noinspection ALL */

namespace Wikimedia\Rector\Tests\Rectors\MethodParamClassToInterface\Fixture;

use Wikimedia\Rector\Tests\Rectors\MethodParamClassToInterface\Classes\TestClass;

class NoUsagesNullable {
    /**
     * @param TestClass|null $param
     */
    public function test( ?TestClass $param ) {}
}

?>
-----
<?php /** @noinspection ALL */

namespace Wikimedia\Rector\Tests\Rectors\MethodParamClassToInterface\Fixture;

use Wikimedia\Rector\Tests\Rectors\MethodParamClassToInterface\Classes\TestInterface;
use Wikimedia\Rector\Tests\Rectors\MethodParamClassToInterface\Classes\TestClass;

class NoUsagesNullable {
    /**
     * @param TestInterface|null $param
     */
    public function test( ?TestInterface $param ) {}
}

?>