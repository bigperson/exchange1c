<?php
/**
 * This file is part of bigperson/exchange1c package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Services;

use Bigperson\Exchange1C\Config;
use Bigperson\Exchange1C\Services\AuthService;
use Bigperson\Exchange1C\Services\CatalogService;
use Bigperson\Exchange1C\Services\CategoryService;
use Bigperson\Exchange1C\Services\FileLoaderService;
use Bigperson\Exchange1C\Services\OfferService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Tests\TestCase;

class CatalogServiceTest extends TestCase
{
    public function testCheckAuth(): void
    {
        $config = $this->createMock(Config::class);
        $request = $this->createMock(Request::class);
        $loader = $this->createMock(FileLoaderService::class);
        $auth = $this->createMock(AuthService::class);
        $auth->method('checkAuth')
            ->willReturn('success');
        $category = $this->createMock(CategoryService::class);
        $offer = $this->createMock(OfferService::class);
        $service = new CatalogService($request, $config, $auth, $loader, $category, $offer);

        $this->assertEquals('success', $service->checkauth());
    }

    public function testInit(): void
    {
        $config = $this->createMock(Config::class);
        $request = $this->createMock(Request::class);
        $loader = $this->createMock(FileLoaderService::class);
        $auth = $this->createMock(AuthService::class);
        $auth->method('auth');
        $category = $this->createMock(CategoryService::class);
        $offer = $this->createMock(OfferService::class);
        $service = new CatalogService($request, $config, $auth, $loader, $category, $offer);

        $this->assertTrue(is_string($service->init()));
    }

    public function testFile(): void
    {
        $config = $this->createMock(Config::class);
        $request = $this->createMock(Request::class);
        $loader = $this->createMock(FileLoaderService::class);
        $auth = $this->createMock(AuthService::class);
        $auth->method('auth');
        $category = $this->createMock(CategoryService::class);
        $offer = $this->createMock(OfferService::class);
        $service = new CatalogService($request, $config, $auth, $loader, $category, $offer);
        $loader->method('load')
            ->willReturn('success');

        $this->assertEquals('success', $service->file());
    }

    public function testImportImport(): void
    {
        $config = $this->createMock(Config::class);
        $request = $this->createMock(Request::class);
        $request->method('get')
            ->with('filename')
            ->willReturn('import.xml');
        $session = $this->createMock(SessionInterface::class);
        $session->method('getId')
            ->willReturn('1231243');
        $request->method('getSession')
            ->willReturn($session);
        $loader = $this->createMock(FileLoaderService::class);
        $auth = $this->createMock(AuthService::class);
        $auth->method('auth');
        $category = $this->createMock(CategoryService::class);
        $category->method('import')
            ->willReturn('success');
        $offer = $this->createMock(OfferService::class);
        $service = new CatalogService($request, $config, $auth, $loader, $category, $offer);

        $this->assertTrue(is_string($service->import()));
    }

    public function testImportOffers(): void
    {
        $config = $this->createMock(Config::class);
        $request = $this->createMock(Request::class);
        $request->method('get')
            ->with('filename')
            ->willReturn('offers.xml');
        $session = $this->createMock(SessionInterface::class);
        $session->method('getId')
            ->willReturn('1231243');
        $request->method('getSession')
            ->willReturn($session);
        $loader = $this->createMock(FileLoaderService::class);
        $auth = $this->createMock(AuthService::class);
        $auth->method('auth');
        $category = $this->createMock(CategoryService::class);
        $offer = $this->createMock(OfferService::class);
        $offer->method('import')
            ->willReturn('success');
        $service = new CatalogService($request, $config, $auth, $loader, $category, $offer);

        $this->assertTrue(is_string($service->import()));
    }
}
