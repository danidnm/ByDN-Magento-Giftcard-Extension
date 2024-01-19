<?php

namespace Bydn\Giftcard\Observer\Order;

class CreateGiftcards implements \Magento\Framework\Event\ObserverInterface
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
     * @var \Bydn\Giftcard\Model\GiftcardFactory
     */
    private $giftcardFactory;

    /**
     * @var \Bydn\Logger\Model\LoggerInterface
     */
    private \Bydn\Logger\Model\LoggerInterface $logger;

    /**
     * @var array
     */
    private $orderGiftcards;


    /**
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Bydn\Giftcard\Helper\Config $giftcardConfig
     * @param \Bydn\Giftcard\Model\ResourceModel\Giftcard\CollectionFactory $giftcardCollectionFactory
     * @param \Bydn\Giftcard\Model\ResourceModel\Giftcard $giftcardResource
     * @param \Bydn\Giftcard\Model\GiftcardFactory $giftcardFactory
     * @param \Bydn\Logger\Model\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Bydn\Giftcard\Helper\Config $giftcardConfig,
        \Bydn\Giftcard\Model\ResourceModel\Giftcard\CollectionFactory $giftcardCollectionFactory,
        \Bydn\Giftcard\Model\ResourceModel\Giftcard $giftcardResource,
        \Bydn\Giftcard\Model\GiftcardFactory $giftcardFactory,
        \Bydn\Logger\Model\LoggerInterface $logger
    ) {
        $this->date = $date;
        $this->giftcardConfig = $giftcardConfig;
        $this->giftcardCollectionFactory = $giftcardCollectionFactory;
        $this->giftcardResource = $giftcardResource;
        $this->giftcardFactory = $giftcardFactory;
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
        $order = $observer->getEvent()->getOrder();
        if ($order) {

            $this->logger->writeInfo(__METHOD__, __LINE__, 'Processing order: ' . $order->getId());

            // Check for status change.
            // This event should be only processed once when the order reaches state = complete
            // Also ensure we only give one giftcard per order (even if the orders reaches complete multiple times)
            if ($this->orderTransitionsToComplete($order)) {

                $this->logger->writeInfo(__METHOD__, __LINE__, 'Order is complete');

                //  Extract giftcards
                $giftcards = $this->extractOrderGiftcards($order);
                foreach ($giftcards as $giftcard) {

                    // Create if not exists
                    if (!$this->giftcardAlreadyCreated($order, $giftcard)) {
                        $this->createGiftcardForItem($order, $giftcard);
                    }
                }
            }
        }

        $this->logger->writeInfo(__METHOD__, __LINE__, 'End');
    }

    /**
     * Check if the orders is transitioning to status complete
     * @param $order
     * @return bool
     */
    private function orderTransitionsToComplete($order) {

        // Get current and new status and check
        $currentStatus = $order->getOrigData('status');
        $newStatus = $order->getData('status');
        return (($currentStatus != 'complete') && ($newStatus == 'complete'));
    }

    /**
     * Extract product giftcards from an order
     *
     * @return array
     */
    private function extractOrderGiftcards($order) {

        // Get all items and iterate
        $giftcards = [];
        $items = $order->getAllVisibleItems();
        foreach ($items as $item) {
            if ($item->getProduct()->getTypeId() == \Bydn\Giftcard\Model\Product\Type\Giftcard::TYPE_GIFTCARD)
            $giftcards[] = $item;
        }

        return $giftcards;
    }

    /**
     * Check if the orders already has a reward
     *
     * @param $item
     * @return bool
     */
    private function giftcardAlreadyCreated($order, $item) {

        // Get all order giftcards if any
        if (!$this->orderGiftcards) {

            $this->orderGiftcards = [];
            $collection = $this->giftcardCollectionFactory->create();
            $collection->addFieldToFilter('order_id', $order->getId());
            foreach ($collection as $giftcard) {
                $this->orderGiftcards[$giftcard->getItemId()] = $giftcard;
            }
        }

        // Check if exists
        return (isset($this->orderGiftcards[$item->getItemId()]));
    }

    /**
     * Create a giftcard for an order item
     *
     * @param $item
     */
    private function createGiftcardForItem($order, $item) {

        $this->logger->writeInfo(__METHOD__, __LINE__, 'Creating giftcard for order/item: ' . $order->getIncrementId() . ' / ' . $item->getItemId());

        // Improbable case, but possible. Some purchases two or more identical giftcards
        $qty = $item->getQtyOrdered();

        // Get Item Option Values
        $data = $this->extractOptions($item);

        // Usually will be only one, but create as many as purchased
        for($i=0; $i<$qty; $i++) {

            // Creates a new giftcard and set the data
            $giftcard = $this->giftcardFactory->create();
            $giftcard->setData($data);

            // Order data
            $giftcard->setCode($this->generateCode());
            $giftcard->setTotalAmount($item->getPriceInclTax());
            $giftcard->setAvailableAmount($item->getPriceInclTax());
            $giftcard->setCurrencyCode($order->getOrderCurrencyCode());
            $giftcard->setOrderId($order->getId());
            $giftcard->setItemId($item->getItemId());
            $giftcard->setEmailSent(0);
            $giftcard->setStatus(\Bydn\Giftcard\Model\Giftcard::GIFTCARD_PENDING);

            // Expire date (configurable)
            $expireDays = $this->giftcardConfig->getExpirationDays() ?? 365;
            $timeStamp = $this->date->timestamp("+" . $expireDays . " days");
            $expireDate = $this->date->date('Y-m-d H:i:s', $timeStamp);
            $giftcard->setExpireAt($expireDate);

            // Save the giftcard
            $this->giftcardResource->save($giftcard);
            $this->logger->writeInfo(__METHOD__, __LINE__, 'Giftcard created: ' . $giftcard->getCode());
        }

        $this->logger->writeInfo(__METHOD__, __LINE__, ': end');
    }

    /**
     * Generate a code for a giftcard and checkout it does still yet
     *
     * @return string
     */
    private function generateCode() {

        do {

            // Generate random code
            $allowedChars = '23456789BCDEFGHJKLMNPQRSTUVWXYZ';
            $newCode = 'GC-' . substr(str_shuffle($allowedChars), 0, 8);

            // Check does not exists
            $exists = false;
            $collection = $this->giftcardCollectionFactory->create();
            $collection->addFieldToFilter('code', $newCode);
            $exists = (count($collection) > 0);
        }
        while($exists);

        return $newCode;
    }

    /**
     * Prepares data array with giftcard data
     * @param $item
     * @return array
     */
    private function extractOptions($item) {

        // Extract product and giftcard options
        $product = $item->getProduct();
        $options = $product->getOptions();
        foreach ($options as $option) {
            $giftcardOptionIds[$option->getOptionId()] = $option->getSku();
        }

        // Extract options
        $giftcardData = [];
        $productOptions = $item->getProductOptions();
        $itemOptions = $productOptions['options'];
        foreach ($itemOptions as $itemOption) {

            // Extract option ID and find in the SKUs array
            $optionId = $itemOption['option_id'] ?? 'unknown';
            $sku = $giftcardOptionIds[$optionId] ?? null;
            if ($sku) {
                $key = $this->translateOptionSkuToDataKey($sku);
                if ($key) {
                    $giftcardData[$key] = $itemOption['option_value'];
                }
            }
        }

        return $giftcardData;
    }

    /**
     * @param $sku
     * @return string|null
     */
    private function translateOptionSkuToDataKey($sku) {

        switch ($sku) {
            case 'sender-name':
            case 'friend-name':
            case 'friend-email':
            case 'friend-message':
                return str_replace('-', '_', $sku);
            case 'date-to-send':
                return 'email_date';
        }

        return null;
    }
}
