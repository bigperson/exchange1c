<?php


namespace Mikkimike\Exchange1C\PayloadTypes;


use Zenwalker\CommerceML\Model\Offer;
use Zenwalker\CommerceML\Model\Product;
use Zenwalker\CommerceML\Model\Property;

class User1c implements PayloadTypeInterface
{
    public $user;
    public $type;
    public $addresses;

    public function __construct($user, $type)
    {
        $this->user = $user;
        $addresses = [];

        foreach ($user->Адреса->АдресДоставки as $address) {
            $addresses[] = $address;
        }

        $addresses[] = $user->Адреса->ФактическийАдрес;
        $addresses[] = $user->Адреса->ЮридическийАдрес;
        $addresses[] = $user->Адреса->ПочтовыйАдрес;

        foreach ($addresses as &$address) {
            $address->addChild('ID', (string) $address->Представление->attributes()['Тип']);

        }
        
        $this->addresses = $addresses;
        $this->type = $type;
    }

}
