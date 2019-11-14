<?php
/*
*
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
*
*/
namespace TIG\Buckaroo\Model\Notification;

use Magento\Framework\FlagManager;
use Magento\Framework\View\Layout\Condition\VisibilityConditionInterface;

class CanViewNotification implements VisibilityConditionInterface
{
    /**
     * @var string
     */
    private static $conditionName = 'can_view_tig_notification';

    /** @var FlagManager $flagManager */
    private $flagManager;

    /**
     * @param FlagManager $flagManager
     */
    public function __construct(FlagManager $flagManager)
    {
        $this->flagManager = $flagManager;
    }

    /**
     * {@inheritdoc}
     */
    public function isVisible(array $arguments)
    {
        return ! (bool) $this->flagManager->getFlagData('tig_buckaroo_view_install_screen');
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return self::$conditionName;
    }
}
