<?php

namespace Bydn\Giftcard\Observer;

/**
 * Magento or third party extensions sometimes loads entities without using the repository
 * With this we ensure extension attributes for giftcard are always attached to the entity
 */
class CreditmemoLoadExtensionAttribute implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Bydn\Giftcard\Api\GiftcardCreditmemoRepositoryInterface
     */
    private $giftcardCreditmemoRepository;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @param \Bydn\Giftcard\Api\GiftcardCreditmemoRepositoryInterface $giftcardCreditmemoRepository
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Bydn\Giftcard\Api\GiftcardCreditmemoRepositoryInterface $giftcardCreditmemoRepository,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->giftcardCreditmemoRepository = $giftcardCreditmemoRepository;
        $this->logger = $logger;
    }

    /**
     * Attach giftcard data to the creditmemo
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
//        $this->logger->info('Ini');

        $creditmemo = $observer->getEvent()->getCreditmemo();
        $creditmemoExtension = $creditmemo->getExtensionAttributes();
        if ($creditmemoExtension) {
            $creditmemoGiftcardData = $creditmemoExtension->getGiftcardData();
            if (!$creditmemoGiftcardData) {
                $creditmemoGiftcardData = $this->giftcardCreditmemoRepository->getByCreditmemoId($creditmemo->getId());
                $creditmemoExtension->setGiftcardData($creditmemoGiftcardData);
                $creditmemo->setExtensionAttributes($creditmemoExtension);
            }
        }

//        $this->logger->info('End');
    }
}
