<?php
namespace SublimeSkinz\SublimeVault;

use Analog\Logger;
use Analog\Handler\Stderr;
use Analog\Handler\File;

class VaultLogger extends Logger
{
    public function __construct()
    {
        $logger = new Logger();

        switch (getenv('LOGGER_HANDLER')) {
            case 'File':
                $logger->handler(File::init(__DIR__ . '/../' . getenv('LOGGER_FILE_PATH')));
                break;

            default:
                $logger->handler(Stderr::init());
                break;
        }
    }
}
