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

namespace Bydn\Giftcard\Cron;

class Expire
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $date;

    /**
     * @var \Bydn\Giftcard\Helper\Config
     */
    private $giftcardConfig;

    /**
     * @var \Bydn\Giftcard\Model\ResourceModel\Giftcard\CollectionFactory
     */
    private $giftcardCollectionFactory;

    /**
     * @var \Bydn\Giftcard\Model\ResourceModel\Giftcard
     */
    private $giftcardResource;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Bydn\Giftcard\Helper\Config $giftcardConfig
     * @param \Bydn\Giftcard\Model\ResourceModel\Giftcard\CollectionFactory $giftcardCollectionFactory
     * @param \Bydn\Giftcard\Model\ResourceModel\Giftcard $giftcardResource
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Bydn\Giftcard\Helper\Config $giftcardConfig,
        \Bydn\Giftcard\Model\ResourceModel\Giftcard\CollectionFactory $giftcardCollectionFactory,
        \Bydn\Giftcard\Model\ResourceModel\Giftcard $giftcardResource,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->date = $date;
        $this->giftcardConfig = $giftcardConfig;
        $this->giftcardCollectionFactory = $giftcardCollectionFactory;
        $this->giftcardResource = $giftcardResource;
        $this->logger = $logger;
    }

    /**
     * Finds expired cards and expire them
     *
     * @return void
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function expireCards()
    {
        $this->logger->info('Ini');

        // Check expiration enabled
        if (!$this->giftcardConfig->isExpirationEnabled()) {
            $this->logger->info('Not enabled');
            return;
        }

        // Get all giftcards pending or active
        $collection = $this->giftcardCollectionFactory->create();
        $collection->addFieldToFilter('expire_at', ['lteq' => $this->date->gmtDate()]);
        $collection->addFieldToFilter('status', ['in' => [
            \Bydn\Giftcard\Model\Giftcard::GIFTCARD_PENDING,
            \Bydn\Giftcard\Model\Giftcard::GIFTCARD_ACTIVE
        ]]);

        // Iterate over the giftcards, send and mark as sent with date
        foreach ($collection as $giftcard) {

            // Save the giftcard with the new status
            $giftcard->setStatus(\Bydn\Giftcard\Model\Giftcard::GIFTCARD_EXPIRED);
            $giftcard->setEmailSent(1);
            $giftcard->setEmailDate($this->date->gmtDate());
            $this->giftcardResource->save($giftcard);
        }

        $this->logger->info('End');
    }
}
