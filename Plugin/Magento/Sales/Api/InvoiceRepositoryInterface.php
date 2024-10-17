<?php

namespace Bydn\Giftcard\Plugin\Magento\Sales\Api;

use phpseclib3\Exception\FileNotFoundException;

class InvoiceRepositoryInterface
{
    /**
     * @var \Bydn\Giftcard\Model\GiftcardInvoiceRepository
     */
    private $giftcardInvoiceRepository;

    /**
     * @param \Bydn\Giftcard\Model\GiftcardInvoiceRepository $giftcardInvoiceRepository
     */
    public function __construct(
        \Bydn\Giftcard\Model\GiftcardInvoiceRepository $giftcardInvoiceRepository
    ) {
        $this->giftcardInvoiceRepository = $giftcardInvoiceRepository;
    }

    /**
     * Adds extension attribute data to the invoice
     *
     * @param \Magento\Sales\Api\InvoiceRepositoryInterface $subject
     * @param \Magento\Sales\Api\Data\InvoiceInterface $entity
     * @return \Magento\Sales\Api\Data\InvoiceInterface
     */
    public function afterGet(
        \Magento\Sales\Api\InvoiceRepositoryInterface $subject,
        \Magento\Sales\Api\Data\InvoiceInterface $entity
    ) {
        $giftcardData = $this->giftcardInvoiceRepository->getByInvoiceId($entity->getId());
        $extensionAttributes = $entity->getExtensionAttributes();
        $extensionAttributes->setGiftcardData($giftcardData);
        $entity->setExtensionAttributes($extensionAttributes);

        return $entity;
    }

    /**
     * Adds extension attribute data to an invoice list
     *
     * @param \Magento\Sales\Api\InvoiceRepositoryInterface $subject
     * @param \Magento\Sales\Api\Data\InvoiceSearchResultInterface $searchResults
     * @return \Magento\Sales\Api\Data\InvoiceSearchResultInterface
     */
    public function afterGetList(
        \Magento\Sales\Api\InvoiceRepositoryInterface $subject,
        \Magento\Sales\Api\Data\InvoiceSearchResultInterface $searchResults
    ) : \Magento\Sales\Api\Data\InvoiceSearchResultInterface {

        $invoices = [];
        foreach ($searchResults->getItems() as $entity) {
            $giftcardData = $this->giftcardInvoiceRepository->getByInvoiceId($entity->getId());
            $extensionAttributes = $entity->getExtensionAttributes();
            $extensionAttributes->setGiftcardData($giftcardData);
            $entity->setExtensionAttributes($extensionAttributes);
            $invoices[] = $entity;
        }
        $searchResults->setItems($invoices);
        return $searchResults;
    }

    /**
     * Save extension attribute data for an invoice
     *
     * @param \Magento\Sales\Api\InvoiceRepositoryInterface $subject
     * @param \Magento\Sales\Api\Data\InvoiceInterface|null $result
     * @param \Magento\Sales\Api\Data\InvoiceInterface $entity
     * @return \Magento\Sales\Api\Data\InvoiceInterface
     */
    public function afterSave(
        \Magento\Sales\Api\InvoiceRepositoryInterface $subject,
        \Magento\Sales\Api\Data\InvoiceInterface|null $result,
        \Magento\Sales\Api\Data\InvoiceInterface $entity
    ) {
        // IMPORTANT: Magento does not always use Invoice Repository to save the invoices, so sometimes saving
        // the invoice does not reach here. See InvoiceSaveExtensionAttribute observer for the rest of the cases.
        $extensionAttributes = $entity->getExtensionAttributes();
        $giftcardData = $extensionAttributes->getGiftcardData();
        if ($giftcardData) {
            $giftcardData->setInvoiceId($entity->getId());
            $this->giftcardInvoiceRepository->save($giftcardData);
        }
        return $result;
    }
}
