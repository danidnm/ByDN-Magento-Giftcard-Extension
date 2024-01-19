<?php

namespace Bydn\Giftcard\Api\Data;

use Bydn\Giftcard\Api\Data\GiftcardInvoiceInterface;
use Magento\Framework\Api\SearchResultsInterface;

interface GiftcardInvoiceSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get Giftcard invoice items
     *
     * @return GiftcardInvoiceInterface[]
     */
    public function getItems(): array;

    /**
     * Set Giftcard invoice items
     *
     * @param GiftcardInvoiceInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
