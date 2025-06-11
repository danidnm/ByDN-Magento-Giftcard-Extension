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

namespace Bydn\Giftcard\Model\Total\Quote;

use Magento\SalesRule\Model\Quote\Discount as DiscountCollector;

class Giftcard extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{
    /**
     * Total code
     */
    public const COLLECTOR_TYPE_CODE = 'giftcard_discount';

    /**
     * @var \Bydn\Giftcard\Api\GiftcardRepositoryInterface
     */
    private $giftcardRepository;

    /**
     * @var \Bydn\Giftcard\Api\GiftcardQuoteRepositoryInterface
     */
    private $giftcardQuoteRepository;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * Amount total
     * @var double
     */
    private $amount = 0;

    /**
     * @var int
     */
    private $baseAmount = 0;

    /**
     * @param \Bydn\Giftcard\Api\GiftcardRepositoryInterface $giftcardRepository
     * @param \Bydn\Giftcard\Api\GiftcardQuoteRepositoryInterface $giftcardQuoteRepository
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Bydn\Giftcard\Api\GiftcardRepositoryInterface $giftcardRepository,
        \Bydn\Giftcard\Api\GiftcardQuoteRepositoryInterface $giftcardQuoteRepository,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->amount = 0;
        $this->baseAmount = 0;

        $this->setCode(self::COLLECTOR_TYPE_CODE);

        $this->giftcardRepository = $giftcardRepository;
        $this->giftcardQuoteRepository = $giftcardQuoteRepository;
        $this->logger = $logger;
    }

    /**
     * Returns quote grand total based on items row total
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @return array
     */
    private function getQuoteGrandTotal($quote)
    {
//        $this->logger->info('');

        // Iterate over the items and get the value
        $grandTotal = 0;
        $baseGrandTotal = 0;
        foreach ($quote->getAllVisibleItems() as $item) {
            $grandTotal += $item->getRowTotalInclTax();
            $baseGrandTotal += $item->getBaseRowTotalInclTax();
        }

        // Get shipping amount
        // FIXME: Check with discounts applied to the shipping amount
        // FIXME: Check with change on shipping amount in checkout (change from guest to customer logged in)
        $shippingAddress = $quote->getShippingAddress();
        $shippingAmount = $shippingAddress->getShippingAmount();
        $baseShippingAmount = $shippingAddress->getBaseShippingAmount();

        $this->logger->info('Items total: ' . $grandTotal);
        $this->logger->info('Shipping amount: ' . $shippingAmount);

        // Add shipping
        $grandTotal += $shippingAmount;
        $baseGrandTotal += $baseShippingAmount;

        $this->logger->info('Grand total: ' . $grandTotal);

        return [$grandTotal, $baseGrandTotal];
    }

    /**
     * Collect giftcard totals
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return $this
     */
    public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
        parent::collect($quote, $shippingAssignment, $total);

//        $this->logger->info(': Ini');

        // FIXME: Check user logging in at checkout and get in more products into the cart

        // Clear quote values
        $quote->getGiftcardDiscount(0);
        $quote->getGiftcardBaseDiscount(0);

        // No items => nothing to do
        $items = $shippingAssignment->getItems();
        if (!count($items)) {
            return $this;
        }

        // Get the giftcard applied and amount
        $appliedGiftcardCode = '';
        $appliedGiftcardAmount = 0;
        $appliedGiftcardBaseAmount = 0;
        $availableAmountInGiftcard = 0;
        $quoteExtension = $quote->getExtensionAttributes();
        $giftcardData = $quoteExtension->getGiftcardData();
        if ($giftcardData) {
            $appliedGiftcardCode = $giftcardData->getGiftcardCode();
            $appliedGiftcardAmount = $giftcardData->getGiftcardAmount();
            $appliedGiftcardBaseAmount = $giftcardData->getGiftcardBaseAmount();
        }
        if ($appliedGiftcardCode) {
            try {
                $giftcard = $this->giftcardRepository->getByCode($appliedGiftcardCode);
                $availableAmountInGiftcard = $giftcard->getAvailableAmount();
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                ;
            }
        }

        // Calculate quote totals including shipping and discounts
        list($quoteGrandTotal, $quoteBaseGrandTotal) = $this->getQuoteGrandTotal($quote);

        // Get quote total including shipping and excluding applied giftcard
        //$quoteGrandTotal = $quote->getGrandTotal() + $appliedGiftcardAmount /*+ $shippingAmount*/;
        //$quoteBaseGrandTotal = $quote->getBaseGrandTotal() + $appliedGiftcardBaseAmount /*+ $baseShippingAmount*/;

        //$this->logger->info(': Quote Shipping: ' . $shippingAmount);
        $this->logger->info('Quote Grand Total (quote): ' . $quote->getGrandTotal());
        $this->logger->info('Quote Grand Total (calculated): ' . $quoteGrandTotal);
        $this->logger->info('Giftcard Quote Amount: ' . $appliedGiftcardAmount);

        // FIXME for multiple currencies
        // Calculate the highest possible amount
        $this->amount = min($availableAmountInGiftcard, $quoteGrandTotal);
        $this->baseAmount = min($availableAmountInGiftcard, $quoteBaseGrandTotal);

        $this->logger->info(': Calculated Amount: ' . $this->amount);

        // Save in extension info
        if ($giftcardData) {
            $giftcardData->setGiftcardAmount($this->amount);
            $giftcardData->setGiftcardBaseAmount($this->baseAmount);
            $this->giftcardQuoteRepository->save($giftcardData);
        }

        $quote->getGiftcardDiscount((-1) * $this->amount);
        $quote->getGiftcardBaseDiscount((-1) * $this->baseAmount);

        $total->setTotalAmount(self::COLLECTOR_TYPE_CODE, (-1) * $this->amount);
        $total->setBaseTotalAmount(self::COLLECTOR_TYPE_CODE, (-1) * $this->baseAmount);

//        $total->setGiftcardDiscountAmount($this->amount);
//        $total->setBaseGiftcardDiscountAmount($this->baseAmount);

//        $total->setGrandTotal($total->getGrandTotal() + $this->amount);
//        $total->setBaseGrandTotal($total->getBaseGrandTotal() + $this->amount);

        return $this;
    }

    /**
     * Fetch giftcard total
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return array
     */
    public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total)
    {
        // Get the giftcard applied and amount
        $giftcardAmount = 0;
        $quoteExtension = $quote->getExtensionAttributes();
        $giftcardData = $quoteExtension->getGiftcardData();
        if ($giftcardData) {
            $giftcardAmount = $giftcardData->getGiftcardAmount();
        }

        return [
            'code' => $this->getCode(),
            'title' => 'Giftcard payment',
            'value' => (-1) * $giftcardAmount
        ];
    }

    /**
     * Returns label for the giftcard total
     *
     * @return \Magento\Framework\Phrase
     */
    public function getLabel()
    {
        return __('Giftcard payment');
    }
}
