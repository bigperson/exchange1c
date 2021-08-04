<?php


namespace Mikkimike\Exchange1C\PayloadTypes;

/**
 * Class ConsoleProgressStart
 * @package Mikkimike\Exchange1C\PayloadTypes
 */
class ConsoleProgressStart implements PayloadTypeInterface
{
    /**
     * @var int $count
     */
    public $count;

    /**
     * ProductCount constructor.
     * @param $products
     */
    public function __construct($products)
    {
        return $this->count = count($products);
    }
}
