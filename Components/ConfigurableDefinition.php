<?php

namespace ArvPlentyConnectorEnhancements\Components;

use PlentyConnector\Connector\ValueObject\Definition\Definition;
use Shopware\Components\Plugin\ConfigReader;

/**
 * Class ConfigurableDefinition
 */
class ConfigurableDefinition extends Definition
{
    /**
     * @var array
     */
    private $config;

    /**
     * ConfigurableDefinition constructor.
     *
     * @param ConfigReader $configReader
     * @param Definition $parentDefinition
     */
    public function __construct(
        ConfigReader $configReader,
        Definition $parentDefinition
    ) {
        $this->config = $configReader->getByPluginName('ArvPlentyConnectorEnhancements');

        $this->setDestinationAdapterName($parentDefinition->getDestinationAdapterName());
        $this->setObjectType($parentDefinition->getObjectType());
        $this->setOriginAdapterName($parentDefinition->getOriginAdapterName());
        $this->setPriority($parentDefinition->getPriority());
    }

    /**
     * {@inheritdoc}
     */
    public function isActive()
    {
        if (!isset($this->config['syncProductData'])) {
            return true;
        }

        return (bool) $this->config['syncProductData'];
    }
}
