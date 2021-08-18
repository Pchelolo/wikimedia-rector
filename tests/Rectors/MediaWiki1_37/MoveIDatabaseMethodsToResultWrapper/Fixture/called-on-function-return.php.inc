<?php

use Wikimedia\Rector\Tests\Rectors\MediaWiki1_37\MoveIDatabaseMethodsToResultWrapper\Classes\TestIDatabase;
use Wikimedia\Rector\Tests\Rectors\MediaWiki1_37\MoveIDatabaseMethodsToResultWrapper\Classes\TestIResultWrapper;

function test(): TestIDatabase {
    return new TestIDatabase();
}
$res = new TestIResultWrapper();
test()->fetchObject( $res );

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

?>