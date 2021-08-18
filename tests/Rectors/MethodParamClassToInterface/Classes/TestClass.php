<?php

namespace Wikimedia\Rector\Tests\Rectors\MethodParamClassToInterface\Classes;

class TestClass implements TestInterface
{
    public $property;

    public function notInterfaceMethod(): string {
        return 'bla';
    }

    public function interfaceMethod(): bool {
        return true;
    }
}