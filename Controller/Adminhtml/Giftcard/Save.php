<?php

namespace Bydn\Giftcard\Controller\Adminhtml\Giftcard;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Serialize\SerializerInterface;
use Bydn\Giftcard\Model\GiftcardFactory;
use Bydn\Giftcard\Model\ResourceModel\Giftcard as GiftcardResource;
use Bydn\Giftcard\Model\Giftcard;

class Save extends Action implements HttpPostActionInterface
{
    /**
     * @var GiftcardResource
     */
    private GiftcardResource $giftcardResource;

    /**
     * @var GiftcardFactory
     */
    private GiftcardFactory $giftcardFactory;

    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\Filter\Date
     */
    private $dateFilter;

    /**
     * Save constructor.
     *
     * @param Context $context
     * @param GiftcardResource $giftcardResource
     * @param GiftcardFactory $giftcardFactory
     * @param SerializerInterface $serializer
     */
    public function __construct(
        Context             $context,
        GiftcardResource      $giftcardResource,
        GiftcardFactory       $giftcardFactory,
        SerializerInterface $serializer,
        \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter,

    ) {
        parent::__construct($context);

        $this->giftcardResource = $giftcardResource;
        $this->giftcardFactory = $giftcardFactory;
        $this->serializer = $serializer;

        $this->dateFilter = $dateFilter;
    }

    /**
     * Executes the controller
     *
     * @return Redirect|ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $giftcardPostData = $this->getRequest()->getPostValue();

        // Sets the fields to be filtered
        $filterValues = [
            'email_date' => $this->dateFilter,
            'expire_at' => $this->dateFilter
        ];

        // Fields are disabled but the value is still sent
        if (isset($giftcardPostData['created_at'])) unset($giftcardPostData['created_at']);
        if (isset($giftcardPostData['updated_at'])) unset($giftcardPostData['updated_at']);

        // Converts the dates to the correct format
        $inputFilter = new \Magento\Framework\Filter\FilterInput($filterValues, [], $giftcardPostData);

        // Retrieve the filtered data
        $giftcardPostData = $inputFilter->getUnescaped();

        $giftcard = $this->initGiftcard();
        $giftcardPostData = $this->saveStoreIds($giftcardPostData);
        $giftcard->addData($giftcardPostData);

        $isNew = ($giftcard->getId() == '');
        if ($isNew) {
            $giftcard->setAvailableAmount($giftcard->getTotalAmount());
        }

        try {
            $this->giftcardResource->save($giftcard);
            $this->messageManager->addSuccessMessage('Giftcard saved successfully!');
        } catch (\Throwable $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            return $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        }

        return $resultRedirect->setPath('giftcard/giftcard/edit', ['id' => $giftcard->getData('id')]);
    }

    /**
     * Initializes giftcard instance from parameter "id"
     *
     * @return Giftcard
     */
    private function initGiftcard(): Giftcard
    {
        $giftcard = $this->giftcardFactory->create();
        $giftcardId = (int)$this->getRequest()->getParam('id', false) ?:
            (int)$this->getRequest()->getParam('id', false);

        if ($giftcardId) {
            $this->giftcardResource->load($giftcard, $giftcardId);
        }

        return $giftcard;
    }

    /**
     * Serializes store_ids for the database
     *
     * @param array $giftcardPostData
     * @return array
     */
    private function saveStoreIds(array $giftcardPostData): array
    {
        $ids = $giftcardPostData['store_ids'] ?? [];
        if (\count($ids) > 0) {
            $giftcardPostData['store_ids'] = $this->serializer->serialize($ids);
        }

        return $giftcardPostData;
    }
}
