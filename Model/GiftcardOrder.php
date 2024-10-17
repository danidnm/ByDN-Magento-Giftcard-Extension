<?php

namespace Bydn\Giftcard\Model;

use Magento\Framework\Model\AbstractModel;

class GiftcardOrder extends AbstractModel implements \Bydn\Giftcard\Api\Data\GiftcardOrderInterface
{
    /**
     * Internal constructor to initialize database table and field

     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Bydn\Giftcard\Model\ResourceModel\GiftcardOrder::class);
        $this->setIdFieldName('id');
    }

    /**
     * Get order ID
     *
     * @return int|null
     */
    public function getOrderId()
    {
        return $this->getData(self::ORDER_ID);
    }

    /**
     * Set order ID
     *
     * @param int $orderId
     * @return $this
     */
    public function setOrderId($orderId)
    {
        return $this->setData(self::ORDER_ID, $orderId);
    }

    /**
     * Get giftcard amount
     *
     * @return float|null
     */
    public function getGiftcardAmount()
    {
        return $this->getData(self::GIFTCARD_AMOUNT);
    }

    /**
     * Set giftcard amount
     *
     * @param float $amount
     * @return $this
     */
    public function setGiftcardAmount($amount)
    {
        return $this->setData(self::GIFTCARD_AMOUNT, $amount);
    }

    /**
     * Get giftcard amount
     *
     * @return float|null
     */
    public function getGiftcardBaseAmount()
    {
        return $this->getData(self::GIFTCARD_BASE_AMOUNT);
    }

    /**
     * Set giftcard amount
     *
     * @param float $amount
     * @return $this
     */
    public function setGiftcardBaseAmount($amount)
    {
        return $this->setData(self::GIFTCARD_BASE_AMOUNT, $amount);
    }

    /**
     * Get giftcard code
     *
     * @return float|null
     */
    public function getGiftcardCode()
    {
        return $this->getData(self::GIFTCARD_CODE);
    }

    /**
     * Set giftcard amount
     *
     * @param string $giftcardCode
     * @return $this
     */
    public function setGiftcardCode($giftcardCode)
    {
        return $this->setData(self::GIFTCARD_CODE, $giftcardCode);
    }
}
