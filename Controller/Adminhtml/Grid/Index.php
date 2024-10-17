<?php

namespace Bydn\Giftcard\Controller\Adminhtml\Grid;

use Magento\Framework\View\Result\Page;

class Index extends \Magento\Backend\App\Action implements \Magento\Framework\App\Action\HttpGetActionInterface
{
    public const MENU_ID = 'Bydn_Giftcard::giftcard';
    public const ADMIN_RESOURCE = 'Bydn_Giftcard::grid';

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @param \Magento\Framework\View\Result\PageFactory $_resultPageFactory
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Framework\View\Result\PageFactory $_resultPageFactory,
        \Magento\Backend\App\Action\Context $context
    ) {
        parent::__construct($context);
        $this->_resultPageFactory = $_resultPageFactory;
    }

    /**
     * Executes the controller
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|Page
     */
    public function execute()
    {
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu(static::MENU_ID);
        $resultPage->getConfig()->getTitle()->prepend('Card List');
        return $resultPage;
    }
}
