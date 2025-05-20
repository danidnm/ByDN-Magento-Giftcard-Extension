<?php

namespace Bydn\Giftcard\Pricing\Render;

use LogicException;
use BadMethodCallException;
use Magento\Catalog\Pricing\Render as CatalogRender;
use Magento\Framework\Pricing\Price\PriceInterface;
use RuntimeException;

/**
 * Class for final_price rendering
 */
class FinalPriceBox extends CatalogRender\FinalPriceBox
{

    /** @var \Bydn\Giftcard\Helper\Config */
    protected $config;

    /**
     * Check if bundle product has one or more options, or custom options, with different prices
     *
     * @return bool
     */
    public function showRangePrice()
    {

        $config = $this->getConfig();
        $showRange = $config->getShowPrices() == $config::SHOW_PRICES_RANGE && $this->hasRange();

        return $showRange;
    }

    public function showAsLowestPrice()
    {
        $config = $this->getConfig();
        return $config->getShowPrices() == $config::SHOW_PRICES_LOWEST && $this->hasRange();
    }

    public function hasRange(): bool
    {
        $finalPrice = $this->getPrice();
        return $finalPrice->getMinimalPrice() != $finalPrice->getMaximalPrice();
    }

    /**
     * Returns the Config helper instance.
     *
     * Note: The helper is retrieved via ObjectManager instead of constructor injection
     * to avoid overriding the parent constructor. This approach is chosen because
     * extending Magento's FinalPriceBox is discouraged and its constructor signature
     * may change between Magento versions, making dependency injection fragile.
     * Using ObjectManager here ensures compatibility and reduces maintenance risk.
     *
     * @return \Bydn\Giftcard\Helper\Config
     */
    public function getConfig()
    {
        if (!$this->config) {
            $this->config = \Magento\Framework\App\ObjectManager::getInstance()
                ->get(\Bydn\Giftcard\Helper\Config::class);
        }
        return $this->config;
    }
}
