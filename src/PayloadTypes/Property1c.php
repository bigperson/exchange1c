<?php


namespace Mikkimike\Exchange1C\PayloadTypes;


use Zenwalker\CommerceML\Model\Product;
use Zenwalker\CommerceML\Model\Property;

class Property1c implements PayloadTypeInterface
{
    public $property;

    public function __construct(Property $property)
    {
        $this->property = $property->xml;
    }

}
