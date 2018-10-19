<?php
/**
 * This file is part of bigperson/exchange1c package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Bigperson\Exchange1C\Events;

use Bigperson\Exchange1C\Interfaces\OfferInterface;
use Symfony\Component\EventDispatcher\Event;
use Zenwalker\CommerceML\Model\Offer;

class AfterUpdateOffer extends Event
{
    const NAME = 'after.update.offer';

    /**
     * @var OfferInterface
     */
    public $model;

    /**
     * @var Offer
     */
    public $offer;

    /**
     * AfterUpdateOffer constructor.
     *
     * @param OfferInterface $model
     * @param Offer          $offer
     */
    public function __construct(OfferInterface $model, Offer $offer)
    {
        $this->model = $model;
        $this->offer = $offer;
    }
}
