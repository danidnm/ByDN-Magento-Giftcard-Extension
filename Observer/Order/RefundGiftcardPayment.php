<?php

namespace Bydn\Giftcard\Observer\Order;

class RefundGiftcardPayment implements \Magento\Framework\Event\ObserverInterface
{
    const CONCEPT_KEY = 'Refund';

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
        \Bydn\Giftcard\Helper\Config $giftcardConfig,
        \Bydn\Giftcard\Model\ResourceModel\GiftcardMovement\CollectionFactory $giftcardMovementCollectionFactory,
        \Bydn\Giftcard\Model\ResourceModel\Giftcard $giftcardResource,
        \Bydn\Giftcard\Model\GiftcardFactory $giftcardFactory,
        \Bydn\Giftcard\Model\GiftcardRepository $giftcardRepository,
        \Bydn\Giftcard\Model\ResourceModel\GiftcardMovement $giftcardMovementResource,
        \Bydn\Giftcard\Model\GiftcardMovementFactory $giftcardMovementFactory,
        \Psr\Log\LoggerInterface $logger
    ) {
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
        $creditmemo = $observer->getEvent()->getCreditmemo();
        if ($creditmemo) {

            $this->logger->info('Processing creditmemo: ' . $creditmemo->getId());

            // Get giftcard data
            $extensionAttributes = $creditmemo->getExtensionAttributes();
            $giftcardData = $extensionAttributes->getGiftcardData();
            if ($giftcardData) {

                // Extract amount and code
                $giftcardAmout = $giftcardData->getGiftcardAmount();
                $giftcardCode = $giftcardData->getGiftcardCode();
                if ($giftcardCode) {

                    $this->logger->info('Has giftcard applied: ' . $giftcardCode);

                    // Check the movement still does not exists
                    if (!$this->movementExists($creditmemo->getOrderId())) {

                        // Create movement and adjust the card
                        $this->createMovement($creditmemo, $giftcardCode, (-1) * $giftcardAmout);
                    }
                }
            }
        }

        $this->logger->info('End');
    }

    /**
     * Checks if a movement is already annotated in the database
     *
     * @param $order
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
     * @return void
     */
    public function createMovement($order, $giftcardCode, $giftcardAmout)
    {
        $this->logger->info('Saving movement for giftcard: ' . $giftcardCode . ' with value: ' . $giftcardAmout);

        // Get the giftcard instance
        /** @var \Bydn\Giftcard\Model\Giftcard $giftcard */
        $giftcard = $this->giftcardRepository->getByCode($giftcardCode);
        if (!$giftcard) {
            $this->logger->info(': GIFTCARD ALERT: Applied giftcard that does not exists in order ' . $order->getIncrementId());
            $this->logger->sendAlertTelegram('GIFTCARD ALERT: Applied giftcard that does not exists in order ' . $order->getIncrementId(), 'it');
            return;
        }

        // Discount amount and save
        $availableAmount = $giftcard->getAvailableAmount();
        $availableAmount = $availableAmount - $giftcardAmout;
        $giftcard->setAvailableAmount($availableAmount);
        if ($availableAmount > 0.01) {
            $giftcard->setStatus(\Bydn\Giftcard\Model\Giftcard::GIFTCARD_ACTIVE);
        }
        $this->giftcardResource->save($giftcard);

        // Create the movement
        $movement = $this->giftcardMovementFactory->create();
        $movement->setCardId($giftcard->getId());
        $movement->setOrderId($order->getId());
        $movement->setAmount($giftcardAmout);
        $movement->setConcept(self::CONCEPT_KEY . " in creditmemo " . $order->getIncrementId());
        $this->giftcardMovementResource->save($movement);

        // Do some security checks
        if (($giftcard->getStatus() != \Bydn\Giftcard\Model\Giftcard::GIFTCARD_ACTIVE) || ($availableAmount < 0)) {
            $this->logger->info(': GIFTCARD ALERT: Fraudulent use of giftcard detected in order ' . $order->getIncrementId());
            $this->logger->sendAlertTelegram('GIFTCARD ALERT: Fraudulent use of giftcard detected in order ' . $order->getIncrementId(), 'it');
        }

        $this->logger->info('end');
    }
}
