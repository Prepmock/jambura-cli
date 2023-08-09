<?php

include __DIR__.'/../src/Cli.php';

\Jambura\Cli::init()
    ->setCommandsPath('commands/')
    ->run();

