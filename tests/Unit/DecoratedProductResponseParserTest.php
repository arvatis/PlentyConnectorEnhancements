<?php

namespace ArvPlentyConnectorEnhancements\tests\Unit;

use ArvPlentyConnectorEnhancements\Components\DecoratedProductResponseParser;
use PHPUnit\Framework\TestCase;
use PlentyConnector\Connector\TransferObject\Product\Barcode\Barcode;
use PlentymarketsAdapter\ResponseParser\Product\ProductResponseParserInterface;
use Shopware\Components\Plugin\ConfigReader;

/**
 * Class DecoratedProductResponseParserTest
 */
class DecoratedProductResponseParserTest extends TestCase
{
    /**
     * @param $productIdentifierField
     *
     * @return ConfigReader
     */
    private function getConfigReaderMock($productIdentifierField)
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

    /**
     * @return ProductResponseParserInterface
     */
    private function getParentParserMock()
    {
        $parentParser = $this->createMock( ProductResponseParserInterface::class);
        $parentParser
            ->expects($this->once())
            ->method('parse')
            ->willReturnArgument(0);

        return $parentParser;
    }

    /**
     * @dataProvider productProvider
     *
     * @param null|string $productIdentifierField
     * @param null|string $expectedValue
     * @param array $variation
     */
    public function test_switch_product_identifier($productIdentifierField, $expectedValue, array $variation)
    {
        $configReader = $this->getConfigReaderMock($productIdentifierField);
        $parentRepsonseParser = $this->getParentParserMock();

        $repsonseParser = new DecoratedProductResponseParser($configReader, $parentRepsonseParser);
        $productArray = $repsonseParser->parse($variation);

        $this->assertEquals($expectedValue, $productArray['variations'][0]['number']);
    }

    public function productProvider()
    {
        $variation = [
            'variations' => [
                [
                    'id' => 'id',
                    'number' => 'number',
                    'variationBarcodes' => [
                        [
                            'barcodeId' => Barcode::TYPE_GTIN13,
                            'code' => 'ean'
                        ]
                    ]
                ]
            ]
        ];

        return [
            ['id', 'id', $variation],
            ['number', 'number', $variation],
            ['ean', 'ean', $variation],
            [null, 'number', $variation],
        ];
    }
}
