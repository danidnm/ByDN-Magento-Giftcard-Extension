<?php

namespace Bydn\Giftcard\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class GiftcardMovement extends AbstractDb
{
    /**
     * Resource model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('bydn_giftcard_movement', 'id');
    }
}
