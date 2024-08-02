define([
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote',
    'Bydn_Giftcard/js/model/giftcard'
], function (wrapper, quote, giftcard) {
    'use strict';

    return function (shippingSaveProcessor) {
        shippingSaveProcessor.saveShippingInformation = wrapper.wrapSuper(
            shippingSaveProcessor.saveShippingInformation,
            function (type) {
                var updateGiftcardCallback;

                /**
                 * Update giftcard form
                 */
                updateGiftcardCallback = function () {
                    if (quote.totals() && !quote.totals()['giftcard_code']) {
                        giftcard.setGiftcardCode('');
                        giftcard.setIsApplied(false);
                    }
                };

                return this._super(type).done(updateGiftcardCallback);
            }
        );

        return shippingSaveProcessor;
    };
});
