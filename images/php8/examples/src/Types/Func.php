<?php

namespace Pilot114\Php8\Types;

use Typing\Collection;
use Typing\Type;

class Func extends Type
{
    public string $name;
    public string $extension;
    /**
     * @var Param[]
     */
    public Collection $params;
    public Ret $return;

    public function __construct($array = [])
    {
        $this->return = new Ret();
        $this->params = new Collection();
        parent::__construct($array);
    }
}