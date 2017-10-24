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

coreshop.country.item = Class.create(coreshop.country.item, {
    getFormPanel: function ($super) {
        var panel = $super(),
            data = this.data;

        panel.down("fieldset").add([
            {
                xtype: 'coreshop.currency',
                value: data.currency
            },
            {
                xtype: 'coreshop.store',
                multiSelect: true,
                typeAhead: false,
                name: 'stores',
                value: data.stores
            }
        ]);

        this.formPanel = panel;

        return this.formPanel;
    }
});
