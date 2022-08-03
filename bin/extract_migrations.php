#!/usr/bin/php
<?php

require_once __DIR__ . '/autoload_require_composer.php';

\Atelier\Logger::info('Update migrations command started');
\Atelier\Commands::extractMigrations();