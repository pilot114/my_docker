<?php

include './vendor/autoload.php';

$i = new \Pilot114\Php8\Info();

echo "###\n";
$i->printInternalInfo();
echo "###\n";

$i->printGroupByModules();
echo "###\n";

$i->printTree('standard', ['_']);
echo "###\n";
