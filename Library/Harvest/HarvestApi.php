<?php
namespace WeAreBuilders\HarvestBundle\Library\Harvest;

/**
 * Class Sef_Harvest_Api
 *
 */
class HarvestApi extends \Harvest\HarvestApi
{
    /**
     * gets the activity of the requesting user for the day
     *
     * <code>
     * $day_of_year = 267;
     * $year = 2009
     * $userId = 1
     * $api = new HarvestAPI();
     *
     * $result = $api->getDailyActivity( $day_of_year, $year );
     * if( $result->isSuccess() ) {
     *     $entries= $result->data;
     * }
     * </code>
     *
     * @param int $userId      user id
     * @param int $day_of_year Day of Year
     * @param int $year        Year
     * @return \Harvest\Model\Result
     */
    public function getDailyActivityByUserId($userId, $day_of_year = null, $year = null)
    {
        $url = "daily/";
        if (!is_null($day_of_year) && !is_null($year)) {
            $url .= $day_of_year . "/" . $year . '/';
        }
        $url .= sprintf('?of_user=%1$s', $userId);

        return $this->performGET($url, false);
    }

    /**
     * get all users
     *
     * <code>
     * $api = new HarvestAPI();
     *
     * $result = $api->getUsers();
     * if( $result->isSuccess() ) {
     *     $users = $result->data;
     * }
     * </code>
     *
     * @return \Harvest\Model\Result
     */
    public function getActiveUsers()
    {
        $url = "people?active=1";

        return $this->performGET($url, true);
    }

    /**
     * Creates an entry and starts a timer for given user id, project id and task id
     *
     * @param int    $userId
     * @param int    $projectId
     * @param int    $taskId
     * @param string $comments
     * @return \Harvest\Model\Result
     */
    public function startNewTimerByUserIdAndProjectIdAndTaskId($userId, $projectId, $taskId, $comments)
    {
        $entry = new DayEntry();
        $entry->set("notes", $comments);
        $entry->set("project_id", $projectId);
        $entry->set("task_id", $taskId);
        $entry->set("spent_at", date('D, j M Y'));
        $entry->set("hours", " ");

        $url = sprintf('daily/add?of_user=%1$s', $userId);

        return $this->performPOST($url, $entry->toXML(), false);
    }

    /**
     * parse xml node
     *
     * @param \DOMElement $node document element
     * @return mixed
     */
    protected function parseNode($node)
    {
        $item = null;

        switch ($node->nodeName) {
            case "timer":
                $item = new Timer();
                break;
            case "add":
                $children = $node->childNodes;
                foreach ($children as $child) {
                    if ($child->nodeName == "day_entry") {
                        $node = $child;
                        break;
                    }
                }
            case "day_entry":
            case "day-entry":
                $item = new DayEntry();
                break;
            case 'task':
                $item = new Task();
                break;
            case 'task-assignment':
                $item = new TaskAssignment();
                break;
            case "project":
                $item = new Project();
                break;
            case "client":
                $item = new Client();
                break;
            default:
                break;
        }
        if (!is_null($item)) {
            $item->parseXML($node);
        } else {
            $item = parent::parseNode($node);
        }

        return $item;
    }

    /**
     * Retrieve day entry for user
     *
     * @param int $harvestDayEntryId
     * @param int $harvestUserId
     * @return \Harvest\Model\Result
     */
    public function getDayEntryByHarvestUserId($harvestDayEntryId, $harvestUserId)
    {
        return $this->performGET(sprintf('daily/show/%1$d/?of_user=%2$d', $harvestDayEntryId, $harvestUserId), false);
    }

    /**
     * Toggle timer for day entry for user
     *
     * @param int $harvestDayEntryId
     * @param int $harvestUserId
     * @return \Harvest\Model\Result
     */
    public function toggleTimerByDayEntryIdAndHarvestUserId($harvestDayEntryId, $harvestUserId)
    {
        return $this->performGET(sprintf('/daily/timer/%1$d/?of_user=%2$d', $harvestDayEntryId, $harvestUserId), false);
    }
}