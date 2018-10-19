<?php
/**
 * This file is part of bigperson/exchange1c package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);


namespace Tests\Models;
use Bigperson\Exchange1C\Interfaces\GroupInterface;
use Bigperson\Exchange1C\Interfaces\OfferInterface;
use Bigperson\Exchange1C\Interfaces\ProductInterface;
use Zenwalker\CommerceML\Model\PropertyCollection;

/**
 * Class ProductTestModel
 */
class ProductTestModel implements ProductInterface
{
    /**
     * Возвращаем имя поля в базе данных, в котором хранится ID из 1с
     *
     * @return string
     */
    public static function getIdFieldName1c()
    {
        // TODO: Implement getIdFieldName1c() method.
    }

    /**
     * Получение уникального идентификатора продукта в рамках БД сайта
     * @return int|string
     */
    public function getPrimaryKey()
    {
        // TODO: Implement getPrimaryKey() method.
    }

    /**
     * Если по каким то причинам файлы import.xml или offers.xml были модифицированы и какие то данные
     * не попадают в парсер, в самом конце вызывается данный метод, в $product и $cml можно получить все
     * возможные данные для ручного парсинга
     *
     * @param \Zenwalker\CommerceML\CommerceML $cml
     * @param \Zenwalker\CommerceML\Model\Product $product
     * @return void
     */
    public function setRaw1cData($cml, $product)
    {
        // TODO: Implement setRaw1cData() method.
    }

    /**
     * Установка реквизитов, (import.xml > Каталог > Товары > Товар > ЗначенияРеквизитов > ЗначениеРеквизита)
     * $name - Наименование
     * $value - Значение
     *
     * @param string $name
     * @param string $value
     * @return void
     */
    public function setRequisite1c($name, $value)
    {
        // TODO: Implement setRequisite1c() method.
    }

    /**
     * Предпологается, что дерево групп у Вас уже создано (\carono\exchange1c\interfaces\GroupInterface::createTree1c)
     *
     * @param \Zenwalker\CommerceML\Model\Group $group
     * @return mixed
     */
    public function setGroup1c($group)
    {
        // TODO: Implement setGroup1c() method.
    }

    /**
     * import.xml > Классификатор > Свойства > Свойство
     * $property - Свойство товара
     *
     * import.xml > Классификатор > Свойства > Свойство > Значение
     * $property->value - Разыменованное значение (string)
     *
     * import.xml > Классификатор > Свойства > Свойство > ВариантыЗначений > Справочник
     * $property->getValueModel() - Данные по значению, Ид значения, и т.д
     *
     * @param \Zenwalker\CommerceML\Model\Property $property
     * @return void
     */
    public function setProperty1c($property)
    {
        // TODO: Implement setProperty1c() method.
    }

    /**
     * @param string $path
     * @param string $caption
     * @return void
     */
    public function addImage1c($path, $caption)
    {
        // TODO: Implement addImage1c() method.
    }

    /**
     * @return GroupInterface
     */
    public function getGroup1c()
    {
        // TODO: Implement getGroup1c() method.
    }

    /**
     * Создание всех свойств продутка
     * import.xml > Классификатор > Свойства
     *
     * $properties[]->availableValues - список доступных значений, для этого свойства
     * import.xml > Классификатор > Свойства > Свойство > ВариантыЗначений > Справочник
     *
     * @param PropertyCollection $properties
     * @return mixed
     */
    public static function createProperties1c($properties)
    {
        // TODO: Implement createProperties1c() method.
    }

    /**
     * @param \Zenwalker\CommerceML\Model\Offer $offer
     * @return OfferInterface
     */
    public function getOffer1c($offer)
    {
        return new OfferTestModel();
    }

    /**
     * @param \Zenwalker\CommerceML\Model\Product $product
     * @return self
     */
    public static function createModel1c($product)
    {
        return new self();
    }

    /**
     * @param string $id
     * @return ProductInterface|null
     */
    public static function findProductBy1c(string $id): ?ProductInterface
    {
        return new self();
    }
}
