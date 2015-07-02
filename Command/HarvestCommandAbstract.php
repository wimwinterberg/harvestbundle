<?php
namespace WeAreBuilders\HarvestBundle\Command;

use Doctrine\Common\Cache\CacheProvider;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use WeAreBuilders\HarvestBundle\Services\Harvest;

/**
 * Class HarvestCommandAbstract
 *
 * @package WeAreBuilders\AutomateBundle\Command
 */
abstract class HarvestCommandAbstract extends ContainerAwareCommand
{
    /**
     * Options
     *
     */
    const OPTION_FORCE_RELOAD = 'force-reload';

    /**
     * Cache namespace
     *
     * @var string
     */
    protected static $cacheNamespace = 'harvest.cache';

    /**
     * Retrieve harvest api interface
     *
     * @return Harvest
     */
    protected function getWrbHarvest()
    {
        return $this->getContainer()->get('wrb_harvest');
    }

    /**
     * Retrieve projects
     *
     * @param bool $forceReload (Optional) Defaults to false
     * @return array
     * @throws \Exception
     */
    protected function getProjects($forceReload = false)
    {
        $cache        = $this->getCache();
        $cacheDataKey = 'projects';

        // retrieve cached retValue
        $retValue = $cache->fetch($cacheDataKey);

        if ($retValue === false || $forceReload) {
            $retValue = array();
            foreach ($this->getWrbHarvest()->getProjects() as $project) {
                $retValue[$project->getId()] = $project->dump();
            }
            $cache->save($cacheDataKey, $retValue, 3600);
        }

        return $retValue;
    }

    /**
     * Retrieve tasks
     *
     * @param bool $forceReload (Optional) Defaults to false
     * @return array
     * @throws \Exception
     */
    protected function getTasks($forceReload = false)
    {
        $cache        = $this->getCache();
        $cacheDataKey = 'tasks';

        // retrieve cached retValue
        $retValue = $cache->fetch($cacheDataKey);

        if ($retValue === false || $forceReload) {
            $retValue = array();
            foreach ($this->getWrbHarvest()->getTasks() as $project) {
                $retValue[$project->getId()] = $project->dump();
            }
            $cache->save($cacheDataKey, $retValue, 3600);
        }

        return $retValue;
    }

    /**
     * Retrieve clients
     *
     * @param bool $forceReload (Optional) Defaults to false
     * @return array
     * @throws \Exception
     */
    protected function getClients($forceReload = false)
    {
        $cache        = $this->getCache();
        $cacheDataKey = 'clients';

        // retrieve cached retValue
        $retValue = $cache->fetch($cacheDataKey);

        if ($retValue === false || $forceReload) {
            $retValue = array();
            foreach ($this->getWrbHarvest()->getClients() as $client) {
                $retValue[$client->getId()] = $client->dump();
            }
            $cache->save($cacheDataKey, $retValue, 3600);
        }

        return $retValue;
    }

    /**
     * Retrieve users
     *
     * @param bool $forceReload (Optional) Defaults to false
     * @return array
     * @throws \Exception
     */
    protected function getUsers($forceReload = false)
    {
        $cache        = $this->getCache();
        $cacheDataKey = 'users';

        // retrieve cached retValue
        $retValue = $cache->fetch($cacheDataKey);

        if ($retValue === false || $forceReload) {
            $retValue = array();
            foreach ($this->getWrbHarvest()->getActiveUsers() as $user) {
                $retValue[$user->getId()] = $user->dump();
            }
            $cache->save($cacheDataKey, $retValue, 3600);
        }

        return $retValue;
    }

    /**
     * Retrieve cache
     *
     * @return CacheProvider
     */
    protected function getCache()
    {
        $retValue = $this->getContainer()->get('cache');
        /* @var $retValue CacheProvider */
        $retValue->setNamespace(self::$cacheNamespace);

        return $retValue;
    }
}