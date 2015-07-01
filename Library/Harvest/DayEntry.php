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
     * @return string
     */
    public function getHours()
    {
        return $this->get('hours');
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
        return $this->get('project-id');
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
        return $this->get('task-id');
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
        return $this->get('user-id');
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
     * @return string
     */
    public function getHoursWithTimer()
    {
        return $this->get('hours-with-timer');
    }
}