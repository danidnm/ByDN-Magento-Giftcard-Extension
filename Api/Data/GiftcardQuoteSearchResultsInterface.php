<?php

namespace Bydn\Giftcard\Api\Data;

use Bydn\Giftcard\Api\Data\GiftcardQuoteInterface;
use Magento\Framework\Api\SearchResultsInterface;

interface GiftcardQuoteSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get Giftcard quote items
     *
     * @return GiftcardQuoteInterface[]
     */
    public function getItems(): array;

    /**
     * Set Giftcard quote items
     *
     * @param GiftcardQuoteInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
