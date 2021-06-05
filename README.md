# wikimedia-rector

Running to update a MediaWiki clone in place:

```shell
composer install
export WORKING_DIR=<absolute or relative path to your MW clone>
vendor/bin/rector process --working-dir=${WORKING_DIR} --autoload-file=vendor/autoload.php includes/
```