<?php
/**
 * This file is part of bigperson/exchange1c package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Mikkimike\Exchange1C\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Mikkimike\Exchange1C\Config;
use Mikkimike\Exchange1C\Events\AfterProductsSync;
use Mikkimike\Exchange1C\Events\AfterUpdateProduct;
use Mikkimike\Exchange1C\Events\BeforeProductsSync;
use Mikkimike\Exchange1C\Events\BeforeUpdateProduct;
use Mikkimike\Exchange1C\Events\ImportLog;
use Mikkimike\Exchange1C\Events\ImportProcessDataBridge;
use Mikkimike\Exchange1C\Exceptions\Exchange1CException;
use Mikkimike\Exchange1C\Interfaces\EventDispatcherInterface;
use Mikkimike\Exchange1C\Interfaces\GroupInterface;
use Mikkimike\Exchange1C\Interfaces\ModelBuilderInterface;
use Mikkimike\Exchange1C\Interfaces\OfferInterface;
use Mikkimike\Exchange1C\Interfaces\ProductInterface;
use Mikkimike\Exchange1C\PayloadTypes\BatchStart;
use Mikkimike\Exchange1C\PayloadTypes\ConsoleNextStep;
use Mikkimike\Exchange1C\PayloadTypes\ConsoleProgressFinish;
use Mikkimike\Exchange1C\PayloadTypes\ConsoleProgressStart;
use Mikkimike\Exchange1C\PayloadTypes\PayloadTypeInterface;
use Mikkimike\Exchange1C\PayloadTypes\Product1c;
use Mikkimike\Exchange1C\PayloadTypes\ProductCount;
use Mikkimike\Exchange1C\PayloadTypes\TransactionRollback;
use Symfony\Component\HttpFoundation\Request;
use Zenwalker\CommerceML\CommerceML;
use Zenwalker\CommerceML\Model\Product;

/**
 * Class SectionsService.
 */
class CategoryService
{
    /**
     * @var array Массив идентификаторов товаров которые были добавлены и обновлены
     */
    protected $_ids;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var ModelBuilderInterface
     */
    private $modelBuilder;


    private $continue = false;

    /**
     * CategoryService constructor.
     *
     * @param Request $request
     * @param Config $config
     * @param EventDispatcherInterface $dispatcher
     * @param ModelBuilderInterface $modelBuilder
     */
    public function __construct(Request $request, Config $config, EventDispatcherInterface $dispatcher, ModelBuilderInterface $modelBuilder)
    {
        $this->request = $request;
        $this->config = $config;
        $this->dispatcher = $dispatcher;
        $this->modelBuilder = $modelBuilder;
    }

    /**
     * Базовый метод запуска импорта.
     *
     * @throws Exchange1CException
     */
    public function import(): void
    {
        $filename = basename($this->request->get('filename'));
        $commerce = new CommerceML();
        $category = false;
        if ($this->request->has('category')) $category = $this->request->get('category');
        $commerce->loadImportXml($this->config->getFullPath($filename, $category));
        $classifierFile = $this->config->getFullPath('classifier.xml', $category);
        if ($commerce->classifier->xml) {
            $commerce->classifier->xml->saveXML($classifierFile);
        } else {
            $commerce->classifier->xml = simplexml_load_string(file_get_contents($classifierFile));
        }

        $this->beforeProductsSync();
        $productClass = $this->getProductClass();
        $productClass->createProperties1c($commerce->classifier->getProperties());
        $productClass->createCategories1c($commerce->classifier->xml->Категории);
        $products = $commerce->catalog->getProducts();
        $this->ImportProcessDataBridge(new ConsoleProgressStart($products));
        foreach ($products as $product) {
            $this->ImportProcessDataBridge(new Product1c($product, $commerce));
            $this->ImportProcessDataBridge(new ConsoleNextStep());
        }
        $this->ImportProcessDataBridge(new ConsoleProgressFinish());
        $this->ImportProcessDataBridge(new BatchStart("PRODUCT IMPORT"));

    }


    /**
     * @return GroupInterface|null
     */
    protected function getGroupClass(): ?GroupInterface
    {
        return $this->modelBuilder->getInterfaceClass($this->config, GroupInterface::class);
    }

    /**
     * @return ProductInterface|null
     */
    protected function getProductClass(): ?ProductInterface
    {
        return $this->modelBuilder->getInterfaceClass($this->config, ProductInterface::class);
    }

    /**
     * @param Product $product
     */
    protected function parseGroupsAndProperties($model, Product $product): void
    {
        //$group = $product->getGroup();
        // $model->setGroup1cAndProperties($group, $product->getProperties());
    }

    /**
     * @param ProductInterface $model
     * @param Product $product
     */
    protected function parseProperties($model, $product): void
    {
        foreach ($product->getProperties() as $property) {
            $model->setProperty1c($property, $model);
        }
    }

    /**
     * @param ProductInterface $model
     * @param Product $product
     */
    protected function parseRequisites(ProductInterface $model, Product $product): void
    {
        $requisites = $product->getRequisites();
        foreach ($requisites as $requisite) {
            $model->setRequisite1c($requisite->name, $requisite->value);
        }
    }

    /**
     * @param ProductInterface $model
     * @param Product $product
     */
    protected function parseImage(ProductInterface $model, Product $product)
    {
        $images = $product->getImages();
        foreach ($images as $image) {
            $path = $this->config->getFullPath(basename($image->path));
            if (file_exists($path)) {
                $model->addImage1c($path, $image->caption);
            }
        }
    }

    protected function beforeProductsSync(): void
    {
        $event = new BeforeProductsSync();
        $this->dispatcher->dispatch($event);
    }

    protected function afterProductsSync(): void
    {
        $event = new AfterProductsSync($this->_ids);
        $this->dispatcher->dispatch($event);
    }

    /**
     * @param ProductInterface $model
     */
    protected function beforeUpdateProduct(ProductInterface $model): void
    {
        $event = new BeforeUpdateProduct($model);
        $this->dispatcher->dispatch($event);
    }

    /**
     * @param ProductInterface $model
     */
    protected function afterUpdateProduct(ProductInterface $model): void
    {
        $event = new AfterUpdateProduct($model);
        $this->dispatcher->dispatch($event);
    }

    /**
     * @param ProductInterface $model
     */
    protected function ImportProcessDataBridge(PayloadTypeInterface $model): void
    {
        $event = new ImportProcessDataBridge($model);
        $this->dispatcher->dispatch($event);
    }
}
