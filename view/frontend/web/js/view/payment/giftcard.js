define([
    'jquery',
    'ko',
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'Bydn_Giftcard/js/action/set-giftcard-code',
    'Bydn_Giftcard/js/action/cancel-giftcard',
    'Bydn_Giftcard/js/model/giftcard'
], function ($, ko, Component, quote, setGiftcardAction, cancelGiftcardAction, giftcard) {
    'use strict';

    var totals = quote.getTotals();
    var giftcardCode = giftcard.getGiftcardCode();
    var isApplied = giftcard.getIsApplied();

    if (totals()) {
        giftcardCode(totals()['giftcard_code']);
    }
    isApplied(giftcardCode() != null);

    return Component.extend({
        defaults: {
            template: 'Bydn_Giftcard/payment/giftcard'
        },
        giftcardCode: giftcardCode,
        isApplied: isApplied,

        /**
         * Giftcard code application procedure
         */
        apply: function () {
            if (this.validate()) {
                setGiftcardAction(giftcardCode(), isApplied);
            }
        },

        /**
         * Cancel using giftcard
         */
        cancel: function () {
            if (this.validate()) {
                giftcardCode('');
                cancelGiftcardAction(isApplied);
            }
        },

        /**
         * Giftcard form validation
         *
         * @returns {Boolean}
         */
        validate: function () {
            var form = '#giftcard-form';
            return $(form).validation() && $(form).validation('isValid');
        }
    });
});
