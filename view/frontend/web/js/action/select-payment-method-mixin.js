define([
    'jquery',
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote',
    'Bydn_Giftcard/js/model/payment/giftcard-messages',
    'Magento_Checkout/js/action/set-payment-information-extended',
    'Magento_Checkout/js/action/get-totals',
    'Bydn_Giftcard/js/model/giftcard'
], function ($, wrapper, quote, messageContainer, setPaymentInformationExtended, getTotalsAction, giftcard) {
    'use strict';

    return function (selectPaymentMethodAction) {

        return wrapper.wrap(selectPaymentMethodAction, function (originalSelectPaymentMethodAction, paymentMethod) {

            originalSelectPaymentMethodAction(paymentMethod);

            if (paymentMethod === null) {
                return;
            }

            $.when(
                setPaymentInformationExtended(
                    messageContainer,
                    {
                        method: paymentMethod.method
                    },
                    true
                )
            ).done(
                function () {
                    var deferred = $.Deferred(),

                        /**
                         * Update giftcard form.
                         */
                        updateGiftcardCallback = function () {
                            if (quote.totals() && !quote.totals()['giftcard_code']) {
                                giftcard.setGiftcardCode('');
                                giftcard.setIsApplied(false);
                            }
                        };

                    getTotalsAction([], deferred);
                    $.when(deferred).done(updateGiftcardCallback);
                }
            );
        });
    };

});
