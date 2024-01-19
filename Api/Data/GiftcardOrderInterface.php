<?php

namespace Bydn\Giftcard\Api\Data;

interface GiftcardOrderInterface
{
    /**
     * Constants for the column names in the database table.
     */
    public const ID = 'id';
    public const ORDER_ID = 'order_id';
    public const GIFTCARD_CODE = 'giftcard_code';
    public const GIFTCARD_AMOUNT = 'giftcard_amount';
    public const GIFTCARD_BASE_AMOUNT = 'giftcard_base_amount';

    /**
     * Get movement ID
     *
     * @return int|null
     */
    public function getId();

    /**
     *
     * Get order ID
     *
     * @return int|null
     */
    public function getOrderId();

    /**
     * Get giftcard amount
     *
     * @return float|null
     */
    public function getGiftcardAmount();

    /**
     * Get giftcard amount
     *
     * @return float|null
     */
    public function getGiftcardBaseAmount();

    /**
     * Get giftcard code
     *
     * @return string|null
     */
    public function getGiftcardCode();

    /**
     * Set order ID
     *
     * @param int $orderId
     * @return $this
     */
    public function setOrderId($orderId);

    /**
     * Set giftcard amount
     *
     * @param float $amount
     * @return $this
     */
    public function setGiftcardAmount($amount);

    /**
     * Set giftcard amount
     *
     * @param float $amount
     * @return $this
     */
    public function setGiftcardBaseAmount($amount);

    /**
     * Set giftcard amount
     *
     * @param string $giftcardCode
     * @return $this
     */
    public function setGiftcardCode($giftcardCode);
}
