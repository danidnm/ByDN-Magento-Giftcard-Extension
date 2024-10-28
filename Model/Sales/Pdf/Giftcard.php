<?php

namespace Bydn\Giftcard\Model\Sales\Pdf;

use Magento\Tax\Helper\Data;
use Magento\Tax\Model\Calculation;
use Magento\Tax\Model\ResourceModel\Sales\Order\Tax\CollectionFactory;

class Giftcard extends \Magento\Sales\Model\Order\Pdf\Total\DefaultTotal
{
    /**
     * Returns giftcard applied amount
     *
     * @return float|int|void
     */
    public function getAmount()
    {
        $source = $this->getSource();
        $extensionAttributes = $source->getExtensionAttributes();
        if ($extensionAttributes) {
            $giftcardData = $extensionAttributes->getGiftcardData();
        }
        $amount = $giftcardData->getGiftcardAmount();
        if ($amount > 0) {
            return ($amount > 0) ? (-1)*$amount : 0;
        }
    }
}
