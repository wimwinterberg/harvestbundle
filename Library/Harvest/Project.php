<?php
namespace WeAreBuilders\HarvestBundle\Library\Harvest;

/**
 * Sef_Harvest_Project
 * Introduced getters
 *
 */
class Project extends \Harvest\Model\Project
{
    /**
     * Retrieve property 'active'
     *
     * @return bool
     */
    public function getActive()
    {
        return $this->get('active') == 'true';
    }

    /**
     * Retrieve property 'active-task-assignments-count'
     *
     * @return string
     */
    public function getActiveTaskAssignmentsCount()
    {
        return $this->get('active-task-assignments-count');
    }

    /**
     * Retrieve property 'active-user-assignments-count'
     *
     * @return string
     */
    public function getActiveUserAssignmentsCount()
    {
        return $this->get('active-user-assignments-count');
    }

    /**
     * Retrieve property 'basecamp-id'
     *
     * @return string
     */
    public function getBasecampId()
    {
        return $this->get('basecamp-id');
    }

    /**
     * Retrieve property 'bill-by'
     *
     * @return string
     */
    public function getBillBy()
    {
        return $this->get('bill-by');
    }

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
     * Retrieve property 'budget-by'
     *
     * @return string
     */
    public function getBudgetBy()
    {
        return $this->get('budget-by');
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
     * Retrieve property 'client-id'
     *
     * @return string
     */
    public function getClientId()
    {
        return $this->get('client-id');
    }

    /**
     * Retrieve property 'code'
     *
     * @return string
     */
    public function getCode()
    {
        return $this->get('code');
    }

    /**
     * Retrieve property 'cost-budget'
     *
     * @return string
     */
    public function getCostBudget()
    {
        return $this->get('cost-budget');
    }

    /**
     * Retrieve property 'cost-budget-include-expenses'
     *
     * @return string
     */
    public function getCostBudgetIncludeExpenses()
    {
        return $this->get('cost-budget-include-expenses');
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
     * Retrieve property 'fees'
     *
     * @return string
     */
    public function getFees()
    {
        return $this->get('fees');
    }

    /**
     * Retrieve property 'highrise-deal-id'
     *
     * @return string
     */
    public function getHighriseDealId()
    {
        return $this->get('highrise-deal-id');
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
     * Retrieve property 'name'
     *
     * @return string
     */
    public function getName()
    {
        return $this->get('name');
    }

    /**
     * Retrieve property 'notify-when-over-budget'
     *
     * @return string
     */
    public function getNotifyWhenOverBudget()
    {
        return $this->get('notify-when-over-budget');
    }

    /**
     * Retrieve property 'over-budget-notification-percentage'
     *
     * @return string
     */
    public function getOverBudgetNotificationPercentage()
    {
        return $this->get('over-budget-notification-percentage');
    }

    /**
     * Retrieve property 'over-budget-notified-at'
     *
     * @return string
     */
    public function getOverBudgetNotifiedAt()
    {
        return $this->get('over-budget-notified-at');
    }

    /**
     * Retrieve property 'show-budget-to-all'
     *
     * @return string
     */
    public function getShowBudgetToAll()
    {
        return $this->get('show-budget-to-all');
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
     * Retrieve property 'estimate-by'
     *
     * @return string
     */
    public function getEstimateBy()
    {
        return $this->get('estimate-by');
    }

    /**
     * Retrieve property 'notes'
     *
     * @return string
     */
    public function getNotes()
    {
        return $this->get('notes');
    }
}