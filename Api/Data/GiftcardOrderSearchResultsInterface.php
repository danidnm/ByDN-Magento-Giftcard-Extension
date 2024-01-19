<?php

namespace Bydn\Giftcard\Api\Data;

use Bydn\Giftcard\Api\Data\GiftcardOrderInterface;
use Magento\Framework\Api\SearchResultsInterface;

interface GiftcardOrderSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get Giftcard order items
     *
     * @return GiftcardOrderInterface[]
     */
    public function getItems(): array;

    /**
     * Set Giftcard order items
     *
     * @param GiftcardOrderInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
