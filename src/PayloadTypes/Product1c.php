<?php


namespace Mikkimike\Exchange1C\PayloadTypes;


use Zenwalker\CommerceML\Model\Product;

class Product1c implements PayloadTypeInterface
{
    public $product;

    public function __construct(Product $product)
    {
        $this->product = $product->xml;
    }

}
