<?php
/**
 * This file is part of bigperson/exchange1c package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Mikkimike\Exchange1C\Interfaces;

use App\Services\CategoryAssociate\CategoryAssociateInterface;
use Zenwalker\CommerceML\Model\Product;
use Zenwalker\CommerceML\Model\PropertyCollection;
use Illuminate\Http\Request;

/**
 * Interface ProductInterface.
 */
interface ProductInterface extends IdentifierInterface
{
    /**
     * Получение уникального идентификатора продукта в рамках БД сайта.
     *
     * @return int|string
     */
    public function getPrimaryKey();

    public function createModel(Product $product): self;

    public function createProperties1c(PropertyCollection $properties);

    public function createCategories1c($categories);

}
