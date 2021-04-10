<?php
/**
 * This file is part of bigperson/exchange1c package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Mikkimike\Exchange1C\Events;

use Mikkimike\Exchange1C\PayloadTypes\PayloadTypeInterface;

/**
 * Class ImportProcessDataBridge
 * @package Mikkimike\Exchange1C\Events
 */
class ImportProcessDataBridge extends AbstractEventInterface
{
    /**
     * @var $payload
     */
    public $payload;
    
    public function __construct(PayloadTypeInterface $payload)
    {
        $this->payload = $payload;
    }
}
