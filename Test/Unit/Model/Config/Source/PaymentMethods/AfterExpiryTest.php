<?php
/**
 *
 *          ..::..
 *     ..::::::::::::..
 *   ::'''''':''::'''''::
 *   ::..  ..:  :  ....::
 *   ::::  :::  :  :   ::
 *   ::::  :::  :  ''' ::
 *   ::::..:::..::.....::
 *     ''::::::::::::''
 *          ''::''
 *
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to servicedesk@tig.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@tig.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\Buckaroo\Test\Unit\Model\Config\Source\PayPerEmail;

use TIG\Buckaroo\Model\Config\Source\PaymentMethods\AfterExpiry;
use TIG\Buckaroo\Test\BaseTest;

class AfterExpiryTest extends BaseTest
{
    protected $instanceClass = AfterExpiry::class;

    /**
     * @return array
     */
    public function toOptionArrayProvider()
    {
        return [
            [
                ['value' => 'amex',               'label' => 'American Express']
            ],
            [
                ['value' => 'eps',                'label' => 'EPS']
            ],
            [
                ['value' => 'giftcard',           'label' => 'Giftcards']
            ],
            [
                ['value' => 'giropay',            'label' => 'Giropay']
            ],
            [
                ['value' => 'ideal',              'label' => 'iDEAL']
            ],
            [
                ['value' => 'idealprocessing',    'label' => 'iDEAL Processing']
            ],
            [
                ['value' => 'mastercard',         'label' => 'Mastercard']
            ],
            [
                ['value' => 'paypal',             'label' => 'PayPal']
            ],
            [
                ['value' => 'sofortueberweisung', 'label' => 'Sofort Banking']
            ],
            [
                ['value' => 'transfer',           'label' => 'Bank Transfer']
            ],
            [
                ['value' => 'visa',               'label' => 'Visa']
            ],
            [
                ['value' => 'maestro',            'label' => 'Maestro']
            ],
            [
                ['value' => 'visaelectron',       'label' => 'Visa Electron']
            ],
            [
                ['value' => 'vpay',               'label' => 'V PAY']
            ]
        ];
    }

    /**
     * @param $paymentOption
     *
     * @dataProvider toOptionArrayProvider
     */
    public function testToOptionArray($paymentOption)
    {
        $instance = $this->getInstance();
        $result = $instance->toOptionArray();

        $this->assertContains($paymentOption, $result);
    }
}
