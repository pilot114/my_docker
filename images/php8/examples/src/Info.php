<?php

namespace Pilot114\Php8;

use Typing\Collection;

class Info
{
    public function default($ext = null)
    {
        phpinfo();
        phpcredits();
        echo zend_version();
        echo phpversion($ext);
    }

    public function getAllFunctions(array $modules = null): Collection
    {
        $grouped = $this->getFunctionInfoGroupByModules($modules ?? $this->getInternalInfo()[2]);
        $functionsFlat = [];
        foreach ($grouped as $group) {
            $functionsFlat = array_merge($functionsFlat, is_array($group) ? $group : []);
        }
        return new Collection($functionsFlat);
    }

    /**
     * Печатает список встроенных глобальных функций / констант
     */
    public function printInternalInfo(): void
    {
        [$functions, $constants, $modules] = $this->getInternalInfo();

        echo sprintf("Total internal functions: %s\n", count($functions));
        echo sprintf("Total internal constants: %s\n", count($constants));
        echo sprintf("Total modules: %s\n", count($modules));
    }

    public function printGroupByModules(array $modules = null)
    {
        $grouped = $this->getFunctionInfoGroupByModules($modules ?? $this->getInternalInfo()[2]);

        $i = 0;
        foreach ($grouped as $moduleName => $group) {
            $i++;
            $countFns = $group ? count($group) : 0;
            echo sprintf("%s) functions in %s: %s\n", $i, $moduleName, $countFns);
        }
    }

    public function printTree(array $modules = null, array $delimiters = [])
    {
        $functionsFlat = $this->getAllFunctions($modules);

        $tree = new PrefixTree();
        $tree->setData($functionsFlat->toArray(), $delimiters);
        $result = $tree->foldByPrefixes();
        $this->printDump($result);
    }

    /**
     * Печатает массив с отступами
     */
    protected function printDump(array $arr, string $padding = ''): void
    {
        ksort($arr);
        foreach ($arr as $key => $val) {
            if (is_array($val)) {
                echo sprintf("%s%s\n", $padding, $key);
                $shift = $padding . str_pad('', mb_strlen($key));
                $this->printDump($val, $shift);
            } else {
                echo sprintf("%s%s : %s\n", $padding, $key, gettype($val));
            }
        }
    }

    /**
     *  Получает список встроенных функций с группировкой по модулям
     */
    protected function getFunctionInfoGroupByModules(array $modules)
    {
        $grouped = [];
        foreach ($modules as $module) {
            $grouped[$module] = get_extension_funcs($module);
        }
        return $grouped;
    }

    /**
     * Получает список функций / констант / модулей
     */
    protected function getInternalInfo(): array
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
    protected function getUserLibInfo(): array
    {
        $functions = get_defined_functions();
        $constants = get_defined_constants();

        return [
            $functions['user'] ?? [],
            $constants['user'] ?? [],
            get_defined_vars() // ассоциативный массив с объявленными в текущей области видимости переменными
        ];
    }
}