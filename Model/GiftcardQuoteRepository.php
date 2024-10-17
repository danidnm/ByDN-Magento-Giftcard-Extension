<?php

namespace Bydn\Giftcard\Model;

use Bydn\Giftcard\Api\GiftcardQuoteRepositoryInterface;
use Bydn\Giftcard\Api\Data\GiftcardQuoteInterface;
use Bydn\Giftcard\Api\Data\GiftcardQuoteInterfaceFactory;
use Bydn\Giftcard\Api\Data\GiftcardQuoteSearchResultsInterface;
use Bydn\Giftcard\Api\Data\GiftcardQuoteSearchResultsInterfaceFactory;
use Bydn\Giftcard\Model\ResourceModel\GiftcardQuote as GiftcardQuoteResource;
use Bydn\Giftcard\Model\ResourceModel\GiftcardQuote\CollectionFactory as GiftcardQuoteCollectionFactory;
use \Psr\Log\LoggerInterface as Logger;
use Bydn\Giftcard\Model\GiftcardQuoteFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;

/**
 * Giftcard update repository
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class GiftcardQuoteRepository implements GiftcardQuoteRepositoryInterface
{
    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var GiftcardQuoteResource
     */
    private $resource;

    /**
     * @var GiftcardQuoteCollectionFactory
     */
    private $collectionFactory;

    /**
     * @var GiftcardQuoteFactory
     */
    private $giftcardQuoteFactory;

    /**
     * @var GiftcardQuoteInterfaceFactory
     */
    private $giftcardQuoteInterfaceFactory;

    /**
     * @var GiftcardQuoteSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @param GiftcardQuoteResource $resource
     * @param \Bydn\Giftcard\Model\GiftcardQuoteFactory $giftcardQuoteFactory
     * @param GiftcardQuoteInterfaceFactory $giftcardQuoteInterfaceFactory
     * @param GiftcardQuoteCollectionFactory $collectionFactory
     * @param GiftcardQuoteSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param Logger $logger
     */
    public function __construct(
        GiftcardQuoteResource                      $resource,
        GiftcardQuoteFactory                       $giftcardQuoteFactory,
        GiftcardQuoteInterfaceFactory              $giftcardQuoteInterfaceFactory,
        GiftcardQuoteCollectionFactory             $collectionFactory,
        GiftcardQuoteSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface             $collectionProcessor,
        Logger                                   $logger
    ) {
        $this->resource = $resource;
        $this->giftcardQuoteFactory = $giftcardQuoteFactory;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->giftcardQuoteInterfaceFactory = $giftcardQuoteInterfaceFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->logger = $logger;
    }

    /**
     * Retrieve entity.
     *
     * @param int $id
     * @return \Bydn\Giftcard\Api\Data\GiftcardQuoteInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($id)
    {
        $entity = $this->giftcardQuoteFactory->create();
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
     * @return \Bydn\Giftcard\Api\Data\GiftcardQuoteInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByQuoteId($id)
    {
        $entity = $this->giftcardQuoteFactory->create();
        $entity->load($id, 'quote_id');
        if (!$entity->getId()) {
            $entity->setQuoteId($id);
            $entity->setGiftcardAmount(0);
        }
        return $entity;
    }

    /**
     * Retrieve giftcard quotes matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Bydn\Giftcard\Api\Data\GiftcardQuoteSearchResultsInterface
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
     * Save giftcard quotes
     *
     * @param ?\Bydn\Giftcard\Api\Data\GiftcardQuoteInterface $giftcardQuote
     * @return \Bydn\Giftcard\Api\Data\GiftcardQuoteInterface\|null
     * @throws LocalizedException
     */
    public function save(?GiftcardQuoteInterface $giftcardQuote): ?GiftcardQuoteInterface
    {
        try {
            if ($giftcardQuote) {
                $this->resource->save($giftcardQuote);
            }
        } catch (LocalizedException $exception) {
            throw new CouldNotSaveException(
                __('Could not save the giftcard quote: %1', $exception->getMessage()),
                $exception
            );
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __('Could not save the giftcard quote: %1', $exception->getMessage()),
                $exception
            );
        }
        return $giftcardQuote;
    }

    /**
     * Save giftcard quotes
     *
     * @param \Bydn\Giftcard\Api\Data\GiftcardQuoteInterface[] $entities
     * @return \Bydn\Giftcard\Api\Data\GiftcardQuoteInterface[]
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
