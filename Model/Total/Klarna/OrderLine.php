<?php

namespace Bydn\Giftcard\Model\Total\Klarna;

use Klarna\Base\Model\Api\DataHolder;
use Klarna\Base\Model\Api\Parameter;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Sales\Api\Data\OrderInterface;

/**
 * Class CustomFee


 */
class OrderLine implements \Klarna\Orderlines\Api\OrderLineInterface
{
    const TOTAL_KEY = 'giftcard_discount';

    private $giftcardAmount = 0;

    public function collectPrePurchase(Parameter $parameter, DataHolder $dataHolder, CartInterface $quote)
    {
        return $this->collect($parameter, $dataHolder);
    }

    public function collectPostPurchase(Parameter $parameter, DataHolder $dataHolder, OrderInterface $order)
    {
        return $this->collect($parameter, $dataHolder);
    }

    /**
     * Collecting the values
     *
     * @param Parameter $parameter
     * @param DataHolder $dataHolder
     * @return $this
     */
    private function collect(Parameter $parameter, DataHolder $dataHolder)
    {
        // Extract totals from quote
        $totals = $dataHolder->getTotals();

        // Check giftcard is used
        if (!is_array($totals) || !isset($totals[self::TOTAL_KEY])) {
            return $this;
        }

        // Get used amount
        $total = $totals[self::TOTAL_KEY];
        $this->giftcardAmount = $total->getValue();
        $this->giftcardAmount = round($this->giftcardAmount * 100);

        return $this;
    }

    public function fetch(Parameter $parameter)
    {
        // If not used, do not add a new line total
        if ($this->giftcardAmount == 0) {
            return $this;
        }

        // Add total line data
        $item = [
            'type' => 'gift_card',
            'name' => __('Giftcard'),
            'quantity' => 1,
            'unit_price' => $this->giftcardAmount,
            'tax_rate' => 0,
            'total_amount' => $this->giftcardAmount,
            'total_tax_amount' => 0,
        ];

        $parameter->addOrderLine($item);

        return $this;
    }
}
