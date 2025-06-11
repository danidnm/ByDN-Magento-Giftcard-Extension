<?php
/**
 * @package     Bydn_Giftcard
 * @author      Daniel Navarro <https://github.com/danidnm>
 * @license     GPL-3.0-or-later
 * @copyright   Copyright (c) 2025 Daniel Navarro
 *
 * This file is part of a free software package licensed under the
 * GNU General Public License v3.0.
 * You may redistribute and/or modify it under the same license.
 */

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
