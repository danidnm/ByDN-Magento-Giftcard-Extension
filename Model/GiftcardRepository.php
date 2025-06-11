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

use Bydn\Giftcard\Api\GiftcardRepositoryInterface;
use Bydn\Giftcard\Api\Data\GiftcardInterface;
use Bydn\Giftcard\Api\Data\GiftcardInterfaceFactory;
use Bydn\Giftcard\Api\Data\GiftcardSearchResultsInterface;
use Bydn\Giftcard\Api\Data\GiftcardSearchResultsInterfaceFactory;
use Bydn\Giftcard\Model\ResourceModel\Giftcard as GiftcardResource;
use Bydn\Giftcard\Model\ResourceModel\Giftcard\CollectionFactory as GiftcardCollectionFactory;
use \Psr\Log\LoggerInterface as Logger;
use Bydn\Giftcard\Model\GiftcardFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;

class GiftcardRepository implements GiftcardRepositoryInterface
{
    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var GiftcardResource
     */
    private $resource;

    /**
     * @var GiftcardCollectionFactory
     */
    private $collectionFactory;

    /**
     * @var GiftcardFactory
     */
    private $giftcardFactory;

    /**
     * @var GiftcardInterfaceFactory
     */
    private $giftcardInterfaceFactory;

    /**
     * @var GiftcardSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @param GiftcardResource $resource
     * @param \Bydn\Giftcard\Model\GiftcardFactory $giftcardFactory
     * @param GiftcardInterfaceFactory $giftcardInterfaceFactory
     * @param GiftcardCollectionFactory $collectionFactory
     * @param GiftcardSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param Logger $logger
     */
    public function __construct(
        GiftcardResource                      $resource,
        GiftcardFactory                       $giftcardFactory,
        GiftcardInterfaceFactory              $giftcardInterfaceFactory,
        GiftcardCollectionFactory             $collectionFactory,
        GiftcardSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface             $collectionProcessor,
        Logger                                   $logger
    ) {
        $this->resource = $resource;
        $this->giftcardFactory = $giftcardFactory;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->giftcardInterfaceFactory = $giftcardInterfaceFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->logger = $logger;
    }

    /**
     * Retrieve entity.
     *
     * @param int $id
     * @return \Bydn\Giftcard\Api\Data\GiftcardInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($id)
    {
        $entity = $this->giftcardFactory->create();
        $entity->load($id);
        if (!$entity->getId()) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(__('Could not find entity with id "%1"', $id));
        }
        return $entity;
    }

    /**
     * Retrieve entity by code
     *
     * @param int $code
     * @return \Bydn\Giftcard\Api\Data\GiftcardInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByCode($code)
    {
        $entity = $this->giftcardFactory->create();
        $entity->load($code, 'code');
        if (!$entity->getId()) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(
                __('Could not find entity with code "%1"', $code)
            );
        }
        return $entity;
    }

    /**
     * Retrieve giftcards matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Bydn\Giftcard\Api\Data\GiftcardSearchResultsInterface
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
     * Save a giftcard
     *
     * @param \Bydn\Giftcard\Api\Data\GiftcardInterface $giftcard
     * @return \Bydn\Giftcard\Api\Data\GiftcardInterface
     * @throws LocalizedException
     */
    public function save(GiftcardInterface $giftcard): GiftcardInterface
    {
        try {
            if ($giftcard->getId() == '') {
                if ($giftcard->getStatus() == '') {
                    $giftcard->setStatus(\Bydn\Giftcard\Model\Giftcard::GIFTCARD_PENDING);
                }
            }
            $this->resource->save($giftcard);
        } catch (LocalizedException $exception) {
            throw new CouldNotSaveException(
                __('Could not save the giftcard %1', $exception->getMessage()),
                $exception
            );
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __('Could not save the giftcard: %1', $exception->getMessage()),
                $exception
            );
        }
        return $giftcard;
    }

    /**
     * Save giftcard updates
     *
     * @param \Bydn\Giftcard\Api\Data\GiftcardInterface[] $entities
     * @return \Bydn\Giftcard\Api\Data\GiftcardInterface[]
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
