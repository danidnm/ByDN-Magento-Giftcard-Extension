<?php

namespace Bydn\Giftcard\Observer;

/**
 * Magento or third party extensions sometimes saves entities without using the repository
 * With this we ensure extension attributes for giftcard are always saved into the corresponding tables
 */
class InvoiceSaveExtensionAttribute implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Bydn\Giftcard\Api\GiftcardInvoiceRepositoryInterface
     */
    private $giftcardInvoiceRepository;

    /**
     * @var \Bydn\Logger\Model\LoggerInterface
     */
    private $logger;


    /**
     * @param \Bydn\Giftcard\Api\GiftcardInvoiceRepositoryInterface $giftcardInvoiceRepository
     * @param \Bydn\Logger\Model\LoggerInterface $logger
     */
    public function __construct(
        \Bydn\Giftcard\Api\GiftcardInvoiceRepositoryInterface $giftcardInvoiceRepository,
        \Bydn\Logger\Model\LoggerInterface $logger
    ) {
        $this->giftcardInvoiceRepository = $giftcardInvoiceRepository;
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

        $invoice = $observer->getEvent()->getInvoice();
        $invoiceExtension = $invoice->getExtensionAttributes();
        if ($invoiceExtension) {
            $invoiceGiftcardData = $invoiceExtension->getGiftcardData();
            if ($invoiceGiftcardData) {
                $invoiceGiftcardData->setInvoiceId($invoice->getId());
                $this->giftcardInvoiceRepository->save($invoiceGiftcardData);
            }
        }

//        $this->logger->writeInfo(__METHOD__, __LINE__, 'End');
    }
}
