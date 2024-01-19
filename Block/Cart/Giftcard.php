<?php

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
