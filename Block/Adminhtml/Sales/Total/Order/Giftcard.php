<?php

namespace Bydn\Giftcard\Block\Adminhtml\Sales\Total\Order;

class Giftcard extends \Magento\Sales\Block\Adminhtml\Order\Totals
{
    /**
     * Init totals handle to add giftcard total info
     *
     * @return $this
     */
    public function initTotals()
    {
        // Read giftcard applied amount from extension attributes
        $extensionAttributes = $this->getSource()->getExtensionAttributes();
        $giftcardData = $extensionAttributes->getGiftcardData();
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
            'label' => __('Giftcard'), //$this->_dataHelper->getGiftcardDiscountLabel(),
        ]);
        $this->getParentBlock()->addTotal($total, 'tax');

        return $this;
    }
}
