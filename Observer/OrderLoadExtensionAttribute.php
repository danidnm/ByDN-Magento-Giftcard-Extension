<?php

namespace Bydn\Giftcard\Observer;

/**
 * Magento or third party extensions sometimes loads entities without using the repository
 * With this we ensure extension attributes for giftcard are always attached to the entity
 */
class OrderLoadExtensionAttribute implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Bydn\Giftcard\Api\GiftcardOrderRepositoryInterface
     */
    private $giftcardOrderRepository;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @param \Bydn\Giftcard\Api\GiftcardOrderRepositoryInterface $giftcardOrderRepository
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Bydn\Giftcard\Api\GiftcardOrderRepositoryInterface $giftcardOrderRepository,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->giftcardOrderRepository = $giftcardOrderRepository;
        $this->logger = $logger;
    }

    /**
     * Attach giftcard data to the order
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
//        $this->logger->info('Ini');

        $order = $observer->getEvent()->getOrder();
        $orderExtension = $order->getExtensionAttributes();
        if ($orderExtension) {
            $orderGiftcardData = $orderExtension->getGiftcardData();
            if (!$orderGiftcardData) {
                $orderGiftcardData = $this->giftcardOrderRepository->getByOrderId($order->getId());
                $orderExtension->setGiftcardData($orderGiftcardData);
                $order->setExtensionAttributes($orderExtension);
            }
        }

//        $this->logger->info('End');
    }
}
