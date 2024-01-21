<?php

namespace Bydn\Giftcard\Controller\Adminhtml\Giftcard;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Backend\App\Action\Context;
use Bydn\Giftcard\Model\GiftcardFactory;
use Bydn\Giftcard\Model\ResourceModel\Giftcard as GiftcardResource;

class Delete extends Action implements HttpPostActionInterface
{
    private GiftcardResource $giftcardResource;
    private GiftcardFactory $giftcardFactory;

    /**
     * Save constructor.
     * @param Context $context
     * @param GiftcardResource $giftcardResource
     * @param GiftcardFactory $giftcardFactory
     */
    public function __construct(
        Context $context,
        GiftcardResource $giftcardResource,
        GiftcardFactory $giftcardFactory
    ) {
        parent::__construct($context);

        $this->giftcardResource = $giftcardResource;
        $this->giftcardFactory = $giftcardFactory;
    }

    /**
     * @return Redirect|ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $giftcardId = $this->getRequest()->getParam('id');

        if ($giftcardId !== false) {
            try {
                $giftcard = $this->giftcardFactory->create();
                $this->giftcardResource->load($giftcard, $giftcardId);
                $this->giftcardResource->delete($giftcard);
                $this->messageManager->addSuccessMessage('Giftcard deleted successfully!');
            } catch (\Throwable $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $resultRedirect->setUrl($this->_redirect->getRefererUrl());
            }
        }

        return $resultRedirect->setPath('giftcard/grid/index');
    }
}
