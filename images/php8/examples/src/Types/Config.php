<?php

namespace Pilot114\Php8\Types;

use Typing\Collection;
use Typing\Type;

class Config extends Type
{
    /**
     * @var Extension[]
     */
    public Collection $extensions;

    public function __construct($array = [])
    {
        $this->extensions = new Collection();
        parent::__construct($array);
    }
}