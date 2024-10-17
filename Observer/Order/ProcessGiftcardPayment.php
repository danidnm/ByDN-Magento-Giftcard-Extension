<?php

namespace Bydn\Giftcard\Observer\Order;

class ProcessGiftcardPayment implements \Magento\Framework\Event\ObserverInterface
{
    public const CONCEPT_KEY = 'Spent';

    /**
     * @var \Magento\Directory\Model\CurrencyFactory
     */
    private $currencyFactory;

    /**
     * @var \Bydn\Giftcard\Helper\Config
     */
    private $giftcardConfig;

    /**
     * @var \Bydn\Giftcard\Model\ResourceModel\GiftcardMovement\CollectionFactory
     */
    private $giftcardMovementCollectionFactory;

    /**
     * @var \Bydn\Giftcard\Model\ResourceModel\Giftcard
     */
    private $giftcardResource;

    /**
     * @var \Bydn\Giftcard\Model\GiftcardFactory
     */
    private $giftcardFactory;

    /**
     * @var \Bydn\Giftcard\Model\GiftcardRepository
     */
    private $giftcardRepository;

    /**
     * @var \Bydn\Giftcard\Model\ResourceModel\GiftcardMovement
     */
    private $giftcardMovementResource;

    /**
     * @var \Bydn\Giftcard\Model\GiftcardMovementFactory
     */
    private $giftcardMovementFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Bydn\Giftcard\Helper\Config $giftcardConfig
     * @param \Bydn\Giftcard\Model\ResourceModel\GiftcardMovement\CollectionFactory $giftcardMovementCollectionFactory
     * @param \Bydn\Giftcard\Model\ResourceModel\Giftcard $giftcardResource
     * @param \Bydn\Giftcard\Model\GiftcardFactory $giftcardFactory
     * @param \Bydn\Giftcard\Model\GiftcardRepository $giftcardRepository
     * @param \Bydn\Giftcard\Model\ResourceModel\GiftcardMovement $giftcardMovementResource
     * @param \Bydn\Giftcard\Model\GiftcardMovementFactory $giftcardMovementFactory
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Bydn\Giftcard\Helper\Config $giftcardConfig,
        \Bydn\Giftcard\Model\ResourceModel\GiftcardMovement\CollectionFactory $giftcardMovementCollectionFactory,
        \Bydn\Giftcard\Model\ResourceModel\Giftcard $giftcardResource,
        \Bydn\Giftcard\Model\GiftcardFactory $giftcardFactory,
        \Bydn\Giftcard\Model\GiftcardRepository $giftcardRepository,
        \Bydn\Giftcard\Model\ResourceModel\GiftcardMovement $giftcardMovementResource,
        \Bydn\Giftcard\Model\GiftcardMovementFactory $giftcardMovementFactory,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->currencyFactory = $currencyFactory;
        $this->giftcardConfig = $giftcardConfig;
        $this->giftcardMovementCollectionFactory = $giftcardMovementCollectionFactory;
        $this->giftcardResource = $giftcardResource;
        $this->giftcardFactory = $giftcardFactory;
        $this->giftcardRepository = $giftcardRepository;
        $this->giftcardMovementResource = $giftcardMovementResource;
        $this->giftcardMovementFactory = $giftcardMovementFactory;
        $this->logger = $logger;
    }

    /**
     * Check if giftcard has been used and discount the amount from balance
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->logger->info('Ini');

        // Get order
        $order = $observer->getEvent()->getOrder();
        if ($order) {

            $this->logger->info('Processing order: ' . $order->getId());

            // Get giftcard data
            $extensionAttributes = $order->getExtensionAttributes();
            $giftcardData = $extensionAttributes->getGiftcardData();
            if ($giftcardData) {

                // Extract amount and code
                $giftcardAmout = $giftcardData->getGiftcardAmount();
                $giftcardCode = $giftcardData->getGiftcardCode();
                if ($giftcardCode) {

                    $this->logger->info('Has giftcard applied: ' . $giftcardCode);

                    // Check the movement still does not exists
                    if (!$this->movementExists($order->getId())) {

                        // Create movement and adjust the card
                        $this->createMovement($order, $giftcardCode, $giftcardAmout);
                    }
                }
            }
        }

        $this->logger->info('End');
    }

    /**
     * Checks if a movement is already annotated in the database
     *
     * @param int $orderId
     * @return false
     */
    private function movementExists($orderId)
    {
        $collection = $this->giftcardMovementCollectionFactory->create();
        $collection->addFieldToFilter('order_id', $orderId);
        $collection->addFieldToFilter('concept', ['like' => '%' . self::CONCEPT_KEY . '%']);
        return (count($collection) > 0);
    }

    /**
     * Creates a movement for the giftcard and adjust its balance
     *
     * @param \Magento\Sales\Model\Order $order
     * @param string $giftcardCode
     * @param mixed $giftcardAmout
     * @return void
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createMovement($order, $giftcardCode, $giftcardAmout)
    {
        $this->logger->info('Saving movement for giftcard: ' . $giftcardCode . ' with value: ' . $giftcardAmout);

        // Get the giftcard instance
        /** @var \Bydn\Giftcard\Model\Giftcard $giftcard */
        $giftcard = $this->giftcardRepository->getByCode($giftcardCode);
        if (!$giftcard) {
            $this->logger->info(': GIFTCARD ALERT: Applied giftcard dont exists in order ' . $order->getIncrementId());
            return;
        }

        // Convert currency if needed
        $giftcardAmountAdjustedWithCurrency = $giftcardAmout;
        if ($order->getOrderCurrencyCode() != $giftcard->getCurrencyCode()) {
            $rate = $this->currencyFactory->create()->load($order->getOrderCurrencyCode())->getAnyRate($giftcard->getCurrencyCode());
            $giftcardAmountAdjustedWithCurrency = $giftcardAmout * $rate;
        }

        // Discount amount and save
        $availableAmount = $giftcard->getAvailableAmount();
        $availableAmount = $availableAmount - $giftcardAmountAdjustedWithCurrency;
        $giftcard->setAvailableAmount($availableAmount);

        // Mark as used if needed
        if (($this->giftcardConfig->isSingleUseEnabled()) ||
            ($availableAmount <= 0.01)
        ) {
            $giftcard->setStatus(\Bydn\Giftcard\Model\Giftcard::GIFTCARD_USED);
        }

        // Save the card
        $this->giftcardResource->save($giftcard);

        // Create the movement
        $movement = $this->giftcardMovementFactory->create();
        $movement->setCardId($giftcard->getId());
        $movement->setOrderId($order->getId());
        $movement->setAmount($giftcardAmountAdjustedWithCurrency);
        $movement->setConcept(self::CONCEPT_KEY . " in order " . $order->getIncrementId());
        $this->giftcardMovementResource->save($movement);

        // Do some security checks
        if (($giftcard->getStatus() != \Bydn\Giftcard\Model\Giftcard::GIFTCARD_ACTIVE) || ($availableAmount < 0)) {
            $this->logger->info(': GIFTCARD ALERT: Fraudulent use of giftcard in order ' . $order->getIncrementId());
        }

        $this->logger->info('end');
    }
}
