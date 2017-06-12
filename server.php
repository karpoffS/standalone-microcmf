#!/usr/bin/env php
<?php

require_once __DIR__."/vendor/autoload.php";

$runner = new WebServerRunner\WebServerRunner('127.0.0.1', '8080', './web/');
$runner->setVerbose(true);
$runner->execute();
$runner->stopOnShutdown();
while (true){
    sleep(1);
}
