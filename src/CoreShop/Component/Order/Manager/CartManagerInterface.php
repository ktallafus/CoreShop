<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
*/

namespace CoreShop\Component\Order\Manager;

use CoreShop\Component\Order\Model\CartInterface;

interface CartManagerInterface
{
    /**
     * @param CartInterface $cart
     *
     * @return static
     */
    public function setCurrentCart(CartInterface $cart): CartManagerInterface;

    /**
     * @return CartInterface
     */
    public function getCart(): CartInterface;

    /**
     * @return bool
     */
    public function hasCart(): bool;

    /**
     * @param CartInterface $cart
     *
     * @return mixed
     */
    public function persistCart(CartInterface $cart): CartManagerInterface;

    /**
     * @param $name
     * @param null $user
     * @param null $store
     * @param null $currency
     * @param bool $persist
     *
     * @return CartInterface
     */
    public function createCart($name, $user = null, $store = null, $currency = null, $persist = false): CartInterface;

    /**
     * @param $user
     *
     * @return CartInterface[]
     */
    public function getStoredCarts($user): array;

    /**
     * @param $customer
     * @param $name
     *
     * @return CartInterface[]
     */
    public function getByName($customer, $name): array;

    /**
     * @param int $id
     *
     * @return bool
     */
    public function deleteCart($id): bool;
}
