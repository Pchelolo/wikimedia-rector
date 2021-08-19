<?php

namespace Wikimedia\Rector\Tests\Rectors\MediaWiki1_37\MoveIDatabaseMethodsToResultWrapper;

use Iterator;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @covers \Wikimedia\Rector\Rectors\MediaWiki1_37\MoveIDatabaseMethodsToResultWrapperRector
 */
class MoveIDatabaseMethodsToResultWrapperTest extends AbstractRectorTestCase {

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