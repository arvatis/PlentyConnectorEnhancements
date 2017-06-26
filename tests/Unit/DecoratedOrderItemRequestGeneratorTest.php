<?php

namespace ArvPlentyConnectorEnhancements\tests\Unit;

use ArvPlentyConnectorEnhancements\Components\DecoratedOrderItemRequestGenerator;
use ArvPlentyConnectorEnhancements\DataProvider\VariationDataProviderInterface;
use ArvPlentyConnectorEnhancements\tests\BaseTestCase;
use PlentyConnector\Connector\TransferObject\Order\Order;
use PlentyConnector\Connector\TransferObject\Order\OrderItem\OrderItem;
use PlentymarketsAdapter\RequestGenerator\Order\OrderItem\OrderItemRequestGeneratorInterface;

/**
 * Class DecoratedOrderItemRequestGeneratorTest
 */
class DecoratedOrderItemRequestGeneratorTest extends BaseTestCase
{
    /**
     * @var OrderItemRequestGeneratorInterface
     */
    private $parentResponseParser;

    /**
     * @var VariationDataProviderInterface
     */
    private $dataProvider;

    protected function setUp()
    {
        $this->parentResponseParser = $this->createMock(OrderItemRequestGeneratorInterface::class);
        $this->parentResponseParser->expects($this->once())->method('generate')->willReturn([
            'itemVariationId' => 'number', // default value
        ]);

        $this->dataProvider = $this->createMock(VariationDataProviderInterface::class);
        $this->dataProvider->expects($this->any())->method('getVariationIdentifierByBarcode')->willReturn('ean');
        $this->dataProvider->expects($this->any())->method('getVariationIdentifierByIdentifier')->willReturn('id');
    }

    /**
     * @dataProvider getTestData
     *
     * @param null|string $productIdentifierField
     * @param null|string $expectedValue
     * @param OrderItem   $orderItem
     * @param Order       $order
     */
    public function test_order_item_has_correct_variation_id_when_product_identifier_is_switched(
        $productIdentifierField,
        $expectedValue,
        OrderItem $orderItem,
        Order $order
    ) {
        $configReader = $this->getConfigReaderMock($productIdentifierField);

        $requestGenerator = new DecoratedOrderItemRequestGenerator(
            $configReader,
            $this->parentResponseParser,
            $this->dataProvider
        );

        $request = $requestGenerator->generate($orderItem, $order);

        $this->assertEquals($expectedValue, $request['itemVariationId']);
    }

    /**
     * @return array
     */
    public function getTestData()
    {
        $orderItem = new OrderItem();
        $orderItem->setNumber('number');

        $dummyOrder = new Order();

        return [
            ['id', 'id', $orderItem, $dummyOrder],
            ['number', 'number', $orderItem, $dummyOrder],
            ['ean', 'ean', $orderItem, $dummyOrder],
            [null, 'number', $orderItem, $dummyOrder],
        ];
    }
}
