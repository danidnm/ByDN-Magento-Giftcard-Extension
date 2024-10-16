<?php

namespace Bydn\Giftcard\Block\Checkout;

use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;

class GiftcardSwitcher implements LayoutProcessorInterface
{
    /**
     * @var \Bydn\Giftcard\Helper\Config
     */
    private $giftcardConfig;

    /**
     * @param \Bydn\Giftcard\Helper\Config $giftcardConfig
     */
    public function __construct(
        \Bydn\Giftcard\Helper\Config $giftcardConfig
    ) {
        $this->giftcardConfig = $giftcardConfig;
    }

    /**
     * @param $jsLayout
     * @return array
     */
    public function process($jsLayout) {
        if (!$this->giftcardConfig->isEnabled()) {
            $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['afterMethods']['children']['giftcard']['config']['componentDisabled'] = true;
        }
        return $jsLayout;
    }
}
