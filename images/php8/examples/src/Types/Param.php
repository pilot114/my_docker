<?php

namespace Pilot114\Php8\Types;

use Typing\Type;

class Param extends Type
{
    public string $name;
    public bool $isNull;
    public array $types;

    public function __construct($array = [])
    {
        parent::__construct($array);
    }
}