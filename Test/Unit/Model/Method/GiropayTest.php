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
namespace TIG\Buckaroo\Test\Unit\Model\Method;

use Magento\Framework\DataObject;
use Magento\Payment\Model\InfoInterface;
use TIG\Buckaroo\Model\Method\Giropay;

class GiropayTest extends \TIG\Buckaroo\Test\BaseTest
{
    protected $instanceClass = Giropay::class;

    /**
     * @var Giropay
     */
    protected $object;

    /**
     * @var \TIG\Buckaroo\Gateway\Http\TransactionBuilderFactory|\Mockery\MockInterface
     */
    protected $transactionBuilderFactory;

    /**
     * @var \Magento\Framework\ObjectManagerInterface|\Mockery\MockInterface
     */
    protected $objectManager;

    /**
     * Setup the base mocks.
     */
    public function setUp()
    {
        parent::setUp();

        $productMetadata = \Mockery::mock(\Magento\Framework\App\ProductMetadata::class)->makePartial();
        $this->objectManager = \Mockery::mock(\Magento\Framework\ObjectManagerInterface::class);
        $this->objectManager->shouldReceive('get')
            ->with('Magento\Framework\App\ProductMetadataInterface')
            ->andReturn($productMetadata);

        $this->transactionBuilderFactory = \Mockery::mock(\TIG\Buckaroo\Gateway\Http\TransactionBuilderFactory::class);

        $this->object = $this->objectManagerHelper->getObject(
            Giropay::class,
            [
            'objectManager' => $this->objectManager,
            'transactionBuilderFactory' => $this->transactionBuilderFactory,
            ]
        );
    }

    /**
     * Test the assignData method.
     */
    public function testAssignData()
    {
        $data = $this->getObject(DataObject::class);
        $data->setBuckarooSkipValidation(0);
        $data->setAdditionalData([
            'customer_bic' => 'NL32INGB'
        ]);

        $infoInstanceMock = $this->getFakeMock(InfoInterface::class)
            ->setMethods(['setAdditionalInformation'])
            ->getMockForAbstractClass();
        $infoInstanceMock->expects($this->exactly(2))->method('setAdditionalInformation')->withConsecutive(
            ['buckaroo_skip_validation', 0],
            ['customer_bic', 'NL32INGB']
        );

        $instance = $this->getInstance();
        $instance->setData('info_instance', $infoInstanceMock);

        $result = $instance->assignData($data);
        $this->assertInstanceOf(Giropay::class, $result);
    }

    /**
     * Test the getOrderTransactionBuilder method.
     */
    public function testGetOrderTransactionBuilder()
    {
        $fixture = [
            'customer_bic' => 'biccib',
            'order' => 'orderrr!',
        ];

        $payment = \Mockery::mock(
            InfoInterface::class,
            \Magento\Sales\Api\Data\OrderPaymentInterface::class
        );

        $payment->shouldReceive('getOrder')->andReturn($fixture['order']);
        $payment->shouldReceive('getAdditionalInformation')->with('customer_bic')->andReturn($fixture['customer_bic']);

        $order = \Mockery::mock(\TIG\Buckaroo\Gateway\Http\TransactionBuilder\Order::class);
        $order->shouldReceive('setOrder')->with($fixture['order'])->andReturnSelf();
        $order->shouldReceive('setMethod')->with('TransactionRequest')->andReturnSelf();

        $order->shouldReceive('setServices')->andReturnUsing(
            function ($services) use ($fixture, $order) {
                $this->assertEquals('giropay', $services['Name']);
                $this->assertEquals($fixture['customer_bic'], $services['RequestParameter'][0]['_']);

                return $order;
            }
        );

        $this->transactionBuilderFactory->shouldReceive('get')->with('order')->andReturn($order);

        $infoInterface = \Mockery::mock(InfoInterface::class)->makePartial();

        $this->object->setData('info_instance', $infoInterface);
        $this->assertEquals($order, $this->object->getOrderTransactionBuilder($payment));
    }

    /**
     * Test the getCaptureTransactionBuilder method.
     */
    public function testGetCaptureTransactionBuilder()
    {
        $this->assertFalse($this->object->getCaptureTransactionBuilder(''));
    }

    /**
     * Test the getAuthorizeTransactionBuild method.
     */
    public function testGetAuthorizeTransactionBuilder()
    {
        $this->assertFalse($this->object->getAuthorizeTransactionBuilder(''));
    }

    /**
     * Test the getRefundTransactionBuilder method.
     */
    public function testGetRefundTransactionBuilder()
    {
        $payment = \Mockery::mock(
            InfoInterface::class,
            \Magento\Sales\Api\Data\OrderPaymentInterface::class
        );

        $payment->shouldReceive('getOrder')->andReturn('orderr');
        $payment->shouldReceive('getAdditionalInformation')->with(
            Giropay::BUCKAROO_ORIGINAL_TRANSACTION_KEY_KEY
        )->andReturn('getAdditionalInformation');

        $this->transactionBuilderFactory->shouldReceive('get')->with('refund')->andReturnSelf();
        $this->transactionBuilderFactory->shouldReceive('setOrder')->with('orderr')->andReturnSelf();
        $this->transactionBuilderFactory->shouldReceive('setServices')->andReturnUsing(
            function ($services) {
                $services['Name'] = 'giropay';
                $services['Action'] = 'Refund';

                return $this->transactionBuilderFactory;
            }
        );
        $this->transactionBuilderFactory->shouldReceive('setMethod')->with('TransactionRequest')->andReturnSelf();
        $this->transactionBuilderFactory->shouldReceive('setOriginalTransactionKey')
            ->with('getAdditionalInformation')
            ->andReturnSelf();
        $this->transactionBuilderFactory->shouldReceive('setChannel')->with('CallCenter')->andReturnSelf();

        $this->assertEquals($this->transactionBuilderFactory, $this->object->getRefundTransactionBuilder($payment));
    }

    /**
     * Test the getVoidTransactionBuild method.
     */
    public function testGetVoidTransactionBuilder()
    {
        $this->assertTrue($this->object->getVoidTransactionBuilder(''));
    }

    /**
     * Test the validation method happy path.
     */
    public function testValidate()
    {
        $paymentInfo = \Mockery::mock(InfoInterface::class);
        $paymentInfo->shouldReceive('getQuote', 'getBillingAddress')->andReturnSelf();
        $paymentInfo->shouldReceive('getCountryId')->andReturn(4);

        $paymentInfo->shouldReceive('getAdditionalInformation')->with('buckaroo_skip_validation')->andReturn(false);
        $paymentInfo->shouldReceive('getAdditionalInformation')->with('customer_bic')->andReturn('ABCDEF1E');

        $this->object->setData('info_instance', $paymentInfo);
        $result = $this->object->validate();

        $this->assertInstanceOf(Giropay::class, $result);
    }

    /**
     * Test the validation method happy path.
     */
    public function testValidateInvalidBic()
    {
        $paymentInfo = \Mockery::mock(InfoInterface::class);
        $paymentInfo->shouldReceive('getQuote', 'getBillingAddress')->andReturnSelf();
        $paymentInfo->shouldReceive('getCountryId')->andReturn(4);

        $paymentInfo->shouldReceive('getAdditionalInformation')->with('buckaroo_skip_validation')->andReturn(false);
        $paymentInfo->shouldReceive('getAdditionalInformation')->with('customer_bic')->andReturn('wrong');

        $this->object->setData('info_instance', $paymentInfo);

        try {
            $this->object->validate();
            $this->fail();
        } catch (\Exception $e) {
            $this->assertEquals('Please enter a valid BIC number', $e->getMessage());
            $this->assertInstanceOf(\Magento\Framework\Exception\LocalizedException::class, $e);
        }
    }

    /**
     * Test the validation method happy path.
     */
    public function testValidateSkipValidation()
    {
        $paymentInfo = \Mockery::mock(InfoInterface::class);
        $paymentInfo->shouldReceive('getQuote', 'getBillingAddress')->once()->andReturnSelf();
        $paymentInfo->shouldReceive('getCountryId')->once()->andReturn(4);
        $paymentInfo->shouldReceive('getAdditionalInformation')
            ->with('buckaroo_skip_validation')
            ->once()
            ->andReturn(true);

        $this->object->setData('info_instance', $paymentInfo);

        $result = $this->object->validate();

        $this->assertInstanceOf(Giropay::class, $result);
    }
}
