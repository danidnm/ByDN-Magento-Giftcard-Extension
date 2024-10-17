<?php

namespace Bydn\Giftcard\Model;

use Magento\Framework\Model\AbstractModel;

class GiftcardQuote extends AbstractModel implements \Bydn\Giftcard\Api\Data\GiftcardQuoteInterface
{
    /**
     * Internal constructor to initialize database table and key field

     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Bydn\Giftcard\Model\ResourceModel\GiftcardQuote::class);
        $this->setIdFieldName('id');
    }

    /**
     * Get quote ID
     *
     * @return int|null
     */
    public function getQuoteId()
    {
        return $this->getData(self::QUOTE_ID);
    }

    /**
     * Set quote ID
     *
     * @param int $quoteId
     * @return $this
     */
    public function setQuoteId($quoteId)
    {
        return $this->setData(self::QUOTE_ID, $quoteId);
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
