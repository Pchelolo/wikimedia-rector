<?php

namespace Wikimedia\Rector\TestCode;

class Example {

    public function method1( Class1 $arg1, Interface1 $arg2 ) {
        $this->method2( $arg1, $arg2 );
        echo $arg2->test_test();
    }

    public function method2( Class1 $arg, Interface1 $arg3 ) {
        echo $arg->test();
    }
}