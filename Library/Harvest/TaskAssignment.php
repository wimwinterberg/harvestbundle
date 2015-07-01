<?php
namespace WeAreBuilders\HarvestBundle\Library\Harvest;

/**
 * Sef_Harvest_TaskAssignment
 * Introduced getters
 *
 */
class TaskAssignment extends \Harvest\Model\TaskAssignment
{

    /**
     * Retrieve property 'billable'
     *
     * @return string
     */
    public function getBillable()
    {
        return $this->get('billable');
    }

    /**
     * Retrieve property 'budget'
     *
     * @return string
     */
    public function getBudget()
    {
        return $this->get('budget');
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
     * Retrieve property 'hourly-rate'
     *
     * @return string
     */
    public function getHourlyRate()
    {
        return $this->get('hourly-rate');
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
     * Retrieve property 'project-id'
     *
     * @return string
     */
    public function getProjectId()
    {
        return $this->get('project-id');
    }

    /**
     * Retrieve property 'task-id'
     *
     * @return string
     */
    public function getTaskId()
    {
        return $this->get('task-id');
    }

    /**
     * Retrieve property 'estimate'
     *
     * @return string
     */
    public function getEstimate()
    {
        return $this->get('estimate');
    }

    /**
     * Retrieve property 'deactivated'. Bool format
     *
     * @return bool
     */
    public function isDeactivated()
    {
        return $this->getDeactivated() == 'true';
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