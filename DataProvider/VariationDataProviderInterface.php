<?php

namespace ArvPlentyConnectorEnhancements\DataProvider;

/**
 * Interface VariationDataProviderInterface
 */
interface VariationDataProviderInterface
{
    /**
     * @param $barcode
     *
     * @return int
     */
    public function getVariationIdentifierByBarcode($barcode);

    /**
     * @param $identifier
     *
     * @return int
     */
    public function getVariationIdentifierByIdentifier($identifier);
}
