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
     * Adds extension attributes data to cart
     *
     * @param \Magento\Quote\Api\CartRepositoryInterface $subject
     * @param \Magento\Quote\Api\Data\CartInterface $entity
     * @return \Magento\Quote\Api\Data\CartInterface
     */
    public function afterGet(
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
     * Adds extension attributes data to cart list
     *
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
     * Saves extension attributes data to cart

     * @param \Magento\Quote\Api\CartRepositoryInterface $subject
     * @param \Magento\Quote\Api\Data\CartInterface|null $result
     * @param \Magento\Quote\Api\Data\CartInterface $entity
     * @return \Magento\Quote\Api\Data\CartInterface
     */
    public function afterSave(
        \Magento\Quote\Api\CartRepositoryInterface $subject,
        \Magento\Quote\Api\Data\CartInterface|null $result,
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
