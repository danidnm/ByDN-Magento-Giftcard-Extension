<?php

namespace Bydn\Giftcard\Model\Sales\Pdf;

use Magento\Tax\Helper\Data;
use Magento\Tax\Model\Calculation;
use Magento\Tax\Model\ResourceModel\Sales\Order\Tax\CollectionFactory;

class Giftcard extends \Magento\Sales\Model\Order\Pdf\Total\DefaultTotal
{
    public function __construct(
        Data $taxHelper,
        Calculation $taxCalculation,
        CollectionFactory $ordersFactory,
        array $data = []
    ) {
        parent::__construct($taxHelper, $taxCalculation, $ordersFactory, $data);
    }

    /**
     * Get array of arrays with totals information for display in PDF
     * array(
     *  $index => array(
     *      'amount'   => $amount,
     *      'label'    => $label,
     *      'font_size'=> $font_size
     *  )
     * )
     * @return array
     */
//    public function getTotalsForDisplay(): array
//    {
//        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
//        $partialpayment = $objectManager->create('\Vendor\Module\Model\Partialpayment');
//        $partialPayment = $partialpayment->load($this->getOrder()->getIncrementId(),'order_id');
//        $paynow = $partialPayment->getPaidAmount();
//        if (!$partialPayment->getOrderId()) {
//            return [];
//        }
//        $amountInclTax = $this->getOrder()->formatPriceTxt($paynow);
//        $fontSize = $this->getFontSize() ? $this->getFontSize() : 7;
//        return [
//            [
//                'amount' => 1, //$this->getAmountPrefix() . $amountInclTax,
//                'label' => 'Giftcard', //__($this->getTitle()) . ':',
//                'font_size' => 12 // $fontSize,
//            ]
//        ];
//    }

    /**
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
