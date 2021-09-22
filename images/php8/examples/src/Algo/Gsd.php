<?php

/**
 * Наибольший общий делитель (алгоритм Эвклида)
 * Если gcd(a,b)=1, то числа взаимнопростые
 */
function gcd(int $a, int $b): int
{
    if ($a < $b) {
        [$a, $b] = [$b, $a];
    }

    while ($b) {
        $a %= $b;
        [$a, $b] = [$b, $a];
    }

    return $a;
}

/**
 * Наименьшее общее кратное
 */
function lcm(int $a, int $b): int
{
    return $a / gcd($a, $b) * $b;
}

// Обобщение:
// gcd(a,b,c,d)=gcd(gcd(gcd(a,b),c),d)
// lcm(a,b,c,d)=lcm(lcm(lcm(a,b),c),d)