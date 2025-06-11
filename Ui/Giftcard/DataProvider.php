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

namespace Bydn\Giftcard\Ui\Giftcard;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var \Bydn\Giftcard\Model\ResourceModel\Giftcard\CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private \Magento\Framework\App\RequestInterface $request;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    private \Magento\Framework\Serialize\SerializerInterface $serializer;

    /**
     * @var array|null
     */
    private ?array $loadedData = null;

    /**
     * @param \Bydn\Giftcard\Model\ResourceModel\Giftcard\CollectionFactory $collectionFactory
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Serialize\SerializerInterface $serializer
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        \Bydn\Giftcard\Model\ResourceModel\Giftcard\CollectionFactory $collectionFactory,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collectionFactory = $collectionFactory;
        $this->collection = $this->collectionFactory->create();
        $this->request = $request;
        $this->serializer = $serializer;
    }

    /**
     * Returns data for the current gift card
     *
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getData(): array
    {
        // If already loaded, return data
        if ($this->loadedData !== null) {
            return $this->loadedData;
        }

        // Load current giftcard if any
        $giftcard = $this->getCurrentGiftcard();
        if ($giftcard !== null) {

            // Extract giftcard data to fix some things
            $giftcardData = $giftcard->getData();

            // Fix store ids data
            $giftcardData = $this->getStores($giftcardData);

            // Put the data back
            $this->loadedData[$giftcard->getData('id')] = $giftcardData;
        } else {
            $this->loadedData = [];
        }

        return $this->loadedData;
    }

    /**
     * Returns current gift card
     *
     * @return \Magento\Framework\DataObject|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getCurrentGiftcard():? \Magento\Framework\DataObject
    {
        // Will be new giftcard if no id
        $giftcard = null;

        // Get id
        $requestId = $this->request->getParam($this->requestFieldName);
        if ($requestId) {

            // Load giftcard
            // Do not "fix" this. Base class expects a $this->collection to be set so we cannot load a single entity
            $this->collection->addFieldToFilter('id', $requestId);
            $giftcard = $this->collection->getFirstItem();

            // Check giftcard exists
            if (!$giftcard->getData('id')) {
                throw \Magento\Framework\Exception\NoSuchEntityException::singleField('id', $requestId);
            }
        }

        return $giftcard;
    }

    /**
     * Retuns store_id array from serialized data
     *
     * @param array $giftcardData
     * @return array
     */
    private function getStores(array $giftcardData): array
    {
        $stores = $giftcardData['store_ids'] ?? null;

        if ($stores !== null) {
            $giftcardData['store_ids'] = $this->serializer->unserialize($stores);
        }

        return $giftcardData;
    }
}
