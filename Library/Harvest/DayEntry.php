<?php
namespace WeAreBuilders\HarvestBundle\Library\Harvest;

/**
 * Sef_Harvest_DayEntry
 * Introduced getters
 *
 */
class DayEntry extends \Harvest\Model\DayEntry
{
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
     * Retrieve property 'updated-at'
     *
     * @return \Datetime
     */
    public function getCreatedAtInDateTime()
    {
        return new \DateTime($this->get('created-at'));
    }

    /**
     * Retrieve property 'hours'
     *
     * @return float|null
     */
    public function getHours()
    {
        $retValue = $this->get('hours');

        if ($retValue !== null) {
            $retValue = (float)$retValue;
        }

        return $retValue;
    }

    /**
     * Retrieve property 'id'
     *
     * @return string
     */
    public function getId()
    {
        $retValue = $this->get('id');

        if ($retValue !== null) {
            $retValue = (int)$retValue;
        }

        return $retValue;
    }

    /**
     * Retrieve property 'is-billed'
     *
     * @return bool
     */
    public function getIsBilled()
    {
        return $this->get('is-billed') == 'true';
    }

    /**
     * Retrieve property 'is-closed'
     *
     * @return bool
     */
    public function getIsClosed()
    {
        return $this->get('is-closed') == 'true';
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

    /**
     * Retrieve property 'project-id'
     *
     * @return string
     */
    public function getProjectId()
    {
        $retValue = $this->get('project-id');

        if ($retValue !== null) {
            $retValue = (int)$retValue;
        }

        return $retValue;
    }

    /**
     * Retrieve property 'spent-at'
     *
     * @return string
     */
    public function getSpentAt()
    {
        return $this->get('spent-at');
    }

    /**
     * Retrieve property 'task-id'
     *
     * @return string
     */
    public function getTaskId()
    {
        $retValue = $this->get('task-id');

        if ($retValue !== null) {
            $retValue = (int)$retValue;
        }

        return $retValue;
    }

    /**
     * Retrieve property 'timer-started-at'
     *
     * @return string
     */
    public function getTimerStartedAt()
    {
        return $this->get('timer-started-at');
    }

    /**
     * Retrieve property 'user-id'
     *
     * @return string
     */
    public function getUserId()
    {
        $retValue = $this->get('user-id');

        if ($retValue !== null) {
            $retValue = (int)$retValue;
        }

        return $retValue;
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
     * Retrieve property 'started-at'
     *
     * @return string
     */
    public function getStartedAt()
    {
        return $this->get('started-at');
    }

    /**
     * Retrieve property 'ended-at'
     *
     * @return string
     */
    public function getEndedAt()
    {
        return $this->get('ended-at');
    }

    /**
     * Retrieve property 'hours-with-timer'
     *
     * @return float|null
     */
    public function getHoursWithTimer()
    {
        $retValue = $this->get('hours-with-timer');

        if ($retValue !== null) {
            $retValue = (float)$retValue;
        }

        return $retValue;
    }

    /**
     * Whether timer is running
     *
     * @return bool
     */
    public function isRunning()
    {
        return $this->getHoursWithTimer() !== null;
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