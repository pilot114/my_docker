<?php

namespace Pilot114\Php8;

class Arrays
{
    /**
     * вывести состояние массива
     */
    static function state($name, $arr)
    {
        $green = '0;32';
        $yellow = '1;33';

        $isMatrix = array_reduce(array_map('is_array', $arr), fn($a, $b) => $a && $b, true);
        $isPartialMatrix = array_reduce(array_map('is_array', $arr), fn($a, $b) => $a || $b, false);
        $nonConsist = !$isMatrix && $isPartialMatrix;
        if ($nonConsist) {
            $arr = self::nonConsist($arr);
            $isMatrix = true;
        }

        echo sprintf("\033[{$green}m### %s ###\033[0m\n", $name);

        if ($arr && $isMatrix) {
            self::matrix($arr);
            return;
        }

        foreach ($arr as $key => $value) {
            echo "\033[{$yellow}m$key\033[0m" . "\t";
        }
        echo "\n";
        foreach ($arr as $key => $value) {
            if (is_scalar($value)) {
                echo $value . "\t";
            } else {
                echo json_encode($value) . "\t";
            }
        }
        echo "\n";
    }

    static function matrix($arr)
    {
        $yellow = '1;33';

        $keys = array_keys($arr[0]);
        array_unshift($keys, '#');
        foreach ($keys as $key) {
            echo "\033[{$yellow}m$key\033[0m" . "\t";
        }
        echo "\n";

        foreach ($arr as $y => $row) {
            echo "\033[{$yellow}m$y\033[0m" . "\t";
            foreach ($row as $cell) {
                echo $cell . "\t";
            }
            echo "\n";
        }
    }

    static function nonConsist($arr): array
    {
        $red = '0;31';
        $hiddenField = "\033[{$red}mSCALAR\033[0m";
        // "псевдо" матрица
        $keys = [$hiddenField];
        foreach ($arr as $item) {
            if (is_array($item)) {
                array_push($keys, ...array_keys($item));
            }
        }
        $p = array_flip(array_unique($keys));
        $p = array_map(fn($x) => null, $p);

        $mapped = [];
        foreach ($arr as $key => $item) {
            $el = $p;
            if (is_array($item)) {
                $mapped[$key][$hiddenField] = null;
                foreach ($item as $i => $cell) {
                    $mapped[$key][$i] = $cell;
                }
            } else {
                $el[$hiddenField] = $item;
                $mapped[$key] = $el;
            }
        }

        return $mapped;
    }

    /**
     * провайдер тестовых данных
     */
    static function data(): array
    {
        // автоиндекс
        $numbers = [-10, 0, 12, 44, 100, 42, 13.7, INF, -INF];
        $strings = ['Alpha', "Beta", 'Gamma', 'alpha', "beta", 'gamma', 'DELTA'];
        $scalar = [null, 7, 'string', 666.666, true, false, INF, 'STRING'];
        // разреженный
        $sparse = [
            15 => 'a',
            10 => 'b',
            5  => 'c',
            0  => 'd',
            -5 => 'e'
        ];
        // ассоциативный
        $colours = [
            'red'    => '#C0392B',
            'yellow' => '#F4D03F',
            'green'  => '#27AE60',
            'blue'   => '#85C1E9',
            'white'  => '#FDFEFE',
            'black'  => '#17202A',
        ];
        // матрица
        $matrix = [
            [1, 2, 3, 4],
            [5, 6, 7, 8],
            [9, 10, 11, 12],
        ];
        // записи
        $records = [
            ['name' => 'Roman',  'age' => 25, 'position' => 'Engineer'],
            ['name' => 'Artem',  'age' => 42, 'position' => 'Manager'],
            ['name' => 'Boris',  'age' => 31, 'position' => 'Support'],
            ['name' => 'Viktor', 'age' => 31, 'position' => 'Engineer'],
            ['name' => 'Petr',   'age' => 29, 'position' => 'Manager'],
            ['name' => 'Ivan',   'age' => 45, 'position' => 'Boss'],
        ];

        return [
            '$numbers' => $numbers,
            '$strings' => $strings,
            '$scalar'  => $scalar,
            '$sparse'  => $sparse,
            '$colours' => $colours,
            '$matrix'  => $matrix,
            '$records' => $records
        ];
    }
}