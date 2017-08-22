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

namespace CoreShop\Component\Address\Context;

use CoreShop\Component\Address\Model\CountryInterface;

final class FixedCountryContext implements CountryContextInterface
{
    /**
     * @var CountryInterface
     */
    private $country = null;

    /**
     * {@inheritdoc}
     */
    public function getCountry(): CountryInterface
    {
        if ($this->country instanceof CountryInterface) {
            return $this->country;
        }

        throw new CountryNotFoundException();
    }

    /**
     * @param CountryInterface $country
     */
    public function setCountry(CountryInterface $country)
    {
        $this->country = $country;
    }
}
