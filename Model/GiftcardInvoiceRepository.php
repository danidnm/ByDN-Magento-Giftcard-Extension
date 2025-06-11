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

namespace Bydn\Giftcard\Model;

use Bydn\Giftcard\Api\GiftcardInvoiceRepositoryInterface;
use Bydn\Giftcard\Api\Data\GiftcardInvoiceInterface;
use Bydn\Giftcard\Api\Data\GiftcardInvoiceInterfaceFactory;
use Bydn\Giftcard\Api\Data\GiftcardInvoiceSearchResultsInterface;
use Bydn\Giftcard\Api\Data\GiftcardInvoiceSearchResultsInterfaceFactory;
use Bydn\Giftcard\Model\ResourceModel\GiftcardInvoice as GiftcardInvoiceResource;
use Bydn\Giftcard\Model\ResourceModel\GiftcardInvoice\CollectionFactory as GiftcardInvoiceCollectionFactory;
use \Psr\Log\LoggerInterface as Logger;
use Bydn\Giftcard\Model\GiftcardInvoiceFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;

/**
 * Giftcard update repository
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class GiftcardInvoiceRepository implements GiftcardInvoiceRepositoryInterface
{
    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var GiftcardInvoiceResource
     */
    private $resource;

    /**
     * @var GiftcardInvoiceCollectionFactory
     */
    private $collectionFactory;

    /**
     * @var GiftcardInvoiceFactory
     */
    private $giftcardInvoiceFactory;

    /**
     * @var GiftcardInvoiceInterfaceFactory
     */
    private $giftcardInvoiceInterfaceFactory;

    /**
     * @var GiftcardInvoiceSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @param GiftcardInvoiceResource $resource
     * @param \Bydn\Giftcard\Model\GiftcardInvoiceFactory $giftcardInvoiceFactory
     * @param GiftcardInvoiceInterfaceFactory $giftcardInvoiceInterfaceFactory
     * @param GiftcardInvoiceCollectionFactory $collectionFactory
     * @param GiftcardInvoiceSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param Logger $logger
     */
    public function __construct(
        GiftcardInvoiceResource                      $resource,
        GiftcardInvoiceFactory                       $giftcardInvoiceFactory,
        GiftcardInvoiceInterfaceFactory              $giftcardInvoiceInterfaceFactory,
        GiftcardInvoiceCollectionFactory             $collectionFactory,
        GiftcardInvoiceSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface             $collectionProcessor,
        Logger                                   $logger
    ) {
        $this->resource = $resource;
        $this->giftcardInvoiceFactory = $giftcardInvoiceFactory;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->giftcardInvoiceInterfaceFactory = $giftcardInvoiceInterfaceFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->logger = $logger;
    }

    /**
     * Retrieve entity.
     *
     * @param int $id
     * @return \Bydn\Giftcard\Api\Data\GiftcardInvoiceInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($id)
    {
        $entity = $this->giftcardInvoiceFactory->create();
        $entity->load($id);
        if (!$entity->getId()) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(__('Could not find entity with id "%1"', $id));
        }
        return $entity;
    }

    /**
     * Retrieve entity.
     *
     * @param int $id
     * @return \Bydn\Giftcard\Api\Data\GiftcardInvoiceInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByInvoiceId($id)
    {
        $entity = $this->giftcardInvoiceFactory->create();
        $entity->load($id, 'invoice_id');
        if (!$entity->getId()) {
            $entity->setInvoiceId($id);
            $entity->setGiftcardAmount(0);
        }
        return $entity;
    }

    /**
     * Retrieve giftcard invoices matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Bydn\Giftcard\Api\Data\GiftcardInvoiceSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->collectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * Save giftcard invoices
     *
     * @param ?\Bydn\Giftcard\Api\Data\GiftcardInvoiceInterface $giftcardInvoice
     * @return \Bydn\Giftcard\Api\Data\GiftcardInvoiceInterface\|null
     * @throws LocalizedException
     */
    public function save(?GiftcardInvoiceInterface $giftcardInvoice): ?GiftcardInvoiceInterface
    {
        try {
            if ($giftcardInvoice) {
                $this->resource->save($giftcardInvoice);
            }
        } catch (LocalizedException $exception) {
            throw new CouldNotSaveException(
                __('Could not save the giftcard invoice: %1', $exception->getMessage()),
                $exception
            );
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __('Could not save the giftcard invoice: %1', $exception->getMessage()),
                $exception
            );
        }
        return $giftcardInvoice;
    }

    /**
     * Save giftcard invoices
     *
     * @param \Bydn\Giftcard\Api\Data\GiftcardInvoiceInterface[] $entities
     * @return \Bydn\Giftcard\Api\Data\GiftcardInvoiceInterface[]
     * @throws LocalizedException
     */
    public function bulkSave(array $entities): array
    {
        $savedEntities = [];
        foreach ($entities as $entity) {
            try {
                $this->save($entity);
                $savedEntities[] = $entity;
            } catch (\Exception $exception) {
                throw new \Magento\Framework\Exception\CouldNotSaveException(
                    __('Could not save the entity: %1', $exception->getMessage()),
                    $exception
                );
            }
        }
        return $savedEntities;
    }
}
