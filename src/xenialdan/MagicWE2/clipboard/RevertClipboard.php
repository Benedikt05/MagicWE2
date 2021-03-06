<?php

declare(strict_types=1);

namespace xenialdan\MagicWE2\clipboard;

use pocketmine\block\Block;
use pocketmine\level\format\Chunk;
use pocketmine\level\Level;

class RevertClipboard extends Clipboard
{
    /** @var Chunk[] */
    public $chunks = [];
    /** @var Block[] */
    public $blocksAfter;

    /**
     * RevertClipboard constructor.
     * @param int $levelId
     * @param Chunk[] $chunks
     * @param Block[] $blocksAfter
     */
    public function __construct(int $levelId, array $chunks = [], array $blocksAfter = [])
    {
        $this->levelid = $levelId;
        $this->chunks = $chunks;
        $this->blocksAfter = $blocksAfter;
    }

    /**
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     * @since 5.1.0
     */
    public function serialize()
    {
        $chunks = [];
        foreach ($this->chunks as $chunk)
            $chunks[Level::chunkHash($chunk->getX(), $chunk->getZ())] = $chunk->fastSerialize();
        return serialize([
            $this->levelid,
            $chunks,
            $this->blocksAfter
        ]);
    }

    /**
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     * @since 5.1.0
     */
    public function unserialize($serialized)
    {
        [
            $this->levelid,
            $chunks,
            $this->blocksAfter
        ] = unserialize($serialized);
        foreach ($chunks as $hash => $chunk)
            $this->chunks[$hash] = Chunk::fastDeserialize($chunk);
    }
}