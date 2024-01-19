<?php

namespace Bydn\Giftcard\Api\Data;

use Bydn\Giftcard\Api\Data\GiftcardMovementInterface;
use Magento\Framework\Api\SearchResultsInterface;

interface GiftcardMovementSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get Giftcard movement items
     *
     * @return GiftcardMovementInterface[]
     */
    public function getItems(): array;

    /**
     * Set Giftcard movement items
     *
     * @param GiftcardMovementInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
