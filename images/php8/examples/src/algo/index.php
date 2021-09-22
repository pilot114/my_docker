<?php

include './Factor.php';

foreach (range(1, 10000) as $number) {
    if (isPrime($number)) {
        echo sprintf("%s: prime!", $number);
    } else {
        $f = factorize($number);
        $d = findDividers($number);
        sort($f);
        sort($d);
        echo sprintf("%s: %s", $number, json_encode([$f, $d]));
    }
    echo "\n";
}

//$primes = eratosfen(1000000);
//var_dump(count($primes));
