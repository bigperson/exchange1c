<?php


namespace Mikkimike\Exchange1C\PayloadTypes;


use Zenwalker\CommerceML\CommerceML;
use Zenwalker\CommerceML\Model\Product;

class Product1c implements PayloadTypeInterface
{
    public $product;
    public $group;

    public function __construct(Product $product, CommerceML $commerce)
    {
        $this->product = $product->xml;
        $group = $commerce->classifier->getGroupById(
            (string) $product->xml->Группы->Ид
        );
        $this->group = new \stdClass();
        $this->group->id = $group->id;
        $this->group->name = $group->name;
    }

}