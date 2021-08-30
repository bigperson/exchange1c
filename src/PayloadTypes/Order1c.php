<?php


namespace Mikkimike\Exchange1C\PayloadTypes;


use Zenwalker\CommerceML\Model\Offer;
use Zenwalker\CommerceML\Model\Product;
use Zenwalker\CommerceML\Model\Property;

class Order1c implements PayloadTypeInterface
{
    public $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

}
