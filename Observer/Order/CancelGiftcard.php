<?php

namespace Bydn\Giftcard\Observer\Order;

class CancelGiftcard implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Bydn\Giftcard\Model\ResourceModel\Giftcard\CollectionFactory
     */
    private $giftcardCollectionFactory;

    /**
     * @var \Bydn\Giftcard\Model\ResourceModel\Giftcard
     */
    private $giftcardResource;

    /**
     * @var \Bydn\Logger\Model\LoggerInterface
     */
    private $logger;

    /**
     * @param \Bydn\Giftcard\Model\ResourceModel\Giftcard\CollectionFactory $giftcardCollectionFactory
     * @param \Bydn\Giftcard\Model\ResourceModel\Giftcard $giftcardResource
     * @param \Bydn\Logger\Model\LoggerInterface $logger
     */
    public function __construct(
        \Bydn\Giftcard\Model\ResourceModel\Giftcard\CollectionFactory $giftcardCollectionFactory,
        \Bydn\Giftcard\Model\ResourceModel\Giftcard $giftcardResource,
        \Bydn\Logger\Model\LoggerInterface $logger
    ) {
        $this->giftcardCollectionFactory = $giftcardCollectionFactory;
        $this->giftcardResource = $giftcardResource;
        $this->logger = $logger;
    }

    /**
     * Check if giftcard has been used and discount the amount from balance
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->logger->writeInfo(__METHOD__, __LINE__, 'Ini');

        // Get order
        $creditmemo = $observer->getEvent()->getCreditmemo();
        if ($creditmemo) {

            $this->logger->writeInfo(__METHOD__, __LINE__, 'Processing creditmemo: ' . $creditmemo->getId());

            //  Extract giftcards
            $giftcardItems = $this->extractCreditmemoGiftcards($creditmemo);
            foreach ($giftcardItems as $giftcardItem) {

                // Cancel giftcard
                $this->cancelGiftcardWithCreditmemoItem($giftcardItem);
            }
        }

        $this->logger->writeInfo(__METHOD__, __LINE__, 'End');
    }

    /**
     * @param $item
     * @return void
     */
    private function cancelGiftcardWithCreditmemoItem($item) {

        // Extract order
        $itemId = $item->getOrderItemId();
        $qty = $item->getQty();

        // Find the giftcard
        $collection = $this->giftcardCollectionFactory->create();
        $collection->addFieldToFilter('item_id', $itemId);
        $collection->addFieldToFilter('status', ['in' => [
            \Bydn\Giftcard\Model\Giftcard::GIFTCARD_ACTIVE,
            \Bydn\Giftcard\Model\Giftcard::GIFTCARD_PENDING
        ]]);
        foreach ($collection as $giftcard) {
            $giftcard->setStatus(\Bydn\Giftcard\Model\Giftcard::GIFTCARD_CANCELED);
            $this->giftcardResource->save($giftcard);

            $this->logger->writeInfo(__METHOD__, __LINE__, 'Giftcard canceled: ' . $giftcard->getCode());

            if ((--$qty) <= 0) {
                break;
            }
        }
    }

    /**
     * Extract product giftcards from an order
     *
     * @return array
     */
    private function extractCreditmemoGiftcards($creditmemo) {

        // Get all items and iterate
        $giftcards = [];
        $items = $creditmemo->getItems();
        foreach ($items as $item) {
            if ($item->getOrderItem()->getProduct()->getTypeId() == \Bydn\Giftcard\Model\Product\Type\Giftcard::TYPE_GIFTCARD)
                $giftcards[] = $item;
        }

        return $giftcards;
    }
}
