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

namespace Bydn\Giftcard\Model\Total\Order\Invoice;

use Magento\Sales\Model\OrderFactory;

class Giftcard extends \Magento\Sales\Model\Order\Invoice\Total\AbstractTotal
{
    /**
     * @var Magento\Sales\Model\ResourceModel\Order|\Magento\Sales\Model\ResourceModel\Order
     */
    private $orderResourceModel;

    /**
     * @var Magento\Sales\Model\OrderFactory|OrderFactory
     */
    private $orderFactory;

    /**
     * @var \Bydn\Giftcard\Model\GiftcardOrderFactory
     */
    private $giftcardOrderFactory;

    /**
     * @var \Bydn\Giftcard\Model\ResourceModel\GiftcardOrder
     */
    private $giftcardOrderResource;

    /**
     * @var \Bydn\Giftcard\Model\GiftcardInvoiceFactory
     */
    private $giftcardInvoiceFactory;

    /**
     * @var \Bydn\Giftcard\Api\GiftcardInvoiceRepositoryInterface
     */
    private $giftcardInvoiceRepository;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Bydn\Giftcard\Model\GiftcardOrder|null
     */
    private $giftcardData;

    /**
     * @param \Magento\Sales\Model\ResourceModel\Order $orderResourceModel
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Bydn\Giftcard\Model\GiftcardOrderFactory $giftcardOrderFactory
     * @param \Bydn\Giftcard\Model\ResourceModel\GiftcardOrder $giftcardOrderResource
     * @param \Bydn\Giftcard\Model\GiftcardInvoiceFactory $giftcardInvoiceFactory
     * @param \Bydn\Giftcard\Api\GiftcardInvoiceRepositoryInterface $giftcardInvoiceRepository
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Sales\Model\ResourceModel\Order $orderResourceModel,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Bydn\Giftcard\Model\GiftcardOrderFactory $giftcardOrderFactory,
        \Bydn\Giftcard\Model\ResourceModel\GiftcardOrder $giftcardOrderResource,
        \Bydn\Giftcard\Model\GiftcardInvoiceFactory $giftcardInvoiceFactory,
        \Bydn\Giftcard\Api\GiftcardInvoiceRepositoryInterface $giftcardInvoiceRepository,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->orderResourceModel = $orderResourceModel;
        $this->orderFactory = $orderFactory;
        $this->giftcardOrderFactory = $giftcardOrderFactory;
        $this->giftcardOrderResource = $giftcardOrderResource;
        $this->giftcardInvoiceFactory = $giftcardInvoiceFactory;
        $this->giftcardInvoiceRepository = $giftcardInvoiceRepository;
        $this->logger = $logger;
        parent::__construct();
    }

    /**
     * Returns giftcard associated data in the order
     *
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     * @return \Bydn\Giftcard\Api\Data\GiftcardOrderInterface
     */
    public function getOrderGiftcardData(\Magento\Sales\Model\Order\Invoice $invoice)
    {
        // If invoice is done at the same time as the order (payment method "does not need payment"), the order still
        // don't have an order ID, but the extension attributes are still set
        $order = $invoice->getOrder();
        if ($order) {
            $extensionAttributes = $order->getExtensionAttributes();
            if ($extensionAttributes) {
                $this->giftcardData = $extensionAttributes->getGiftcardData();
            }
        }

        // These cases cover the invoice created after the order is already saved (i.e. from the backoffice)
        if ($this->giftcardData == null && $invoice->getOrderId()) {
            $orderId = $invoice->getOrderId();
            $this->giftcardData = $this->giftcardOrderFactory->create();
            $this->giftcardOrderResource->load($this->giftcardData, $orderId, 'order_id');
        }

        return $this->giftcardData;
    }

    /**
     * Returns the giftcard amount used in the invoice
     *
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     * @return array
     */
    public function getInvoicedGiftcardAmount(\Magento\Sales\Model\Order\Invoice $invoice)
    {
        // If invoice is done at the same time as the order (payment method "does not need payment"), the order still
        // don't have an order ID, but the extension attributes are still set
        /** @var \Magento\Sales\Model\Order $order */
        $order = $invoice->getOrder();
        if (!$order && $invoice->getOrderId()) {
            $orderId = $invoice->getOrderId();
            $order = $this->orderFactory->create();
            $order = $this->orderResourceModel->load($order, $orderId);
        }

        // Get order and substract invoices data
        $amount = 0;
        $baseAmount = 0;
        $invoices = $order->getInvoiceCollection();
        foreach ($invoices as $invoice) {
            $extensionAttributes = $invoice->getExtensionAttributes();
            $invoiceGiftcardData = $extensionAttributes->getGiftcardData();
            $amount += $invoiceGiftcardData->getGiftcardAmount();
            $baseAmount += $invoiceGiftcardData->getGiftcardBaseAmount();
        }

        return [$amount, $baseAmount];
    }

    /**
     * Collects gift card total for the invoice
     *
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     * @return $this
     */
    public function collect(
        \Magento\Sales\Model\Order\Invoice $invoice
    ) {
        // Read giftcard applied amount from order extension attributes
        $orderGiftcardData = $this->getOrderGiftcardData($invoice);
        if (!$orderGiftcardData) {
            return $this;
        }
        $orderCodeApplied = $orderGiftcardData->getGiftcardCode();
        $orderGiftcardAmount = $orderGiftcardData->getGiftcardAmount();
        $orderGiftcardBaseAmount = $orderGiftcardData->getGiftcardBaseAmount();

        // Get invoiced amount and pending
        list($invoicedAmount, $invoicedBaseAmount) = $this->getInvoicedGiftcardAmount($invoice);
        $amountPendingToInvoice = $orderGiftcardAmount - $invoicedAmount;
        $baseAmountPendingToInvoice = $orderGiftcardBaseAmount - $invoicedBaseAmount;

        // Calculate the amount to invoice (all possible in the first invoice)
        $amountToInvoice = min($amountPendingToInvoice, $invoice->getGrandTotal());
        $baseAmountToInvoice = min($baseAmountPendingToInvoice, $invoice->getBaseGrandTotal());

        // Do nothing if zero
        if (empty($amountToInvoice) || ($amountToInvoice < 0.01)) {
            return $this;
        }

        // Save in invoice extension attributes
        $invoiceExtension = $invoice->getExtensionAttributes();
        $invoiceGiftcardData = $invoiceExtension->getGiftcardData();
        if (!$invoiceGiftcardData) {
            $invoiceGiftcardData = $this->giftcardInvoiceFactory->create();
        }
        // Set data. Invoice ID will be set on save.
        $invoiceGiftcardData->setGiftcardCode($orderCodeApplied);
        $invoiceGiftcardData->setGiftcardAmount($amountToInvoice);
        $invoiceGiftcardData->setGiftcardBaseAmount($baseAmountToInvoice);
        $invoiceExtension->setGiftcardData($invoiceGiftcardData);
        $invoice->setExtensionAttributes($invoiceExtension);

        // Calculate new grand totals
        $grandTotal = $invoice->getGrandTotal() - $amountToInvoice;
        $baseGrandTotal = $invoice->getBaseGrandTotal() - $baseAmountToInvoice;

        // Set new totals to the invoice
        $invoice->setGrandTotal($grandTotal);
        $invoice->setBaseGrandTotal($baseGrandTotal);

        return $this;
    }
}
