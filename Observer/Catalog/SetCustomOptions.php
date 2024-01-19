<?php

namespace Bydn\Giftcard\Observer\Catalog;

class SetCustomOptions implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magento\Catalog\Model\Product\OptionFactory
     */
    protected $optionFactory;

    /**
     * @var \Bydn\Logger\Model\LoggerInterface
     */
    private \Bydn\Logger\Model\LoggerInterface $logger;

    /**
     * @param \Bydn\Logger\Model\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Catalog\Model\Product\OptionFactory $optionFactory,
        \Bydn\Logger\Model\LoggerInterface $logger
    ) {
        $this->optionFactory = $optionFactory;
        $this->logger = $logger;
    }

    /**
     * Setting attribute tab block for bundle
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        // Get product and check if it is a giftcard
        $product = $observer->getEvent()->getProduct();
        if ($product->getTypeId() == \Bydn\Giftcard\Model\Product\Type\Giftcard::TYPE_GIFTCARD) {

            $this->logger->writeInfo(__METHOD__, __LINE__, ': Adding options to giftcard');
            $this->addCustomOptionSenderName($product, 1);
            $this->addCustomOptionFriendName($product, 2);
            $this->addCustomOptionFriendEmail($product, 3);
            $this->addCustomOptionFriendMessage($product, 4);
            $this->addCustomOptionDateToSend($product, 5);
            $this->addCustomOptionAmount($product, 6);
        }
    }

    private function addCustomOptionSenderName($product, $order)
    {
        $optionData = array(
            'is_delete' => 0,
            'is_require' => true,
            'price_type' => 'fixed',
            'price' => 0,
            'sku' => 'sender-name',
            'max_characters' => 64,
            'title' => 'Sender name',
            'type' => 'field',
            'sort_order' => $order,
        );

        $optionInstance = $this->getOptionInstance($product);
        $optionInstance->unsetOptions();
        $optionInstance->setProductId($product->getId());
        $optionInstance->addData($optionData);

        $product->setCanSaveCustomOptions(true);
        $product->addOption($optionInstance);
    }

    private function addCustomOptionFriendName($product, $order)
    {
        $optionData = array(
            'is_delete' => 0,
            'is_require' => true,
            'price_type' => 'fixed',
            'price' => 0,
            'sku' => 'friend-name',
            'max_characters' => 64,
            'title' => 'Friend name',
            'type' => 'field',
            'sort_order' => $order,
        );

        $optionInstance = $this->getOptionInstance($product);
        $optionInstance->unsetOptions();
        $optionInstance->setProductId($product->getId());
        $optionInstance->addData($optionData);

        $product->setCanSaveCustomOptions(true);
        $product->addOption($optionInstance);
    }

    private function addCustomOptionFriendEmail($product, $order)
    {
        $optionData = array(
            'is_delete' => 0,
            'is_require' => true,
            'price_type' => 'fixed',
            'price' => 0,
            'sku' => 'friend-email',
            'max_characters' => 128,
            'title' => 'Friend email',
            'type' => 'field',
            'sort_order' => $order,
        );

        $optionInstance = $this->getOptionInstance($product);
        $optionInstance->unsetOptions();
        $optionInstance->setProductId($product->getId());
        $optionInstance->addData($optionData);

        $product->setCanSaveCustomOptions(true);
        $product->addOption($optionInstance);
    }

    private function addCustomOptionFriendMessage($product, $order)
    {
        $optionData = array(
            'is_delete' => 0,
            'is_require' => true,
            'price_type' => 'fixed',
            'price' => 0,
            'sku' => 'friend-message',
            'max_characters' => 512,
            'title' => 'Friend message',
            'type' => 'area',
            'sort_order' => $order,
        );

        $optionInstance = $this->getOptionInstance($product);
        $optionInstance->unsetOptions();
        $optionInstance->setProductId($product->getId());
        $optionInstance->addData($optionData);

        $product->setCanSaveCustomOptions(true);
        $product->addOption($optionInstance);
    }

    private function addCustomOptionDateToSend($product, $order)
    {
        $optionData = array(
            'is_delete' => 0,
            'is_require' => true,
            'price_type' => 'fixed',
            'price' => 0,
            'sku' => 'date-to-send',
            'title' => 'Date to send (set today to send immediately)',
            'type' => 'date',
            'sort_order' => $order,
        );

        $optionInstance = $this->getOptionInstance($product);
        $optionInstance->unsetOptions();
        $optionInstance->setProductId($product->getId());
        $optionInstance->addData($optionData);

        $product->setCanSaveCustomOptions(true);
        $product->addOption($optionInstance);
    }

    private function addCustomOptionAmount($product, $order)
    {
        // En esta opción son siempre valores fijos
        $options = array();

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

        $optionData = array (
            'is_delete'         => 0,
            'is_require'        => true,
            'previous_group'    => '',
            'title'             => 'Card amount',
            'type'              => 'drop_down',
            'values'            => $options,
            'sort_order'        => $order,
        );

        $optionInstance = $this->getOptionInstance($product);
        $optionInstance->unsetOptions();
        $optionInstance->setProductId($product->getId());
        $optionInstance->addData($optionData);

        $product->setCanSaveCustomOptions(true);
        $product->addOption($optionInstance);
    }

    /**
     * Genera una instancia de una custom option. No podemos usar la del modelo product porque se genera una vez
     * y se reutiliza. Aquí queremos crear una cada vez para poder crear varias opciones diferentes.
     * @param $product
     * @return \Magento\Catalog\Model\Product\Option
     */
    private function getOptionInstance($product)
    {
        $optionInstance = $this->optionFactory->create();
        $optionInstance->setProduct($product);
        return $optionInstance;
    }
}
