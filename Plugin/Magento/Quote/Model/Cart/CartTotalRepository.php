<?php

namespace Bydn\Giftcard\Plugin\Magento\Quote\Model\Cart;

class CartTotalRepository
{
    /**
     * @var \Magento\Quote\Api\Data\TotalsExtensionFactory
     */
    private $extensionFactory;

    /**
     * @var \Magento\Checkout\Model\Session $checkoutSession
     */
    private $checkoutSession;

    /**
     * @var \Bydn\Giftcard\Model\GiftcardQuoteRepository $giftcardQuoteRepository
     */
    private $giftcardQuoteRepository;

    /**
     * @param \Magento\Quote\Api\Data\TotalsExtensionFactory $extensionFactory
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Bydn\Giftcard\Model\GiftcardQuoteRepository $giftcardQuoteRepository
     */
    public function __construct(
        \Magento\Quote\Api\Data\TotalsExtensionFactory $extensionFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Bydn\Giftcard\Model\GiftcardQuoteRepository $giftcardQuoteRepository
    ) {
        $this->extensionFactory = $extensionFactory;
        $this->checkoutSession = $checkoutSession;
        $this->giftcardQuoteRepository = $giftcardQuoteRepository;
    }

    /**
     * Adds giftcard extension attribute data to a cart totals instance
     *
     * @param \Magento\Quote\Model\Cart\CartTotalRepository $subject
     * @param \Magento\Quote\Api\Data\TotalsInterface $result
     * @return \Magento\Quote\Api\Data\TotalsInterface
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGet(
        \Magento\Quote\Model\Cart\CartTotalRepository $subject,
        \Magento\Quote\Api\Data\TotalsInterface $result
    ) {
        if ($result->getExtensionAttributes() === null) {
            $extensionAttributes = $this->extensionFactory->create();
            $result->setExtensionAttributes($extensionAttributes);
        }

        $extensionAttributes = $result->getExtensionAttributes();

        $quoteId = $this->checkoutSession->getQuoteId();
        $giftcardData = $this->giftcardQuoteRepository->getByQuoteId($quoteId);
        $giftcardCode = $giftcardData->getGiftcardCode();
        if (empty($giftcardCode)) {
            return $result;
        }

        $extensionAttributes->setGiftcardCode($giftcardCode);
        $result->setExtensionAttributes($extensionAttributes);
        return $result;
    }
}
