<?php

namespace Bydn\Giftcard\Observer\PayPal;

class AddTotal implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $session;

    /**
     * @var \Bydn\Logger\Model\LoggerInterface
     */
    private $logger;


    /**
     * @param \Magento\Checkout\Model\Session $session
     * @param \Bydn\Logger\Model\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Checkout\Model\Session $session,
        \Bydn\Logger\Model\LoggerInterface $logger
    ) {
        $this->session = $session;
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
//        $this->logger->writeInfo(__METHOD__, __LINE__, 'Ini');

        $cart = $observer->getEvent()->getCart();
        $quote = $this->session->getQuote();

        $quoteExtensionAttributes = $quote->getExtensionAttributes();
        $quoteGiftcardData = $quoteExtensionAttributes->getGiftcardData();
        if ($quoteGiftcardData) {
            $cart->addCustomItem('Giftcard', 1, (-1) * $quoteGiftcardData->getGiftcardAmount());
        }

//        $this->logger->writeInfo(__METHOD__, __LINE__, 'End');
    }
}
