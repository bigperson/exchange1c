<?php


namespace Mikkimike\Exchange1C\Events;


class ImportLog extends AbstractEventInterface
{
    const NAME = 'import.log';
    
    public $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }
}
