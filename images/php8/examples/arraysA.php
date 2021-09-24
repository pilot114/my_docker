<?php

include "./vendor/autoload.php";

$all = \Pilot114\Php8\Arrays::data();

foreach ($all as $name => $arr) {
    // 2 параметром можно указать значение для поиска (+ strict для этого поиска)
    \Pilot114\Php8\Arrays::state($name, $arr);
}
