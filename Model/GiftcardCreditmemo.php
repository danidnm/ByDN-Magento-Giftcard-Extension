<?php

namespace Bydn\Giftcard\Model;

use Magento\Framework\Model\AbstractModel;

class GiftcardCreditmemo extends AbstractModel implements \Bydn\Giftcard\Api\Data\GiftcardCreditmemoInterface
{
    /**
     * Internal constructor to initialize database table and key field
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Bydn\Giftcard\Model\ResourceModel\GiftcardCreditmemo::class);
        $this->setIdFieldName('id');
    }

    /**
     * Get creditmemo ID
     *
     * @return int|null
     */
    public function getCreditmemoId()
    {
        return $this->getData(self::CREDITMEMO_ID);
    }

    /**
     * Set creditmemo ID
     *
     * @param int $creditmemoId
     * @return $this
     */
    public function setCreditmemoId($creditmemoId)
    {
        return $this->setData(self::CREDITMEMO_ID, $creditmemoId);
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
