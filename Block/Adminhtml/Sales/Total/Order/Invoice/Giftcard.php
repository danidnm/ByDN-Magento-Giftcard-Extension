<?php

namespace Bydn\Giftcard\Block\Adminhtml\Sales\Total\Order\Invoice;

class Giftcard extends \Magento\Sales\Block\Adminhtml\Order\Invoice\Totals
{
    /**
     * Returns giftcard associated data
     *
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     * @return \Bydn\Giftcard\Api\Data\GiftcardInvoiceInterface|null
     */
    public function getGiftcardData(\Magento\Sales\Model\Order\Invoice $invoice)
    {
        $invoiceExtension = $invoice->getExtensionAttributes();
        if ($invoiceExtension) {
            return $invoiceExtension->getGiftcardData();
        }

        return null;
    }

    /**
     * Adds a total to the invoice totals
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
            'label' => __('Giftcard'),
        ]);
        $this->getParentBlock()->addTotal($total, 'tax');

        return $this;
    }
}
