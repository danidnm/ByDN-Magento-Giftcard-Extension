<?php

namespace Bydn\Giftcard\Model;

use Bydn\Giftcard\Api\GiftcardCreditmemoRepositoryInterface;
use Bydn\Giftcard\Api\Data\GiftcardCreditmemoInterface;
use Bydn\Giftcard\Api\Data\GiftcardCreditmemoInterfaceFactory;
use Bydn\Giftcard\Api\Data\GiftcardCreditmemoSearchResultsInterface;
use Bydn\Giftcard\Api\Data\GiftcardCreditmemoSearchResultsInterfaceFactory;
use Bydn\Giftcard\Model\ResourceModel\GiftcardCreditmemo as GiftcardCreditmemoResource;
use Bydn\Giftcard\Model\ResourceModel\GiftcardCreditmemo\CollectionFactory as GiftcardCreditmemoCollectionFactory;
use \Bydn\Logger\Model\LoggerInterface as Logger;
use Bydn\Giftcard\Model\GiftcardCreditmemoFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;

/**
 * Giftcard update repository
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class GiftcardCreditmemoRepository implements GiftcardCreditmemoRepositoryInterface
{
    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var GiftcardCreditmemoResource
     */
    private $resource;

    /**
     * @var GiftcardCreditmemoCollectionFactory
     */
    private $collectionFactory;

    /**
     * @var GiftcardCreditmemoFactory
     */
    private $giftcardCreditmemoFactory;

    /**
     * @var GiftcardCreditmemoInterfaceFactory
     */
    private $giftcardCreditmemoInterfaceFactory;

    /**
     * @var GiftcardCreditmemoSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var Logger
     */
    private $logger;

    public function __construct(
        GiftcardCreditmemoResource                      $resource,
        GiftcardCreditmemoFactory                       $giftcardCreditmemoFactory,
        GiftcardCreditmemoInterfaceFactory              $giftcardCreditmemoInterfaceFactory,
        GiftcardCreditmemoCollectionFactory             $collectionFactory,
        GiftcardCreditmemoSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface             $collectionProcessor,
        Logger                                   $logger
    ) {
        $this->resource = $resource;
        $this->giftcardCreditmemoFactory = $giftcardCreditmemoFactory;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->giftcardCreditmemoInterfaceFactory = $giftcardCreditmemoInterfaceFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->logger = $logger;
    }

    /**
     * Retrieve entity.
     *
     * @param int $id
     * @return \Bydn\Giftcard\Api\Data\GiftcardCreditmemoInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($id)
    {
        $entity = $this->giftcardCreditmemoFactory->create();
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
     * @return \Bydn\Giftcard\Api\Data\GiftcardCreditmemoInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByCreditmemoId($id)
    {
        $entity = $this->giftcardCreditmemoFactory->create();
        $entity->load($id, 'creditmemo_id');
        if (!$entity->getId()) {
            $entity->setCreditmemoId($id);
            $entity->setGiftcardAmount(0);
        }
        return $entity;
    }

    /**
     * Retrieve giftcard creditmemos matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Bydn\Giftcard\Api\Data\GiftcardCreditmemoSearchResultsInterface
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
     * Save giftcard creditmemos
     *
     * @param ?\Bydn\Giftcard\Api\Data\GiftcardCreditmemoInterface $giftcardCreditmemo
     * @return \Bydn\Giftcard\Api\Data\GiftcardCreditmemoInterface\|null
     * @throws LocalizedException
     */
    public function save(?GiftcardCreditmemoInterface $giftcardCreditmemo): ?GiftcardCreditmemoInterface
    {
        try {
            if ($giftcardCreditmemo) {
                $this->resource->save($giftcardCreditmemo);
            }
        } catch (LocalizedException $exception) {
            throw new CouldNotSaveException(
                __('Could not save the giftcard creditmemo: %1', $exception->getMessage()),
                $exception
            );
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __('Could not save the giftcard creditmemo: %1', $exception->getMessage()),
                $exception
            );
        }
        return $giftcardCreditmemo;
    }

    /**
     * Save giftcard creditmemos
     *
     * @param \Bydn\Giftcard\Api\Data\GiftcardCreditmemoInterface[] $entities
     * @return \Bydn\Giftcard\Api\Data\GiftcardCreditmemoInterface[]
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
