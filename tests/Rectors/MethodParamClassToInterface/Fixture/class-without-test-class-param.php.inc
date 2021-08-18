<?php /** @noinspection ALL */

namespace Wikimedia\Rector\Tests\Rectors\MethodParamClassToInterface\Fixture;

class ClassWithoutTestClassParam {
    /**
     * @param int $i
     * @param string $s
     * @param bool $b
     * @param object $phpdoc
     * @param Iterator $iterator
     */
    public function test( int $i, string $s, bool $b, $phpdoc, Iterator $iterator ) {
        return null;
    }
}

?>


