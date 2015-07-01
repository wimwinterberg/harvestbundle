<?php
namespace WeAreBuilders\HarvestBundle\Library\Harvest;

use Harvest\Model\Client as BaseClient;

/**
 * Client
 *
 * Introduced getters
 *
 */
class Client extends BaseClient
{
    /**
     * Retrieve property 'id'
     *
     * @return string
     */
    public function getId()
    {
        return $this->get('id');
    }

    /**
     * Retrieve property 'name'
     *
     * @return string
     */
    public function getName()
    {
        return $this->get('name');
    }

    /**
     * Whether project is active
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->getActive() == 'true';
    }

    /**
     * Retrieve property 'active'
     *
     * @return string
     */
    public function getActive()
    {
        return $this->get('active');
    }

    /**
     * Retrieve property 'currency'
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->get('currency');
    }

    /**
     * Retrieve property 'highrise-id'
     *
     * @return string
     */
    public function getHighriseId()
    {
        return $this->get('highrise-id');
    }

    /**
     * Retrieve property 'cache-version'
     *
     * @return string
     */
    public function getCacheVersion()
    {
        return $this->get('cache-version');
    }

    /**
     * Retrieve property 'updated-at'
     *
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->get('updated-at');
    }

    /**
     * Retrieve property 'created-at'
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->get('created-at');
    }

    /**
     * Retrieve property 'currency-symbol'
     *
     * @return string
     */
    public function getCurrencySymbol()
    {
        return $this->get('currency-symbol');
    }

    /**
     * Retrieve property 'details'
     *
     * @return string
     */
    public function getDetails()
    {
        return $this->get('details');
    }

    /**
     * Retrieve property 'default-invoice-timeframe'
     *
     * @return string
     */
    public function getDefaultInvoiceTimeframe()
    {
        return $this->get('default-invoice-timeframe');
    }

    /**
     * Retrieve property 'last-invoice-kind'
     *
     * @return string
     */
    public function getLastInvoiceKind()
    {
        return $this->get('last-invoice-kind');
    }

    /**
     * Dump
     *
     * @return array
     */
    public function dump()
    {
        return $this->_values;
    }
}