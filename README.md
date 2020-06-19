# Array export

Export an array as a string that can be later saved as a file with proper formatting. 

As a replacement for `var_export`, supports defining custom indentation size and character.

Usage:
```php
<?php

use Djeux\ArrayExport\Export;

$array = [
    'one' => 'String',
    'two' => 'String',
];

$asString = Export::make()->export($array);
/**
[
    'one' => 'String',
    'two' => 'String',
]
*/

$asFile = Export::make()->asFile($array);
/**
<?php
return [
    'one' => 'String',
    'two' => 'String',
];
*/
```