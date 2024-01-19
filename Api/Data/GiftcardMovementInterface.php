<?php

namespace Bydn\Giftcard\Api\Data;

interface GiftcardMovementInterface
{
    /**
     * Constants for the column names in the database table.
     */
    public const ID = 'id';
    public const CARD_ID = 'card_id';
    public const ORDER_ID = 'order_id';
    public const AMOUNT = 'amount';
    public const CONCEPT = 'concept';
    public const CREATED_AT = 'created_at';

    /**
     * Get movement ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get card ID
     *
     * @return int|null
     */
    public function getCardId();

    /**
     * Get order ID
     *
     * @return int|null
     */
    public function getOrderId();

    /**
     * Get movement amount
     *
     * @return float|null
     */
    public function getAmount();

    /**
     * Get movement description
     *
     * @return string|null
     */
    public function getConcept();

    /**
     * Get updated date
     *
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set card ID
     *
     * @param int $cardId
     * @return $this
     */
    public function setCardId($cardId);

    /**
     * Set order ID
     *
     * @param int $orderId
     * @return $this
     */
    public function setOrderId($orderId);

    /**
     * Set movement amount
     *
     * @param float $amount
     * @return $this
     */
    public function setAmount($amount);

    /**
     * Set movement description
     *
     * @param string $concept
     * @return $this
     */
    public function setConcept($concept);

    /**
     * Set updated date
     *
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt);
}
