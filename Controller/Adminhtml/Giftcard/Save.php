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
    private GiftcardResource $giftcardResource;
    private GiftcardFactory $giftcardFactory;
    private SerializerInterface $serializer;

    /**
     * Save constructor.
     * @param Context $context
     * @param GiftcardResource $giftcardResource
     * @param GiftcardFactory $giftcardFactory
     * @param SerializerInterface $serializer
     */
    public function __construct(
        Context             $context,
        GiftcardResource      $giftcardResource,
        GiftcardFactory       $giftcardFactory,
        SerializerInterface $serializer
    )
    {
        parent::__construct($context);

        $this->giftcardResource = $giftcardResource;
        $this->giftcardFactory = $giftcardFactory;
        $this->serializer = $serializer;
    }

    /**
     * @return Redirect|ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $giftcardPostData = $this->getRequest()->getPostValue();

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
     * @param array $giftcardPostData
     * @return array
     */
    private function saveStoreIds(array $giftcardPostData): array
    {
        $ids = $giftcardPostData['store_ids'] ?? [];
        if (\count($ids) > 0) $giftcardPostData['store_ids'] = $this->serializer->serialize($ids);

        return $giftcardPostData;
    }
}
