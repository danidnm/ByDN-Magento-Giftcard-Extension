define([
    'ko',
    'domReady!'
], function (ko) {
    'use strict';

    var giftcardCode = ko.observable(null);
    var isApplied = ko.observable(null);

    return {
        giftcardCode: giftcardCode,
        isApplied: isApplied,

        /**
         * @return {*}
         */
        getGiftcardCode: function () {
            return giftcardCode;
        },

        /**
         * @return {Boolean}
         */
        getIsApplied: function () {
            return isApplied;
        },

        /**
         * @param {*} giftcardCodeValue
         */
        setGiftcardCode: function (giftcardCodeValue) {
            giftcardCode(giftcardCodeValue);
        },

        /**
         * @param {Boolean} isAppliedValue
         */
        setIsApplied: function (isAppliedValue) {
            isApplied(isAppliedValue);
        }
    };
});
