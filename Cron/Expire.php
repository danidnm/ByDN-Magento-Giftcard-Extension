<?php

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
        $this->logger = $logger;    }

    public function expireCards() {
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
