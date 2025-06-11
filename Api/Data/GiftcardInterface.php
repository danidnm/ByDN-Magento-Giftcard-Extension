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

namespace Bydn\Giftcard\Api\Data;

interface GiftcardInterface
{
    public const ID = 'id';
    public const CODE = 'code';
    public const TOTAL_AMOUNT = 'total_amount';
    public const AVAILABLE_AMOUNT = 'available_amount';
    public const CURRENCY_CODE = 'currency_code';
    public const ORDER_ID = 'order_id';
    public const ITEM_ID = 'item_id';
    public const SENDER_NAME = 'sender_name';
    public const FRIEND_NAME = 'friend_name';
    public const FRIEND_EMAIL = 'friend_email';
    public const FRIEND_MESSAGE = 'friend_message';
    public const EMAIL_SENT = 'email_sent';
    public const STATUS = 'status';
    public const EMAIL_DATE = 'email_date';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';
    public const EXPIRE_AT = 'expire_at';

    /**
     * Get the card ID.
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get the card code.
     *
     * @return string|null
     */
    public function getCode();

    /**
     * Get the original amount of the gift card.
     *
     * @return float|null
     */
    public function getTotalAmount();

    /**
     * Get the available amount of the gift card.
     *
     * @return float|null
     */
    public function getAvailableAmount();

    /**
     * Get the currency code of the gift card.
     *
     * @return string|null
     */
    public function getCurrencyCode();

    /**
     * Get the order ID associated with the gift card.
     *
     * @return int|null
     */
    public function getOrderId();

    /**
     * Get the item ID associated with the gift card.
     *
     * @return int|null
     */
    public function getItemId();

    /**
     * Get the sender's name.
     *
     * @return string|null
     */
    public function getSenderName();

    /**
     * Get the friend's name.
     *
     * @return string|null
     */
    public function getFriendName();

    /**
     * Get the friend's email.
     *
     * @return string|null
     */
    public function getFriendEmail();

    /**
     * Get the friend's message.
     *
     * @return string|null
     */
    public function getFriendMessage();

    /**
     * Get the email sent flag.
     *
     * @return int
     */
    public function getEmailSent();

    /**
     * Get the card status.
     *
     * @return int
     */
    public function getStatus();

    /**
     * Get the date of the email.
     *
     * @return string|null
     */
    public function getEmailDate();

    /**
     * Get the creation date.
     *
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Get the update date.
     *
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * Get the expiration date.
     *
     * @return string|null
     */
    public function getExpireAt();

    /**
     * Set the card code.
     *
     * @param string $code
     * @return $this
     */
    public function setCode($code);

    /**
     * Set the original amount of the gift card.
     *
     * @param float $totalAmount
     * @return $this
     */
    public function setTotalAmount($totalAmount);

    /**
     * Set the available amount of the gift card.
     *
     * @param float $availableAmount
     * @return $this
     */
    public function setAvailableAmount($availableAmount);

    /**
     * Set the currency code of the gift card.
     *
     * @param string $currencyCode
     * @return $this
     */
    public function setCurrencyCode($currencyCode);

    /**
     * Set the order ID associated with the gift card.
     *
     * @param int $orderId
     * @return $this
     */
    public function setOrderId($orderId);

    /**
     * Set the item ID associated with the gift card.
     *
     * @param int $itemId
     * @return $this
     */
    public function setItemId($itemId);

    /**
     * Set the sender's name.
     *
     * @param string $senderName
     * @return $this
     */
    public function setSenderName($senderName);

    /**
     * Set the friend's name.
     *
     * @param string $friendName
     * @return $this
     */
    public function setFriendName($friendName);

    /**
     * Set the friend's email.
     *
     * @param string $friendEmail
     * @return $this
     */
    public function setFriendEmail($friendEmail);

    /**
     * Set the friend's message.
     *
     * @param string $friendMessage
     * @return $this
     */
    public function setFriendMessage($friendMessage);

    /**
     * Set the email sent flag.
     *
     * @param int $emailSent
     * @return $this
     */
    public function setEmailSent($emailSent);

    /**
     * Set the card status.
     *
     * @param int $status
     * @return $this
     */
    public function setStatus($status);

    /**
     * Set the date of the email.
     *
     * @param string $emailDate
     * @return $this
     */
    public function setEmailDate($emailDate);

    /**
     * Set the creation date.
     *
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt);

    /**
     * Set the update date.
     *
     * @param string $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt);

    /**
     * Set the expiration date.
     *
     * @param string $expireAt
     * @return $this
     */
    public function setExpireAt($expireAt);
}
