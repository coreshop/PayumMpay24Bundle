/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('coreshop.provider.gateways.mpay24');
coreshop.provider.gateways.mpay24 = Class.create(coreshop.provider.gateways.abstract, {
    getLayout: function (config) {
        return [
            {
                xtype: 'checkbox',
                fieldLabel: t('mpay24_test'),
                name: 'gatewayConfig.config.test',
                value: config.test ? config.test : true
            },
            {
                xtype: 'textfield',
                fieldLabel: t('mpay24_merchant_id'),
                name: 'gatewayConfig.config.merchantId',
                length: 255,
                value: config.merchantId ? config.merchantId : ""
            },
            {
                xtype: 'textfield',
                fieldLabel: t('mpay24_password'),
                name: 'gatewayConfig.config.password',
                length: 255,
                value: config.password ? config.password : ""
            },
            {
                xtype: 'textfield',
                fieldLabel: t('mpay24_payment_type'),
                name: 'gatewayConfig.config.paymentType',
                length: 255,
                value: config.paymentType ? config.paymentType : ""
            },
            {
                xtype: 'textfield',
                fieldLabel: t('mpay24_brand'),
                name: 'gatewayConfig.config.brand',
                length: 255,
                value: config.brand ? config.brand : ""
            }
        ];
    }
});
