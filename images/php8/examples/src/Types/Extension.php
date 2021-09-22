<?php

namespace Pilot114\Php8\Types;

use Typing\Collection;
use Typing\Type;

class Extension extends Type
{
    public string $name;
    /**
     * @var Func[]
     */
    public Collection $funcs;

    public function __construct($array = [])
    {
        $this->funcs = new Collection();
        parent::__construct($array);
    }
}