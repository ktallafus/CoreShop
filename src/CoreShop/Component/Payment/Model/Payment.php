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

namespace CoreShop\Component\Payment\Model;

use CoreShop\Component\Core\Model\Currency;
use CoreShop\Component\Currency\Model\CurrencyInterface;
use CoreShop\Component\Resource\Model\SetValuesTrait;
use CoreShop\Component\Resource\Model\TimestampableTrait;

class Payment extends \Payum\Core\Model\Payment implements PaymentInterface
{
    use SetValuesTrait;
    use TimestampableTrait;

    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var PaymentProviderInterface
     */
    protected $paymentProvider;

    /**
     * @var string
     */
    protected $currencyCode;

    /**
     * @var string
     */
    protected $state = PaymentInterface::STATE_NEW;

    /**
     * @var array
     */
    protected $details = [];

    /**
     * @var CurrencyInterface
     */
    protected $currency;

    /**
     * @var \DateTime
     */
    protected $datePayment;

    /**
     * @var int
     */
    protected $orderId;

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getPaymentProvider(): PaymentProviderInterface
    {
        return $this->paymentProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function setPaymentProvider(PaymentProviderInterface $paymentProvider): PaymentInterface
    {
        $this->paymentProvider = $paymentProvider;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrencyCode()
    {
        return $this->currency->getIsoCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrency(): CurrencyInterface
    {
        return $this->currency;
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrency($currency): PaymentInterface
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDatePayment(): \DateTime
    {
        return $this->datePayment;
    }

    /**
     * {@inheritdoc}
     */
    public function setDatePayment($datePayment): PaymentInterface
    {
        $this->datePayment = $datePayment;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * {@inheritdoc}
     */
    public function setState($state): PaymentInterface
    {
        $this->state = $state;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * {@inheritdoc}
     */
    public function setDetails($details)
    {
        if ($details instanceof \Traversable) {
            $details = iterator_to_array($details);
        }

        if (!is_array($details)) {
            $details = [];
        }

        $this->details = $details;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrderId(): int
    {
        return $this->orderId;
    }

    /**
     * {@inheritdoc}
     */
    public function setOrderId($orderId): PaymentInterface
    {
        $this->orderId = $orderId;

        return $this;
    }
}
