<?php

namespace Bydn\Giftcard\Block\Adminhtml\Sales\Total\Order\Creditmemo;

use Magento\Framework\View\Element\Template;

class Create extends \Magento\Framework\View\Element\Template
{
    /**
     * @return mixed
     */
    private function getCreditmemo() {
        return $this->getParentBlock()->getCreditmemo();
    }

    /**
     * Get the maximum allowed amount to refund to the giftcard
     * @return int
     */
    public function getGiftcardAmount() {

        // Always from the creditmemo (it is set in the totals calculation)
        $creditmemo = $this->getCreditmemo();
        if ($creditmemo) {
            $invoiceExtension = $creditmemo->getExtensionAttributes();
            if ($invoiceExtension) {
                $giftcardData = $invoiceExtension->getGiftcardData();
                if ($giftcardData) {
                    return $giftcardData->getGiftcardAmount();
                }
            }
        }

        return null;
    }

    /**
     * @return $this
     */
    public function initTotals()
    {
        // Get maximum amount to apply anc check is not zero
        $giftcardDiscount = $this->getGiftcardAmount();
        if ($giftcardDiscount === null) {
            return $this;
        }

        // Add total to the parent block. As we will use the template, we don't need any data...
        $total = new \Magento\Framework\DataObject([
            'block_name' => 'giftcard_refund',
        ]);
        $this->getParentBlock()->addTotal($total, 'tax');

        return $this;
    }
}
