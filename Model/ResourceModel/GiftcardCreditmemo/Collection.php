<?php

namespace Bydn\Giftcard\Model\ResourceModel\GiftcardCreditmemo;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * Initialize resource model and model for the collection
     *
     * @return void
     */
    protected function _construct()
    {
        // Initializing the model and resource model for the collection
        $this->_init(
            \Bydn\Giftcard\Model\GiftcardCreditmemo::class,
            \Bydn\Giftcard\Model\ResourceModel\GiftcardCreditmemo::class
        );
    }
}
