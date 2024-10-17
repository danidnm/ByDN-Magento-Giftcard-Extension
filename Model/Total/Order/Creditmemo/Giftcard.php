<?php

namespace Bydn\Giftcard\Model\Total\Order\Creditmemo;

class Giftcard extends \Magento\Sales\Model\Order\Total\AbstractTotal
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    private $currency;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    private $orderFactory;
    /**
     * @var \Magento\Sales\Model\ResourceModel\Order
     */
    private $orderResourceModel;

    /**
     * @var \Magento\Sales\Model\Order\InvoiceFactory
     */
    private $invoiceFactory;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Invoice
     */
    private $invoiceResourceModel;

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
     * @var \Bydn\Giftcard\Model\GiftcardCreditmemoFactory
     */
    private $giftcardCreditmemoFactory;

    /**
     * @var \Bydn\Giftcard\Api\GiftcardCreditmemoRepositoryInterface
     */
    private $giftcardCreditmemoRepository;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Magento\Sales\Model\Order
     */
    private $order;

    /**
     * @var \Magento\Sales\Model\Order\Invoice
     */
    private $invoice;

    /**
     * @var \Bydn\Giftcard\Model\GiftcardOrder|null
     */
    private $giftcardData;

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $currency
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Sales\Model\ResourceModel\Order $orderResourceModel
     * @param \Magento\Sales\Model\Order\InvoiceFactory $invoiceFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Invoice $invoiceResourceModel
     * @param \Bydn\Giftcard\Model\GiftcardOrderFactory $giftcardOrderFactory
     * @param \Bydn\Giftcard\Model\ResourceModel\GiftcardOrder $giftcardOrderResource
     * @param \Bydn\Giftcard\Model\GiftcardInvoiceFactory $giftcardInvoiceFactory
     * @param \Bydn\Giftcard\Api\GiftcardInvoiceRepositoryInterface $giftcardInvoiceRepository
     * @param \Bydn\Giftcard\Model\GiftcardCreditmemoFactory $giftcardCreditmemoFactory
     * @param \Bydn\Giftcard\Api\GiftcardCreditmemoRepositoryInterface $giftcardCreditmemoRepository
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Pricing\PriceCurrencyInterface $currency,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Sales\Model\ResourceModel\Order $orderResourceModel,
        \Magento\Sales\Model\Order\InvoiceFactory $invoiceFactory,
        \Magento\Sales\Model\ResourceModel\Order\Invoice $invoiceResourceModel,
        \Bydn\Giftcard\Model\GiftcardOrderFactory $giftcardOrderFactory,
        \Bydn\Giftcard\Model\ResourceModel\GiftcardOrder $giftcardOrderResource,
        \Bydn\Giftcard\Model\GiftcardInvoiceFactory $giftcardInvoiceFactory,
        \Bydn\Giftcard\Api\GiftcardInvoiceRepositoryInterface $giftcardInvoiceRepository,
        \Bydn\Giftcard\Model\GiftcardCreditmemoFactory $giftcardCreditmemoFactory,
        \Bydn\Giftcard\Api\GiftcardCreditmemoRepositoryInterface $giftcardCreditmemoRepository,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->request = $request;
        $this->currency = $currency;
        $this->orderFactory = $orderFactory;
        $this->orderResourceModel = $orderResourceModel;
        $this->invoiceFactory = $invoiceFactory;
        $this->invoiceResourceModel = $invoiceResourceModel;
        $this->giftcardOrderFactory = $giftcardOrderFactory;
        $this->giftcardOrderResource = $giftcardOrderResource;
        $this->giftcardInvoiceFactory = $giftcardInvoiceFactory;
        $this->giftcardInvoiceRepository = $giftcardInvoiceRepository;
        $this->giftcardCreditmemoFactory = $giftcardCreditmemoFactory;
        $this->giftcardCreditmemoRepository = $giftcardCreditmemoRepository;
        $this->logger = $logger;
        parent::__construct();
    }

    /**
     * Loads current order from creditmemo
     *
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @return void
     */
    private function prepareOrder($creditmemo)
    {
        $this->order = $creditmemo->getOrder();
        if (!$this->order && $creditmemo->getOrderId()) {
            $orderId = $creditmemo->getOrderId();
            $this->order = $this->orderFactory->create();
            $this->order = $this->orderResourceModel->load($this->order, $orderId);
        }
    }

    /**
     * Loads current invoice from creditmemo
     *
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @return void
     */
    private function prepareInvoice($creditmemo)
    {
        $this->invoice = $creditmemo->getInvoice();
        if (!$this->invoice && $creditmemo->getInvoiceId()) {
            $invoiceId = $creditmemo->getInvoiceId();
            $this->invoice = $this->invoiceFactory->create();
            $this->invoice = $this->invoiceResourceModel->load($this->invoice, $invoiceId);
        }
    }

    /**
     * Returns max amounts to be refunded (store and base)
     *
     * @return array
     */
    private function getMaxTotals()
    {

        if ($this->invoice) {
            return [$this->invoice->getGrandTotal(), $this->invoice->getBaseGrandTotal()];
        }

        return [$this->order->getGrandTotal(), $this->order->getBaseGrandTotal()];
    }

    /**
     * Returns giftcard associated data in the order
     * If we are creating the credit memo from an invoice, it will return the giftcard amount associated to the invoice
     * If we are creating the credit memo from an order, it will return the total giftcard amount in the order
     *
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @return void
     */
    public function prepareOrderGiftcardData(\Magento\Sales\Model\Order\Creditmemo $creditmemo)
    {
        // From order
        $order = $creditmemo->getOrder();
        if ($order) {
            $orderExtension = $order->getExtensionAttributes();
            if ($orderExtension) {
                $this->giftcardData = $orderExtension->getGiftcardData();
            }
        }
    }

    /**
     * Read the posted giftcard amount if there is any
     *
     * @return array
     */
    private function getPostedGiftcardAmount()
    {

        // Get post data if any
        $postedAmount = null;
        $postedBaseAmount = null;
        $postedTotals = $this->request->getParam('creditmemo');
        if ($postedTotals) {
            $postedAmount = $postedTotals['giftcard_amount'] ?? 0;
            $postedBaseAmount = $this->currency->convert($postedAmount);
        }

        return [$postedAmount, $postedBaseAmount];
    }

    /**
     * Collect giftcard total for the creditmemo
     *
     * @param \Magento\Sales\Model\Order\Invoice $creditmemo
     * @return $this
     */
    public function collect(
        \Magento\Sales\Model\Order\Creditmemo $creditmemo
    ) {
        // Read giftcard applied amount from order extension attributes
        $this->prepareOrderGiftcardData($creditmemo);
        if (!$this->giftcardData) {
            return $this;
        }
        $codeApplied = $this->giftcardData->getGiftcardCode();
        $giftcardAmount = $this->giftcardData->getGiftcardAmount();
        $giftcardBaseAmount = $this->giftcardData->getGiftcardBaseAmount();

        // Do nothing if zero
        if (empty($giftcardAmount) || ($giftcardAmount < 0.01)) {
            return $this;
        }

        // Prepare more data
        $this->prepareOrder($creditmemo);
        $this->prepareInvoice($creditmemo);

        // Get order or invoice total paid
        list ($maxGrandTotal, $maxBaseGrandTotal) = $this->getMaxTotals();

        // Get post data if any
        list ($postedAmount, $postedBaseAmount) = $this->getPostedGiftcardAmount();

        // Not more than the posted amount (if posted)
        if ($postedAmount !== null) {
            $giftcardAmount = min($giftcardAmount, $postedAmount);
        }

        // Not more than the memo amount
        $giftcardAmount = min($giftcardAmount, $creditmemo->getSubtotal());
        $giftcardBaseAmount = min($giftcardBaseAmount, $creditmemo->getBaseSubtotal());

        // Not less than the difference between current subtotal and paid
        $giftcardAmount = max($giftcardAmount, ($creditmemo->getGrandTotal() - $maxGrandTotal));
        $giftcardBaseAmount = max($giftcardBaseAmount, ($creditmemo->getBaseGrandTotal() - $maxBaseGrandTotal));

        // Save in creditmemo extension attributes
        $creditmemoExtension = $creditmemo->getExtensionAttributes();
        $creditmemoGiftcardData = $creditmemoExtension->getGiftcardData();
        if (!$creditmemoGiftcardData) {
            $creditmemoGiftcardData = $this->giftcardCreditmemoFactory->create();
        }
        // Set data. Creditmemo ID will be set on save.
        $creditmemoGiftcardData->setGiftcardCode($codeApplied);
        $creditmemoGiftcardData->setGiftcardAmount($giftcardAmount);
        $creditmemoGiftcardData->setGiftcardBaseAmount($giftcardBaseAmount);
        $creditmemoExtension->setGiftcardData($creditmemoGiftcardData);
        $creditmemo->setExtensionAttributes($creditmemoExtension);

        // Calculate new grand totals
        $grandTotal = $creditmemo->getGrandTotal() - $giftcardAmount;
        $baseGrandTotal = $creditmemo->getBaseGrandTotal() - $giftcardBaseAmount;

        // Set new totals to the creditmemo
        $creditmemo->setGrandTotal($grandTotal);
        $creditmemo->setBaseGrandTotal($baseGrandTotal);

        return $this;
    }
}
