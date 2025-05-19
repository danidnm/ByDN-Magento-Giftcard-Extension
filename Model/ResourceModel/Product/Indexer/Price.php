<?php
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
            'giftcard' // The correct product type for gift cards
        );
    }
}