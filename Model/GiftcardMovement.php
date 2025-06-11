<?php
/**
 * @package     Bydn_Giftcard
 * @author      Daniel Navarro <https://github.com/danidnm>
 * @license     GPL-3.0-or-later
 * @copyright   Copyright (c) 2025 Daniel Navarro
 *
 * This file is part of a free software package licensed under the
 * GNU General Public License v3.0.
 * You may redistribute and/or modify it under the same license.
 */

namespace Bydn\Giftcard\Model;

use Magento\Framework\Model\AbstractModel;

class GiftcardMovement extends AbstractModel implements \Bydn\Giftcard\Api\Data\GiftcardMovementInterface
{
    /**
     * Internal constructor to initialize database table and key field
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Bydn\Giftcard\Model\ResourceModel\GiftcardMovement::class);
        $this->setIdFieldName('id');
    }

    /**
     * Get movement ID
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * Set movement ID
     *
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * Get card ID
     *
     * @return int|null
     */
    public function getCardId()
    {
        return $this->getData(self::CARD_ID);
    }

    /**
     * Set card ID
     *
     * @param int $cardId
     * @return $this
     */
    public function setCardId($cardId)
    {
        return $this->setData(self::CARD_ID, $cardId);
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
     * Get movement amount
     *
     * @return float|null
     */
    public function getAmount()
    {
        return $this->getData(self::AMOUNT);
    }

    /**
     * Set movement amount
     *
     * @param float $amount
     * @return $this
     */
    public function setAmount($amount)
    {
        return $this->setData(self::AMOUNT, $amount);
    }

    /**
     * Get movement description
     *
     * @return string|null
     */
    public function getConcept()
    {
        return $this->getData(self::CONCEPT);
    }

    /**
     * Set movement description
     *
     * @param string $concept
     * @return $this
     */
    public function setConcept($concept)
    {
        return $this->setData(self::CONCEPT, $concept);
    }

    /**
     * Get updated date
     *
     * @return string|null
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * Set updated date
     *
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }
}
