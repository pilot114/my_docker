<?php

/**
 * Проверка на простоту
 */
function isPrime(int $x): bool
{
    for ($i = 2; $i <= sqrt($x); $i++) {
        if ($x % $i == 0) {
            return false;
        }
    }
    return true;
}

/**
 * разложение на множители
 */
function factorize(int $x): array
{
    $factors = [];

    for ($i = 2; $i <= sqrt($x); $i++) {
        while ($x % $i == 0) {
            $factors[] = $i;
            $x /= $i;
        }
    }

    if ($x != 1) {
        $factors[] = $x;
    }

    return $factors;
}

/**
 * Поиск всех делителей
 */
function findDividers(int $x): array
{
    $dividers = [];

    for ($i = 1; $i <= sqrt($x); $i++) {
        if ($x % $i == 0) {
            $dividers[] = $i;
            if ($i * $i != $x) {
                $dividers[] = $x / $i;
            }
        }
    }

    return $dividers;
}


/**
 * Решето Эратосфена
 */
function eratosfen(int $n): array
{
    $sieve = [];
    $primes = [];

    for ($i = 2; $i <= $n; $i++) {
        $sieve[$i] = true;
    }

    for ($i = 2; $i <= $n; $i++) {
        if ($sieve[$i]) {
            $primes[] = $i;
            for ($j = $i * $i; $j <= $n; $j += $i) {
                $sieve[$j] = false;
            }
        }
    }
    return $primes;
}