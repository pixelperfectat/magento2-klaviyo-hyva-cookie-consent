/**
 * Gates Klaviyo's Luma checkout email step on marketing consent.
 *
 * The step calls klaviyo.identify() with the email typed at checkout and posts it to
 * reclaim/checkout/email. Both now wait for window.klaviyoConsentGranted(), which this module
 * defines in the page head. Without that function (no consent gating active) the step behaves as
 * Klaviyo ships it.
 */
define([], function () {
    'use strict';

    function consentAllows() {
        return typeof window.klaviyoConsentGranted !== 'function' || window.klaviyoConsentGranted() === true;
    }

    return function (Component) {
        return Component.extend({
            isKlaviyoActive: function () {
                return consentAllows() && this._super();
            },

            postUserEmail: function (customerEmail) {
                if (!consentAllows()) {
                    return;
                }
                return this._super(customerEmail);
            }
        });
    };
});
