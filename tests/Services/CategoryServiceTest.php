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
use Bigperson\Exchange1C\ModelBuilder;
use Bigperson\Exchange1C\Services\CategoryService;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Tests\TestCase;

class CategoryServiceTest extends TestCase
{
    public function testImport(): void
    {
        $configValues = [
            'import_dir' => __DIR__.'/../xml',
            'models'     => [
                \Bigperson\Exchange1C\Interfaces\GroupInterface::class   => \Tests\Models\GroupTestModel::class,
                \Bigperson\Exchange1C\Interfaces\ProductInterface::class => \Tests\Models\ProductTestModel::class,
                \Bigperson\Exchange1C\Interfaces\OfferInterface::class   => \Tests\Models\OfferTestModel::class,
            ],
        ];

        $config = new Config($configValues);
        $request = $this->createMock(Request::class);
        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $builder = new ModelBuilder();
        $request->method('get')
            ->with('filename')
            ->willReturn('import.xml');

        $service = new CategoryService($request, $config, $dispatcher, $builder);
        $this->assertNull($service->import());
    }
}
