<?php
/**
 * This file is part of bigperson/exchange1c package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Mikkimike\Exchange1C\Services;

use Mikkimike\Exchange1C\Config;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AbstractService.
 */
abstract class AbstractService
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var AuthService
     */
    protected $authService;

    /**
     * @var FileLoaderService
     */
    protected $loaderService;

    /**
     * @var CategoryService
     */
    protected $categoryService;

    /**
     * @var OfferService
     */
    protected $offerService;
    
    /**
     * @var OrderService
     */
    protected $orderService;
    
    /**
     * @var UserService
     */
    protected $userService;

    /**
     * AbstractService constructor.
     *
     * @param Request           $request
     * @param Config            $config
     * @param AuthService       $authService
     * @param FileLoaderService $loaderService
     * @param CategoryService   $categoryService
     * @param OfferService      $offerService
     * @param OrderService      $orderService
     */
    public function __construct(
        Request $request,
        Config $config,
        AuthService $authService,
        FileLoaderService $loaderService,
        CategoryService $categoryService,
        OfferService $offerService,
        OrderService $orderService,
        UserService $userService        
    ) {
        $this->request = $request;
        $this->config = $config;
        $this->authService = $authService;
        $this->loaderService = $loaderService;
        $this->categoryService = $categoryService;
        $this->offerService = $offerService;
        $this->orderService = $orderService;
        $this->userService = $userService;
    }
}
