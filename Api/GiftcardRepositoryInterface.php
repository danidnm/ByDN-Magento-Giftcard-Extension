<?php

namespace Bydn\Giftcard\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;

interface GiftcardRepositoryInterface
{
    /**
     * Retrieve entity.
     *
     * @param int $id
     * @return \Bydn\Giftcard\Api\Data\GiftcardInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($id);

    /**
     * Retrieve entity by code
     *
     * @param int $code
     * @return \Bydn\Giftcard\Api\Data\GiftcardInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByCode($code);

    /**
     * Retrieve Giftcard matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Bydn\Giftcard\Api\Data\GiftcardSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Save Giftcards entry
     *
     * @param \Bydn\Giftcard\Api\Data\GiftcardInterface $giftcard
     * @return \Bydn\Giftcard\Api\Data\GiftcardInterface
     * @throws LocalizedException
     */
    public function save(\Bydn\Giftcard\Api\Data\GiftcardInterface $giftcard)
        : \Bydn\Giftcard\Api\Data\GiftcardInterface;
}
