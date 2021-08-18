<?php

use Wikimedia\Rector\Tests\Rectors\MediaWiki1_37\MoveIDatabaseMethodsToResultWrapper\Classes\TestIDatabase;
use Wikimedia\Rector\Tests\Rectors\MediaWiki1_37\MoveIDatabaseMethodsToResultWrapper\Classes\TestIResultWrapper;

$db = new TestIDatabase();
$res = new TestIResultWrapper();
$db->fetchObject( $res );

?>
-----
<?php

use Wikimedia\Rector\Tests\Rectors\MediaWiki1_37\MoveIDatabaseMethodsToResultWrapper\Classes\TestIDatabase;
use Wikimedia\Rector\Tests\Rectors\MediaWiki1_37\MoveIDatabaseMethodsToResultWrapper\Classes\TestIResultWrapper;

$db = new TestIDatabase();
$res = new TestIResultWrapper();
$res->fetchObject();

?>