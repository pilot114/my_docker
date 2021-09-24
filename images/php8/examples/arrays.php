<?php

include "./vendor/autoload.php";

$all = \Pilot114\Php8\Arrays::data();

/**
 * Базовые операции с массивами
 */

//foreach ($all as $name => $arr) {
//    \Pilot114\Php8\Arrays::state("array_values($name)", array_values($arr));
//}
//foreach ($all as $name => $arr) {
//    // 2 параметром можно указать значение для поиска (+ strict для этого поиска)
//    \Pilot114\Php8\Arrays::state("array_keys($name)", array_keys($arr));
//}
//foreach ($all as $name => $arr) {
//    // добавление в конец
//    array_push($arr, 1, 2);
//    \Pilot114\Php8\Arrays::state("array_push($name, 1, 2)", $arr);
//}
//foreach ($all as $name => $arr) {
//    // извлечь последний
//    array_pop($arr);
//    \Pilot114\Php8\Arrays::state("array_pop($name)", $arr);
//}
//foreach ($all as $name => $arr) {
//    // извлечь первый
//    array_shift($arr);
//    \Pilot114\Php8\Arrays::state("array_shift($name)", $arr);
//}
//foreach ($all as $name => $arr) {
//    // вставить в начало
//    array_unshift($arr, 1, 2);
//    \Pilot114\Php8\Arrays::state("array_unshift($name)", $arr);
//}
foreach ($all as $name => $arr) {
    // вырезать (и вставить) по смещению
    array_splice($arr, 1, 1, [1, 2]);
    \Pilot114\Php8\Arrays::state("array_splice($name, 1, 1, [1, 2])", $arr);
}