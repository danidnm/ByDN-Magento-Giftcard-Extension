<?php

namespace Bydn\Giftcard\Model\Validation\Quote;

class Giftcard implements \Magento\Quote\Model\ValidationRules\QuoteValidationRuleInterface
{
    /**
     * @var \Magento\Framework\Validation\ValidationResultFactory
     */
    private $validationResultFactory;

    /**
     * @var \Bydn\Giftcard\Helper\Config
     */
    private $giftcardConfig;

    /**
     * @var \Bydn\Giftcard\Model\ResourceModel\Giftcard
     */
    private $giftcardResource;

    /**
     * @var \Bydn\Giftcard\Model\GiftcardFactory
     */
    private $giftcardFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @param \Magento\Framework\Validation\ValidationResultFactory $validationResultFactory
     * @param \Bydn\Giftcard\Helper\Config $giftcardConfig
     * @param \Bydn\Giftcard\Model\ResourceModel\Giftcard $giftcardResource
     * @param \Bydn\Giftcard\Model\GiftcardFactory $giftcardFactory
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\Validation\ValidationResultFactory $validationResultFactory,
        \Bydn\Giftcard\Helper\Config $giftcardConfig,
        \Bydn\Giftcard\Model\ResourceModel\Giftcard $giftcardResource,
        \Bydn\Giftcard\Model\GiftcardFactory $giftcardFactory,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->validationResultFactory = $validationResultFactory;
        $this->giftcardConfig = $giftcardConfig;
        $this->giftcardResource = $giftcardResource;
        $this->giftcardFactory = $giftcardFactory;
        $this->logger = $logger;
    }

    /**
     * Returns applied giftcard amount
     *
     * @param int $quote
     * @return int
     */
    private function getGiftcardAppliedAmount($quote)
    {

        // Get giftcard data
        $quoteExtension = $quote->getExtensionAttributes();
        $giftcardData = $quoteExtension->getGiftcardData();
        if (!$giftcardData) {
            return 0;
        }

        // Extract amount and check
        $appliedGiftcardCode = $giftcardData->getGiftcardCode();
        $appliedGiftcardAmount = $giftcardData->getGiftcardAmount();
        return $appliedGiftcardAmount;
    }

    /**
     * @inheritdoc
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @return array
     */
    public function validate(\Magento\Quote\Model\Quote $quote): array
    {
        $this->logger->info(': Ini');

        $validationErrors = [];

        // Check module enabled
        if (!$this->giftcardConfig->isEnabled()) {

            // Check giftcard in the cart
            foreach ($quote->getAllItems() as $item) {
                if ($item->getProduct()->getTypeId() == \Bydn\Giftcard\Model\Product\Type\Giftcard::TYPE_GIFTCARD) {
                    $validationErrors[] = __('Sorry. Giftcard purchases are disabled at this time.');
                }
            }

            // Check giftcard redemption in the cart
            if ($this->getGiftcardAppliedAmount($quote) > 0) {
                $validationErrors[] = __('Sorry. Giftcard redemption is disabled at this time.');
            }
        } else {

            // Check discounts in giftcards
            if ($this->giftcardConfig->avoidAnyDiscount()) {
                foreach ($quote->getAllItems() as $item) {
                    if ($item->getProduct()->getTypeId() == \Bydn\Giftcard\Model\Product\Type\Giftcard::TYPE_GIFTCARD) {
                        if ($item->getDiscountAmount() > 0) {
                            $validationErrors[] = __('Sorry. Discounts cannot be applied to a giftcard.');
                        }
                    }
                }
            }

            // Check giftcard with giftcard
            if ($this->getGiftcardAppliedAmount($quote) > 0) {

                // Check items for giftcards
                if ($this->giftcardConfig->avoidGiftcardWithGiftcard()) {
                    foreach ($quote->getAllItems() as $item) {
                        if ($item->getProduct()->getTypeId() ==
                            \Bydn\Giftcard\Model\Product\Type\Giftcard::TYPE_GIFTCARD) {
                            $validationErrors[] = __('A giftcard cannot be purchased with another giftcard.');
                        }
                    }
                }
            }
        }

        $this->logger->info(': End');

        return [$this->validationResultFactory->create(['errors' => $validationErrors])];
    }
}
