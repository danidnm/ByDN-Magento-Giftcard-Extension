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
     * Check if giftcard has been used and discount the amount from balance
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
            if ($orderGiftcardData) {
                $orderGiftcardData->setOrderId($order->getId());
                $this->giftcardOrderRepository->save($orderGiftcardData);
            }
        }

//        $this->logger->info('End');
    }
}
