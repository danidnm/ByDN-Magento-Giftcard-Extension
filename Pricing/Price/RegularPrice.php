<?php

namespace Bydn\Giftcard\Pricing\Price;

use Magento\Framework\ObjectManager\ResetAfterRequestInterface;
use Magento\Framework\Pricing\Amount\AmountInterface;
use Magento\Catalog\Pricing\Price\CustomOptionPrice;

/**
 * Bundle product regular price model
 */
class RegularPrice extends \Magento\Catalog\Pricing\Price\RegularPrice implements \Magento\Framework\Pricing\Price\BasePriceProviderInterface, ResetAfterRequestInterface
{
    /**
     * @var AmountInterface
     */
    protected $maximalPrice;

    /**
     * @inheritdoc
     */
    public function getAmount()
    {
        $price = $this->getValue();
        $valueIndex = (string) $price;
        if (!isset($this->amount[$valueIndex])) {
            /** @var \Magento\Catalog\Pricing\Price\CustomOptionPrice $customOptionPrice */
            $customOptionPrice = $this->priceInfo->getPrice(CustomOptionPrice::PRICE_CODE);
            $price += $customOptionPrice->getCustomOptionRange(true, $this->getPriceCode());
            $this->amount[$valueIndex] = $this->calculator->getAmount($price, $this->getProduct());
        }
        return $this->amount[$valueIndex];
    }

    /**
     * Returns max price
     *
     * @return \Magento\Framework\Pricing\Amount\AmountInterface
     */
    public function getMaximalPrice()
    {
        if (null === $this->maximalPrice) {
            $price = $this->getValue();
            /** @var \Magento\Catalog\Pricing\Price\CustomOptionPrice $customOptionPrice */
            $customOptionPrice = $this->priceInfo->getPrice(CustomOptionPrice::PRICE_CODE);
            $price += $customOptionPrice->getCustomOptionRange(false, $this->getPriceCode());
            $this->maximalPrice = $this->calculator->getAmount($price, $this->getProduct());
        }
        return $this->maximalPrice;
    }

    /**
     * Returns min price
     *
     * @return \Magento\Framework\Pricing\Amount\AmountInterface
     */
    public function getMinimalPrice()
    {
        return $this->getAmount();
    }

    /**
     * @inheritDoc
     */
    public function _resetState(): void
    {
        $this->maximalPrice = null;
        $this->amount = [];
    }
}
