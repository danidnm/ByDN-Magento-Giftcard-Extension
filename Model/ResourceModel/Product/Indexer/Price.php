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

namespace Bydn\Giftcard\Model\ResourceModel\Product\Indexer;

class Price extends \Magento\Catalog\Model\ResourceModel\Product\Indexer\Price\SimpleProductPrice
{
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\Indexer\Price\Query\BaseFinalPrice $baseFinalPrice,
        \Magento\Catalog\Model\ResourceModel\Product\Indexer\Price\IndexTableStructureFactory $indexTableStructureFactory,
        \Magento\Catalog\Model\Indexer\Product\Price\TableMaintainer $tableMaintainer,
        \Magento\Catalog\Model\ResourceModel\Product\Indexer\Price\BasePriceModifier $basePriceModifier
    ) {
        parent::__construct(
            $baseFinalPrice,
            $indexTableStructureFactory,
            $tableMaintainer,
            $basePriceModifier,
            \Bydn\Giftcard\Model\Product\Type\Giftcard::TYPE_GIFTCARD // The correct product type for gift cards
        );
    }
}
