<?php

namespace Bydn\Giftcard\Observer\Quote;

class CopyToOrder implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Bydn\Giftcard\Model\GiftcardOrderFactory
     */
    private $giftcardOrderFactory;

    /**
     * @var \Bydn\Logger\Model\LoggerInterface
     */
    private \Bydn\Logger\Model\LoggerInterface $logger;

    /**
     * @param \Bydn\Giftcard\Model\GiftcardOrderFactory $giftcardOrderFactory
     * @param \Bydn\Logger\Model\LoggerInterface $logger
     */
    public function __construct(
        \Bydn\Giftcard\Model\GiftcardOrderFactory $giftcardOrderFactory,
        \Bydn\Logger\Model\LoggerInterface $logger
    ) {
        $this->giftcardOrderFactory = $giftcardOrderFactory;
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

        /* @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getData('order');
        /* @var \Magento\Quote\Model\Quote $quote */
        $quote = $observer->getEvent()->getData('quote');

        // Quote extension
        $quoteExtensionAttributes = $quote->getExtensionAttributes();
        $quoteGiftcardData = $quoteExtensionAttributes->getGiftcardData();

        // Order extension
        $orderExtensionAttributes = $order->getExtensionAttributes();
        $orderGiftcardData = $orderExtensionAttributes->getGiftcardData();
        if (!$orderGiftcardData) {
            $orderGiftcardData = $this->giftcardOrderFactory->create();
        }

        // Copy
        $orderGiftcardData->setGiftcardAmount($quoteGiftcardData->getGiftcardAmount());
        $orderGiftcardData->setGiftcardBaseAmount($quoteGiftcardData->getGiftcardBaseAmount());
        $orderGiftcardData->setGiftcardCode($quoteGiftcardData->getGiftcardCode());

        // Set attributes back to order
        $orderExtensionAttributes->setGiftcardData($orderGiftcardData);
        $order->setExtensionAttributes($orderExtensionAttributes);

//        $this->logger->writeInfo(__METHOD__, __LINE__, 'End');
    }
}
