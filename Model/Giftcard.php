<?php
/**
 *                  ___________       __            __
 *                  \__    ___/____ _/  |_ _____   |  |
 *                    |    |  /  _ \\   __\\__  \  |  |
 *                    |    | |  |_| ||  |   / __ \_|  |__
 *                    |____|  \____/ |__|  (____  /|____/
 *                                              \/
 *          ___          __                                   __
 *         |   |  ____ _/  |_   ____ _______   ____    ____ _/  |_
 *         |   | /    \\   __\_/ __ \\_  __ \ /    \ _/ __ \\   __\
 *         |   ||   |  \|  |  \  ___/ |  | \/|   |  \\  ___/ |  |
 *         |___||___|  /|__|   \_____>|__|   |___|  / \_____>|__|
 *                  \/                           \/
 *                  ________
 *                 /  _____/_______   ____   __ __ ______
 *                /   \  ___\_  __ \ /  _ \ |  |  \\____ \
 *                \    \_\  \|  | \/|  |_| ||  |  /|  |_| |
 *                 \______  /|__|    \____/ |____/ |   __/
 *                        \/                       |__|
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
 * @copyright Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license   http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\Buckaroo\Model;

use Magento\Framework\Model\AbstractModel;
use TIG\Buckaroo\Api\Data\GiftcardInterface;

class Giftcard extends AbstractModel implements GiftcardInterface
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'tig_buckaroo_giftcard';

    /**
     * @var string
     */
    protected $_eventObject = 'giftcard';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('TIG\Buckaroo\Model\ResourceModel\Giftcard');
    }

    /**
     * @return string
     */
    public function getServicecode()
    {
        return $this->getData('servicecode');
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->getData('label');
    }

    /**
     * @param string $servicecode
     *
     * @return $this
     */
    public function setServicecode($servicecode)
    {
        return $this->setData('servicecode', $servicecode);
    }

    /**
     * @param string $label
     *
     * @return $this
     */
    public function setLabel($label)
    {
        return $this->setData('label', $label);
    }
}
