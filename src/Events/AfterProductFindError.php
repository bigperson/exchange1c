<?php


namespace Mikkimike\Exchange1C\Events;


use Mikkimike\Exchange1C\Interfaces\OfferInterface;
use Zenwalker\CommerceML\Model\Offer;

class AfterProductFindError extends AbstractEventInterface
{
    const NAME = 'after.product.find.error';

    public $id;
    public $offer;

    /**
     * AfterProductFindError constructor.
     * @param string $id ProductId 1c
     * @param Offer $offer 1c Offer model
     */
    public function __construct(string $id, Offer $offer)
    {
        $this->id = $id;
        $this->offer = $offer;
    }
}
