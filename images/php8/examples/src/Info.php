<?php

namespace Pilot114\Php8;

use Pilot114\Php8\Types\Config;
use Pilot114\Php8\Types\Extension;
use Typing\Type;

class Info
{
    public function default()
    {
        phpinfo();
        phpcredits();
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

    public function printTree(string $moduleName = null, array $delimiters = [])
    {
        $moduleList = $moduleName ? [$moduleName] : $this->getInternalInfo()[2];
        $tree = new PrefixTree();

        $functions = $this->getFunctionInfoGroupByModules($moduleList);
        $functionsFlat = [];
        foreach ($functions as $fs) {
            $functionsFlat = array_merge($functionsFlat, is_array($fs) ? $fs : []);
        }
        $tree->setData($functionsFlat, $delimiters);
        $result = $tree->foldByPrefixes();
        $this->printDump($result);
    }

    public function metaInfoByNames(array $names): Config
    {
        $config = new Config();
        foreach ($names as $name) {
            $meta = (new Reflection())->getFunctionMeta($name);
            if (!isset($config->extensions[$meta->extension])) {
                $config->extensions[$meta->extension] = new Extension();
                $config->extensions[$meta->extension]->name = $meta->name;
            }
            $config->extensions[$meta->extension]->funcs[] = $meta;
        }

        return $config;
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