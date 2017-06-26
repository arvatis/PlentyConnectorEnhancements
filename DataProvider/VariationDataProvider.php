<?php

namespace ArvPlentyConnectorEnhancements\DataProvider;

use PlentymarketsAdapter\Client\ClientInterface;

/**
 * Class VariationDataProvider
 */
class VariationDataProvider implements VariationDataProviderInterface
{
    const TYPE_ID = 1;
    const TYPE_BARCODE = 2;

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * VariationDataProvider constructor.
     *
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param $barcode
     *
     * @return int
     */
    public function getVariationIdentifierByBarcode($barcode)
    {
        return $this->getVariationIdentifier(self::TYPE_BARCODE, $barcode);
    }

    /**
     * @param $identifier
     *
     * @return int
     */
    public function getVariationIdentifierByIdentifier($identifier)
    {
        return $this->getVariationIdentifier(self::TYPE_ID, $identifier);
    }

    /**
     * @param int    $type
     * @param string $param
     *
     * @return int
     */
    private function getVariationIdentifier($type, $param)
    {
        if (self::TYPE_ID === $type) {
            $field = 'id';
        } elseif (self::TYPE_BARCODE === $type) {
            $field = 'barcode';
        } else {
            throw new InvalidArgumentException();
        }

        $variations = $this->client->request('GET', 'items/variations', [$field => $param]);

        if (!empty($variations)) {
            $variation = array_shift($variations);

            return $variation['id'];
        }

        return 0;
    }
}
