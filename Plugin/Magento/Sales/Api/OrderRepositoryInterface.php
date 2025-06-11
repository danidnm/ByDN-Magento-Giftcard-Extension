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

class OrderRepositoryInterface
{
    /**
     * @var \Bydn\Giftcard\Model\GiftcardOrderRepository
     */
    private $giftcardOrderRepository;

    /**
     * @param \Bydn\Giftcard\Model\GiftcardOrderRepository $giftcardOrderRepository
     */
    public function __construct(
        \Bydn\Giftcard\Model\GiftcardOrderRepository $giftcardOrderRepository
    ) {
        $this->giftcardOrderRepository = $giftcardOrderRepository;
    }

    /**
     * Adds extension attribute data to the order
     *
     * @param \Magento\Sales\Api\OrderRepositoryInterface $subject
     * @param \Magento\Sales\Api\Data\OrderInterface $entity
     * @return \Magento\Sales\Api\Data\OrderInterface
     */
    public function afterGet(
        \Magento\Sales\Api\OrderRepositoryInterface $subject,
        \Magento\Sales\Api\Data\OrderInterface $entity
    ) {
        $giftcardData = $this->giftcardOrderRepository->getByOrderId($entity->getId());
        $extensionAttributes = $entity->getExtensionAttributes();
        $extensionAttributes->setGiftcardData($giftcardData);
        $entity->setExtensionAttributes($extensionAttributes);

        return $entity;
    }

    /**
     * Adds extension attribute data to an order list
     *
     * @param \Magento\Sales\Api\OrderRepositoryInterface $subject
     * @param \Magento\Sales\Api\Data\OrderSearchResultInterface $searchResults
     * @return \Magento\Sales\Api\Data\OrderSearchResultInterface
     */
    public function afterGetList(
        \Magento\Sales\Api\OrderRepositoryInterface $subject,
        \Magento\Sales\Api\Data\OrderSearchResultInterface $searchResults
    ) : \Magento\Sales\Api\Data\OrderSearchResultInterface {

        // Iterates and add giftcard information
        foreach ($searchResults->getItems() as $entity) {
            $giftcardData = $this->giftcardOrderRepository->getByOrderId($entity->getId());
            $extensionAttributes = $entity->getExtensionAttributes();
            $extensionAttributes->setGiftcardData($giftcardData);
            $entity->setExtensionAttributes($extensionAttributes);
        }

        return $searchResults;
    }

    /**
     * Saves extension attribute data for an order
     *
     * @param \Magento\Sales\Api\OrderRepositoryInterface $subject
     * @param \Magento\Sales\Api\Data\OrderInterface|null $result
     * @param \Magento\Sales\Api\Data\OrderInterface $entity
     * @return \Magento\Sales\Api\Data\OrderInterface
     */
    public function afterSave(
        \Magento\Sales\Api\OrderRepositoryInterface $subject,
        \Magento\Sales\Api\Data\OrderInterface|null $result,
        \Magento\Sales\Api\Data\OrderInterface $entity
    ) {
        $extensionAttributes = $entity->getExtensionAttributes();
        $giftcardData = $extensionAttributes->getGiftcardData();
        if ($giftcardData) {
            $giftcardData->setOrderId($entity->getId());
            $this->giftcardOrderRepository->save($giftcardData);
        }
        return $result;
    }
}
