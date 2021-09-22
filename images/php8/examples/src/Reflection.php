<?php

namespace Pilot114\Php8;

use Pilot114\Php8\Types\Config;
use Pilot114\Php8\Types\Extension;
use Pilot114\Php8\Types\Func;
use Pilot114\Php8\Types\Param;
use Typing\Collection;
use Typing\Type;

class Reflection
{
    static public function metaInfoByNames(array $names): Config
    {
        $config = new Config();

        foreach ($names as $name) {
            $meta = self::getFunctionMeta($name);
            if (!isset($config->extensions[$meta->extension])) {
                $config->extensions[$meta->extension] = new Extension();
            }
            $funcName = $meta->name;
            $extName = $meta->extension;
            unset($meta->name);
            unset($meta->extension);
            $config->extensions[$extName]->funcs[$funcName] = $meta;
        }

        return $config;
    }

    static protected function getFunctionMeta(string $name): Func
    {
        $ref = new \ReflectionFunction($name);
        $meta = new Func();

        $meta->name = $ref->getName();
        $meta->extension = $ref->getExtensionName() ?? 'standard';

        $type = $ref->getReturnType();
        if ($type instanceof \ReflectionUnionType) {
            $col = new Collection($type->getTypes());
            $meta->return->types = $col
                ->map(fn($x) => $x->getName())
                ->filter(fn($x) => $x != 'null')
                ->toArray();
        } else {
            $meta->return->types = [ $ref->getReturnType()->getName() ];
        }

        $meta->return->isNull = $ref->getReturnType()->allowsNull();

        foreach ($ref->getParameters() as $parameter) {
            $key = $parameter->getName();

            $meta->params[$key] = new Param();

            if ($parameter->isVariadic()) {
                $meta->params[$key]->isVariadic = true;
                continue;
            }
            if ($parameter->isPassedByReference()) {
                $meta->params[$key]->byReference = true;
                continue;
            }

            $type = $parameter->getType();
            if (!$type) {
                continue;
            }

            if ($type instanceof \ReflectionUnionType) {
                $col = new Collection($type->getTypes());
                $types = $col
                    ->map(fn($x) => $x->getName())
                    ->filter(fn($x) => $x != 'null')
                    ->toArray();
                $meta->params[$key]->isNull = false;
            } else {
                $types = [ $type->getName() ];
                $meta->params[$key]->isNull = $type->allowsNull();
            }
            $meta->params[$key]->types = $types;
        }
        return $meta;
    }
}