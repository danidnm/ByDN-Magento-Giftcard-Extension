<?php

namespace Bydn\Giftcard\Model;

use Bydn\Giftcard\Api\GiftcardOrderRepositoryInterface;
use Bydn\Giftcard\Api\Data\GiftcardOrderInterface;
use Bydn\Giftcard\Api\Data\GiftcardOrderInterfaceFactory;
use Bydn\Giftcard\Api\Data\GiftcardOrderSearchResultsInterface;
use Bydn\Giftcard\Api\Data\GiftcardOrderSearchResultsInterfaceFactory;
use Bydn\Giftcard\Model\ResourceModel\GiftcardOrder as GiftcardOrderResource;
use Bydn\Giftcard\Model\ResourceModel\GiftcardOrder\CollectionFactory as GiftcardOrderCollectionFactory;
use \Bydn\Logger\Model\LoggerInterface as Logger;
use Bydn\Giftcard\Model\GiftcardOrderFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;

/**
 * Giftcard update repository
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class GiftcardOrderRepository implements GiftcardOrderRepositoryInterface
{
    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var GiftcardOrderResource
     */
    private $resource;

    /**
     * @var GiftcardOrderCollectionFactory
     */
    private $collectionFactory;

    /**
     * @var GiftcardOrderFactory
     */
    private $giftcardOrderFactory;

    /**
     * @var GiftcardOrderInterfaceFactory
     */
    private $giftcardOrderInterfaceFactory;

    /**
     * @var GiftcardOrderSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var Logger
     */
    private $logger;

    public function __construct(
        GiftcardOrderResource                      $resource,
        GiftcardOrderFactory                       $giftcardOrderFactory,
        GiftcardOrderInterfaceFactory              $giftcardOrderInterfaceFactory,
        GiftcardOrderCollectionFactory             $collectionFactory,
        GiftcardOrderSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface             $collectionProcessor,
        Logger                                   $logger
    ) {
        $this->resource = $resource;
        $this->giftcardOrderFactory = $giftcardOrderFactory;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->giftcardOrderInterfaceFactory = $giftcardOrderInterfaceFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->logger = $logger;
    }

    /**
     * Retrieve entity.
     *
     * @param int $id
     * @return \Bydn\Giftcard\Api\Data\GiftcardOrderInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($id)
    {
        $entity = $this->giftcardOrderFactory->create();
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
     * @return \Bydn\Giftcard\Api\Data\GiftcardOrderInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByOrderId($id)
    {
        $entity = $this->giftcardOrderFactory->create();
        $entity->load($id, 'order_id');
        if (!$entity->getId()) {
            $entity->setOrderId($id);
            $entity->setGiftcardAmount(0);
        }
        return $entity;
    }

    /**
     * Retrieve giftcard orders matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Bydn\Giftcard\Api\Data\GiftcardOrderSearchResultsInterface
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
     * Save giftcard orders
     *
     * @param ?\Bydn\Giftcard\Api\Data\GiftcardOrderInterface $giftcardOrder
     * @return \Bydn\Giftcard\Api\Data\GiftcardOrderInterface\|null
     * @throws LocalizedException
     */
    public function save(?GiftcardOrderInterface $giftcardOrder): ?GiftcardOrderInterface
    {
        try {
            if ($giftcardOrder) {
                $this->resource->save($giftcardOrder);
            }
        } catch (LocalizedException $exception) {
            throw new CouldNotSaveException(
                __('Could not save the giftcard order: %1', $exception->getMessage()),
                $exception
            );
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __('Could not save the giftcard order: %1', $exception->getMessage()),
                $exception
            );
        }
        return $giftcardOrder;
    }

    /**
     * Save giftcard orders
     *
     * @param \Bydn\Giftcard\Api\Data\GiftcardOrderInterface[] $entities
     * @return \Bydn\Giftcard\Api\Data\GiftcardOrderInterface[]
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
