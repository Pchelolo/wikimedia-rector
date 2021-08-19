<?php

use Wikimedia\Rector\Tests\Rectors\MediaWiki1_37\MoveIDatabaseMethodsToResultWrapper\Classes\TestIDatabase;
use Wikimedia\Rector\Tests\Rectors\MediaWiki1_37\MoveIDatabaseMethodsToResultWrapper\Classes\TestIResultWrapper;

$db = new TestIDatabase();
$res = new TestIResultWrapper();
$db->fetchObject( $res );
$db->fetchRow( $res );
$db->freeResult( $res );
$db->dataSeek( $res, 10 );
$i = $db->numRows( $res );
?>
-----
<?php

use Wikimedia\Rector\Tests\Rectors\MediaWiki1_37\MoveIDatabaseMethodsToResultWrapper\Classes\TestIDatabase;
use Wikimedia\Rector\Tests\Rectors\MediaWiki1_37\MoveIDatabaseMethodsToResultWrapper\Classes\TestIResultWrapper;

$db = new TestIDatabase();
$res = new TestIResultWrapper();
$res->fetchObject();
$res->fetchRow();
$res->free();
$res->seek( 10 );
$i = $res ? $res->numRows() : 0;
?>