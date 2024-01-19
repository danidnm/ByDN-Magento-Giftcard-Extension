<?php

namespace Bydn\Giftcard\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;

interface GiftcardCreditmemoRepositoryInterface
{
    /**
     * Retrieve entity.
     *
     * @param int $id
     * @return \Bydn\Giftcard\Api\Data\GiftcardCreditmemoInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($id);

    /**
     * Retrieve Giftcard creditmemo matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Bydn\Giftcard\Api\Data\GiftcardCreditmemoSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Save Giftcards creditmemo
     *
     * @param ?\Bydn\Giftcard\Api\Data\GiftcardCreditmemoInterface $creditmemo
     * @return \Bydn\Giftcard\Api\Data\GiftcardCreditmemoInterface|null
     * @throws LocalizedException
     */
    public function save(?\Bydn\Giftcard\Api\Data\GiftcardCreditmemoInterface $creditmemo): ?\Bydn\Giftcard\Api\Data\GiftcardCreditmemoInterface;
}
