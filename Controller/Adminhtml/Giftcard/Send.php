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

class Send extends Action implements HttpPostActionInterface
{
    /**
     * @var \Bydn\Giftcard\Model\ResourceModel\Giftcard
     */
    private \Bydn\Giftcard\Model\ResourceModel\Giftcard $giftcardResource;

    /**
     * @var \Bydn\Giftcard\Model\GiftcardFactory
     */
    private \Bydn\Giftcard\Model\GiftcardFactory $giftcardFactory;

    /**
     * @var \Bydn\Giftcard\Model\MailSender
     */
    private \Bydn\Giftcard\Model\MailSender $mailSender;

    /**
     * @param Action\Context $context
     * @param \Bydn\Giftcard\Model\ResourceModel\Giftcard $giftcardResource
     * @param \Bydn\Giftcard\Model\GiftcardFactory $giftcardFactory
     * @param \Bydn\Giftcard\Model\MailSender $mailSender
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Bydn\Giftcard\Model\ResourceModel\Giftcard $giftcardResource,
        \Bydn\Giftcard\Model\GiftcardFactory $giftcardFactory,
        \Bydn\Giftcard\Model\MailSender $mailSender
    ) {
        parent::__construct($context);

        $this->giftcardResource = $giftcardResource;
        $this->giftcardFactory = $giftcardFactory;
        $this->mailSender = $mailSender;
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
                $this->mailSender->sendGiftcardEmail($giftcard);
                $this->messageManager->addSuccessMessage('Giftcard email sent successfully!');
            } catch (\Throwable $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $resultRedirect->setUrl($this->_redirect->getRefererUrl());
            }
        }

        return $resultRedirect->setPath('giftcard/giftcard/edit', ['id' => $giftcard->getData('id')]);
    }
}
