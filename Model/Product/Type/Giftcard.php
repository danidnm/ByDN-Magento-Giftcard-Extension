<?php

namespace Bydn\Giftcard\Model\Product\Type;

class Giftcard extends \Magento\Catalog\Model\Product\Type\AbstractType
{
    public const TYPE_GIFTCARD = 'giftcard';

    /**
     * Check is virtual product
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function isVirtual($product)
    {
        return true;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return void
     */
    public function deleteTypeSpecificData(\Magento\Catalog\Model\Product $product)
    {
        // Nothing to do
    }
}
