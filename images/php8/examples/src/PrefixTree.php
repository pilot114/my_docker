<?php

namespace Pilot114\Php8;

/**
 * Простая реализация префиксного дерева
 */
class PrefixTree
{
    protected $data;

    /**
     *
     * При передаче $delimiters, префиксы разбиваются по указанной строке, иначе - посимвольно
     */
    public function setData(array $strings, array $delimiters = []): void
    {
        $tree = [];
        foreach ($strings as $string) {
            if ($delimiters) {
                $parts = $this->explodeByDelimiters($string, $delimiters);
            } else {
                $parts = str_split($string);
            }
            $parts = array_reverse($parts);
            $node = &$tree;
            while ($parts) {
                $part = array_pop($parts);
                $node = &$node[$part];
            }
        }
        $this->data = $tree;
    }

    /**
     * Сворачивает префиксное дерево, где это возможно
     */
    public function foldByPrefixes(array $tree = null): array
    {
        $tree = $tree ?? $this->data;

        foreach ($tree as $key => $item) {
            if (is_array($item)) {
                $next = $this->foldByPrefixes($item);
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

    protected function explodeByDelimiters(string $string, array $delimiters)
    {
        // TODO: поддержка массива разделителей
        $d = $delimiters[0];
        $parts = explode($d, $string);
        if (count($parts) > 1) {
            $realParts = [];
            foreach ($parts as $i => $part) {
                $realParts[] = $part;
                if ($i < count($parts) - 1) {
                    $realParts[] = $d;
                }
            }
            $parts = $realParts;
        }
        return $parts;
    }
}