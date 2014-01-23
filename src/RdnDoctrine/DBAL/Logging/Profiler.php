<?php

namespace RdnDoctrine\DBAL\Logging;

use Doctrine\DBAL\Logging\SQLLogger;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;

/**
 * @author Blake Harley <blake@blakeharley.com>
 */
class Profiler implements SQLLogger, EventManagerAwareInterface
{
	const EVENT_LOG_QUERY = 'log.query';

	protected $profiles = array();

	protected $index = 0;

	/**
	 * The entity manager name
	 *
	 * @var string
	 */
	protected $name;

	protected $isLogging = false;

	/**
	 * @var EventManagerInterface
	 */
	protected $events;

	public function __construct($name)
	{
		$this->name = $name;
	}

	public function startQuery($sql, array $params = null, array $types = null)
	{
		$this->profiles[$this->index] = array(
			'sql' => $sql,
			'parameters' => $params,
			'types' => $types,
			'start' => microtime(true),
			'end' => null,
			'elapse' => null,
			'logged' => false,
		);
	}

	public function stopQuery()
	{
		$this->profiles[$this->index]['end'] = microtime(true);
		$this->profiles[$this->index]['elapse'] = $this->profiles[$this->index]['end'] - $this->profiles[$this->index]['start'];

		if ($this->isLogging)
		{
			$this->profiles[$this->index]['logged'] = true;
			$this->events->trigger(self::EVENT_LOG_QUERY, $this, array(
				'manager' => $this->name,
				'query' => $this->profiles[$this->index],
			));
		}

		$this->index++;
	}

	public function getProfiles()
	{
		return $this->profiles;
	}

	public function isLogging()
	{
		return $this->isLogging;
	}

	public function setIsLogging($flag = false)
	{
		$this->isLogging = $flag;
	}

	public function setEventManager(EventManagerInterface $events)
	{
		$events->setIdentifiers(array(
			__CLASS__,
			get_called_class(),
		));
		$this->events = $events;
	}

	/**
	 * @return EventManagerInterface
	 */
	public function getEventManager()
	{
		if ($this->events === null)
		{
			$this->setEventManager(new EventManager);
		}
		return $this->events;
	}
}
