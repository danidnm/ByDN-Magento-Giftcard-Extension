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

namespace Bydn\Giftcard\Observer\Quote;

class CopyToOrder implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Bydn\Giftcard\Model\GiftcardOrderFactory
     */
    private $giftcardOrderFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private \Psr\Log\LoggerInterface $logger;

    /**
     * @param \Bydn\Giftcard\Model\GiftcardOrderFactory $giftcardOrderFactory
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Bydn\Giftcard\Model\GiftcardOrderFactory $giftcardOrderFactory,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->giftcardOrderFactory = $giftcardOrderFactory;
        $this->logger = $logger;
    }

    /**
     * Check if giftcard has been used and discount the amount from balance
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
//        $this->logger->info('Ini');

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

//        $this->logger->info('End');
    }
}
