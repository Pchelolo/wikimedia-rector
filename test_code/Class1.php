<?php

class Class1 implements Interface1
{
    public function test_test( Class1 $param ) {
        if ( $param->test() ) {
            echo 1;
        }
        array_key_exists( 'a', [] );
    }

    public function test(): bool
    {
        return true;
    }
}