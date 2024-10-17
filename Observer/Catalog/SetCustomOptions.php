<?php

namespace Bydn\Giftcard\Observer\Catalog;

class SetCustomOptions implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magento\Catalog\Model\Product\OptionFactory
     */
    protected $optionFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private \Psr\Log\LoggerInterface $logger;

    /**
     * @param \Magento\Catalog\Model\Product\OptionFactory $optionFactory
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Catalog\Model\Product\OptionFactory $optionFactory,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->optionFactory = $optionFactory;
        $this->logger = $logger;
    }

    /**
     * Setting attribute tab block for bundle
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        // Get product and check if it is a giftcard
        $product = $observer->getEvent()->getProduct();
        if ($product->getTypeId() == \Bydn\Giftcard\Model\Product\Type\Giftcard::TYPE_GIFTCARD) {

            $this->logger->info(': Adding options to giftcard');
            $this->addCustomOptionSenderName($product, 1);
            $this->addCustomOptionFriendName($product, 2);
            $this->addCustomOptionFriendEmail($product, 3);
            $this->addCustomOptionFriendMessage($product, 4);
            $this->addCustomOptionDateToSend($product, 5);
            $this->addCustomOptionAmount($product, 6);
        }
    }

    /**
     * Adds custom option to the product being created
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Sales\Model\Order $order
     * @return void
     */
    private function addCustomOptionSenderName($product, $order)
    {
        $optionData = [
            'is_delete' => 0,
            'is_require' => true,
            'price_type' => 'fixed',
            'price' => 0,
            'sku' => 'sender-name',
            'max_characters' => 64,
            'title' => 'Sender name',
            'type' => 'field',
            'sort_order' => $order,
        ];

        $optionInstance = $this->getOptionInstance($product);
        $optionInstance->unsetOptions();
        $optionInstance->setProductId($product->getId());
        $optionInstance->addData($optionData);

        $product->setCanSaveCustomOptions(true);
        $product->addOption($optionInstance);
    }

    /**
     * Adds custom option to the product being created
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Sales\Model\Order $order
     * @return void
     */
    private function addCustomOptionFriendName($product, $order)
    {
        $optionData = [
            'is_delete' => 0,
            'is_require' => true,
            'price_type' => 'fixed',
            'price' => 0,
            'sku' => 'friend-name',
            'max_characters' => 64,
            'title' => 'Friend name',
            'type' => 'field',
            'sort_order' => $order,
        ];

        $optionInstance = $this->getOptionInstance($product);
        $optionInstance->unsetOptions();
        $optionInstance->setProductId($product->getId());
        $optionInstance->addData($optionData);

        $product->setCanSaveCustomOptions(true);
        $product->addOption($optionInstance);
    }

    /**
     * Adds custom option to the product being created
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Sales\Model\Order $order
     * @return void
     */
    private function addCustomOptionFriendEmail($product, $order)
    {
        $optionData = [
            'is_delete' => 0,
            'is_require' => true,
            'price_type' => 'fixed',
            'price' => 0,
            'sku' => 'friend-email',
            'max_characters' => 128,
            'title' => 'Friend email',
            'type' => 'field',
            'sort_order' => $order,
        ];

        $optionInstance = $this->getOptionInstance($product);
        $optionInstance->unsetOptions();
        $optionInstance->setProductId($product->getId());
        $optionInstance->addData($optionData);

        $product->setCanSaveCustomOptions(true);
        $product->addOption($optionInstance);
    }

    /**
     * Adds custom option to the product being created
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Sales\Model\Order $order
     * @return void
     */
    private function addCustomOptionFriendMessage($product, $order)
    {
        $optionData = [
            'is_delete' => 0,
            'is_require' => true,
            'price_type' => 'fixed',
            'price' => 0,
            'sku' => 'friend-message',
            'max_characters' => 512,
            'title' => 'Friend message',
            'type' => 'area',
            'sort_order' => $order,
        ];

        $optionInstance = $this->getOptionInstance($product);
        $optionInstance->unsetOptions();
        $optionInstance->setProductId($product->getId());
        $optionInstance->addData($optionData);

        $product->setCanSaveCustomOptions(true);
        $product->addOption($optionInstance);
    }

    /**
     * Adds custom option to the product being created
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Sales\Model\Order $order
     * @return void
     */
    private function addCustomOptionDateToSend($product, $order)
    {
        $optionData = [
            'is_delete' => 0,
            'is_require' => true,
            'price_type' => 'fixed',
            'price' => 0,
            'sku' => 'date-to-send',
            'title' => 'Date to send (set today to send immediately)',
            'type' => 'date',
            'sort_order' => $order,
        ];

        $optionInstance = $this->getOptionInstance($product);
        $optionInstance->unsetOptions();
        $optionInstance->setProductId($product->getId());
        $optionInstance->addData($optionData);

        $product->setCanSaveCustomOptions(true);
        $product->addOption($optionInstance);
    }

    /**
     * Adds custom option to the product being created
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Sales\Model\Order $order
     * @return void
     */
    private function addCustomOptionAmount($product, $order)
    {
        // En esta opción son siempre valores fijos
        $options = [];

        $amounts = [30, 50, 75, 100, 150, 200, 250];

        $i = 0;
        foreach ($amounts as $amount) {
            $options[$i]['is_delete'] = '';
            $options[$i]['price_type'] = 'fixed';
            $options[$i]['title'] = $amount;
            $options[$i]['sku'] = 'price-'. $amount;
            $options[$i]['price'] = $amount;
            $options[$i]['sort_order'] = ($i+1);
            $i++;
        }

        $optionData =  [
            'is_delete'         => 0,
            'is_require'        => true,
            'previous_group'    => '',
            'title'             => 'Card amount',
            'type'              => 'drop_down',
            'values'            => $options,
            'sort_order'        => $order,
        ];

        $optionInstance = $this->getOptionInstance($product);
        $optionInstance->unsetOptions();
        $optionInstance->setProductId($product->getId());
        $optionInstance->addData($optionData);

        $product->setCanSaveCustomOptions(true);
        $product->addOption($optionInstance);
    }

    /**
     * Creates an instance of a custom option
     *
     * Creates an instance of a custom option. Cannot use the method from product instance because it is being
     * reused between calls. We want to create actual new instances of custom option every time.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return \Magento\Catalog\Model\Product\Option
     */
    private function getOptionInstance($product)
    {
        $optionInstance = $this->optionFactory->create();
        $optionInstance->setProduct($product);
        return $optionInstance;
    }
}
