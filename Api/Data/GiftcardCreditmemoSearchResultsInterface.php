<?php

namespace Bydn\Giftcard\Api\Data;

use Bydn\Giftcard\Api\Data\GiftcardCreditmemoInterface;
use Magento\Framework\Api\SearchResultsInterface;

interface GiftcardCreditmemoSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get Giftcard creditmemo items
     *
     * @return GiftcardCreditmemoInterface[]
     */
    public function getItems(): array;

    /**
     * Set Giftcard creditmemo items
     *
     * @param GiftcardCreditmemoInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
