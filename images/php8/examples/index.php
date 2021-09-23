<?php

include './vendor/autoload.php';

$i = new \Pilot114\Php8\Info();

echo "###\n";
$i->printInternalInfo();
echo "###\n";

$i->printGroupByModules();
echo "###\n";

//$i->printTree(['standard'], ['_']);
//echo "###\n";

$fns = $i->getAllFunctions();
$fns = $fns
//    ->filter(fn($x) => str_contains($x, 'array_'))
    ->toArray();
$meta = \Pilot114\Php8\Reflection::metaInfoByNames($fns);

$yaml = \Symfony\Component\Yaml\Yaml::dump($meta->toArray(), 6);
file_put_contents('./config/meta.yaml', $yaml);
