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

namespace Bydn\Giftcard\Block\Adminhtml\Sales\Total\Order\Creditmemo;

class Giftcard extends \Magento\Sales\Block\Adminhtml\Order\Creditmemo\Totals
{
    /**
     * Returns giftcard associated data
     *
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @return \Bydn\Giftcard\Api\Data\GiftcardCreditmemoInterface|null
     */
    public function getGiftcardData(\Magento\Sales\Model\Order\Creditmemo $creditmemo)
    {
        $creditmemoExtension = $creditmemo->getExtensionAttributes();
        if ($creditmemoExtension) {
            return $creditmemoExtension->getGiftcardData();
        }

        return null;
    }

    /**
     * Init totals handle to add giftcard total info
     *
     * @return $this
     */
    public function initTotals()
    {
        // Read giftcard applied amount from extension attributes
        $giftcardData = $this->getGiftcardData($this->getSource());
        if (!$giftcardData) {
            return $this;
        }
        $giftcardAmount = $giftcardData->getGiftcardAmount();
        $giftcardBaseAmount = $giftcardData->getGiftcardBaseAmount();

        // Do not show if zero
        if (empty($giftcardAmount) || ($giftcardAmount < 0.01)) {
            return $this;
        }

        // Add total
        $total = new \Magento\Framework\DataObject([
            'code' => 'giftcard_discount',
            'value' => (-1)*$giftcardAmount,
            'base_value' => (-1)*$giftcardBaseAmount,
            'label' => __('Refunded to Giftcard') . ' (' . $giftcardData->getGiftcardCode() . ')',
        ]);
        $this->getParentBlock()->addTotal($total, 'subtotal');

        return $this;
    }
}
