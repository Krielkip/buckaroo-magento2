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
 * @copyright Copyright (c) 2015 Total Internet Group B.V. (http://www.tig.nl)
 * @license   http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */

namespace TIG\Buckaroo\Model\Method;

class PayPerEmail extends AbstractMethod
{
    /**
     * Payment Code
     */
    const PAYMENT_METHOD_CODE = 'tig_buckaroo_payperemail';

    /**
     * @var string
     */
    public $buckarooPaymentMethodCode = 'payperemail';

    // @codingStandardsIgnoreStart
    /**
     * Payment method code
     *
     * @var string
     */
    protected $_code                    = self::PAYMENT_METHOD_CODE;

    /**
     * @var bool
     */
    protected $_isGateway               = true;

    /**
     * @var bool
     */
    protected $_canOrder                = true;

    /**
     * @var bool
     */
    protected $_canAuthorize            = false;

    /**
     * @var bool
     */
    protected $_canCapture              = false;

    /**
     * @var bool
     */
    protected $_canCapturePartial       = false;

    /**
     * @var bool
     */
    protected $_canRefund               = false;

    /**
     * @var bool
     */
    protected $_canVoid                 = true;

    /**
     * @var bool
     */
    protected $_canUseInternal          = true;

    /**
     * @var bool
     */
    protected $_canUseCheckout          = true;

    /**
     * @var bool
     */
    protected $_canRefundInvoicePartial = false;
    // @codingStandardsIgnoreEnd

    /** @var \TIG\Buckaroo\Service\CreditManagement\ServiceParameters */
    private $serviceParameters;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
        \Magento\Developer\Helper\Data $developmentHelper,
        \TIG\Buckaroo\Service\CreditManagement\ServiceParameters $serviceParameters,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        \TIG\Buckaroo\Gateway\GatewayInterface $gateway = null,
        \TIG\Buckaroo\Gateway\Http\TransactionBuilderFactory $transactionBuilderFactory = null,
        \TIG\Buckaroo\Model\ValidatorFactory $validatorFactory = null,
        \TIG\Buckaroo\Helper\Data $helper = null,
        \Magento\Framework\App\RequestInterface $request = null,
        \TIG\Buckaroo\Model\RefundFieldsFactory $refundFieldsFactory = null,
        \TIG\Buckaroo\Model\ConfigProvider\Factory $configProviderFactory = null,
        \TIG\Buckaroo\Model\ConfigProvider\Method\Factory $configProviderMethodFactory = null,
        \Magento\Framework\Pricing\Helper\Data $priceHelper = null,
        array $data = []
    ) {
        parent::__construct(
            $objectManager,
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger,
            $developmentHelper,
            $resource,
            $resourceCollection,
            $gateway,
            $transactionBuilderFactory,
            $validatorFactory,
            $helper,
            $request,
            $refundFieldsFactory,
            $configProviderFactory,
            $configProviderMethodFactory,
            $priceHelper,
            $data
        );

        $this->serviceParameters = $serviceParameters;
    }


    /**
     * {@inheritdoc}
     */
    public function assignData(\Magento\Framework\DataObject $data)
    {
        parent::assignData($data);
        $data = $this->assignDataConvertToArray($data);

        if (isset($data['additional_data']['customer_gender'])) {
            $this->getInfoInstance()
                ->setAdditionalInformation('customer_gender', $data['additional_data']['customer_gender']);
        }

        if (isset($data['additional_data']['customer_billingFirstName'])) {
            $this->getInfoInstance()
                ->setAdditionalInformation(
                    'customer_billingFirstName',
                    $data['additional_data']['customer_billingFirstName']
                );
        }

        if (isset($data['additional_data']['customer_billingLastName'])) {
            $this->getInfoInstance()
                ->setAdditionalInformation(
                    'customer_billingLastName',
                    $data['additional_data']['customer_billingLastName']
                );
        }

        if (isset($data['additional_data']['customer_email'])) {
            $this->getInfoInstance()
                ->setAdditionalInformation('customer_email', $data['additional_data']['customer_email']);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrderTransactionBuilder($payment)
    {
        $services = [];
        $services[] = $this->getPayperemailService($payment);

        $cmService = $this->serviceParameters->getCreateCombinedInvoice($payment, 'payperemail');
        if (count($cmService) > 0) {
            $services[] = $cmService;
        }

        $transactionBuilder = $this->transactionBuilderFactory->get('order');

        /**
         * @noinspection PhpUndefinedMethodInspection
         */
        $transactionBuilder->setOrder($payment->getOrder())
            ->setServices($services)
            ->setMethod('TransactionRequest');

        return $transactionBuilder;
    }

    /**
     * @param \Magento\Quote\Api\Data\CartInterface|null $quote
     * @return bool
     */
    public function isAvailable(\Magento\Quote\Api\Data\CartInterface $quote = null)
    {
        $areaCode = $this->_appState->getAreaCode();

        /** @var \TIG\Buckaroo\Model\ConfigProvider\Method\PayPerEmail $ppeConfig */
        $ppeConfig = $this->configProviderMethodFactory->get('payperemail');

        if (!$ppeConfig->isVisibleForAreaCode($areaCode)) {
            return false;
        }

        /**
         * Return the regular isAvailable result
         */
        return parent::isAvailable($quote);
    }

    /**
     * {@inheritdoc}
     */
    public function canProcessPostData($payment, $postData)
    {
        $transactionKey = $payment->getAdditionalInformation(AbstractMethod::BUCKAROO_ORIGINAL_TRANSACTION_KEY_KEY);
        if ($transactionKey != $postData['brq_transactions']) {
            return false;
        }

        return true;
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderPaymentInterface|\Magento\Payment\Model\InfoInterface $payment
     *
     * @return array
     */
    private function getPayperemailService($payment)
    {
        /** @var \TIG\Buckaroo\Model\ConfigProvider\Method\PayPerEmail $config */
        $config = $this->configProviderMethodFactory->get('payperemail');

        $services = [
            'Name'             => 'payperemail',
            'Action'           => 'PaymentInvitation',
            'Version'          => 1,
            'RequestParameter' => [
                [
                    '_'    => $payment->getAdditionalInformation('customer_gender'),
                    'Name' => 'customergender',
                ],
                [
                    '_'    => $payment->getAdditionalInformation('customer_email'),
                    'Name' => 'CustomerEmail',
                ],
                [
                    '_'    => $payment->getAdditionalInformation('customer_billingFirstName'),
                    'Name' => 'CustomerFirstName',
                ],
                [
                    '_'    => $payment->getAdditionalInformation('customer_billingLastName'),
                    'Name' => 'CustomerLastName',
                ],
                [
                    '_'    => $config->getSendMail() ? 'false' : 'true',
                    'Name' => 'MerchantSendsEmail',
                ],
                [
                    '_'    => $config->getPaymentMethod(),
                    'Name' => 'PaymentMethodsAllowed',
                ],
            ],
        ];

        return $services;
    }

    /**
     * {@inheritdoc}
     */
    protected function afterOrder($payment, $response)
    {
        if (empty($response[0]->Services->Service)) {
            return parent::afterOrder($payment, $response);
        }

        $invoiceKey = '';
        $services = $response[0]->Services->Service;

        if (!is_array($services)) {
            $services = [$services];
        }

        foreach ($services as $service) {
            if ($service->Name == 'CreditManagement3' && $service->ResponseParameter->Name == 'InvoiceKey') {
                $invoiceKey = $service->ResponseParameter->_;
            }
        }

        if (strlen($invoiceKey) > 0) {
            $payment->setAdditionalInformation('buckaroo_cm3_invoice_key', $invoiceKey);
        }

        return parent::afterOrder($payment, $response);
    }

    /**
     * {@inheritdoc}
     */
    public function getCaptureTransactionBuilder($payment)
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthorizeTransactionBuilder($payment)
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getRefundTransactionBuilder($payment)
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getVoidTransactionBuilder($payment)
    {
        $services = $this->serviceParameters->getCreateCreditNote($payment);

        if (count($services) <= 0) {
            return true;
        }

        $transactionBuilder = $this->transactionBuilderFactory->get('order');

        $transactionBuilder->setOrder($payment->getOrder())
            ->setAmount(0)
            ->setType('void')
            ->setServices($services)
            ->setMethod('DataRequest')
            ->setInvoiceId($payment->getOrder()->getIncrementId() . '-creditnote')
            ->setOriginalTransactionKey(
                $payment->getAdditionalInformation(
                    self::BUCKAROO_ORIGINAL_TRANSACTION_KEY_KEY
                )
            );

        return $transactionBuilder;
    }
}
