<?php

namespace Wikimedia\Rector\Tests\Rectors\MethodParamClassToInterface;

use Iterator;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @covers \Wikimedia\Rector\Rectors\MethodParamClassToInterface
 */
class MethodParamClassToInterfaceTest extends AbstractRectorTestCase {

    /**
     * @dataProvider provideData()
     */
    public function test( SmartFileInfo $fileInfo ): void {
        $this->doTestFileInfo( $fileInfo );
    }

    public function provideData(): Iterator {
        return $this->yieldFilesFromDirectory(__DIR__ . '/Fixture');
    }

    public function provideConfigFilePath(): string {
        return __DIR__ . '/Config/config.php';
    }
}