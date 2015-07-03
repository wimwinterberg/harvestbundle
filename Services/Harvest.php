<?php
namespace WeAreBuilders\HarvestBundle\Services;

use Harvest\Model\Client;
use Harvest\Model\Range;
use WeAreBuilders\HarvestBundle\Library;

/**
 * HarvestInterface - interface with harvest
 *
 */
class Harvest
{
    /**
     * Harvest user settings
     *
     */
    const HARVEST_USER_EMAIL_USER_SETTING       = 'harvest.user.email';
    const HARVEST_USER_PROFILE_URL_USER_SETTING = 'harvest.user.profile.url';

    /**
     * The connection for with harvest
     *
     * @var \WeAreBuilders\HarvestBundle\Library\Harvest\HarvestApi
     */
    private $harvestConnection = null;

    /**
     * Client info
     *
     * @var null|string
     */
    private $harvestClientInfo = null;

    /**
     * Exception number for Harvest
     *
     */
    const HARVEST_ERROR_ISSUE_NOT_DELETED                    = 1;
    const HARVEST_ERROR_ENTRY_NOT_CREATED                    = 2;
    const HARVEST_ERROR_CLIENT_NOT_FOUND                     = 3;
    const HARVEST_ERROR_EDIT_TASK                            = 4;
    const HARVEST_ERROR_UNABLE_TO_START_TIMER                = 5;
    const HARVEST_ERROR_REFERENCE_NOT_SET_FOR_TASK_INTERFACE = 6;

    /**
     * Cache of active harvest users
     *
     * @var array
     */
    private $activeUsers = null;

    /**
     * Constructor
     *
     * @param string $user
     * @param string $password
     * @param string $account
     */
    public function __construct($user, $password, $account)
    {
        $this->harvestConnection = new Library\Harvest\HarvestApi();
        $this->harvestConnection->setUser($user);
        $this->harvestConnection->setPassword($password);
        $this->harvestConnection->setAccount($account);
        //$this->harvestConnection->setSSL($this->harvestInfo[3]);
    }

    /**
     * Initialise the harvestConnection
     *
     */
    public function init()
    {
    }

    /**
     * Retrieve harvest account name
     *
     * @return string
     */
    public function getHarvestAccountName()
    {
        return $this->harvestClientInfo;
    }

    /**
     * Retrieve running timers for all users
     *
     * @param integer $dayOfYear
     * @param integer $year
     * @return array of entries which are running plus harvestUser
     * @throws \Exception
     */
    public function getRunningTimersForAllActiveUsers($dayOfYear = null, $year = null)
    {
        $retValue = array();

        $this->init();

        foreach ($this->getActiveUsers() as $userId => $harvestUser) {
            /* @var $harvestUser \Harvest\Model\User */
            $result = $this->harvestConnection->getDailyActivityByUserId($userId, $dayOfYear, $year);

            if ($result->isSuccess() && $result->get('code') == 200) {
                $harvestDailyActivity = $result->get('data');
                /* @var $harvestDailyActivity \Harvest\Model\DailyActivity */
                $dayEntries          = $harvestDailyActivity->get('dayEntries');
                $runningTimerEntries = array();

                foreach ($dayEntries as $dayEntryId => $dayEntry) {
                    /* @var $dayEntry \Harvest\Model\DayEntry */
                    if ($dayEntry->get('timer-started-at') !== null) {
                        $runningTimerEntries[$dayEntryId] = $dayEntry;
                    }
                }

                if (count($runningTimerEntries)) {
                    $retValue[$userId] = array(
                        'harvestUser' => $harvestUser,
                        'entries'     => $runningTimerEntries
                    );
                }
            } else {
                throw new \Exception("Harvest could not retrieve projects");
            }
        }

        return $retValue;
    }

    /**
     * Retrieve all users
     *
     * @param bool $forceReload (Optional) defaults to false - when true will reload via api
     * @return Library\Harvest\User[]
     * @throws \Exception
     */
    public function getActiveUsers($forceReload = false)
    {
        $retValue = null;

        if ($forceReload || $this->activeUsers === null) {
            $this->init();

            $result = $this->harvestConnection->getUsers();
            if ($result->isSuccess() && $result->get('code') == 200) {
                $retValue = array();
                foreach ($result->get('data') as $userId => $harvestUser) {
                    /* @var $harvestUser \Harvest\Model\User */
                    if ($harvestUser->get('is-active') == 'true') {
                        $retValue[$userId] = $harvestUser;
                    }
                }
            } else {
                throw new \Exception("Harvest could not retrieve users");
            }

            $this->activeUsers = $retValue;
        } else {
            $retValue = $this->activeUsers;
        }

        return $retValue;
    }

    /**
     * Get a list of all projects
     *
     *
     * @throws \Exception
     * @return Library\Harvest\Project[]
     */
    public function getProjects()
    {
        $retValue = null;

        $this->init();

        $result = $this->harvestConnection->getProjects();

        if ($result->isSuccess() && $result->get('code') == 200) {
            $retValue = $result->get('data');
        } else {
            throw new \Exception("Harvest could not retrieve projects");
        }

        return $retValue;
    }

    /**
     * Get a list of all projects by client reference
     *
     *
     * @param string $clientReference
     * @return \WeAreBuilders\HarvestBundle\Library\Harvest\Project[]
     * @throws \Exception
     * @throws \Harvest\Exception\HarvestException
     */
    public function getProjectsByClientReference($clientReference)
    {
        $retValue = null;

        $this->init();

        $result = $this->harvestConnection->getClientProjects($clientReference);

        if ($result->isSuccess() && $result->get('code') == 200) {
            $retValue = $result->get('data');
        } else {
            throw new \Exception("Harvest could not retrieve projects");
        }

        return $retValue;
    }

    /**
     * Add a new project with the given name
     *
     * @param string $projectName
     * @param string $harvestClientRef (Optional) defaults to client harvest ref in config
     * @throws \Exception
     * @return integer
     */
    public function addNewProject($projectName, $harvestClientRef = null)
    {
        $this->init();

        if ($harvestClientRef === null) {
            $harvestClientRef = $this->harvestClientInfo;
        }

        $project = new Library\Harvest\Project();
        $project->set("name", $projectName);
        $project->set("client-id", $harvestClientRef);
        $result = $this->harvestConnection->createProject($project);

        if ($result->isSuccess() && $result->get('code') == 201) {
            // get id of created project
            $project_id = $result->get('data');
        } else {
            throw new \Exception("Harvest could not create the project");
        }

        return $project_id;
    }

    /**
     * Edit project with the given projectId
     *
     * @param int    $harvestRef
     * @param string $projectName
     * @throws \Exception
     * @return boolean
     */
    public function editProject($harvestRef, $projectName)
    {
        $this->init();

        $project = new Library\Harvest\Project();
        $project->set("id", $harvestRef);
        $project->set("name", $projectName);
        $project->set("client-id", $this->harvestClientInfo);

        $result = $this->harvestConnection->updateProject($project);
        if ($result->isSuccess()) {
            return true;
        } else {
            throw new \Exception("Harvest could not update the project");
        }
    }

    /**
     * Add a new task
     *
     * @param string  $taskName
     * @param boolean $billable (Optional) defaults to false
     * @return integer
     * @throws \Exception if task could not be added in harvest
     */
    public function addNewTask($taskName, $billable = false)
    {
        $task = new Library\Harvest\Task();
        $task->set("name", $taskName);
        $task->set('billable-by-default', (int)$billable);

        $this->init();
        $result = $this->harvestConnection->createTask($task);
        if ($result->isSuccess()) {
            // get id of created task
            $task_id = $result->get('data');
        } else {
            throw new \Exception("Harvest could not add the task");
        }

        return $task_id;
    }

    /**
     * Edit the task
     *
     * @param integer $harvestTaskRef
     * @param string  $taskName
     * @param boolean $billable (Optional) defaults to false
     * @return boolean
     * @throws \Exception if task could not be updated from harvest
     */
    private function editTask($harvestTaskRef, $taskName, $billable = false)
    {
        $task = new Library\Harvest\Task();
        $task->set("id", $harvestTaskRef);
        $task->set("name", $taskName);
        $task->set('billable-by-default', (int)$billable);
        $this->init();

        $result = $this->harvestConnection->updateTask($task);
        if ($result->isSuccess()) {
            return true;
        } else {
            throw new \Exception("Harvest could not update the task", self::HARVEST_ERROR_EDIT_TASK);
        }
    }

    /**
     * Remove a task from harvest
     *
     * @param integer $harvestTaskRef
     * @return bool
     * @throws \Exception
     */
    public function deleteTask($harvestTaskRef)
    {
        $this->init();

        $result = $this->harvestConnection->deleteTask($harvestTaskRef);
        if ($result->isSuccess()) {
            return true;
        } else {
            throw new \Exception('Harvest could not delete the task', self::HARVEST_ERROR_ISSUE_NOT_DELETED);
        }
    }

    /**
     * Add a new task assginment
     *
     * @param integer $projectId
     * @param integer $taskId
     * @return integer
     * @throws \Exception if task could not be assigned to a project
     */
    public function assignTaskToProject($projectId, $taskId)
    {
        $this->init();
        $result = $this->harvestConnection->assignTaskToProject($projectId, $taskId);
        if ($result->isSuccess()) {
            // get taskAssignment id
            $task_assignment_id = $result->get('data');
        } else {
            throw new \Exception("Harvest could not assign the task to the project");
        }

        return $task_assignment_id;
    }

    /**
     * Get all entries from harvest for the project in the given timeframe
     *
     * @param int $harvestProjectRef
     * @param int $startDate (yyyymmdd)
     * @param int $endDate   (yyyymmdd)
     * @param int $user_id   (optional)
     * @return Library\Harvest\DayEntry[]
     * @throws \Exception if entries could not be loaded from Harvest
     */
    public function getTrackedTimeByProject($harvestProjectRef, $startDate, $endDate, $user_id = null)
    {
        $range = new Range($startDate, $endDate);

        $this->init();
        $result = $this->harvestConnection->getProjectEntries($harvestProjectRef, $range, $user_id);
        if ($result->isSuccess()) {
            // get taskAssignment id
            $dayEntries = $result->get('data');
        } else {
            throw new \Exception("Harvest could not load the entries for this project");
        }

        return $dayEntries;
    }

    /**
     * Retrieve all harvest tasks that are linked to a project
     *
     * @param int $harvestProjectRef
     * @return Library\Harvest\TaskAssignment[]|null
     */
    public function getTaskAssignmentsByProject($harvestProjectRef)
    {
        $this->init();

        $tasksForProject = null;
        $result          = $this->harvestConnection->getProjectTaskAssignments($harvestProjectRef);

        if ($result->isSuccess()) {
            $tasksForProject = $result->get('data');
        }

        return $tasksForProject;
    }

    /**
     * Create entry
     *
     * @param string    $harvestProjectRef
     * @param string    $harvestTaskRef
     * @param \DateTime $date
     * @param float     $hours
     * @param string    $notes
     * @return mixed
     * @throws \Exception
     * @throws \Harvest\Exception\HarvestException
     */
    public function createEntry($harvestProjectRef, $harvestTaskRef, \DateTime $date, $hours, $notes)
    {
        $entry = new Library\Harvest\DayEntry();
        $entry->set("notes", $notes);
        $entry->set("hours", $hours);
        $entry->set("project_id", $harvestProjectRef);
        $entry->set("task_id", $harvestTaskRef);
        $entry->set("spent_at", $date->format('D, j M Y'));

        $this->init();
        $result = $this->harvestConnection->createEntry($entry);
        if ($result->isSuccess()) {
            return $result->get('data');
        } else {
            throw new \Exception('Harvest create enty', self::HARVEST_ERROR_ENTRY_NOT_CREATED);
        }
    }

    /**
     * Retrieve harvest client
     *
     * @param integer $harvestClientRef
     * @throws \Exception
     * @return Client
     */
    public function getClient($harvestClientRef)
    {
        $this->init();
        $result = $this->harvestConnection->getClient($harvestClientRef);

        if ($result->isSuccess()) {
            return $result->get('data');
        } else {
            throw new \Exception('Harvest client not found', self::HARVEST_ERROR_CLIENT_NOT_FOUND);
        }
    }

    /**
     * Retrieve all harvest clients
     *
     * @throws \Exception
     * @return Library\Harvest\Client[]
     */
    public function getClients()
    {
        $this->init();
        $result = $this->harvestConnection->getClients();

        if ($result->isSuccess()) {
            return $result->get('data');
        } else {
            throw new \Exception('Harvest client not found', self::HARVEST_ERROR_CLIENT_NOT_FOUND);
        }
    }

    /**
     * Retrieve day entry of user
     *
     * @param string $harvestDayEntryId
     * @param string $harvestUserId
     * @throws \Exception
     * @return Library\Harvest\DayEntry
     */
    public function getDayEntryByHarvestUserId($harvestDayEntryId, $harvestUserId)
    {
        $this->init();

        $result = $this->harvestConnection->getDayEntryByHarvestUserId($harvestDayEntryId, $harvestUserId);

        if ($result->isSuccess()) {
            return $result->get('data');
        } else {
            throw new \Exception(sprintf('%1$s: unable to fetch day entry[%2$s] for user[%3$s]', __METHOD__, $harvestDayEntryId, $harvestUserId));
        }
    }

    /**
     * Stops the running timer
     *
     * @param string $harvestDayEntryId
     * @param string $harvestUserId
     * @throws \Exception
     * @return \Harvest\Model\DayEntry|null|bool false when task was not found or not running
     */
    public function stopTimerByDayEntryIdAndHarvestUserId($harvestDayEntryId, $harvestUserId)
    {
        $retValue  = null;
        $isRunning = false;

        // init interface
        $this->init();

        // determine whether day entry timer is running
        try {
            $harvestDayEntry = $this->getDayEntryByHarvestUserId($harvestDayEntryId, $harvestUserId);
            $isRunning       = $harvestDayEntry->getTimerStartedAt() !== null;
        } catch (\Exception $e) {
            // ignore when day entry was not found
            $retValue = false;
        }

        if ($isRunning) {
            $result = $this->harvestConnection->toggleTimerByDayEntryIdAndHarvestUserId($harvestDayEntryId, $harvestUserId);
            if ($result->isSuccess()) {
                $harvestTimer = $result->get('data');
                /* @var $harvestTimer Library\Harvest\Timer */

                $dayEntry = $harvestTimer->getDayEntry();

                if ($dayEntry->getTimerStartedAt() !== null) {
                    // timer was started instead of stopped
                    // recall myself with new added timer
                    $retValue = $this->stopTimerByDayEntryIdAndHarvestUserId($dayEntry->getId(), $harvestUserId);
                }
            }
        } else {
            $retValue = false;
        }

        return $retValue;
    }

    /**
     * Add a new client
     *
     * @param string $clientName
     * @return integer
     * @throws \Exception if client could not be added in harvest
     */
    public function addNewClient($clientName)
    {
        $client = new Library\Harvest\Client();
        $client->set('name', $clientName);

        $this->init();
        $result = $this->harvestConnection->createClient($client);
        if ($result->isSuccess()) {
            // get id of created client
            $clientId = $result->get('data');
        } else {
            throw new \Exception('Harvest could not add the client');
        }

        return $clientId;
    }

    /**
     * Get all entries from harvest for the user in the given timeframe
     *
     * @param int $harvestUserRef
     * @param int $startDate (yyyymmdd)
     * @param int $endDate   (yyyymmdd)
     * @param int $user_id   (optional)
     * @return Library\Harvest\DayEntry[]
     * @throws \Exception if entries could not be loaded from Harvest
     */
    public function getTrackedTimeByUser($harvestUserRef, $startDate, $endDate, $user_id = null)
    {
        $range = new Range($startDate, $endDate);

        $this->init();
        $result = $this->harvestConnection->getUserEntries($harvestUserRef, $range, $user_id);
        if ($result->isSuccess()) {
            // get taskAssignment id
            $dayEntries = $result->get('data');
        } else {
            throw new \Exception("Harvest could not load the entries for this user");
        }

        return $dayEntries;
    }

    /**
     * Get a list of all tasks
     *
     *
     * @throws \Exception
     * @return Library\Harvest\Task[]
     */
    public function getTasks()
    {
        $retValue = null;

        $this->init();

        $result = $this->harvestConnection->getTasks();

        if ($result->isSuccess() && $result->get('code') == 200) {
            $retValue = $result->get('data');
        } else {
            throw new \Exception("Harvest could not retrieve tasks");
        }

        return $retValue;
    }

    /**
     * Starts a new timer
     *
     * @param int    $userId
     * @param int    $projectId
     * @param int    $taskId
     * @param string $comments
     * @return mixed|null
     * @throws \Exception
     */
    public function startNewTimerForUserByHarvestTaskInterface($userId, $projectId, $taskId, $comments)
    {
        $retValue = null;
        $this->init();
        $result = $this->harvestConnection->startNewTimerByUserIdAndProjectIdAndTaskId($userId, $projectId, $taskId, $comments);
        if ($result->isSuccess()) {
            $retValue = $result->get('data');
        } else {
            throw new \Exception("Harvest could not start new timer");
        }

        return $retValue;
    }

    /**
     * Stops the running timer
     *
     * @param \WeAreBuilders\HarvestBundle\Library\Harvest\DayEntry $dayEntry
     * @return Library\Harvest\DayEntry
     * @throws \Harvest\Exception\HarvestException
     */
    public function stopTimerByDayEntry(Library\Harvest\DayEntry $dayEntry)
    {
        $retValue = $dayEntry;

        // init interface
        $this->init();

        $result = $this->harvestConnection->toggleTimerByDayEntryIdAndHarvestUserId($dayEntry->getId(), $dayEntry->getUserId());
        if ($result->isSuccess()) {
            $harvestTimer = $result->get('data');
            /* @var $harvestTimer Library\Harvest\Timer */

            $retValue = $harvestTimer->getDayEntry();

            if ($retValue->isRunning()) {
                // timer was started instead of stopped
                // recall myself with new added timer
                $retValue = $this->stopTimerByDayEntry($retValue);
            }
        }

        return $retValue;
    }
}