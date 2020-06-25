<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\UpgradeTool\Config;

use Magento\UpgradeTool\Config\Dom;

class CommandFlow
{
    /**
     * @var \Magento\UpgradeTool\Config\Dom
     */
    private Dom $dom;

    /**
     * @var \DOMNode
     */
    private \DOMNode $node;

    /**
     * CommandFlow constructor.
     * @param \Magento\UpgradeTool\Config\Dom $dom
     * @param \DOMNode $node
     */
    public function __construct(Dom $dom, \DOMNode $node)
    {
        $this->dom = $dom;
        $this->node = $node;
    }

    /**
     * @return array
     */
    public function getBefore(): array
    {
        $commandNodes = $this->dom->getBefore($this->node);
        return $this->getCommandList($commandNodes);
    }

    /**
     * @return array
     */
    public function getAfter(): array
    {
        $commandNodes = $this->dom->getAfter($this->node);
        return $this->getCommandList($commandNodes);

    }

    /**
     * @param \DOMNodeList $commandNodes
     * @return array
     */
    private function getCommandList(\DOMNodeList $commandNodes): array
    {
        $commandList = [];
        foreach($commandNodes as $commandNode) {
            $commandList[] = new Command($this->dom, $commandNode);
        }
        return $commandList;
    }
}
