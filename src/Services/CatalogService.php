<?php
/**
 * This file is part of bigperson/exchange1c package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Mikkimike\Exchange1C\Services;

/**
 * Class Catalog
 * Class for implementing CommerceML protocol
 * http://v8.1c.ru/edi/edi_stnd/90/92.htm
 * http://v8.1c.ru/edi/edi_stnd/131/.
 */
class CatalogService extends AbstractService
{
    /**
     * Начало сеанса
     * Выгрузка данных начинается с того, что система "1С:Предприятие" отправляет http-запрос следующего вида:
     * http://<сайт>/<путь> /1c-exchange?type=catalog&mode=checkauth.
     * В ответ система управления сайтом передает системе «1С:Предприятие» три строки (используется разделитель строк "\n"):
     * - слово "success";
     * - имя Cookie;
     * - значение Cookie.
     * Примечание. Все последующие запросы к системе управления сайтом со стороны "1С:Предприятия" содержат в заголовке запроса имя и значение Cookie.
     *
     * @return string
     */
    public function checkauth(): string
    {
        return $this->authService->checkAuth();
    }

    /**
     * Запрос параметров от сайта
     * Далее следует запрос следующего вида:
     * http://<сайт>/<путь> /1c-exchange?type=catalog&mode=init
     * В ответ система управления сайтом передает две строки:
     * 1. zip=yes, если сервер поддерживает обмен
     * в zip-формате -  в этом случае на следующем шаге файлы должны быть упакованы в zip-формате
     * или zip=no - в этом случае на следующем шаге файлы не упаковываются и передаются каждый по отдельности.
     * 2. file_limit=<число>, где <число> - максимально допустимый размер файла в байтах для передачи за один запрос.
     * Если системе "1С:Предприятие" понадобится передать файл большего размера, его следует разделить на фрагменты.
     *
     * @return string
     */
    public function init(): string
    {
        $this->authService->auth();
        $this->loaderService->clearImportDirectory();
        $zipEnable = function_exists('zip_open') && $this->config->isUseZip();
        $response = 'zip='.($zipEnable ? 'yes' : 'no')."\n";
        $response .= 'file_limit='.$this->config->getFilePart();

        return $response;
    }

    /**
     * Загрузка и сохранение файлов на сервер
     *
     * @return string
     */
    public function file(): string
    {
        $this->authService->auth();

        return $this->loaderService->load();
    }

    /**
     * На последнем шаге по запросу из "1С:Предприятия" производится пошаговая загрузка данных по запросу
     * с параметрами вида http://<сайт>/<путь> /1c_exchange.php?type=catalog&mode=import&filename=<имя файла>
     * Во время загрузки система управления сайтом может отвечать в одном из следующих вариантов.
     * 1. Если в первой строке содержится слово "progress" - это означает необходимость послать тот же запрос еще раз.
     * В этом случае во второй строке будет возвращен текущий статус обработки, объем  загруженных данных, статус импорта и т.д.
     * 2. Если в ответ передается строка со словом "success", то это будет означать сообщение об успешном окончании
     * обработки файла.
     * Примечание. Если в ходе какого-либо запроса произошла ошибка, то в первой строке ответа системы управления
     * сайтом будет содержаться слово "failure", а в следующих строках - описание ошибки, произошедшей в процессе
     * обработки запроса.
     * Если произошла необрабатываемая ошибка уровня ядра продукта или sql-запроса, то будет возвращен html-код.
     *
     * @return string
     */
    public function import(): string
    {
        $this->authService->auth();
        $filename = $this->request->get('filename');
        switch ($filename) {
            case 'import.xml':
                {
                    $this->categoryService->import();
                    break;
                }
            case 'offers.xml':
                {
                    $this->offerService->import();
                    break;
                }
        }

        $response = "success\n";
        $response .= "laravel_session\n";
        $response .= $this->request->getSession()->getId()."\n";
        $response .= 'timestamp='.time();

        return $response;
    }
}
