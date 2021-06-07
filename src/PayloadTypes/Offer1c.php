<?php


namespace Mikkimike\Exchange1C\PayloadTypes;


use Zenwalker\CommerceML\Model\Offer;
use Zenwalker\CommerceML\Model\Product;
use Zenwalker\CommerceML\Model\Property;

class Offer1c implements PayloadTypeInterface
{
    public $offer;

    public function __construct(Offer $offer)
    {
        $this->offer = $offer->xml;
    }

}
