#!/usr/bin/php
<?php

use League\CLImate\CLImate;

require_once __DIR__ . '/autoload_require_composer.php';

$climate = new CLImate();

$climate->red('Whoa now this text is red.');
$climate->blue('Blue? Wow!');