<?php

namespace ArvPlentyConnectorEnhancements\tests\Unit;

use ArvPlentyConnectorEnhancements\Components\DecoratedProductResponseParser;
use ArvPlentyConnectorEnhancements\tests\BaseTestCase;
use PlentyConnector\Connector\TransferObject\Product\Barcode\Barcode;
use PlentymarketsAdapter\ResponseParser\Product\ProductResponseParserInterface;

/**
 * Class DecoratedProductResponseParserTest
 */
class DecoratedProductResponseParserTest extends BaseTestCase
{
    /**
     * @var ProductResponseParserInterface
     */
    private $parentResponseParser;

    protected function setUp()
    {
        $this->parentResponseParser = $this->createMock(ProductResponseParserInterface::class);
        $this->parentResponseParser->expects($this->once())->method('parse')->willReturnArgument(0);
    }

    /**
     * @dataProvider getTestData
     *
     * @param null|string $productIdentifierField
     * @param null|string $expectedValue
     * @param array       $variation
     */
    public function test_switch_product_identifier_to_expected_value(
        $productIdentifierField,
        $expectedValue,
        array $variation
    ) {
        $configReader = $this->getConfigReaderMock($productIdentifierField);

        $repsonseParser = new DecoratedProductResponseParser($configReader, $this->parentResponseParser);
        $productArray = $repsonseParser->parse($variation);

        self::assertEquals($expectedValue, $productArray['variations'][0]['number']);
    }

    /**
     * @return array
     */
    public function getTestData()
    {
        $variation = [
            'variations' => [
                [
                    'id' => 'id',
                    'number' => 'number',
                    'variationBarcodes' => [
                        [
                            'barcodeId' => Barcode::TYPE_GTIN13,
                            'code' => 'ean',
                        ],
                    ],
                ],
            ],
        ];

        return [
            ['id', 'id', $variation],
            ['number', 'number', $variation],
            ['ean', 'ean', $variation],
            [null, 'number', $variation],
        ];
    }
}
