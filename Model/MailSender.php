<?php
/**
 * @package     Bydn_Giftcard
 * @author      Daniel Navarro <https://github.com/danidnm>
 * @license     GPL-3.0-or-later
 * @copyright   Copyright (c) 2025 Daniel Navarro
 *
 * This file is part of a free software package licensed under the
 * GNU General Public License v3.0.
 * You may redistribute and/or modify it under the same license.
 */

namespace Bydn\Giftcard\Model;

class MailSender
{
    private const XML_PATH_EMAIL_IDENTITY = 'contact/email/sender_email_identity';

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    private $inlineTranslation;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var \Magento\Framework\Escaper
     */
    private $escaper;

    /**
     * @var \Bydn\Giftcard\Helper\Config
     */
    private $giftcardConfig;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Framework\Escaper $escaper
     * @param \Bydn\Giftcard\Helper\Config $giftcardConfig
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\Escaper $escaper,
        \Bydn\Giftcard\Helper\Config $giftcardConfig,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->inlineTranslation = $inlineTranslation;
        $this->transportBuilder = $transportBuilder;
        $this->scopeConfig = $scopeConfig;
        $this->priceCurrency = $priceCurrency;
        $this->giftcardConfig = $giftcardConfig;
        $this->escaper = $escaper;
        $this->logger = $logger;
    }

    /**
     * Sends email for the giftcard
     *
     * @param \Bydn\Giftcard\Model\Giftcard $giftcard
     * @return int
     */
    public function sendGiftcardEmail($giftcard)
    {
        $this->logger->info('Sending giftcard: ' . $giftcard->getCode());

        try {
            $this->inlineTranslation->suspend();
            $priceFormat = $this->priceCurrency->convertAndFormat($giftcard->getTotalAmount(), false, 0);
            $priceFormat = htmlentities($priceFormat);
            $priceFormat = str_replace('&nbsp;', '', $priceFormat);
            $transport = $this->transportBuilder
                ->setTemplateIdentifier($this->giftcardConfig->getEmailTemplate())
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    ]
                )
                ->setTemplateVars([
                    'sender_name' => $giftcard->getSenderName(),
                    'friend_name' => $giftcard->getFriendName(),
                    'friend_message' => $giftcard->getFriendMessage(),
                    'giftcard_code' => $giftcard->getCode(),
                    'giftcard_amount' => $priceFormat,
                ])
                ->setFromByScope(
                    $this->scopeConfig->getValue(
                        self::XML_PATH_EMAIL_IDENTITY,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                    )
                )
                ->addTo($giftcard->getFriendEmail())
                ->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();

            $this->logger->info('Sent');
        } catch (\Exception $e) {
            $this->logger->info('ERROR Sending giftcard: ' . $e->getMessage());
            return -1;
        }

        return 0;
    }
}
