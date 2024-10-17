<?php

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
     * @param \Magento\Sales\Api\Data\OrderInterface $result
     * @param \Magento\Sales\Api\Data\OrderInterface $entity
     * @return \Magento\Sales\Api\Data\OrderInterface
     */
    public function afterSave(
        \Magento\Sales\Api\OrderRepositoryInterface $subject,
        \Magento\Sales\Api\Data\OrderInterface $result,
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
