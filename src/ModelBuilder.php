<?php
/**
 * This file is part of bigperson/exchange1c package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Bigperson\Exchange1C;

use Bigperson\Exchange1C\Exceptions\Exchange1CException;
use Bigperson\Exchange1C\Interfaces\ModelBuilderInterface;

/**
 * Class ModelBuilder.
 */
class ModelBuilder implements ModelBuilderInterface
{
    /**
     * Если модель в конфиге не установлена, то импорт не будет произведен.
     *
     * @param Config $config
     * @param string $interface
     *
     * @throws Exchange1CException
     *
     * @return null|mixed
     */
    public function getInterfaceClass(Config $config, string $interface)
    {
        $model = $config->getModelClass($interface);
        if ($model) {
            $modelInstance = new $model();
            if ($modelInstance instanceof $interface) {
                return $modelInstance;
            }
        }

        throw new Exchange1CException(sprintf('Model %s not instantiable interface %s', $config->getModelClass($interface), $interface));
    }
}
