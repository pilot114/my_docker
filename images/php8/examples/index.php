<?php

require __DIR__ . '/info.php';
require __DIR__ . '/openssl.php';

[$functions, $constants, $modules] = getRuntimeInfo();
printLibInfo($functions, $constants, $modules);

$groups = getFunctionInfoGroupByModules($modules);
printGroupByModules($groups);

//sort($groups['openssl']);
//var_dump($groups['openssl']);

