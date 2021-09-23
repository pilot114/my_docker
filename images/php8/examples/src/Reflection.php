<?php

namespace Pilot114\Php8;

use Pilot114\Php8\Types\Config;
use Pilot114\Php8\Types\Extension;
use Pilot114\Php8\Types\Func;
use Pilot114\Php8\Types\Param;
use Typing\Collection;

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
        if ($type) {
            list($types, $isNull) = self::extractType($type);
        } else {
            list($types, $isNull) = self::hardcodeReturnTypes($name);
        }
        $meta->return->types = $types;
        $meta->return->isNull = $isNull;

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

            list($types, $isNull) = self::extractType($type);
            $meta->params[$key]->types = $types;
            $meta->params[$key]->isNull = $isNull;
        }
        return $meta;
    }

    static protected function extractType($type): array
    {
        if ($type instanceof \ReflectionUnionType) {
            $col = new Collection($type->getTypes());
            $types = $col
                ->map(fn($x) => $x->getName())
                ->map(fn($x) => $x === 'false' ? 'bool' : $x)
                ->filter(fn($x) => $x != 'null')
                ->toArray();
            $isNull = in_array('null', $type->getTypes());
        } else {
            $types = [ $type->getName() ];
            $isNull = $type->allowsNull();
        }
        return [$types, $isNull];
    }

    static protected function hardcodeReturnTypes($name): array
    {
        $handlers = ['set_error_handler', 'set_exception_handler'];
        $resources = [
            'gzopen', 'finfo_open', 'ftp_connect', 'ftp_ssl_connect',
            'opendir', 'popen', 'fopen', 'tmpfile', 'fsockopen', 'pfsockopen',
            'proc_open', 'stream_context_create', 'stream_context_get_default',
            'stream_context_set_default', 'stream_filter_prepend', 'stream_filter_append',
            'stream_socket_client', 'stream_socket_server', 'stream_socket_accept',
            'zip_read', 'zip_open'
        ];

        if (in_array($name, $handlers)) {
            $types = ['string', 'array', 'object'];
            $isNull = true;
        } else if (in_array($name, $resources)) {
            $types = ['resource', 'bool'];
            $isNull = false;
        } else if ($name === 'parse_url') {
            $types = ['array', 'string', 'int'];
            $isNull = true;
        } else {
            dump($name);
            die();
        }
        return [$types, $isNull];
    }
}