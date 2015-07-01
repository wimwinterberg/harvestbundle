<?php
namespace WeAreBuilders\HarvestBundle\Library\Harvest;

    /**
     * Timer
     *
     * This file contains the class Sef_Harvest_Timer
     *
     * @author  Matthew John Denton <matt@mdbitz.com>
     * @package com.mdbitz.harvest
     */

/**
 * Harvest Timer Object
 *
 * <b>Properties</b>
 * <ul>
 *   <li>day_entry</li>
 *   <li>hours_for_previously_running_entry</li>
 * </ul>
 *
 * @package com.mdbitz.harvest
 */
class Timer extends \Harvest\Model\Timer
{
    /**
     * Parse XML represenation into a Harvest Timer object
     *
     * @param \XMLNode $node xml node to parse
     * @return void
     */
    public function parseXML($node)
    {
        foreach ($node->childNodes as $item) {
            switch ($item->nodeName) {
                case "day_entry":
                    $this->_dayEntry = new DayEntry();
                    $this->_dayEntry->parseXML($item);
                    break;
                case "hours_for_previously_running_timer":
                    $this->_hoursForPrevious = $item->nodeValue;
                    break;
                default:
                    break;
            }
        }
    }

    /**
     * Retrieve day entry
     *
     * @return DayEntry|null
     */
    public function getDayEntry()
    {
        return $this->_dayEntry;
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