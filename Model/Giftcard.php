<?php

namespace Bydn\Giftcard\Model;

use Magento\Framework\Model\AbstractModel;

class Giftcard extends AbstractModel implements \Bydn\Giftcard\Api\Data\GiftcardInterface
{
    public const GIFTCARD_PENDING = 0;
    public const GIFTCARD_ACTIVE = 1;
    public const GIFTCARD_USED = 2;
    public const GIFTCARD_EXPIRED = 3;
    public const GIFTCARD_CANCELED = 4;

    /**
     * Internal constructor to initialize database table and field
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Bydn\Giftcard\Model\ResourceModel\Giftcard::class);
        $this->setIdFieldName('id');
    }

    /**
     * Get the card ID.
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * Get the card code.
     *
     * @return string|null
     */
    public function getCode()
    {
        return $this->getData(self::CODE);
    }

    /**
     * Get the original amount of the gift card.
     *
     * @return float|null
     */
    public function getTotalAmount()
    {
        return $this->getData(self::TOTAL_AMOUNT);
    }

    /**
     * Get the available amount of the gift card.
     *
     * @return float|null
     */
    public function getAvailableAmount()
    {
        return $this->getData(self::AVAILABLE_AMOUNT);
    }

    /**
     * Get the currency code of the gift card.
     *
     * @return string|null
     */
    public function getCurrencyCode()
    {
        return $this->getData(self::CURRENCY_CODE);
    }

    /**
     * Get the order ID associated with the gift card.
     *
     * @return int|null
     */
    public function getOrderId()
    {
        return $this->getData(self::ORDER_ID);
    }

    /**
     * Get the item ID associated with the gift card.
     *
     * @return int|null
     */
    public function getItemId()
    {
        return $this->getData(self::ITEM_ID);
    }

    /**
     * Get the sender's name.
     *
     * @return string|null
     */
    public function getSenderName()
    {
        return $this->getData(self::SENDER_NAME);
    }

    /**
     * Get the friend's name.
     *
     * @return string|null
     */
    public function getFriendName()
    {
        return $this->getData(self::FRIEND_NAME);
    }

    /**
     * Get the friend's email.
     *
     * @return string|null
     */
    public function getFriendEmail()
    {
        return $this->getData(self::FRIEND_EMAIL);
    }

    /**
     * Get the friend's message.
     *
     * @return string|null
     */
    public function getFriendMessage()
    {
        return $this->getData(self::FRIEND_MESSAGE);
    }

    /**
     * Get the email sent flag.
     *
     * @return int
     */
    public function getEmailSent()
    {
        return $this->getData(self::EMAIL_SENT);
    }

    /**
     * Get the card status.
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * Get the date of the email.
     *
     * @return string|null
     */
    public function getEmailDate()
    {
        return $this->getData(self::EMAIL_DATE);
    }

    /**
     * Get the creation date.
     *
     * @return string|null
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * Get the update date.
     *
     * @return string|null
     */
    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * Get the expiration date.
     *
     * @return string|null
     */
    public function getExpireAt()
    {
        return $this->getData(self::EXPIRE_AT);
    }

    /**
     * Set the card code.
     *
     * @param string $code
     * @return $this
     */
    public function setCode($code)
    {
        return $this->setData(self::CODE, $code);
    }

    /**
     * Set the original amount of the gift card.
     *
     * @param float $totalAmount
     * @return $this
     */
    public function setTotalAmount($totalAmount)
    {
        return $this->setData(self::TOTAL_AMOUNT, $totalAmount);
    }

    /**
     * Set the available amount of the gift card.
     *
     * @param float $availableAmount
     * @return $this
     */
    public function setAvailableAmount($availableAmount)
    {
        return $this->setData(self::AVAILABLE_AMOUNT, $availableAmount);
    }

    /**
     * Set the currency code of the gift card.
     *
     * @param string $currencyCode
     * @return $this
     */
    public function setCurrencyCode($currencyCode)
    {
        return $this->setData(self::CURRENCY_CODE, $currencyCode);
    }

    /**
     * Set the order ID associated with the gift card.
     *
     * @param int $orderId
     * @return $this
     */
    public function setOrderId($orderId)
    {
        return $this->setData(self::ORDER_ID, $orderId);
    }

    /**
     * Set the item ID associated with the gift card.
     *
     * @param int $orderId
     * @return $this
     */
    public function setItemId($orderId)
    {
        return $this->setData(self::ITEM_ID, $orderId);
    }

    /**
     * Set the sender's name.
     *
     * @param string $senderName
     * @return $this
     */
    public function setSenderName($senderName)
    {
        return $this->setData(self::SENDER_NAME, $senderName);
    }

    /**
     * Set the friend's name.
     *
     * @param string $friendName
     * @return $this
     */
    public function setFriendName($friendName)
    {
        return $this->setData(self::FRIEND_NAME, $friendName);
    }

    /**
     * Set the friend's email.
     *
     * @param string $friendEmail
     * @return $this
     */
    public function setFriendEmail($friendEmail)
    {
        return $this->setData(self::FRIEND_EMAIL, $friendEmail);
    }

    /**
     * Set the friend's message.
     *
     * @param string $friendMessage
     * @return $this
     */
    public function setFriendMessage($friendMessage)
    {
        return $this->setData(self::FRIEND_MESSAGE, $friendMessage);
    }

    /**
     * Set the email sent flag.
     *
     * @param int $emailSent
     * @return $this
     */
    public function setEmailSent($emailSent)
    {
        return $this->setData(self::EMAIL_SENT, $emailSent);
    }

    /**
     * Set the card status.
     *
     * @param int $status
     * @return $this
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Set the date of the email.
     *
     * @param string $emailDate
     * @return $this
     */
    public function setEmailDate($emailDate)
    {
        return $this->setData(self::EMAIL_DATE, $emailDate);
    }

    /**
     * Set the creation date.
     *
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * Set the update date.
     *
     * @param string $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }

    /**
     * Set the expiration date.
     *
     * @param string $expireAt
     * @return $this
     */
    public function setExpireAt($expireAt)
    {
        return $this->setData(self::EXPIRE_AT, $expireAt);
    }
}
