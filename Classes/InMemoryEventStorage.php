<?php
namespace Flowpack\EventStore\InMemoryStorageAdapter;

/*
 * This file is part of the Flowpack.Cqrs package.
 *
 * (c) Hand crafted with love in each details by medialib.tv
 */

use Flowpack\EventStore\EventStreamData;
use Flowpack\EventStore\Exception\ConcurrencyException;
use Flowpack\EventStore\Storage\EventStorageInterface;
use TYPO3\Flow\Annotations as Flow;

/**
 * EventStore
 */
class InMemoryEventStorage implements EventStorageInterface
{
    /**
     * @var array
     */
    protected $streamData = [];

    /**
     * @param string $identifier
     * @return EventStreamData
     */
    public function load(string $identifier)
    {
        if (isset($this->streamData[$identifier])) {
            return $this->streamData[$identifier];
        }
        return null;
    }

    /**
     * @param string $aggregateIdentifier
     * @param string $aggregateName
     * @param array $data
     * @param integer $version
     * @throws ConcurrencyException
     */
    public function commit(string $aggregateIdentifier, string $aggregateName, array $data, int $version)
    {
        if (isset($this->streamData[$aggregateIdentifier])) {
            $currentData = $this->streamData[$aggregateIdentifier]->getData();
            $data = array_merge($currentData, $data);
        }
        $this->streamData[$aggregateIdentifier] = new EventStreamData($aggregateIdentifier, $aggregateName, $data, $version);
    }

    /**
     * @param string $identifier
     * @return boolean
     */
    public function contains(string $identifier): bool
    {
        return isset($this->streamData[$identifier]);
    }

    /**
     * @param  string $identifier
     * @return integer Current Aggregate Root version
     */
    public function getCurrentVersion(string $identifier): int
    {
        if (isset($this->streamData[$identifier])) {
            return $this->streamData[$identifier]->getVersion();
        }
        return 1;
    }
}
