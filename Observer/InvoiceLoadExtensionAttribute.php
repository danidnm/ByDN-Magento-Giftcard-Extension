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
 * Magento or third party extensions sometimes loads entities without using the repository
 * With this we ensure extension attributes for giftcard are always attached to the entity
 */
class InvoiceLoadExtensionAttribute implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Bydn\Giftcard\Api\GiftcardInvoiceRepositoryInterface
     */
    private $giftcardInvoiceRepository;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @param \Bydn\Giftcard\Api\GiftcardInvoiceRepositoryInterface $giftcardInvoiceRepository
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Bydn\Giftcard\Api\GiftcardInvoiceRepositoryInterface $giftcardInvoiceRepository,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->giftcardInvoiceRepository = $giftcardInvoiceRepository;
        $this->logger = $logger;
    }

    /**
     * Attach giftcard data to the invoice
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
//        $this->logger->info('Ini');

        $invoice = $observer->getEvent()->getInvoice();
        $invoiceExtension = $invoice->getExtensionAttributes();
        if ($invoiceExtension) {
            $invoiceGiftcardData = $invoiceExtension->getGiftcardData();
            if (!$invoiceGiftcardData) {
                $invoiceGiftcardData = $this->giftcardInvoiceRepository->getByInvoiceId($invoice->getId());
                $invoiceExtension->setGiftcardData($invoiceGiftcardData);
                $invoice->setExtensionAttributes($invoiceExtension);
            }
        }

//        $this->logger->info('End');
    }
}
