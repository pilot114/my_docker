<?php

//phpinfo();
//phpcredits();

/**
 * Получает список функций / констант / модулей
 */
function getRuntimeInfo(): array
{
    $functions = get_defined_functions();
    $constants = get_defined_constants();
    $modules = get_loaded_extensions();

    return [
        $functions['internal'] ?? $functions,
        array_keys($constants['internal'] ?? $constants),
        $modules
    ];
}

/**
 * Получает список пользовательских функций / констант / переменных
 */
function getUserLibInfo(): array
{
    $functions = get_defined_functions();
    $constants = get_defined_constants();

    return [
        $functions['user'] ?? [],
        $constants['user'] ?? [],
        get_defined_vars() // ассоциативный массив с объявленными в текущей области видимости переменными
    ];
}

/**
 *  Получает список встроенных функций с группировкой по модулям
 */
function getFunctionInfoGroupByModules($modules)
{
    $grouped = [];
    foreach ($modules as $i => $module) {
        $grouped[$module] = get_extension_funcs($module);
    }
    return $grouped;
}

function printGroupByModules($grouped)
{
    $i = 0;
    foreach ($grouped as $moduleName => $group) {
        $i++;
        $countFns = $group ? count($group) : 0;
        echo sprintf("%s) functions in %s: %s\n", $i, $moduleName, $countFns);
    }
}


/**
 * Печатает список встроенных глобальных функций / констант
 */
function printLibInfo(array $functions, array $constants, array $modules): void
{
    echo sprintf("Total internal functions: %s\n", count($functions));
    echo sprintf("Total internal constants: %s\n", count($constants));
    echo sprintf("Total modules: %s\n", count($modules));
}

/**
 * Печатает массив с отступами
 */
function printDump(array $arr, $padding = ''): void
{
    ksort($arr);
    foreach ($arr as $key => $val) {
        if (is_array($val)) {
            echo sprintf("%s%s\n", $padding, $key);
            $shift = $padding . str_pad('', mb_strlen($key));
            printDump($val, $shift);
        } else {
            echo sprintf("%s%s : %s\n", $padding, $key, gettype($val));
        }
    }
}

/**
 * Простая реализация префиксного дерева
 * При передаче $delimiter, префиксы разбиваются по указанной строке, иначе - посимвольно
 */
function prefixTree(array $strings, string $delimiter = null): array
{
    $tree = [];
    foreach ($strings as $string) {
        $parts = $delimiter ? explode($delimiter, $string) : str_split($string);
        $parts = array_reverse($parts);
        $node = &$tree;
        while ($parts) {
            $part = array_pop($parts);
            $node = &$node[$part];
        }
    }
    return $tree;
}

/**
 * Сворачивает префиксное дерево, где это возможно
 */
function foldPrefixTree(array $tree)
{
    foreach ($tree as $key => $item) {
        if (is_array($item)) {
            $next = foldPrefixTree($item);
            if (count($next) === 1) {
                $nextKey = array_key_first($next);
                $nexVal = $next[$nextKey];
                $tree[$key . $nextKey] = $nexVal;
                unset($tree[$key]);
            } else {
                $tree[$key] = $next;
            }
        } else {
            $tree[$key] = $item;
        }
    }
    return $tree;
}