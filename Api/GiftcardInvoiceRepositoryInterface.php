<?php

namespace Bydn\Giftcard\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;

interface GiftcardInvoiceRepositoryInterface
{
    /**
     * Retrieve entity.
     *
     * @param int $id
     * @return \Bydn\Giftcard\Api\Data\GiftcardInvoiceInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($id);

    /**
     * Retrieve Giftcard invoice matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Bydn\Giftcard\Api\Data\GiftcardInvoiceSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Save Giftcards invoice
     *
     * @param ?\Bydn\Giftcard\Api\Data\GiftcardInvoiceInterface $invoice
     * @return \Bydn\Giftcard\Api\Data\GiftcardInvoiceInterface|null
     * @throws LocalizedException
     */
    public function save(?\Bydn\Giftcard\Api\Data\GiftcardInvoiceInterface $invoice): ?\Bydn\Giftcard\Api\Data\GiftcardInvoiceInterface;
}
