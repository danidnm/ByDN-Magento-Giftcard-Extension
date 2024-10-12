define([
    'ko',
    'jquery',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/resource-url-manager',
    'Magento_Checkout/js/model/error-processor',
    'Bydn_Giftcard/js/model/payment/giftcard-messages',
    'mage/storage',
    'mage/translate',
    'Magento_Checkout/js/action/get-payment-information',
    'Magento_Checkout/js/model/totals',
    'Magento_Checkout/js/model/full-screen-loader',
    'Magento_Checkout/js/action/recollect-shipping-rates'
], function (ko, $, quote, urlManager, errorProcessor, messageContainer, storage, $t, getPaymentInformationAction,
    totals, fullScreenLoader, recollectShippingRates
) {
    'use strict';

    var dataModifiers = [],
        successCallbacks = [],
        failCallbacks = [],
        action;

    /**
     * Apply provided coupon.
     *
     * @param {String} couponCode
     * @param {Boolean}isApplied
     * @returns {Deferred}
     */
    action = function (couponCode, isApplied) {
        var quoteId = quote.getQuoteId(),
            url = window.BASE_URL + 'checkout/checkout/giftcardPost',
            message = $t('Your giftcard was successfully applied.'),
            errorMessage = $t('The giftcard code is not valid or already used.'),
            postData = {
                'giftcard_code' : couponCode,
            };

        fullScreenLoader.startLoader();

        console.log(url);
        console.log(postData);

        $.ajax({
            url: url,
            dataType: 'json',
            type: 'POST',
            data : postData,
            success : function (response) {
                var deferred;
                if(
                    response.result === 'added' ||
                    response.result === 'removed'
                ) {
                    deferred = $.Deferred();

                    isApplied(true);
                    totals.isLoading(true);
                    recollectShippingRates();
                    getPaymentInformationAction(deferred);
                    $.when(deferred).done(function () {
                        fullScreenLoader.stopLoader();
                        totals.isLoading(false);
                    });
                    messageContainer.addSuccessMessage({
                        'message': response.message
                    });
                }
                else {
                    fullScreenLoader.stopLoader();
                    totals.isLoading(false);
                    messageContainer.addErrorMessage({
                        'message': response.message
                    });
                }
            },
            error : function () {
                alert({
                    title : 'Error',
                    content :'There has been an error. Please try again later.'
                });
            }
        })
    };

    /**
     * Modifying data to be sent.
     *
     * @param {Function} modifier
     */
    action.registerDataModifier = function (modifier) {
        dataModifiers.push(modifier);
    };

    /**
     * When successfully added a coupon.
     *
     * @param {Function} callback
     */
    action.registerSuccessCallback = function (callback) {
        successCallbacks.push(callback);
    };

    /**
     * When failed to add a coupon.
     *
     * @param {Function} callback
     */
    action.registerFailCallback = function (callback) {
        failCallbacks.push(callback);
    };

    return action;
});
