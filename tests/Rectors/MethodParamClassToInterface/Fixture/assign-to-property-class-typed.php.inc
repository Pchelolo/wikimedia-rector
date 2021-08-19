<?php /** @noinspection ALL */

namespace Wikimedia\Rector\Tests\Rectors\MethodParamClassToInterface\Fixture;

use Wikimedia\Rector\Tests\Rectors\MethodParamClassToInterface\Classes\TestClass;

class AssignToPropertyClassTyped {

    /**
     * @var TestClass
     */
    private $property;

    /**
     * @param TestClass $param
     */
    public function test( TestClass $param ) {
        $this->property = $param;
    }
}

?>
