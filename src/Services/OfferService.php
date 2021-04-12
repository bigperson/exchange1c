<?php
/**
 * This file is part of bigperson/exchange1c package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Mikkimike\Exchange1C\Services;

use Illuminate\Support\Facades\Log;
use Mikkimike\Exchange1C\Config;
use Mikkimike\Exchange1C\Events\AfterOffersSync;
use Mikkimike\Exchange1C\Events\AfterProductFindError;
use Mikkimike\Exchange1C\Events\AfterUpdateOffer;
use Mikkimike\Exchange1C\Events\BeforeOffersSync;
use Mikkimike\Exchange1C\Events\BeforeUpdateOffer;
use Mikkimike\Exchange1C\Events\ImportLog;
use Mikkimike\Exchange1C\Events\ImportProcessDataBridge;
use Mikkimike\Exchange1C\Exceptions\Exchange1CException;
use Mikkimike\Exchange1C\Interfaces\EventDispatcherInterface;
use Mikkimike\Exchange1C\Interfaces\ModelBuilderInterface;
use Mikkimike\Exchange1C\Interfaces\OfferInterface;
use Mikkimike\Exchange1C\Interfaces\ProductInterface;
use Mikkimike\Exchange1C\PayloadTypes\ConsoleNextStep;
use Mikkimike\Exchange1C\PayloadTypes\ConsoleProgressFinish;
use Mikkimike\Exchange1C\PayloadTypes\ConsoleProgressStart;
use Mikkimike\Exchange1C\PayloadTypes\PayloadTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Zenwalker\CommerceML\CommerceML;
use Zenwalker\CommerceML\Model\Offer;

/**
 * Class OfferService.
 */
class OfferService
{
    /**
     * @var array Массив идентификаторов торговых предложений которые были добавлены и обновлены
     */
    private $_ids;
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

    /**
     * CategoryService constructor.
     *
     * @param Request                  $request
     * @param Config                   $config
     * @param EventDispatcherInterface $dispatcher
     * @param ModelBuilderInterface    $modelBuilder
     */
    public function __construct(Request $request, Config $config, EventDispatcherInterface $dispatcher, ModelBuilderInterface $modelBuilder)
    {
        $this->request = $request;
        $this->config = $config;
        $this->dispatcher = $dispatcher;
        $this->modelBuilder = $modelBuilder;
    }

    /**
     * @throws Exchange1CException
     */
    public function import()
    {
        $filename = basename($this->request->get('filename'));
        $this->_ids = [];
        $commerce = new CommerceML();
        $category = false;
        if ($this->request->has('category')) {
            $category = $this->request->get('category');
        }

        $commerce->loadOffersXml($this->config->getFullPath($filename, $category));
        if ($offerClass = $this->getOfferClass()) {
            $offerClass::createPriceTypes1c($commerce->offerPackage->getPriceTypes());
        }
        $this->beforeOfferSync();

        $this->dispatcher->dispatch(new ImportLog('Sync offers'));

        $getOffers = $commerce->offerPackage->getOffers();

        $this->ImportProcessDataBridge(new ConsoleProgressStart($getOffers));

        foreach ($getOffers as $offer) {
            $productId = $offer->getClearId();
            if ($product = $this->findProductModelById($productId)) {
                $model = $product->getOffer1c($offer);
                $this->parseProductOffer($model, $offer);
                $this->_ids[] = $model->getPrimaryKey();
            } else {
                $this->dispatcher->dispatches([
                    new AfterProductFindError($productId, $offer),
                    new ImportLog("Продукт $productId не найден в базе")
                ]);
                continue;

                //throw new Exchange1CException("Продукт $productId не найден в базе");
            }
            unset($model);
            $this->ImportProcessDataBridge(new ConsoleNextStep());
        }
        $this->afterOfferSync();
        $this->ImportProcessDataBridge(new ConsoleProgressFinish());
    }

    /**
     * @return OfferInterface|null
     */
    private function getOfferClass(): ?OfferInterface
    {
        return $this->modelBuilder->getInterfaceClass($this->config, OfferInterface::class);
    }

    /**
     * @param string $id
     *
     * @return ProductInterface|null
     */
    protected function findProductModelById(string $id) //: ?ProductInterface
    {
        /**
         * @var ProductInterface
         */
        $class = $this->modelBuilder->getInterfaceClass($this->config, ProductInterface::class);

        return $class::findProductBy1c($id);
    }

    /**
     * @param OfferInterface $model
     * @param Offer          $offer
     */
    protected function parseProductOffer(OfferInterface $model, Offer $offer): void
    {
        $this->beforeUpdateOffer($model, $offer);
        $this->parseSpecifications($model, $offer);
        $this->parsePrice($model, $offer);
        $this->afterUpdateOffer($model, $offer);
    }

    /**
     * @param OfferInterface $model
     * @param Offer          $offer
     */
    protected function parseSpecifications(OfferInterface $model, Offer $offer)
    {
        foreach ($offer->getSpecifications() as $specification) {
            $model->setSpecification1c($specification);
        }
    }

    /**
     * @param OfferInterface $model
     * @param Offer          $offer
     */
    protected function parsePrice(OfferInterface $model, Offer $offer)
    {
        foreach ($offer->getPrices() as $price) {
            $model->setPrice1c($price, $offer->xml->Количество);
        }
    }

    public function beforeOfferSync(): void
    {
        $event = new BeforeOffersSync();
        $this->dispatcher->dispatch($event);
    }

    public function afterOfferSync(): void
    {
        $event = new AfterOffersSync($this->_ids);
        $this->dispatcher->dispatch($event);
    }

    /**
     * @param OfferInterface $model
     * @param Offer          $offer
     */
    public function beforeUpdateOffer(OfferInterface $model, Offer $offer)
    {
        $event = new BeforeUpdateOffer($model, $offer);
        $this->dispatcher->dispatch($event);
    }

    /**
     * @param OfferInterface $model
     * @param Offer          $offer
     */
    public function afterUpdateOffer(OfferInterface $model, Offer $offer)
    {
        $event = new AfterUpdateOffer($model, $offer);
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
