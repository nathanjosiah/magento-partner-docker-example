<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\UpgradeTool\Config;

/**
 *
 */
class ConfigReader
{
    /**
     * @var Converter
     */
    private $converter;
    /**
     * @var FileCollector
     */
    private $fileCollector;
    /**
     * @var Dom
     */
    private $dom;

    public function __construct(Converter $converter, FileCollector $fileCollector, Dom $dom)
    {
        $this->converter = $converter;
        $this->fileCollector = $fileCollector;
        $this->dom = $dom;
    }

    public function read(): array
    {
        $files = $this->fileCollector->collect();
        // TODO some merging of files to a big xml document
        $document = $this->dom->read(file_get_contents($files[0]));

        return $this->converter->convert($document);
    }
}
