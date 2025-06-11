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

interface GiftcardCreditmemoInterface
{
    /**
     * Constants for the column names in the database table.
     */
    public const ID = 'id';
    public const CREDITMEMO_ID = 'creditmemo_id';
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
     * Get creditmemo ID
     *
     * @return int|null
     */
    public function getCreditmemoId();

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
     * Set creditmemo ID
     *
     * @param int $creditmemoId
     * @return $this
     */
    public function setCreditmemoId($creditmemoId);

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
