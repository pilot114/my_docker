<?php

namespace Pilot114\Php8;

use Pilot114\Php8\Types\Func;
use Pilot114\Php8\Types\Param;

class Reflection
{
    public function getFunctionMeta(string $name): Func
    {
        $ref = new \ReflectionFunction($name);

        $meta = new Func();

        $meta->name = $ref->getName();
        $meta->extension = $ref->getExtensionName() ?? 'standard';
        $meta->return->type = $ref->getReturnType()->getName();
        $meta->return->isNull = $ref->getReturnType()->allowsNull();

        foreach ($ref->getParameters() as $parameter) {
            $i = $parameter->getPosition();
            $p = new Param();
            $p->name = $parameter->getName();
            $p->isNull = $parameter->getType()->allowsNull();
            $meta->params[$i] = $p;

            $type = $parameter->getType();
            if ($type instanceof \ReflectionUnionType) {
                $types = array_map(fn($x) => $x->getName(), $type->getTypes());
            } else {
                $types = [ $parameter->getType()->getName() ];
            }
            $meta->params[$i]->types = $types;
        }
        return $meta;
    }
}