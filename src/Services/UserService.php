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
use Mikkimike\Exchange1C\Events\ImportLog;
use Mikkimike\Exchange1C\Events\ImportProcessDataBridge;
use Mikkimike\Exchange1C\Exceptions\Exchange1CException;
use Mikkimike\Exchange1C\Interfaces\EventDispatcherInterface;
use Mikkimike\Exchange1C\Interfaces\ModelBuilderInterface;
use Mikkimike\Exchange1C\Interfaces\ProductInterface;
use Mikkimike\Exchange1C\PayloadTypes\BatchStart;
use Mikkimike\Exchange1C\PayloadTypes\User1c;
use Mikkimike\Exchange1C\PayloadTypes\PayloadTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Zenwalker\CommerceML\CommerceML;
use Zenwalker\CommerceML\Model\Offer;

/**
 * Class UserService.
 */
class UserService
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

        $this->dispatcher->dispatch(new ImportLog('Sync users'));

        $xml = simplexml_load_string(file_get_contents(storage_path('app/users_person/user_person.xml')));

        foreach ($xml as $item) {
            $this->ImportProcessDataBridge(new User1c($item));
        }

        $this->ImportProcessDataBridge(new BatchStart("USER IMPORT"));
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
