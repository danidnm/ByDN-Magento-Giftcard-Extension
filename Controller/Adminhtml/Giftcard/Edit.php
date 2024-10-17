<?php

namespace Bydn\Giftcard\Controller\Adminhtml\Giftcard;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\ResponseInterface;

class Edit extends Action implements HttpGetActionInterface
{
    /**
     * @var PageFactory
     */
    private PageFactory $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Controller logic
     *
     * @return ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        // Title changes if the giftcard is new
        $isExistingGiftcard = (bool)$this->getRequest()->getParam('id');

        // Create page and set active menu
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Bydn_Giftcard::grid');
        if ($isExistingGiftcard) {
            $resultPage->getConfig()->getTitle()->prepend(__('Edit Giftcard'));
        } else {
            $resultPage->getConfig()->getTitle()->prepend(__('New Giftcard'));
        }
        return $resultPage;
    }
}
