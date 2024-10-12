<?php

namespace Bydn\Giftcard\Helper;

class Config extends \Magento\Framework\App\Helper\AbstractHelper
{
    const PATH_GIFTCARD_ENABLE = 'bydn_giftcard/general/enable';
    const PATH_GIFTCARD_AMOUNTS = 'bydn_giftcard/general/amounts';
    const PATH_GIFTCARD_EMAIL_ENABLED = 'bydn_giftcard/emails/send_by_email';
    const PATH_GIFTCARD_EMAIL_SENDER = 'bydn_giftcard/emails/sender_email_identity';
    const PATH_GIFTCARD_EMAIL_TEMPLATE = 'bydn_giftcard/emails/email_template';
    const PATH_GIFTCARD_SINGLE_USE_ENABLED = 'bydn_giftcard/expiration/single_use';
    const PATH_GIFTCARD_EXPIRATION_ENABLED = 'bydn_giftcard/expiration/expire_cards';
    const PATH_GIFTCARD_EXPIRATION_TIME = 'bydn_giftcard/expiration/expiration_time';
    const PATH_GIFTCARD_WITH_GIFTCARD = 'bydn_giftcard/discounts/avoid_giftcard_with_giftcard';
    const PATH_GIFTCARD_AVOID_DISCOUNTS = 'bydn_giftcard/discounts/avoid_discounts_on_giftcards';

    /**
     * Get default giftcard amounts
     * @param $store_id
     * @return mixed
     */
    public function isEnabled($store_id = null) {
        return $this->scopeConfig->getValue(
            self::PATH_GIFTCARD_ENABLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store_id
        );
    }

    /**
     * Get default giftcard amounts
     * @param $store_id
     * @return mixed
     */
    public function getDefaultAmounts($store_id = null) {
        return $this->scopeConfig->getValue(
            self::PATH_GIFTCARD_AMOUNTS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store_id
        );
    }

    /**
     * Check if sending emails is enabled
     * @param $store_id
     * @return mixed
     */
    public function isEmailEnabled($store_id = null) {
        return $this->scopeConfig->getValue(
            self::PATH_GIFTCARD_EMAIL_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store_id
        );
    }

    /**
     * Returns the email sender identity
     * @param $store_id
     * @return mixed
     */
    public function getEmailSender($store_id = null) {
        return $this->scopeConfig->getValue(
            self::PATH_GIFTCARD_EMAIL_SENDER,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store_id
        );
    }

    /**
     * Returns the email template to be used
     * @param $store_id
     * @return mixed
     */
    public function getEmailTemplate($store_id = null) {
        return $this->scopeConfig->getValue(
            self::PATH_GIFTCARD_EMAIL_TEMPLATE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store_id
        );
    }

    /**
     * Checks if card expiration is enabled
     * @param $store_id
     * @return mixed
     */
    public function isSingleUseEnabled($store_id = null) {
        return $this->scopeConfig->getValue(
            self::PATH_GIFTCARD_SINGLE_USE_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store_id
        );
    }

    /**
     * Checks if card expiration is enabled
     * @param $store_id
     * @return mixed
     */
    public function isExpirationEnabled($store_id = null) {
        return $this->scopeConfig->getValue(
            self::PATH_GIFTCARD_EXPIRATION_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store_id
        );
    }

    /**
     * Checks if card expiration is enabled
     * @param $store_id
     * @return mixed
     */
    public function getExpirationDays($store_id = null) {
        return $this->scopeConfig->getValue(
            self::PATH_GIFTCARD_EXPIRATION_TIME,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store_id
        );
    }

    /**
     * Check if we should avoid paying a giftcard with another giftcard
     * @param $store_id
     * @return mixed
     */
    public function avoidGiftcardWithGiftcard($store_id = null) {
        return $this->scopeConfig->getValue(
            self::PATH_GIFTCARD_WITH_GIFTCARD,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store_id
        );
    }

    /**
     * Checks if we should avoid any discount applied to a giftcard
     * @param $store_id
     * @return mixed
     */
    public function avoidAnyDiscount($store_id = null) {
        return $this->scopeConfig->getValue(
            self::PATH_GIFTCARD_AVOID_DISCOUNTS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store_id
        );
    }
}
