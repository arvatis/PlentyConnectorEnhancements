<?php

namespace ArvPlentyConnectorEnhancements\tests;

use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Shopware\Components\Plugin\ConfigReader;

/**
 * Class BaseTestCase
 */
abstract class BaseTestCase extends TestCase
{
    /**
     * @param $productIdentifierField
     *
     * @return PHPUnit_Framework_MockObject_MockObject|ConfigReader
     */
    protected function getConfigReaderMock($productIdentifierField)
    {
        $configReader = $this->createMock(ConfigReader::class);
        $configReader
            ->expects($this->once())
            ->method('getByPluginName')
            ->with('ArvPlentyConnectorEnhancements')->willReturn([
                'product_identifier_field' => $productIdentifierField,
            ]);

        return $configReader;
    }
}
