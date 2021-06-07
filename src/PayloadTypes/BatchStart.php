<?php


namespace Mikkimike\Exchange1C\PayloadTypes;


class BatchStart implements PayloadTypeInterface
{
    public $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }
}
