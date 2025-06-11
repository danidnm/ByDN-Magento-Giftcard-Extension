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
        $showRange = $config->showPriceRange() && $this->hasRange();

        return $showRange;
    }

    public function showAsLowestPrice()
    {
        $config = $this->getConfig();
        return $config->showPriceLowest() && $this->hasRange();
    }

    public function hasRange(): bool
    {
        /** @var \Bydn\Giftcard\Pricing\Price\FinalPrice $finalPrice */
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
