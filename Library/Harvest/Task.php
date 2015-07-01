<?php
namespace WeAreBuilders\HarvestBundle\Library\Harvest;

/**
 * Sef_Harvest_Task
 * Introduced getters
 *
 */
class Task extends \Harvest\Model\Task
{
    /**
     * Retrieve property 'billable-by-default'
     *
     * @return string
     */
    public function getBillableByDefault()
    {
        return $this->get('billable-by-default');
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
     * Retrieve property 'created-at'
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->get('created-at');
    }

    /**
     * Retrieve property 'created-at'
     *
     * @return \Datetime
     */
    public function getCreatedAtInDateTime()
    {
        return new \DateTime($this->get('created-at'));
    }

    /**
     * Retrieve property 'deactivated'
     *
     * @return string
     */
    public function getDeactivated()
    {
        return $this->get('deactivated');
    }

    /**
     * Retrieve property 'default-hourly-rate'
     *
     * @return string
     */
    public function getDefaultHourlyRate()
    {
        return $this->get('default-hourly-rate');
    }

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
     * Retrieve property 'is-default'
     *
     * @return string
     */
    public function getIsDefault()
    {
        return $this->get('is-default');
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
     * Retrieve property 'updated-at'
     *
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->get('updated-at');
    }

    /**
     * Retrieve property 'updated-at'
     *
     * @return \Datetime
     */
    public function getUpdatedAtInDateTime()
    {
        return new \DateTime($this->get('updated-at'));
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