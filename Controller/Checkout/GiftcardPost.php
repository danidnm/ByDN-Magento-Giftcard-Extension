<?php

namespace Bydn\Giftcard\Controller\Checkout;

class GiftcardPost implements \Magento\Framework\App\Action\HttpPostActionInterface
{
    public const RESULT_APPLIED = 'added';
    public const RESULT_REMOVED = 'removed';
    public const RESULT_NOT_VALID = 'not_valid';
    public const RESULT_GIFTCARD_WITH_GIFTCARD = 'giftcard_with_giftcard';

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $cart;

    /**
     * Sales quote repository
     *
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    private $jsonFactory;

    /**
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;

    /**
     * @var \Bydn\Giftcard\Helper\Config
     */
    protected $giftcardConfig;

    /**
     * @var \Bydn\Giftcard\Api\GiftcardRepositoryInterface
     */
    protected $giftcardRepository;

    /**
     * @var \Bydn\Giftcard\Api\GiftcardQuoteRepositoryInterface
     */
    protected $giftcardQuoteRepository;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     * @param \Magento\Framework\Escaper $escaper
     * @param \Bydn\Giftcard\Helper\Config $giftcardConfig
     * @param \Bydn\Giftcard\Api\GiftcardRepositoryInterface $giftcardRepository
     * @param \Bydn\Giftcard\Api\GiftcardQuoteRepositoryInterface $giftcardQuoteRepository
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Magento\Framework\Escaper $escaper,
        \Bydn\Giftcard\Helper\Config $giftcardConfig,
        \Bydn\Giftcard\Api\GiftcardRepositoryInterface $giftcardRepository,
        \Bydn\Giftcard\Api\GiftcardQuoteRepositoryInterface $giftcardQuoteRepository,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->request = $request;
        $this->cart = $cart;
        $this->quoteRepository = $quoteRepository;
        $this->jsonFactory = $jsonFactory;
        $this->escaper = $escaper;
        $this->giftcardConfig = $giftcardConfig;
        $this->giftcardRepository = $giftcardRepository;
        $this->giftcardQuoteRepository = $giftcardQuoteRepository;
        $this->logger = $logger;
    }

    /**
     * Checks if a new giftcard code is applicable to the cart
     *
     * @param string $code Giftcard code
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
     * Executes controller
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $this->logger->info('Ini');

        // New code applied or remove
        $newGiftcardCode = $this->request->getParam('remove') == 1
            ? ''
            : trim($this->request->getParam('giftcard_code'));

        // Get quote and giftcard data
        $cartQuote = $this->cart->getQuote();
        $quoteExtension = $cartQuote->getExtensionAttributes();
        $giftcardData = $quoteExtension->getGiftcardData();
        if (!$giftcardData) {
            return $this->returnResult(self::RESULT_NOT_VALID, $newGiftcardCode);
        }

        // Old applied code
        $oldGiftcardCode = $giftcardData->getGiftcardCode();

        // If no old coupon and no new...
        if (!strlen($newGiftcardCode) && !strlen($oldGiftcardCode)) {
            return $this->returnResult(self::RESULT_NOT_VALID, $newGiftcardCode);
        }

        $this->logger->info('Quote: ' . $cartQuote->getId());
        $this->logger->info('Old: ' . $oldGiftcardCode);
        $this->logger->info('New: ' . $newGiftcardCode);

        // Check code is OK
        if (strlen($newGiftcardCode) && !$this->isGiftcardCodeValid($newGiftcardCode)) {
            return $this->returnResult(self::RESULT_NOT_VALID, $newGiftcardCode);
        }

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
            return $this->returnResult(self::RESULT_REMOVED, $newGiftcardCode);
        } else {
            return $this->returnResult(self::RESULT_APPLIED, $newGiftcardCode);
        }
    }

    /**
     * Prepares the json result to be returned
     *
     * @param int $result
     * @param string $code
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function returnResult($result, $code)
    {
        if ($result == self::RESULT_APPLIED) {
            $message =  __(
                'You applied the giftcard: %1.',
                $this->escaper->escapeHtml($code)
            );
        } elseif ($result == self::RESULT_REMOVED) {
            $message =  __(
                'You removed the giftcard: %1.',
                $this->escaper->escapeHtml($code)
            );
        } else {
            $message =  __(
                'The giftcard code "%1" is not valid.',
                $this->escaper->escapeHtml($code)
            );
        }

        // Send request to the browser
        $resultJson = $this->jsonFactory->create();
        $resultJson->setData([
            'result' => $result,
            'message' => $message
        ]);
        return $resultJson;
    }
}
