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

namespace Bydn\Giftcard\Block\Cart;

class Giftcard extends \Magento\Checkout\Block\Cart\AbstractCart
{
    /**
     * Returns the applied code if any
     *
     * @return string
     * @codeCoverageIgnore
     */
    public function getGiftcardCode()
    {
        $quote = $this->getQuote();
        $quoteExtension = $quote->getExtensionAttributes();
        $giftcardData = $quoteExtension->getGiftcardData();
        return $giftcardData->getGiftcardCode();
    }
}
