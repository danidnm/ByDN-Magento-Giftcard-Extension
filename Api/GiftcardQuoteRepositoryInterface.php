<?php

namespace Bydn\Giftcard\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;

interface GiftcardQuoteRepositoryInterface
{
    /**
     * Retrieve entity.
     *
     * @param int $id
     * @return \Bydn\Giftcard\Api\Data\GiftcardQuoteInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($id);

    /**
     * Retrieve Giftcard quotes matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Bydn\Giftcard\Api\Data\GiftcardQuoteSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Save Giftcards quote
     *
     * @param ?\Bydn\Giftcard\Api\Data\GiftcardQuoteInterface $quote
     * @return \Bydn\Giftcard\Api\Data\GiftcardQuoteInterface|null
     * @throws LocalizedException
     */
    public function save(?\Bydn\Giftcard\Api\Data\GiftcardQuoteInterface $quote)
        : ?\Bydn\Giftcard\Api\Data\GiftcardQuoteInterface;
}
