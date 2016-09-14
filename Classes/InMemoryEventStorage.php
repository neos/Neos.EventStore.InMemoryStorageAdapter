<?php
namespace Neos\EventStore\InMemoryStorageAdapter;

/*
 * This file is part of the Neos.EventStore.DatabaseStorageAdapter package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\EventStore\EventStreamData;
use Neos\EventStore\Exception\ConcurrencyException;
use Neos\EventStore\Storage\EventStorageInterface;

/**
 * In Memory event storage, for testing purpose
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
        $stream = $this->load($aggregateIdentifier);
        if ($stream !== null) {
            $data = array_merge($data, $stream->getData());
        }
        $this->streamData[$aggregateIdentifier] = new EventStreamData($aggregateIdentifier, $aggregateName, $data, $version);
    }

    /**
     * @param string $identifier
     * @return boolean
     */
    public function contains(string $identifier): bool
    {
        return $this->load($identifier) ? true : false;
    }

    /**
     * @param  string $identifier
     * @return integer Current Aggregate Root version
     */
    public function getCurrentVersion(string $identifier): int
    {
        $stream = $this->load($identifier);
        if ($stream !== null) {
            return $stream->getVersion();
        }
        return 0;
    }
}
