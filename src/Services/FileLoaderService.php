<?php
/**
 * This file is part of bigperson/exchange1c package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Bigperson\Exchange1C\Services;

use Bigperson\Exchange1C\Config;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class FileLoaderService.
 */
class FileLoaderService
{
    /**
     * @var Request
     */
    private $request;
    /**
     * @var Config
     */
    private $config;

    /**
     * FileLoaderService constructor.
     *
     * @param Request $request
     * @param Config  $config
     */
    public function __construct(Request $request, Config $config)
    {
        $this->request = $request;
        $this->config = $config;
    }

    /**
     * @return string
     */
    public function load(): string
    {
        $filename = basename($this->request->get('filename'));
        $filePath = $this->config->getFullPath($filename);
        if ($filename === 'orders.xml') {
            throw new \LogicException('This method is not released');
        } else {
            $directory = dirname($filePath);
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }
            $f = fopen($filePath, 'a+');
            fwrite($f, file_get_contents('php://input'));
            fclose($f);
            if ($this->config->isUseZip()) {
                $zip = new \ZipArchive();
                $zip->open($filePath);
                $zip->extractTo($this->config->getImportDir());
                $zip->close();
                unlink($filePath);
            }

            return "success\n";
        }
    }

    /**
     * Delete all files from tmp directory.
     */
    public function clearImportDirectory(): void
    {
        $tmp_files = glob($this->config->getImportDir().DIRECTORY_SEPARATOR.'*.*');
        if (is_array($tmp_files)) {
            foreach ($tmp_files as $v) {
                unlink($v);
            }
        }
    }
}
