<?php

namespace ArvPlentyConnectorEnhancements\Components;

use ArvPlentyConnectorEnhancements\DataProvider\VariationDataProviderInterface;
use PlentyConnector\Connector\TransferObject\Order\Order;
use PlentyConnector\Connector\TransferObject\Order\OrderItem\OrderItem;
use PlentymarketsAdapter\RequestGenerator\Order\OrderItem\OrderItemRequestGeneratorInterface;
use Shopware\Components\Plugin\ConfigReader;

/**
 * Class DecoratedOrderItemRequestGenerator
 */
class DecoratedOrderItemRequestGenerator implements OrderItemRequestGeneratorInterface
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var OrderItemRequestGeneratorInterface
     */
    private $parentRequestGenerator;

    /**
     * @var VariationDataProviderInterface
     */
    private $dataProvider;

    /**
     * DecoratedOrderItemRequestGenerator constructor.
     *
     * @param ConfigReader                       $configReader
     * @param OrderItemRequestGeneratorInterface $parentRequestGenerator
     * @param VariationDataProviderInterface     $dataProvider
     */
    public function __construct(
        ConfigReader $configReader,
        OrderItemRequestGeneratorInterface $parentRequestGenerator,
        VariationDataProviderInterface $dataProvider
    ) {
        $this->config = $configReader->getByPluginName('ArvPlentyConnectorEnhancements');
        $this->parentRequestGenerator = $parentRequestGenerator;
        $this->dataProvider = $dataProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(OrderItem $orderItem, Order $order)
    {
        $request = $this->parentRequestGenerator->generate($orderItem, $order);

        if ($this->config['product_identifier_field'] === 'number') {
            return $request;
        }

        if ($this->config['product_identifier_field'] === 'id') {
            $request['itemVariationId'] = $this->dataProvider->getVariationIdentifierByIdentifier($orderItem->getNumber());
        }

        if ($this->config['product_identifier_field'] === 'ean') {
            $request['itemVariationId'] = $this->dataProvider->getVariationIdentifierByBarcode($orderItem->getNumber());
        }

        return $request;
    }
}
