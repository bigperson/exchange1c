<?php


namespace Mikkimike\Exchange1C\PayloadTypes;


use Zenwalker\CommerceML\Model\Offer;
use Zenwalker\CommerceML\Model\Product;
use Zenwalker\CommerceML\Model\Property;

class User1c implements PayloadTypeInterface
{
    public $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

}
