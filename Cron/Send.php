<?php

namespace Bydn\Giftcard\Cron;

use PHPUnit\Exception;

class Send
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
     * @var \Bydn\Giftcard\Model\MailSender
     */
    private $mailSender;

    /**
     * @var \Bydn\Logger\Model\LoggerInterface
     */
    private $logger;

    /**
     * @param \Bydn\Giftcard\Helper\Config $giftcardConfig
     * @param \Bydn\Logger\Model\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Bydn\Giftcard\Helper\Config $giftcardConfig,
        \Bydn\Giftcard\Model\ResourceModel\Giftcard\CollectionFactory $giftcardCollectionFactory,
        \Bydn\Giftcard\Model\ResourceModel\Giftcard $giftcardResource,
        \Bydn\Giftcard\Model\MailSender $mailSender,
        \Bydn\Logger\Model\LoggerInterface $logger
    ) {
        $this->date = $date;
        $this->giftcardConfig = $giftcardConfig;
        $this->giftcardCollectionFactory = $giftcardCollectionFactory;
        $this->giftcardResource = $giftcardResource;
        $this->mailSender = $mailSender;
        $this->logger = $logger;
    }

    public function sendCards() {
        $this->logger->writeInfo(__METHOD__, __LINE__, 'Ini');

        // Get all giftcards pending
        $collection = $this->giftcardCollectionFactory->create();
        $collection->addFieldToFilter('email_date', ['lteq' => $this->date->gmtDate()]);
        $collection->addFieldToFilter('email_sent', '0');
        $collection->addFieldToFilter('status', \Bydn\Giftcard\Model\Giftcard::GIFTCARD_PENDING);

        // Iterate over the giftcards, send and mark as sent with date
        foreach ($collection as $giftcard) {

            // Send the giftcard
            if ($this->mailSender->sendGiftcardEmail($giftcard) == 0) {

                // Save the giftcard with the new status
                $giftcard->setStatus(\Bydn\Giftcard\Model\Giftcard::GIFTCARD_ACTIVE);
                $giftcard->setEmailSent(1);
                $giftcard->setEmailDate($this->date->gmtDate());
                $this->giftcardResource->save($giftcard);
            }
        }

        $this->logger->writeInfo(__METHOD__, __LINE__, 'End');
    }
}
