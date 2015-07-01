<?php
namespace WeAreBuilders\HarvestBundle\Library\Harvest;

use Harvest\Model\User as BaseUser;

/**
 * User
 *
 * Introduced getters
 *
 */
class User extends BaseUser
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
     * Retrieve property 'email'
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->get('email');
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
     * Retrieve property 'is-admin'
     *
     * @return string
     */
    public function getIsAdmin()
    {
        return $this->get('is-admin');
    }

    /**
     * Retrieve property 'first-name'
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->get('first-name');
    }

    /**
     * Retrieve property 'last-name'
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->get('last-name');
    }

    /**
     * Retrieve property 'timezone'
     *
     * @return string
     */
    public function getTimezone()
    {
        return $this->get('timezone');
    }

    /**
     * Retrieve property 'is-contractor'
     *
     * @return string
     */
    public function getIsContractor()
    {
        return $this->get('is-contractor');
    }

    /**
     * Retrieve property 'telephone'
     *
     * @return string
     */
    public function getTelephone()
    {
        return $this->get('telephone');
    }

    /**
     * Whether project is active
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->getIsActive() == 'true';
    }

    /**
     * Retrieve property 'is-active'
     *
     * @return string
     */
    public function getIsActive()
    {
        return $this->get('is-active');
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
     * Retrieve property 'department'
     *
     * @return string
     */
    public function getDepartment()
    {
        return $this->get('department');
    }

    /**
     * Retrieve property 'wants-newsletter'
     *
     * @return string
     */
    public function getWantsNewsletter()
    {
        return $this->get('wants-newsletter');
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
     * Retrieve property 'cost-rate'
     *
     * @return string
     */
    public function getCostRate()
    {
        return $this->get('cost-rate');
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