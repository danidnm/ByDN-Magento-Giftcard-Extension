<?php

namespace Bydn\Giftcard\Observer\PayPal;

class AddTotal implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $session;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;


    /**
     * @param \Magento\Checkout\Model\Session $session
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Checkout\Model\Session $session,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->session = $session;
        $this->logger = $logger;
    }

    /**
     * Check if giftcard has been used and discount the amount from balance
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
//        $this->logger->info('Ini');

        $cart = $observer->getEvent()->getCart();
        $quote = $this->session->getQuote();

        $quoteExtensionAttributes = $quote->getExtensionAttributes();
        $quoteGiftcardData = $quoteExtensionAttributes->getGiftcardData();
        if ($quoteGiftcardData) {
            $cart->addCustomItem('Giftcard', 1, (-1) * $quoteGiftcardData->getGiftcardAmount());
        }

//        $this->logger->info('End');
    }
}
