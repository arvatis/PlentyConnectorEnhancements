<?php

namespace ArvPlentyConnectorEnhancements\Components;

use PlentyConnector\Connector\TransferObject\Product\Barcode\Barcode;
use PlentymarketsAdapter\ResponseParser\Product\ProductResponseParserInterface;
use Shopware\Components\Plugin\ConfigReader;

/**
 * Class DecoratedProductResponseParser
 */
class DecoratedProductResponseParser implements ProductResponseParserInterface
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var ProductResponseParserInterface
     */
    private $parentRroductResponseParser;

    /**
     * DecoratedProductResponseParser constructor.
     *
     * @param ConfigReader                   $configReader
     * @param ProductResponseParserInterface $parentRroductResponseParser
     */
    public function __construct(
        ConfigReader $configReader,
        ProductResponseParserInterface $parentRroductResponseParser
    ) {
        $this->config = $configReader->getByPluginName('ArvPlentyConnectorEnhancements');
        $this->parentRroductResponseParser = $parentRroductResponseParser;
    }

    /**
     * {@inheritdoc}
     */
    public function parse(array $product)
    {
        if (empty($this->config['product_identifier_field'])) {
            return $this->parentRroductResponseParser->parse($product);
        }

        foreach ($product['variations'] as $key => $variation) {
            if ($this->config['product_identifier_field'] === 'number') {
                continue;
            }

            if ($this->config['product_identifier_field'] === 'id') {
                $product['variations'][$key]['number'] = $variation['id'];
            }

            if ($this->config['product_identifier_field'] === 'ean') {
                $barcodes = array_filter($variation['variationBarcodes'], function (array $barcode) {
                    return $barcode['barcodeId'] === Barcode::TYPE_GTIN13;
                });

                if (empty($barcodes)) {
                    continue;
                }

                $barcode = array_shift($barcodes);
                $product['variations'][$key]['number'] = $barcode['code'];
            }
        }

        return $this->parentRroductResponseParser->parse($product);
    }
}
