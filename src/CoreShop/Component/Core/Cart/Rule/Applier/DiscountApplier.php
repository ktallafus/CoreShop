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

namespace CoreShop\Component\Core\Cart\Rule\Applier;

use CoreShop\Component\Core\Product\ProductTaxCalculatorFactoryInterface;
use CoreShop\Component\Order\Distributor\ProportionalIntegerDistributor;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\ProposalCartPriceRuleItemInterface;
use CoreShop\Component\Taxation\Calculator\TaxCalculatorInterface;
use CoreShop\Component\Taxation\Collector\TaxCollectorInterface;

class DiscountApplier implements DiscountApplierInterface
{
    /**
     * @var ProportionalIntegerDistributor
     */
    private $distributor;

    /**
     * @var ProductTaxCalculatorFactoryInterface
     */
    private $taxCalculatorFactory;

    /**
     * @var TaxCollectorInterface
     */
    private $taxCollector;

    /**
     * @param ProportionalIntegerDistributor       $distributor
     * @param ProductTaxCalculatorFactoryInterface $taxCalculatorFactory
     * @param TaxCollectorInterface                $taxCollector
     */
    public function __construct(
        ProportionalIntegerDistributor $distributor,
        ProductTaxCalculatorFactoryInterface $taxCalculatorFactory,
        TaxCollectorInterface $taxCollector
    ) {
        $this->distributor = $distributor;
        $this->taxCalculatorFactory = $taxCalculatorFactory;
        $this->taxCollector = $taxCollector;
    }

    /**
     * {@inheritdoc}
     */
    public function applyDiscount(CartInterface $cart, ProposalCartPriceRuleItemInterface $cartPriceRuleItem, int $discount, $withTax = false)
    {
        $totalAmount = [];

        foreach ($cart->getItems() as $item) {
            $totalAmount[] = $item->getTotal(false);
        }

        $distributedAmount = $this->distributor->distribute($totalAmount, $discount);

        $totalDiscountNet = 0;
        $totalDiscountGross = 0;
        $i = 0;

        foreach ($cart->getItems() as $item) {
            $applicableAmount = $distributedAmount[$i++];
            $itemDiscountGross = 0;
            $itemDiscountNet = 0;

            if (0 === $applicableAmount) {
                continue;
            }

            if ($withTax) {
                $itemDiscountGross = $applicableAmount;
            }
            else {
                $itemDiscountNet = $applicableAmount;
            }

            $taxCalculator = $this->taxCalculatorFactory->getTaxCalculator(
                $item->getProduct(),
                $cart->getShippingAddress()
            );

            if ($taxCalculator instanceof TaxCalculatorInterface) {
                if ($withTax) {
                    $itemDiscountNet = $applicableAmount / (1 + $taxCalculator->getTotalRate() / 100);
                } else {
                    $itemDiscountGross = $applicableAmount * (1 + ($taxCalculator->getTotalRate() / 100));
                }

                $taxItems = $item->getTaxes();
                $taxItems->setItems($this->taxCollector->collectTaxes($taxCalculator, -1 * $itemDiscountNet, $taxItems->getItems()));
            }
            else {
                if ($withTax) {
                    $itemDiscountNet = $applicableAmount;
                } else {
                    $itemDiscountGross = $applicableAmount;
                }
            }

            $totalDiscountNet += $itemDiscountNet;
            $totalDiscountGross += $itemDiscountGross;
        }

        $cartPriceRuleItem->setDiscount((int)round($totalDiscountNet), false);
        $cartPriceRuleItem->setDiscount((int)round($totalDiscountGross), true);
    }
}
