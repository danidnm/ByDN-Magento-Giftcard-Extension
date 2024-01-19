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
     * @var \Bydn\Logger\Model\LoggerInterface
     */
    private $logger;

    /**
     * @param \Magento\Framework\Validation\ValidationResultFactory $validationResultFactory
     * @param \Bydn\Giftcard\Helper\Config $giftcardConfig
     * @param \Bydn\Giftcard\Model\ResourceModel\Giftcard $giftcardResource
     * @param \Bydn\Giftcard\Model\GiftcardFactory $giftcardFactory
     * @param \Bydn\Logger\Model\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\Validation\ValidationResultFactory $validationResultFactory,
        \Bydn\Giftcard\Helper\Config $giftcardConfig,
        \Bydn\Giftcard\Model\ResourceModel\Giftcard $giftcardResource,
        \Bydn\Giftcard\Model\GiftcardFactory $giftcardFactory,
        \Bydn\Logger\Model\LoggerInterface $logger
    ) {
        $this->validationResultFactory = $validationResultFactory;
        $this->giftcardConfig = $giftcardConfig;
        $this->giftcardResource = $giftcardResource;
        $this->giftcardFactory = $giftcardFactory;
        $this->logger = $logger;
    }

    /**
     * @param $quote
     * @return int
     */
    private function getGiftcardAppliedAmount($quote) {

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
        $this->logger->writeInfo(__METHOD__, __LINE__, ': Ini');

        $validationErrors = [];

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

        // Get giftcard amount
        $appliedAmount = $this->getGiftcardAppliedAmount($quote);
        if ($appliedAmount > 0) {

            // Check items for giftcards
            if ($this->giftcardConfig->avoidGiftcardWithGiftcard()) {
                foreach ($quote->getAllItems() as $item) {
                    if ($item->getProduct()->getTypeId() == \Bydn\Giftcard\Model\Product\Type\Giftcard::TYPE_GIFTCARD) {
                        $validationErrors[] = __('A giftcard cannot be purchased with another giftcard.');
                    }
                }
            }
        }

        $this->logger->writeInfo(__METHOD__, __LINE__, ': End');

        return [$this->validationResultFactory->create(['errors' => $validationErrors])];
    }
}
