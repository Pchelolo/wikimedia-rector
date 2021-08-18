<?php /** @noinspection ALL */

namespace Wikimedia\Rector\Tests\Rectors\MethodParamClassToInterface\Fixture;

use Wikimedia\Rector\Tests\Rectors\MethodParamClassToInterface\Classes\TestClass;

class Constructed {
    /** @var TestClass */
    private $param;

    /**
     * @param TestClass $param
     */
    public function __construct( TestClass $param ) {
        $this->param = $param;
    }
}
class UsedInConstructor {

    /**
     * @param TestClass $param
     */
    public function test( TestClass $param ) {
        $test = new Constructed( $param );
    }
}

?>
