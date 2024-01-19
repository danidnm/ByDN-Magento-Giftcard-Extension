<?php

namespace Bydn\Giftcard\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;

interface GiftcardMovementRepositoryInterface
{

    /**
     * Retrieve entity.
     *
     * @param int $id
     * @return \Bydn\Giftcard\Api\Data\GiftcardMovementInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($id);

    /**
     * Retrieve Giftcard movements matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Bydn\Giftcard\Api\Data\GiftcardMovementSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Save Giftcards movement
     *
     * @param \Bydn\Giftcard\Api\Data\GiftcardMovementInterface $movement
     * @return \Bydn\Giftcard\Api\Data\GiftcardMovementInterface
     * @throws LocalizedException
     */
    public function save(\Bydn\Giftcard\Api\Data\GiftcardMovementInterface $movement): \Bydn\Giftcard\Api\Data\GiftcardMovementInterface;
}
