define([
    'jquery',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/resource-url-manager',
    'Magento_Checkout/js/model/error-processor',
    'Bydn_Giftcard/js/model/payment/giftcard-messages',
    'mage/storage',
    'Magento_Checkout/js/action/get-payment-information',
    'Magento_Checkout/js/model/totals',
    'mage/translate',
    'Magento_Checkout/js/model/full-screen-loader',
    'Magento_Checkout/js/action/recollect-shipping-rates'
], function ($, quote, urlManager, errorProcessor, messageContainer, storage, getPaymentInformationAction, totals, $t,
  fullScreenLoader, recollectShippingRates
) {
    'use strict';

    var successCallbacks = [],
        action,
        callSuccessCallbacks;

    /**
     * Execute callbacks when a coupon is successfully canceled.
     */
    callSuccessCallbacks = function () {
        successCallbacks.forEach(function (callback) {
            callback();
        });
    };

    /**
     * Cancel applied giftcard.
     *
     * @param {Boolean} isApplied
     * @returns {Deferred}
     */
    action =  function (isApplied) {
        var url = window.BASE_URL + 'checkout/checkout/giftcardPost',
            message = $t('Your giftcard was successfully removed.'),
            postData = {
                'giftcard_code' : '',
            }

        messageContainer.clear();
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

                    isApplied(false);
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
     * Callback for when the cancel-coupon process is finished.
     *
     * @param {Function} callback
     */
    action.registerSuccessCallback = function (callback) {
        successCallbacks.push(callback);
    };

    return action;
});
