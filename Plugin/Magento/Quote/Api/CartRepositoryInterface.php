<?php

namespace Bydn\Giftcard\Plugin\Magento\Quote\Api;

use phpseclib3\Exception\FileNotFoundException;

class CartRepositoryInterface
{
    /**
     * @var \Bydn\Giftcard\Model\GiftcardQuoteRepository
     */
    private $giftcardQuoteRepository;

    /**
     * @param \Bydn\Giftcard\Model\GiftcardQuoteRepository $giftcardQuoteRepository
     */
    public function __construct(
        \Bydn\Giftcard\Model\GiftcardQuoteRepository $giftcardQuoteRepository
    ) {
       $this->giftcardQuoteRepository = $giftcardQuoteRepository;
    }

    /**
     * @param \Magento\Quote\Api\CartRepositoryInterface $subject
     * @param \Magento\Quote\Api\Data\CartInterface $entity
     * @return \Magento\Quote\Api\Data\CartInterface
     */
    public function afterGet
    (
        \Magento\Quote\Api\CartRepositoryInterface $subject,
        \Magento\Quote\Api\Data\CartInterface $entity
    ) {
        $giftcardData = $this->giftcardQuoteRepository->getByQuoteId($entity->getId());
        $extensionAttributes = $entity->getExtensionAttributes();
        $extensionAttributes->setGiftcardData($giftcardData);
        $entity->setExtensionAttributes($extensionAttributes);

        return $entity;
    }

    /**
     * @param \Magento\Quote\Api\CartRepositoryInterface $subject
     * @param \Magento\Quote\Api\Data\CartSearchResultsInterface $searchResults
     * @return \Magento\Quote\Api\Data\CartSearchResultsInterface
     */
    public function afterGetList(
        \Magento\Quote\Api\CartRepositoryInterface $subject,
        \Magento\Quote\Api\Data\CartSearchResultsInterface $searchResults
    ) : \Magento\Quote\Api\Data\CartSearchResultsInterface {

        $quotes = [];
        foreach ($searchResults->getItems() as $entity) {
            $giftcardData = $this->giftcardQuoteRepository->getByQuoteId($entity->getId());
            $extensionAttributes = $entity->getExtensionAttributes();
            $extensionAttributes->setGiftcardData($giftcardData);
            $entity->setExtensionAttributes($extensionAttributes);
            $quotes[] = $entity;
        }
        $searchResults->setItems($quotes);
        return $searchResults;
    }

    /**
     * @param \Magento\Quote\Api\CartRepositoryInterface $subject
     * @param $result
     * @param \Magento\Quote\Api\Data\CartInterface $entity
     * @return \Magento\Quote\Api\Data\CartInterface
     */
    public function afterSave
    (
        \Magento\Quote\Api\CartRepositoryInterface $subject,
        $result,
        \Magento\Quote\Api\Data\CartInterface $entity
    ) {
        $extensionAttributes = $entity->getExtensionAttributes();
        $giftcardData = $extensionAttributes->getGiftcardData();
        if ($giftcardData) {
            $this->giftcardQuoteRepository->save($giftcardData);
        }
        return $result;
    }
}
