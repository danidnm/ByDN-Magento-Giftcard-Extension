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

namespace Bydn\Giftcard\Model\Source\Giftcard;

class Currency implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private \Magento\Store\Model\StoreManagerInterface $storeManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
    }
    /**
     * Returns possible values for giftcard status field
     *
     * @return array
     */
    public function toOptionArray()
    {
        // Store ID
        $currenStoreId = $this->storeManager->getStore()->getStoreId();

        // Get list of available currencies
        $options = [];
        $allowedCurrencies = $this->scopeConfig->getValue(
            'currency/options/allow',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $currenStoreId
        );
        $allowedCurrencies = explode(',', $allowedCurrencies);
        foreach ($allowedCurrencies as $allowedCurrency) {
            $options[] = [
                'label' => $allowedCurrency,
                'value' => $allowedCurrency,
            ];
        }

        return $options;
    }
}
