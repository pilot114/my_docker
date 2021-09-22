<?php

namespace Pilot114\Php8\Types;

use Typing\Type;

class Param extends Type
{
    public string $name;
    public array $types;
    public bool $isNull;
    // если переменное число параметров
    public bool $isVariadic = false;
    public bool $byReference = false;
}