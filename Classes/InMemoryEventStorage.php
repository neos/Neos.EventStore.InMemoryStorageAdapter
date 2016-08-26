<?php
namespace Ttree\EventStore\InMemoryStorageAdapter;

/*
 * This file is part of the Ttree.Cqrs package.
 *
 * (c) Hand crafted with love in each details by medialib.tv
 */

use Ttree\EventStore\EventStreamData;
use Ttree\EventStore\Exception\ConcurrencyException;
use Ttree\EventStore\Storage\EventStorageInterface;
use TYPO3\Flow\Annotations as Flow;

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
     * @param string $identifier
     * @param string $aggregateName
     * @param array $data
     * @param integer $version
     * @throws ConcurrencyException
     */
    public function commit(string $identifier, string $aggregateName, array $data, int $version)
    {
        $stream = $this->load($identifier);
        if ($stream !== null) {
            $iterator = new \MultipleIterator();
            $iterator->attachIterator(new \ArrayIterator($data));
            $iterator->attachIterator($stream->getData());
            $data = (array)$iterator;
        }
        $this->streamData[$identifier] = new EventStreamData($identifier, $aggregateName, $data, $version);
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
        return 1;
    }
}
