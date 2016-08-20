/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    "jquery",
    "jquery/ui",
    "Magento_Catalog/js/product/list/toolbar"

], function($) {
    /**
     * ProductListToolbarForm Widget - this widget is setting cookie and submitting form according to toolbar controls
     */
    $.widget('mage.productListToolbarForm', $.mage.productListToolbarForm, {

        options: {
            modeControl: '[data-role="mode-switcher"]',
            directionControl: '[data-role="direction-switcher"]',
            orderControl: '[data-role="sorter"]',
            limitControl: '[data-role="limiter"]',
            mode: 'product_list_mode',
            direction: 'product_list_dir',
            order: 'product_list_order',
            limit: 'product_list_limit',
            modeDefault: 'grid',
            directionDefault: 'asc',
            orderDefault: 'position',
            limitDefault: '9',
            url: ''
        },

        _create: function () {
            this._super();
            //console.log('toolbar');
        },

        changeUrl: function (paramName, paramValue, defaultValue) {
            var urlPaths = this.options.url.split('?'),
                baseUrl = urlPaths[0],
                urlParams = urlPaths[1] ? urlPaths[1].split('&') : [],
                paramData = {},
                parameters;
            for (var i = 0; i < urlParams.length; i++) {
                parameters = urlParams[i].split('=');
                var paramKey = parameters[0] !== undefined
                    ? window.decodeURIComponent(parameters[0].replace(/\+/g, '%20')): '';
                paramData[paramKey] = parameters[1] !== undefined
                    ? window.decodeURIComponent(parameters[1].replace(/\+/g, '%20'))
                    : '';
            }
            paramData[paramName] = paramValue;
            if (paramValue == defaultValue) {
                delete paramData[paramName];
            }
            paramData = $.param(paramData);

            location.href = baseUrl + (paramData.length ? '?' + paramData : '');
        }

    });

    return $.mage.productListToolbarForm;
});
