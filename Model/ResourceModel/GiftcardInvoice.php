<?php

namespace Bydn\Giftcard\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class GiftcardInvoice extends AbstractDb
{
    /**
     * Resource model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('bydn_giftcard_invoice', 'id');
    }
}
