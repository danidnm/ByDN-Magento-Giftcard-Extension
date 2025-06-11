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

namespace Bydn\Giftcard\Plugin\Magento\Sales\Api;

use phpseclib3\Exception\FileNotFoundException;

class CreditmemoRepositoryInterface
{
    /**
     * @var \Bydn\Giftcard\Model\GiftcardCreditmemoRepository
     */
    private $giftcardCreditmemoRepository;

    /**
     * @param \Bydn\Giftcard\Model\GiftcardCreditmemoRepository $giftcardCreditmemoRepository
     */
    public function __construct(
        \Bydn\Giftcard\Model\GiftcardCreditmemoRepository $giftcardCreditmemoRepository
    ) {
        $this->giftcardCreditmemoRepository = $giftcardCreditmemoRepository;
    }

    /**
     * Adds credit memo extension attribute data to the creditmemo
     *
     * @param \Magento\Sales\Api\CreditmemoRepositoryInterface $subject
     * @param \Magento\Sales\Api\Data\CreditmemoInterface $entity
     * @return \Magento\Sales\Api\Data\CreditmemoInterface
     */
    public function afterGet(
        \Magento\Sales\Api\CreditmemoRepositoryInterface $subject,
        \Magento\Sales\Api\Data\CreditmemoInterface $entity
    ) {
        $giftcardData = $this->giftcardCreditmemoRepository->getByCreditmemoId($entity->getId());
        $extensionAttributes = $entity->getExtensionAttributes();
        $extensionAttributes->setGiftcardData($giftcardData);
        $entity->setExtensionAttributes($extensionAttributes);

        return $entity;
    }

    /**
     * Adds credit memo extension attribute data to a creditmemo list
     *
     * @param \Magento\Sales\Api\CreditmemoRepositoryInterface $subject
     * @param \Magento\Sales\Api\Data\CreditmemoSearchResultInterface $searchResults
     * @return \Magento\Sales\Api\Data\CreditmemoSearchResultInterface
     */
    public function afterGetList(
        \Magento\Sales\Api\CreditmemoRepositoryInterface $subject,
        \Magento\Sales\Api\Data\CreditmemoSearchResultInterface $searchResults
    ) : \Magento\Sales\Api\Data\CreditmemoSearchResultInterface {

        $creditmemos = [];
        foreach ($searchResults->getItems() as $entity) {
            $giftcardData = $this->giftcardCreditmemoRepository->getByCreditmemoId($entity->getId());
            $extensionAttributes = $entity->getExtensionAttributes();
            $extensionAttributes->setGiftcardData($giftcardData);
            $entity->setExtensionAttributes($extensionAttributes);
            $creditmemos[] = $entity;
        }
        $searchResults->setItems($creditmemos);
        return $searchResults;
    }

    /**
     * Saves credit memo extension attribute data for the creditmemo
     *
     * @param \Magento\Sales\Api\CreditmemoRepositoryInterface $subject
     * @param \Magento\Sales\Api\Data\CreditmemoInterface|null $result
     * @param \Magento\Sales\Api\Data\CreditmemoInterface $entity
     * @return \Magento\Sales\Api\Data\CreditmemoInterface
     */
    public function afterSave(
        \Magento\Sales\Api\CreditmemoRepositoryInterface $subject,
        \Magento\Sales\Api\Data\CreditmemoInterface|null $result,
        \Magento\Sales\Api\Data\CreditmemoInterface $entity
    ) {
        // IMPORTANT: Magento does not always use Creditmemo Repository to save the invoices, so sometimes saving
        // the creditmemo does not reach here. See CreditmemoSaveExtensionAttribute observer for the rest of the cases.
        $extensionAttributes = $entity->getExtensionAttributes();
        $giftcardData = $extensionAttributes->getGiftcardData();
        if ($giftcardData) {
            $giftcardData->setCreditmemoId($entity->getId());
            $this->giftcardCreditmemoRepository->save($giftcardData);
        }
        return $result;
    }
}
