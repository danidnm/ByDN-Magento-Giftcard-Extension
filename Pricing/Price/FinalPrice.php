<?php

namespace Bydn\Giftcard\Pricing\Price;

use Magento\Catalog\Api\ProductCustomOptionRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Pricing\Price\CustomOptionPrice;
use Magento\Framework\Pricing\Adjustment\CalculatorInterface;
use Magento\Framework\Pricing\Amount\AmountInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Final price model
 */
class FinalPrice extends \Magento\Catalog\Pricing\Price\FinalPrice implements \Magento\Catalog\Pricing\Price\FinalPriceInterface
{
    /**
     * @var AmountInterface
     */
    protected $maximalPrice;

    /**
     * @var AmountInterface
     */
    protected $minimalPrice;

    /**
     * @var AmountInterface
     */
    protected $priceWithoutOption;

    /**
     * @var BundleOptionPrice
     */
    protected $bundleOptionPrice;

    /**
     * @var ProductCustomOptionRepositoryInterface
     */
    private $productOptionRepository;

    /**
     * @param Product $saleableItem
     * @param float $quantity
     * @param CalculatorInterface $calculator
     * @param PriceCurrencyInterface $priceCurrency
     * @param ProductCustomOptionRepositoryInterface $productOptionRepository
     */
    public function __construct(
        Product $saleableItem,
        $quantity,
        CalculatorInterface $calculator,
        PriceCurrencyInterface $priceCurrency,
        ProductCustomOptionRepositoryInterface $productOptionRepository
    ) {
        $this->productOptionRepository = $productOptionRepository;
        parent::__construct($saleableItem, $quantity, $calculator, $priceCurrency);
    }

    /**
     * Returns max price
     *
     * @return AmountInterface
     */
    public function getMaximalPrice()
    {
        if (!$this->maximalPrice) {
            /** @var CustomOptionPrice $customOptionPrice */
            $customOptionPrice = $this->priceInfo->getPrice(CustomOptionPrice::PRICE_CODE);
            $price = $this->getValue() + $customOptionPrice->getCustomOptionRange(false);
            $this->maximalPrice = $this->calculator->getAmount($price, $this->getProduct());
        }

        return $this->maximalPrice;
    }

    /**
     * Returns min price
     *
     * @return AmountInterface
     */
    public function getMinimalPrice()
    {
        return $this->getAmount();
    }

    /**
     * Returns price amount
     *
     * @return AmountInterface
     */
    public function getAmount()
    {
        if (!$this->minimalPrice) {
            $this->loadProductCustomOptions();
            /** @var CustomOptionPrice $customOptionPrice */
            $customOptionPrice = $this->priceInfo->getPrice(CustomOptionPrice::PRICE_CODE);
            $price = $this->getValue() + $customOptionPrice->getCustomOptionRange(true);
            $this->minimalPrice = $this->calculator->getAmount($price, $this->getProduct());
        }
        return $this->minimalPrice;
    }

    /**
     * Load product custom options
     *
     * @return void
     */
    private function loadProductCustomOptions()
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->product;
        if (!$product->getOptions()) {
            $options = [];
            foreach ($this->productOptionRepository->getProductOptions($product) as $option) {
                /** @var \Magento\Catalog\Model\Product\Option $option */
                $option->setProduct($this->product);
                $options[] = $option;
            }
            $product->setOptions($options);
        }
    }
}
