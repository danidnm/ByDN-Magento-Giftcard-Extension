define([
    'jquery',
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote',
    'Bydn_Giftcard/js/model/giftcard',
    'Magento_Checkout/js/action/get-totals'
], function ($, wrapper, quote, giftcard, getTotalsAction) {
    'use strict';

    return function (placeOrderAction) {
        return wrapper.wrap(placeOrderAction, function (originalAction, paymentData, messageContainer) {
            var result;

            $.when(
                result = originalAction(paymentData, messageContainer)
            ).fail(
                function () {
                    var deferred = $.Deferred(),

                        /**
                         * Update giftcard form
                         */
                        updateGiftcardCallback = function () {
                            if (giftcard.totals() && !giftcard.totals()['giftcard_code']) {
                                giftcard.setGiftcardCode('');
                                giftcard.setIsApplied(false);
                            }
                        };

                    getTotalsAction([], deferred);
                    $.when(deferred).done(updateGiftcardCallback);
                }
            );

            return result;
        });
    };
});
