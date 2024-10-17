<?php

namespace Bydn\Giftcard\Controller\Cart;

class GiftcardPost extends \Magento\Checkout\Controller\Cart implements
    \Magento\Framework\App\Action\HttpPostActionInterface
{
    /**
     * Sales quote repository
     *
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var \Magento\Framework\Escaper
     */
    protected \Magento\Framework\Escaper $escaper;

    /**
     * @var \Bydn\Giftcard\Api\GiftcardRepositoryInterface
     */
    protected \Bydn\Giftcard\Api\GiftcardRepositoryInterface $giftcardRepository;

    /**
     * @var \Bydn\Giftcard\Api\GiftcardQuoteRepositoryInterface
     */
    protected \Bydn\Giftcard\Api\GiftcardQuoteRepositoryInterface $giftcardQuoteRepository;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected \Psr\Log\LoggerInterface $logger;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Framework\Escaper $escaper
     * @param \Bydn\Giftcard\Api\GiftcardRepositoryInterface $giftcardRepository
     * @param \Bydn\Giftcard\Api\GiftcardQuoteRepositoryInterface $giftcardQuoteRepository
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Framework\Escaper $escaper,
        \Bydn\Giftcard\Api\GiftcardRepositoryInterface $giftcardRepository,
        \Bydn\Giftcard\Api\GiftcardQuoteRepositoryInterface $giftcardQuoteRepository,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::__construct(
            $context,
            $scopeConfig,
            $checkoutSession,
            $storeManager,
            $formKeyValidator,
            $cart
        );
        $this->giftcardRepository = $giftcardRepository;
        $this->giftcardQuoteRepository = $giftcardQuoteRepository;
        $this->quoteRepository = $quoteRepository;
        $this->escaper = $escaper;
        $this->logger = $logger;
    }

    /**
     * Checks if a new giftcard code is applicable to the cart
     *
     * @param string $code
     * @return bool
     */
    private function isGiftcardCodeValid($code)
    {

        try {
            // Try to load the card
            $code = trim($code);
            $card = $this->giftcardRepository->getByCode($code);

            // Check status...
            if ($card->getStatus() != \Bydn\Giftcard\Model\Giftcard::GIFTCARD_ACTIVE) {
                return false;
            }
            // ... and balance
            if ($card->getAvailableAmount() <= 0) {
                return false;
            }
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            return false;
        }

        return true;
    }

    /**
     * Initialize coupon
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $this->logger->info('Ini');

        // New code applied or remove
        $newGiftcardCode = $this->getRequest()->getParam('remove') == 1
            ? ''
            : trim($this->getRequest()->getParam('giftcard_code'));

        // Get quote and giftcard data
        $cartQuote = $this->cart->getQuote();
        $quoteExtension = $cartQuote->getExtensionAttributes();
        $giftcardData = $quoteExtension->getGiftcardData();
        if (!$giftcardData) {
            return $this->_goBack();
        }

        // Old applied code
        $oldGiftcardCode = $giftcardData->getGiftcardCode();

        // If no old coupon and no new...
        if (!strlen($newGiftcardCode) && !strlen($oldGiftcardCode)) {
            return $this->_goBack();
        }

        $this->logger->info('Quote: ' . $cartQuote->getId());
        $this->logger->info('Old: ' . $oldGiftcardCode);
        $this->logger->info('New: ' . $newGiftcardCode);

        // Check code is OK
        if (strlen($newGiftcardCode) && !$this->isGiftcardCodeValid($newGiftcardCode)) {
            $this->messageManager->addErrorMessage(
                __(
                    'The giftcard code "%1" is not valid.',
                    $this->escaper->escapeHtml($newGiftcardCode)
                )
            );
            return $this->_goBack();
        }

        // Apply new giftcard code (FIXME for multiple codes)
//        $allGiftcardCode = explode(',', $oldGiftcardCode);
//        $allGiftcardCode = array_map('trim', $allGiftcardCode);
//        if (strlen($newGiftcardCode)) {
//            $allGiftcardCode[] = $newGiftcardCode;
//        }
//        $allGiftcardCode = array_filter($allGiftcardCode);
//        $allGiftcardCode = implode(',', $allGiftcardCode);

        // One only code support
        $allGiftcardCode = $newGiftcardCode;

        $this->logger->info('All: ' . $allGiftcardCode);

        // Set in the quote and save
        $giftcardData->setGiftcardCode($allGiftcardCode);
        $quoteExtension->setGiftcardData($giftcardData);
        $cartQuote->setExtensionAttributes($quoteExtension);

        // Save the extension attributes as saving quote, first reloads it ....
        $this->giftcardQuoteRepository->save($giftcardData);

        // Collect totals and save
        $cartQuote->collectTotals();

        /// Save the quote
        $this->quoteRepository->save($cartQuote);

        // Give the message to the user
        if (!strlen($newGiftcardCode)) {
            $this->messageManager->addSuccessMessage(
                __(
                    'You removed the giftcard: %1.',
                    $this->escaper->escapeHtml($oldGiftcardCode)
                )
            );
        } else {
            $this->messageManager->addSuccessMessage(
                __(
                    'You applied the giftcard: %1.',
                    $this->escaper->escapeHtml($newGiftcardCode)
                )
            );
        }

        return $this->_goBack();
    }
}
