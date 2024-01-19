<?php

namespace Bydn\Giftcard\Api\Data;

use Bydn\Giftcard\Api\Data\GiftcardInterface;
use Magento\Framework\Api\SearchResultsInterface;

interface GiftcardSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get Giftcard items
     *
     * @return GiftcardInterface[]
     */
    public function getItems(): array;

    /**
     * Set Giftcard items
     *
     * @param GiftcardInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
