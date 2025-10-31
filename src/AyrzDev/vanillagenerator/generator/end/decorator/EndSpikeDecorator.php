<?php

declare(strict_types=1);

namespace AyrzDev\vanillagenerator\generator\end\decorator;

use AyrzDev\vanillagenerator\generator\Decorator;
use pocketmine\utils\Random;
use pocketmine\world\ChunkManager;
use pocketmine\world\format\Chunk;
use pocketmine\block\VanillaBlocks;
use pocketmine\block\BlockTypeIds;

class EndSpikeDecorator extends Decorator
{
    public function __construct(){
    }

    public function decorate(ChunkManager $world, Random $random, int $chunk_x, int $chunk_z, Chunk $chunk): void
    {
        $minY = $world->getMinY();
        $maxY = $world->getMaxY();

        // choose a random local x,z inside the chunk
        $localX = $random->nextBoundedInt(16);
        $localZ = $random->nextBoundedInt(16);

        // find the highest non-air block at that column within this chunk
        $highest = $chunk->getHighestBlockAt($localX, $localZ);
        if($highest === null){
            return; // nothing to attach spike to
        }

        // if the highest is end stone, grow a small spike above it
        $endState = VanillaBlocks::END_STONE()->getStateId();
        $topState = $chunk->getBlockStateId($localX, $highest, $localZ);
        if($topState !== $endState){
            return;
        }

        $spikeHeight = 1 + $random->nextBoundedInt(4);
        for($i = 1; $i <= $spikeHeight; ++$i){
            $y = $highest + $i;
            if($y >= $minY && $y < $maxY){
                $chunk->setBlockStateId($localX, $y, $localZ, $endState);
            }
        }
    }
}
