<?php

namespace Bydn\Giftcard\Model;

use Bydn\Giftcard\Api\GiftcardMovementRepositoryInterface;
use Bydn\Giftcard\Api\Data\GiftcardMovementInterface;
use Bydn\Giftcard\Api\Data\GiftcardMovementInterfaceFactory;
use Bydn\Giftcard\Api\Data\GiftcardMovementSearchResultsInterface;
use Bydn\Giftcard\Api\Data\GiftcardMovementSearchResultsInterfaceFactory;
use Bydn\Giftcard\Model\ResourceModel\GiftcardMovement as GiftcardMovementResource;
use Bydn\Giftcard\Model\ResourceModel\GiftcardMovement\CollectionFactory as GiftcardMovementCollectionFactory;
use \Psr\Log\LoggerInterface as Logger;
use Bydn\Giftcard\Model\GiftcardMovementFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;

/**
 * Giftcard update repository
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class GiftcardMovementRepository implements GiftcardMovementRepositoryInterface
{
    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var GiftcardMovementResource
     */
    private $resource;

    /**
     * @var GiftcardMovementCollectionFactory
     */
    private $collectionFactory;

    /**
     * @var GiftcardMovementFactory
     */
    private $giftcardMovementFactory;

    /**
     * @var GiftcardMovementInterfaceFactory
     */
    private $giftcardMovementInterfaceFactory;

    /**
     * @var GiftcardMovementSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @param GiftcardMovementResource $resource
     * @param \Bydn\Giftcard\Model\GiftcardMovementFactory $giftcardMovementFactory
     * @param GiftcardMovementInterfaceFactory $giftcardMovementInterfaceFactory
     * @param GiftcardMovementCollectionFactory $collectionFactory
     * @param GiftcardMovementSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param Logger $logger
     */
    public function __construct(
        GiftcardMovementResource                      $resource,
        GiftcardMovementFactory                       $giftcardMovementFactory,
        GiftcardMovementInterfaceFactory              $giftcardMovementInterfaceFactory,
        GiftcardMovementCollectionFactory             $collectionFactory,
        GiftcardMovementSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface             $collectionProcessor,
        Logger                                   $logger
    ) {
        $this->resource = $resource;
        $this->giftcardMovementFactory = $giftcardMovementFactory;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->giftcardMovementInterfaceFactory = $giftcardMovementInterfaceFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->logger = $logger;
    }

    /**
     * Retrieve entity.
     *
     * @param int $id
     * @return \Bydn\Giftcard\Api\Data\GiftcardMovementInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($id)
    {
        $entity = $this->giftcardMovementFactory->create();
        $entity->load($id);
        if (!$entity->getId()) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(__('Could not find entity with id "%1"', $id));
        }
        return $entity;
    }

    /**
     * Retrieve giftcard movements matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Bydn\Giftcard\Api\Data\GiftcardMovementSearchResultsInterface
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
     * Save giftcard movements
     *
     * @param \Bydn\Giftcard\Api\Data\GiftcardMovementInterface $giftcardMovement
     * @return \Bydn\Giftcard\Api\Data\GiftcardMovementInterface
     * @throws LocalizedException
     */
    public function save(GiftcardMovementInterface $giftcardMovement): GiftcardMovementInterface
    {
        try {
            if ($giftcardMovement->getId() == '') {
                ;
            }
            $this->resource->save($giftcardMovement);
        } catch (LocalizedException $exception) {
            throw new CouldNotSaveException(
                __('Could not save the giftcard movement: %1', $exception->getMessage()),
                $exception
            );
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __('Could not save the giftcard movement: %1', $exception->getMessage()),
                $exception
            );
        }
        return $giftcardMovement;
    }

    /**
     * Save giftcard movements
     *
     * @param \Bydn\Giftcard\Api\Data\GiftcardMovementInterface[] $entities
     * @return \Bydn\Giftcard\Api\Data\GiftcardMovementInterface[]
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
