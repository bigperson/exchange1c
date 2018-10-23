# PHP exchange1c - обмен 1С предприятие с сайтом на php
![Packagist](https://img.shields.io/packagist/l/doctrine/orm.svg?style=flat-square)
[![Packagist](https://img.shields.io/packagist/dt/bigperson/exchange1c.svg?style=flat-square)](https://packagist.org/packages/bigperson/exchange1c)
[![Packagist](https://img.shields.io/packagist/v/bigperson/exchange1c.svg?style=flat-square)](https://packagist.org/packages/bigperson/exchange1c)
[![Travis (.org)](https://img.shields.io/travis/bigperson/exchange1c.svg?style=flat-square)](https://travis-ci.org/bigperson/exchange1c)
[![Codecov](https://img.shields.io/codecov/c/github/bigperson/exchange1c.svg?style=flat-square)](https://codecov.io/gh/bigperson/exchange1c)
[![StyleCI](https://github.styleci.io/repos/153751681/shield?branch=master)](https://github.styleci.io/repos/153751681)




Установка этой библиотеки, должна упрощать интеграцию 1С в ваш сайт.

Библиотека содержит набор интерфейсов, которые необходимо реализовать, чтобы получить возможность обмениваться товарами и документами с 1С. Предполагается, что у Вас есть 1С:Предприятие 8, Управление торговлей", редакция 11.3, версия 11.3.2 на платформе 8.3.9.2033. 

Если у вас версия конфигурации ниже, то скорее всего библиотека все равно будет работать, т.к. по большей части, обмен с сайтами сильно не меняется в 1С от версии к версии.

Данная библиотека была написана на основе модуля https://github.com/carono/yii2-1c-exchange - все основные интерфейсы взяты именно из этого модуля.

# Зависимости
* php ^7.1
* carono/commerceml
* symfony/http-foundation ^4.1

# Установка
`composer require bigperson/exchange1c`

# Использование
Для использования библиотеки вам неободимо определить массив конфигов и реализовать интерфейсы в ваших моделях.

```php
require_once './../vendor/autoload.php'; //Подулючаем автолоад

// Определяем конфиг
$configValues = [
    'import_dir'    => '1c_exchange',
    'login'    => 'admin',
    'password' => 'admin',
    'use_zip'       => false,
    'file_part' => 0,
    'models'    => [
        \Bigperson\Exchange1C\Interfaces\GroupInterface::class => \Tests\Models\GroupTestModel::class,
        \Bigperson\Exchange1C\Interfaces\ProductInterface::class => \Tests\Models\ProductTestModel::class,
        \Bigperson\Exchange1C\Interfaces\OfferInterface::class => \Tests\Models\OfferTestModel::class,
    ],
];
$config = new \Bigperson\Exchange1C\Config($configValues);
$request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();
$symfonyDispatcher = new \Symfony\Component\EventDispatcher\EventDispatcher();
$dispatcher = new \Bigperson\Exchange1C\SymfonyEventDispatcher($symfonyDispatcher);
$modelBuilder = new \Bigperson\Exchange1C\ModelBuilder();
// Создаем необходимые сервисы
$loaderService = new \Bigperson\Exchange1C\Services\FileLoaderService($request, $config);
$authService = new \Bigperson\Exchange1C\Services\AuthService($request, $config);
$categoryService = new \Bigperson\Exchange1C\Services\CategoryService($request, $config, $dispatcher, $modelBuilder);
$offerService = new \Bigperson\Exchange1C\Services\OfferService($request, $config, $dispatcher, $modelBuilder);
$catalogService = new \Bigperson\Exchange1C\Services\CatalogService($request, $config, $authService, $loaderService, $categoryService, $offerService);


$mode = $request->get('mode');
$type = $request->get('type');

try {
    if ($type == 'catalog') {
        if (!method_exists($catalogService, $mode)) {
            throw new Exception('not correct request, mode=' . $mode);
        }
        //Запускаем сервис импорта каталога
        $body = $catalogService->$mode();
        $response = new \Symfony\Component\HttpFoundation\Response($body, 200, ['Content-Type', 'text/plain']);
        $response->send();
    } else {
        throw new \LogicException(sprintf('Logic for method %s not released', $type));
    }
} catch (\Exception $e) {
    $body = "failure\n";
    $body .= $e->getMessage() . "\n";
    $body .= $e->getFile() . "\n";
    $body .= $e->getLine() . "\n";

    $response = new \Symfony\Component\HttpFoundation\Response($body, 500, ['Content-Type', 'text/plain']);
    $response->send();
}
```

Более подробную информацию по интерфейсам и их реализациям можно почитаь в документации https://github.com/carono/yii2-1c-exchange

Документация будет добалена позже.


