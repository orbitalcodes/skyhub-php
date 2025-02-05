<?php
/**
 * B2W Digital - Companhia Digital
 *
 * Do not edit this file if you want to update this SDK for future new versions.
 * For support please contact the e-mail bellow:
 *
 * sdk@e-smart.com.br
 *
 * @category  SkyHub
 * @package   SkyHub
 *
 * @copyright Copyright (c) 2018 B2W Digital - BSeller Platform. (http://www.bseller.com.br).
 *
 * @author    Bruno Gemelli <bruno.gemelli@e-smart.com.br>
 */

namespace SkyHub\Api\Handler\Request\Shipment;

use SkyHub\Api\EntityInterface\Shipment\Plp;
use SkyHub\Api\Handler\Request\HandlerAbstract;
use SkyHub\Api\DataTransformer\Shipment\Plp\Group as GroupTransformer;
use SkyHub\Api\DataTransformer\Shipment\Order\Collect as CollectTransformer;

/**
 * Class PlpHandler
 *
 * @package SkyHub\Api\Handler\Request\Shipment
 */
class PlpHandler extends HandlerAbstract
{
    /**
     * @var int
     */
    const OFFSET_LIMIT = 25;

    /**
     * @var string
     */
    protected $baseUrlPath = '/shipments/b2w';

    /**
     * Retrieves a list of all PLP's in SkyHub.
     *
     * @return \SkyHub\Api\Handler\Response\HandlerInterface
     */
    public function plps()
    {
        /** @var \SkyHub\Api\Handler\Response\HandlerInterface $responseHandler */
        $responseHandler = $this->service()->get($this->baseUrlPath());

        return $responseHandler;
    }

    /**
     * Retrieves a list of all orders ready to be grouped in a PLP.
     *
     * @param int $offset
     *
     * @return \SkyHub\Api\Handler\Response\HandlerInterface
     */
    public function ordersReadyToGroup(int $offset = 1)
    {
        $query = [
            'offset' => min(max($offset, 1), self::OFFSET_LIMIT)
        ];

        /** @var \SkyHub\Api\Handler\Response\HandlerInterface $responseHandler */
        $responseHandler = $this->service()->get($this->baseUrlPath('/to_group', $query));

        return $responseHandler;
    }

    /**
     * Group multiple orders in a PLP.
     *
     * @param array $orders
     *
     * @return \SkyHub\Api\Handler\Response\HandlerInterface
     */
    public function group(array $orders)
    {
        $transformer = new GroupTransformer($orders);

        $body = $transformer->output();

        /** @var \SkyHub\Api\Handler\Response\HandlerInterface $responseHandler */
        $responseHandler = $this->service()->post($this->baseUrlPath(), $body);

        return $responseHandler;
    }

    /**
     * Get PLP file
     *
     * @param string $id
     *
     * @return \SkyHub\Api\Handler\Response\HandlerInterface
     */
    public function viewFile($id)
    {
        $query = [
            'plp_id' => $id
        ];

        /** @var \SkyHub\Api\Handler\Response\HandlerInterface $responseHandler */
        $responseHandler = $this->service()->get($this->baseUrlPath('/view', $query));

        return $responseHandler;
    }

    /**
     * Ungroup a PLP.
     *
     * @param string $id
     *
     * @return \SkyHub\Api\Handler\Response\HandlerInterface
     */
    public function ungroup($id)
    {
        $params = [
            'plp_id' => $id,
        ];

        /** @var \SkyHub\Api\Handler\Response\HandlerInterface $responseHandler */
        $responseHandler = $this->service()->delete($this->baseUrlPath(), $params);

        return $responseHandler;
    }

    /**
     * @return Plp
     */
    public function entityInterface()
    {
        return new Plp($this);
    }

    /**
     * Retrieves a list of all orders collectables in SkyHub.
     *
     * @param bool $requested orders that have already had your pickup requested
     * @param int|null $offset
     * @return \SkyHub\Api\Handler\Response\HandlerInterface
     */
    public function collectables(bool $requested, int $offset = null)
    {
        $query['requested'] = $requested ? 'true' : 'false';

        if ($offset !== null) {
            $query['offset'] = min(max($offset, 1), self::OFFSET_LIMIT);
        }

        /** @var \SkyHub\Api\Handler\Response\HandlerInterface $responseHandler */
        $responseHandler = $this->service()->get($this->baseUrlPath('/collectables', $query));

        return $responseHandler;
    }

    /**
     * Request collect multiple orders.
     *
     * @param array $orders
     *
     * @return \SkyHub\Api\Handler\Response\HandlerInterface
     */
    public function confirmCollection(array $orders)
    {
        $transformer = new CollectTransformer($orders);

        $body = $transformer->output();

        /** @var \SkyHub\Api\Handler\Response\HandlerInterface $responseHandler */
        $responseHandler = $this->service()->post($this->baseUrlPath('/confirm_collection'), $body);

        return $responseHandler;
    }
}
