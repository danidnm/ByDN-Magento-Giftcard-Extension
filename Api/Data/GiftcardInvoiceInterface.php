<?php

namespace Bydn\Giftcard\Api\Data;

interface GiftcardInvoiceInterface
{
    /**
     * Constants for the column names in the database table.
     */
    public const ID = 'id';
    public const INVOICE_ID = 'invoice_id';
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
     * Get invoice ID
     *
     * @return int|null
     */
    public function getInvoiceId();

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
     * Set invoice ID
     *
     * @param int $invoiceId
     * @return $this
     */
    public function setInvoiceId($invoiceId);

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
