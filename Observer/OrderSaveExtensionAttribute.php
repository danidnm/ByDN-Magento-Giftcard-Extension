<?php

namespace Bydn\Giftcard\Observer;

/**
 * Magento or third party extensions sometimes saves entities without using the repository
 * With this we ensure extension attributes for giftcard are always saved into the corresponding tables
 */
class OrderSaveExtensionAttribute implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Bydn\Giftcard\Api\GiftcardOrderRepositoryInterface
     */
    private $giftcardOrderRepository;

    /**
     * @var \Bydn\Logger\Model\LoggerInterface
     */
    private $logger;


    /**
     * @param \Bydn\Giftcard\Api\GiftcardOrderRepositoryInterface $giftcardOrderRepository
     * @param \Bydn\Logger\Model\LoggerInterface $logger
     */
    public function __construct(
        \Bydn\Giftcard\Api\GiftcardOrderRepositoryInterface $giftcardOrderRepository,
        \Bydn\Logger\Model\LoggerInterface $logger
    ) {
        $this->giftcardOrderRepository = $giftcardOrderRepository;
        $this->logger = $logger;
    }

    /**
     * Check if giftcard has been used and discount the amount from balance
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
//        $this->logger->writeInfo(__METHOD__, __LINE__, 'Ini');

        $order = $observer->getEvent()->getOrder();
        $orderExtension = $order->getExtensionAttributes();
        if ($orderExtension) {
            $orderGiftcardData = $orderExtension->getGiftcardData();
            if ($orderGiftcardData) {
                $orderGiftcardData->setOrderId($order->getId());
                $this->giftcardOrderRepository->save($orderGiftcardData);
            }
        }

//        $this->logger->writeInfo(__METHOD__, __LINE__, 'End');
    }
}
