define([
        'Magento_Captcha/js/view/checkout/defaultCaptcha',
        'Magento_Captcha/js/model/captchaList',
        'Bydn_Giftcard/js/action/set-giftcard-code',
        'Bydn_Giftcard/js/action/cancel-giftcard',
        'Magento_Checkout/js/model/quote',
        'ko'
    ],
    function (defaultCaptcha, captchaList, setCouponCodeAction, cancelCouponAction, quote, ko) {
        'use strict';

        var totals = quote.getTotals(),
            couponCode = ko.observable(null),
            isApplied;

        if (totals()) {
            couponCode(totals()['coupon_code']);
        }
        //Captcha can only be required for adding a coupon so we need to know if one was added already.
        isApplied = ko.observable(couponCode() != null);

        return defaultCaptcha.extend({
            /** @inheritdoc */
            initialize: function () {
                var self = this,
                    currentCaptcha;

                this._super();
                //Getting coupon captcha model.
                currentCaptcha = captchaList.getCaptchaByFormId(this.formId);

                if (currentCaptcha != null) {
                    if (!isApplied()) {
                        //Show captcha if we don't have a coupon applied.
                        currentCaptcha.setIsVisible(true);
                    }
                    this.setCurrentCaptcha(currentCaptcha);
                    //Add captcha code to coupon-apply request.
                    setCouponCodeAction.registerDataModifier(function (headers) {
                        if (self.isRequired()) {
                            headers['X-Captcha'] = self.captchaValue()();
                        }
                    });
                    //Refresh captcha after failed request.
                    setCouponCodeAction.registerFailCallback(function () {
                        if (self.isRequired()) {
                            self.refresh();
                        }
                    });
                    //Hide captcha when a coupon has been applied.
                    setCouponCodeAction.registerSuccessCallback(function () {
                        self.setIsVisible(false);
                    });
                    //Show captcha again if it was canceled.
                    cancelCouponAction.registerSuccessCallback(function () {
                        if (self.isRequired()) {
                            self.setIsVisible(true);
                        }
                    });
                }
            }
        });
    });
