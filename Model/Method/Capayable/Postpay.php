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
 * to support@tig.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact support@tig.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\Buckaroo\Model\Method\Capayable;

use TIG\Buckaroo\Model\Method\Capayable;

class Postpay extends Capayable
{
    /** Payment Code */
    const PAYMENT_METHOD_CODE = 'tig_buckaroo_capayablepostpay';

    const CAPAYABLE_ORDER_SERVICE_ACTION = 'Pay';

    /** @var string */
    public $buckarooPaymentMethodCode = 'capayablepostpay';

    /** @var string */
    // @codingStandardsIgnoreLine
    protected $_code = 'tig_buckaroo_capayablepostpay';
}
