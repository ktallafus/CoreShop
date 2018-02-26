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

namespace CoreShop\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Address\Model\ZoneInterface;
use CoreShop\Component\Core\Model\CategoryInterface;
use CoreShop\Component\Core\Model\CountryInterface;
use CoreShop\Component\Core\Model\CurrencyInterface;
use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Customer\Model\CustomerGroupInterface;
use CoreShop\Component\Product\Model\ProductPriceRuleInterface;
use CoreShop\Component\Product\Repository\ProductPriceRuleRepositoryInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Rule\Model\Action;
use CoreShop\Component\Rule\Model\ActionInterface;
use CoreShop\Component\Rule\Model\Condition;
use CoreShop\Component\Rule\Model\ConditionInterface;
use Doctrine\Common\Persistence\ObjectManager;

final class ProductPriceRuleContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var FactoryInterface
     */
    private $productPriceRuleFactory;

    /**
     * @var ProductPriceRuleRepositoryInterface
     */
    private $productPriceRuleRepository;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * ProductPriceRuleContext constructor.
     * @param SharedStorageInterface $sharedStorage
     * @param ObjectManager $objectManager
     * @param FactoryInterface $productPriceRuleFactory
     * @param ProductPriceRuleRepositoryInterface $productPriceRuleRepository
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        ObjectManager $objectManager,
        FactoryInterface $productPriceRuleFactory,
        ProductPriceRuleRepositoryInterface $productPriceRuleRepository)
    {
        $this->sharedStorage = $sharedStorage;
        $this->productPriceRuleFactory = $productPriceRuleFactory;
        $this->productPriceRuleRepository = $productPriceRuleRepository;
        $this->objectManager = $objectManager;
    }

    /**
     * @Given /^adding a product price rule named "([^"]+)"$/
     */
    public function addingAProductPriceRule($ruleName)
    {
        /**
         * @var $rule ProductPriceRuleInterface
         */
        $rule = $this->productPriceRuleFactory->createNew();
        $rule->setName($ruleName);

        $this->objectManager->persist($rule);
        $this->objectManager->flush();

        $this->sharedStorage->set('product-price-rule', $rule);
    }

    /**
     * @Given /^the (price rule "[^"]+") is active$/
     * @Given /^the (price rule) is active$/
     */
    public function theProductPriceRuleIsActive(ProductPriceRuleInterface $rule)
    {
        $rule->setActive(true);

        $this->objectManager->persist($rule);
        $this->objectManager->flush();
    }

    /**
     * @Given /^the (price rule "[^"]+") is inactive$/
     * @Given /^the (price rule) is inactive$/
     */
    public function theProductPriceRuleIsInActive(ProductPriceRuleInterface $rule)
    {
        $rule->setActive(false);

        $this->objectManager->persist($rule);
        $this->objectManager->flush();
    }

    /**
     * @Given /^the (price rule "[^"]+") has a condition countries with (country "[^"]+")$/
     * @Given /^the (price rule) has a condition countries with (country "[^"]+")$/
     */
    public function theProductPriceRuleHasACountriesCondition(ProductPriceRuleInterface $rule, CountryInterface $country)
    {
        $configuration = [
            'countries' => [
                $country->getId()
            ]
        ];

        $condition = new Condition();
        $condition->setType('countries');
        $condition->setConfiguration($configuration);

        $this->addCondition($rule, $condition);
    }

    /**
     * @Given /^the (price rule "[^"]+") has a condition customers with (customer "[^"]+")$/
     * @Given /^the (price rule) has a condition customers with (customer "[^"]+")$/
     */
    public function theProductPriceRuleHasACustomerCondition(ProductPriceRuleInterface $rule, CustomerInterface $customer)
    {
        $configuration = [
            'customers' => [
                $customer->getId()
            ]
        ];

        $condition = new Condition();
        $condition->setType('customers');
        $condition->setConfiguration($configuration);

        $this->addCondition($rule, $condition);
    }

    /**
     * @Given /^the (price rule "[^"]+") has a condition timespan which is valid from "([^"]+") to "([^"]+)"$/
     * @Given /^the (price rule) has a condition timespan which is valid from "([^"]+)" to "([^"]+)"$/
     */
    public function theProductPriceRuleHasATimeSpanCondition(ProductPriceRuleInterface $rule, $from, $to)
    {
        $from = new \DateTime($from);
        $to = new \DateTime($to);

        $configuration = [
            'dateFrom' => $from->getTimestamp() * 1000,
            'dateTo' => $to->getTimestamp() * 1000
        ];

        $condition = new Condition();
        $condition->setType('timespan');
        $condition->setConfiguration($configuration);

        $this->addCondition($rule, $condition);
    }

    /**
     * @Given /^the (price rule "[^"]+") has a condition customer-groups with (customer-group "[^"]+")$/
     * @Given /^the (price rule) has a condition customer-groups with (customer-group "[^"]+")$/
     */
    public function theProductPriceRuleHasACustomerGroupCondition(ProductPriceRuleInterface $rule, CustomerGroupInterface $group)
    {
        $configuration = [
            'customerGroups' => [
                $group->getId()
            ]
        ];

        $condition = new Condition();
        $condition->setType('customerGroups');
        $condition->setConfiguration($configuration);

        $this->addCondition($rule, $condition);
    }

    /**
     * @Given /^the (price rule "[^"]+") has a condition stores with (store "[^"]+")$/
     * @Given /^the (price rule) has a condition stores with (store "[^"]+")$/
     */
    public function theProductPriceRuleHasAStoreCondition(ProductPriceRuleInterface $rule, StoreInterface $store)
    {
        $configuration = [
            'stores' => [
                $store->getId()
            ]
        ];

        $condition = new Condition();
        $condition->setType('stores');
        $condition->setConfiguration($configuration);

        $this->addCondition($rule, $condition);
    }

    /**
     * @Given /^the (price rule "[^"]+") has a condition zones with (zone "[^"]+")$/
     * @Given /^the (price rule) has a condition zones with (zone "[^"]+")$/
     */
    public function theProductPriceRuleHasAZoneCondition(ProductPriceRuleInterface $rule, ZoneInterface $zone)
    {
        $configuration = [
            'zones' => [
                $zone->getId()
            ]
        ];

        $condition = new Condition();
        $condition->setType('zones');
        $condition->setConfiguration($configuration);

        $this->addCondition($rule, $condition);
    }

    /**
     * @Given /^the (price rule "[^"]+") has a condition currencies with (currency "[^"]+")$/
     * @Given /^the (price rule) has a condition currencies with (currency "[^"]+")$/
     */
    public function theProductPriceRuleHasACurrencyCondition(ProductPriceRuleInterface $rule, CurrencyInterface $currency)
    {
        $configuration = [
            'currencies' => [
                $currency->getId()
            ]
        ];

        $condition = new Condition();
        $condition->setType('currencies');
        $condition->setConfiguration($configuration);

        $this->addCondition($rule, $condition);
    }

    /**
     * @Given /^the (price rule "[^"]+") has a condition categories with (category "[^"]+")$/
     * @Given /^the (price rule) has a condition categories with (category "[^"]+")$/
     */
    public function theProductPriceRuleHasACategoriesCondition(ProductPriceRuleInterface $rule, CategoryInterface $category)
    {
        $configuration = [
            'categories' => [
                $category->getId()
            ]
        ];

        $condition = new Condition();
        $condition->setType('categories');
        $condition->setConfiguration($configuration);

        $this->addCondition($rule, $condition);
    }

    /**
     * @Given /^the (price rule "[^"]+") has a condition products with (product "[^"]+")$/
     * @Given /^the (price rule) has a condition products with (product "[^"]+")$/
     * @Given /^the (price rule) has a condition products with (product "[^"]+") and (product "[^"]+")$/
     */
    public function theProductPriceRuleHasAProductCondition(ProductPriceRuleInterface $rule, ProductInterface $product, ProductInterface $product2 = null)
    {
        $configuration = [
            'products' => [
                $product->getId()
            ]
        ];

        if (null !== $product2) {
            $configuration['products'][] = $product2->getId();
        }

        $condition = new Condition();
        $condition->setType('products');
        $condition->setConfiguration($configuration);

        $this->addCondition($rule, $condition);
    }

    /**
     * @Given /^the (price rule "[^"]+") has a action discount-percent with ([^"]+)% discount$/
     * @Given /^the (price rule) has a action discount-percent with ([^"]+)% discount$/
     */
    public function theProductPriceRuleHasADiscountPercentAction(ProductPriceRuleInterface $rule, $discount)
    {
        $configuration = [
            'percent' => intval($discount)
        ];

        $action = new Action();
        $action->setType('discountPercent');
        $action->setConfiguration($configuration);

        $this->addAction($rule, $action);
    }

    /**
     * @Given /^the (price rule "[^"]+") has a action discount with ([^"]+) in (currency "[^"]+") off$/
     * @Given /^the (price rule) has a action discount with ([^"]+) in (currency "[^"]+") off$/
     */
    public function theProductPriceRuleHasADiscountAmountAction(ProductPriceRuleInterface $rule, $amount, CurrencyInterface $currency)
    {
        $configuration = [
            'amount' => intval($amount),
            'currency' => $currency->getId()
        ];

        $action = new Action();
        $action->setType('discountAmount');
        $action->setConfiguration($configuration);

        $this->addAction($rule, $action);
    }

    /**
     * @Given /^the (price rule "[^"]+") has a action discount-price of ([^"]+) in (currency "[^"]+")$/
     * @Given /^the (price rule) has a action discount-price of ([^"]+) in (currency "[^"]+")$/
     */
    public function theProductPriceRuleHasADiscountPrice(ProductPriceRuleInterface $rule, $price, CurrencyInterface $currency)
    {
        $configuration = [
            'price' => intval($price),
            'currency' => $currency->getId()
        ];

        $action = new Action();
        $action->setType('discountPrice');
        $action->setConfiguration($configuration);

        $this->addAction($rule, $action);
    }

    /**
     * @Given /^the (price rule "[^"]+") has a action price of ([^"]+) in (currency "[^"]+")$/
     * @Given /^the (price rule) has a action price of ([^"]+) in (currency "[^"]+")$/
     */
    public function theProductPriceRuleHasAPrice(ProductPriceRuleInterface $rule, $price, CurrencyInterface $currency)
    {
        $configuration = [
            'price' => intval($price),
            'currency' => $currency->getId()
        ];

        $action = new Action();
        $action->setType('price');
        $action->setConfiguration($configuration);

        $this->addAction($rule, $action);
    }

    /**
     * @param ProductPriceRuleInterface $rule
     * @param ConditionInterface $condition
     */
    private function addCondition(ProductPriceRuleInterface $rule, ConditionInterface $condition)
    {
        $rule->addCondition($condition);

        $this->objectManager->persist($rule);
        $this->objectManager->flush();
    }

    /**
     * @param ProductPriceRuleInterface $rule
     * @param ActionInterface $action
     */
    private function addAction(ProductPriceRuleInterface $rule, ActionInterface $action)
    {
        $rule->addAction($action);

        $this->objectManager->persist($rule);
        $this->objectManager->flush();
    }
}
