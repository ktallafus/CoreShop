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

namespace CoreShop\Bundle\OrderBundle\Controller;

use CoreShop\Bundle\MoneyBundle\CoreExtension\Money;
use CoreShop\Bundle\ResourceBundle\Controller\PimcoreController;
use CoreShop\Component\Address\Formatter\AddressFormatterInterface;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Address\Model\CountryInterface;
use CoreShop\Component\Customer\Model\CustomerInterface;
use CoreShop\Component\Currency\Model\CurrencyInterface;
use CoreShop\Component\Order\Model\CartPriceRuleInterface;
use CoreShop\Component\Order\Model\ProposalCartPriceRuleItemInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\Order\Model\SaleInterface;
use CoreShop\Component\Order\Model\SaleItemInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Repository\PimcoreRepositoryInterface;
use CoreShop\Component\Store\Model\StoreInterface;
use CoreShop\Component\Taxation\Model\TaxItemInterface;
use Pimcore\Admin\Helper\QueryParams;
use Pimcore\Model\DataObject;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractSaleController extends PimcoreController
{
    /**
     * @param mixed $data
     *
     * @return array
     */
    protected function getDataForObject($data)
    {
        if (!$data instanceof DataObject\AbstractObject) {
            return [];
        }

        $objectData = [];
        DataObject\Service::loadAllObjectFields($data);

        foreach ($data->getClass()->getFieldDefinitions() as $key => $def) {
            $getter = 'get' . ucfirst($key);
            $fieldData = $data->$getter();

            if ($def instanceof DataObject\ClassDefinition\Data\Href) {
                if ($fieldData instanceof DataObject\Concrete) {
                    $objectData[$key] = $this->getDataForObject($fieldData);
                }
            } elseif ($def instanceof DataObject\ClassDefinition\Data\Multihref) {
                $objectData[$key] = [];

                foreach ($fieldData as $object) {
                    if ($object instanceof DataObject\Concrete) {
                        $objectData[$key][] = $this->getDataForObject($object);
                    }
                }
            } elseif ($def instanceof DataObject\ClassDefinition\Data) {
                if ($def instanceof Money) {
                    $value = $fieldData;
                } else {
                    $value = $def->getDataForEditmode($fieldData, $data, false);
                }

                $objectData[$key] = $value;
            } else {
                $objectData[$key] = null;
            }
        }

        $objectData['o_id'] = $data->getId();
        $objectData['o_creationDate'] = $data->getCreationDate();
        $objectData['o_modificationDate'] = $data->getModificationDate();

        return $objectData;
    }
}
