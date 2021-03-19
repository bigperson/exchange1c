<?php
/**
 * This file is part of bigperson/exchange1c package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Mikkimike\Exchange1C\Interfaces;

interface DocumentInterface
{
    /**
     * Список заказов с сайта.
     *
     *
     * @return DocumentInterface[]
     */
    public static function findDocuments1c();

    /**
     * Список предложений в этом заказе.
     *
     * @return OfferInterface[]
     */
    public function getOffers1c();

    /**
     * Получить список реквизитов в заказе.
     *
     * @return mixed
     */
    public function getRequisites1c();

    /**
     * Получаем контрагента у документа.
     *
     * @return PartnerInterface
     */
    public function getPartner1c();
}
