<?php
/**
 * This file is part of bigperson/exchange1c package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Bigperson\Exchange1C\Interfaces;


interface OfferInterface extends ExportFieldsInterface, IdentifierInterface
{
    /**
     * @return GroupInterface
     */
    public function getGroup1c();

    /**
     * offers.xml > ПакетПредложений > Предложения > Предложение > Цены
     *
     * Цена товара,
     * К $price можно обратиться как к массиву, чтобы получить список цен (Цены > Цена)
     * $price->type - тип цены (offers.xml > ПакетПредложений > ТипыЦен > ТипЦены)
     *
     * @param \Zenwalker\CommerceML\Model\Price $price
     * @return void
     */
    public function setPrice1c($price);

    /**
     * @param $types
     * @return void
     */
    public static function createPriceTypes1c($types);

    /**
     * offers.xml > ПакетПредложений > Предложения > Предложение > ХарактеристикиТовара > ХарактеристикаТовара
     *
     * Характеристики товара
     * $name - Наименование
     * $value - Значение
     *
     * @param \Zenwalker\CommerceML\Model\Simple $specification
     * @return void
     */
    public function setSpecification1c($specification);
}
