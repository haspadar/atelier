#!/usr/bin/php
<?php

require_once __DIR__ . '/autoload_require_composer.php';
use phpseclib3\Crypt\PublicKeyLoader;

\Atelier\Logger::info('Update commits command started');

\Atelier\Commands::extractCommits('km', PublicKeyLoader::load(file_get_contents('/Users/haspadar/.ssh/id_rsa_km')));