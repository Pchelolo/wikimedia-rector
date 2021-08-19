<?php

use Wikimedia\Rector\Tests\Rectors\MediaWiki1_37\MoveIDatabaseMethodsToResultWrapper\Classes\TestIDatabase;
use Wikimedia\Rector\Tests\Rectors\MediaWiki1_37\MoveIDatabaseMethodsToResultWrapper\Classes\TestIResultWrapper;

function test(): TestIDatabase {
    return new TestIDatabase();
}
$res = new TestIResultWrapper();
test()->fetchObject( $res );
test()->fetchRow( $res );
test()->freeResult( $res );
test()->dataSeek( $res, 10 );
$i = test()->numRows( $res );
?>
-----
<?php

use Wikimedia\Rector\Tests\Rectors\MediaWiki1_37\MoveIDatabaseMethodsToResultWrapper\Classes\TestIDatabase;
use Wikimedia\Rector\Tests\Rectors\MediaWiki1_37\MoveIDatabaseMethodsToResultWrapper\Classes\TestIResultWrapper;

function test(): TestIDatabase {
    return new TestIDatabase();
}
$res = new TestIResultWrapper();
$res->fetchObject();
$res->fetchRow();
$res->free();
$res->seek( 10 );
$i = $res ? $res->numRows() : 0;
?>