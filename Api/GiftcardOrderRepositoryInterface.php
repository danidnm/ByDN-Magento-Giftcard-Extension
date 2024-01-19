<?php

namespace Bydn\Giftcard\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;

interface GiftcardOrderRepositoryInterface
{
    /**
     * Retrieve entity.
     *
     * @param int $id
     * @return \Bydn\Giftcard\Api\Data\GiftcardOrderInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($id);

    /**
     * Retrieve Giftcard orders matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Bydn\Giftcard\Api\Data\GiftcardOrderSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Save Giftcards order
     *
     * @param ?\Bydn\Giftcard\Api\Data\GiftcardOrderInterface $order
     * @return \Bydn\Giftcard\Api\Data\GiftcardOrderInterface|null
     * @throws LocalizedException
     */
    public function save(?\Bydn\Giftcard\Api\Data\GiftcardOrderInterface $order): ?\Bydn\Giftcard\Api\Data\GiftcardOrderInterface;
}
