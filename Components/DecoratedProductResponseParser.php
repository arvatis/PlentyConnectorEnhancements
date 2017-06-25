<?php

namespace ArvPlentyConnectorEnhancements\Components;

use PlentyConnector\Connector\TransferObject\Product\Barcode\Barcode;
use PlentyConnector\Connector\TransferObject\TransferObjectInterface;
use PlentymarketsAdapter\ResponseParser\Product\ProductResponseParserInterface;

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
     * @param ProductResponseParserInterface $parentRroductResponseParser
     */
    public function __construct(ProductResponseParserInterface $parentRroductResponseParser)
    {
        $this->parentRroductResponseParser = $parentRroductResponseParser;
        $this->config = Shopware()->Container()->get('shopware.plugin.config_reader')->getByPluginName('ArvPlentyConnectorEnhancements');
    }

    /**
     * @param array $product
     *
     * @return TransferObjectInterface[]
     */
    public function parse(array $product)
    {
        if (empty($this->config['ProductIdentifierField'])) {
            return $this->parentRroductResponseParser->parse($product);
        }

        foreach ($product['variations'] as $key => $variation) {
            if ($this->config['ProductIdentifierField'] === 'number') {
                continue;
            }

            if ($this->config['ProductIdentifierField'] === 'id') {
                $product['variations'][$key]['number'] = $variation['id'];
            }

            if ($this->config['ProductIdentifierField'] === 'ean') {
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
