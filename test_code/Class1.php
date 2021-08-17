<?php

namespace Wikimedia\Rector\TestCode;

class Class1 implements Interface1
{
    public function test_test() {
        return 'bla';
    }

    public function test(): bool
    {
        return true;
    }
}