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
    /**
     * @var GiftcardResource
     */
    private GiftcardResource $giftcardResource;

    /**
     * @var GiftcardFactory
     */
    private GiftcardFactory $giftcardFactory;

    /**
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
     * Controller logic
     *
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
