<?php

declare(strict_types=1);

namespace AyrzDev\vanillagenerator\generator\end\decorator;

use AyrzDev\vanillagenerator\generator\Decorator;
use pocketmine\utils\Random;
use pocketmine\world\ChunkManager;
use pocketmine\world\format\Chunk;
use pocketmine\block\VanillaBlocks;
use pocketmine\block\BlockTypeIds;
use AyrzDev\vanillagenerator\Loader;

class EndSpawnDecorator extends Decorator
{
    // radius (in blocks) of the central spawn island
    private int $spawnRadius = 500; // ~1000 diameter as requested
    private int $baseY = 60; // base level for island
    private int $spawnHeight = 48; // additional height at center

    public function __construct(){
    }

    public function decorate(ChunkManager $world, Random $random, int $chunk_x, int $chunk_z, Chunk $chunk): void
    {
        $startX = $chunk_x << Chunk::COORD_BIT_SIZE;
        $startZ = $chunk_z << Chunk::COORD_BIT_SIZE;

        // build a rounded island centered at (0,0)
        $endStoneState = VanillaBlocks::END_STONE()->getStateId();
        $minY = $world->getMinY();
        $maxY = $world->getMaxY();

        for($lx = 0; $lx < 16; ++$lx){
            for($lz = 0; $lz < 16; ++$lz){
                $bx = $startX + $lx;
                $bz = $startZ + $lz;

                $d = sqrt($bx * $bx + $bz * $bz);
                if($d > $this->spawnRadius) continue;

                $factor = 1.0 - ($d / $this->spawnRadius);
                $top = $this->baseY + (int)($factor * $factor * $this->spawnHeight);
                $thickness = 4 + (int)($factor * 6);

                // fill a small column (will be smoothed by overlapping calls)
                for($y = $top - $thickness + 1; $y <= $top; ++$y){
                    if($y >= $minY && $y < $maxY){
                        // write into the provided chunk only (local coords)
                        $localX = $lx;
                        $localZ = $lz;
                        $chunk->setBlockStateId($localX, $y, $localZ, $endStoneState);
                    }
                }
            }
        }

        // Add a set of obsidian pillars surrounding the central portal
        $towerCount = 8;
        for($i = 0; $i < $towerCount; ++$i){
            $angle = ($i / $towerCount) * M_PI * 2.0;
            $rad = (int)(max(6, $this->spawnRadius * 0.12));
            $tx = (int)(cos($angle) * $rad);
            $tz = (int)(sin($angle) * $rad);
            $bx = $tx;
            $bz = $tz;

            // only build if tower column intersects this chunk
            if($bx >= $startX && $bx < $startX + 16 && $bz >= $startZ && $bz < $startZ + 16){
                $localX = $bx - $startX;
                $localZ = $bz - $startZ;

                // find highest non-air block at column within this chunk
                $topY = $chunk->getHighestBlockAt($localX, $localZ);
                if($topY === null) $topY = $this->baseY;

                // build obsidian pillar using chunk-local writes
                $obsState = VanillaBlocks::OBSIDIAN()->getStateId();
                $towerHeight = 10 + $random->nextBoundedInt(24);
                for($h = 1; $h <= $towerHeight; ++$h){
                    $y = $topY + $h;
                    if($y >= $minY && $y < $maxY){
                        $chunk->setBlockStateId($localX, $y, $localZ, $obsState);
                    }
                }

                // schedule EndCrystal on top of this pillar (defer to main thread). Use plugin queue if available.
                try{
                    $pm = \pocketmine\Server::getInstance()->getPluginManager();
                    $plugin = $pm->getPlugin("VanillaGenerator");
                    if($plugin instanceof \AyrzDev\vanillagenerator\Loader){
                        $topEntityY = $topY + $towerHeight + 1;
                        $worldName = $world instanceof \pocketmine\world\World ? $world->getFolderName() : '';
                        if($worldName !== ''){
                            $plugin->queueCrystal($worldName, $bx, $topEntityY, $bz, true);
                        }
                    }
                }catch(\Throwable $_){
                    // ignore if queueing fails (best-effort)
                }
            }
        }

        // create a small obsidian platform in the very center for a portal placeholder (chunk-local)
        $centerY = $this->baseY + (int)($this->spawnHeight * 0.5);
        $obsState = VanillaBlocks::OBSIDIAN()->getStateId();
        for($ox = -2; $ox <= 2; ++$ox){
            for($oz = -2; $oz <= 2; ++$oz){
                $absX = $ox;
                $absZ = $oz;
                if($absX >= $startX && $absX < $startX + 16 && $absZ >= $startZ && $absZ < $startZ + 16){
                    $localX = $absX - $startX;
                    $localZ = $absZ - $startZ;
                    if($centerY >= $minY && $centerY < $maxY){
                        $chunk->setBlockStateId($localX, $centerY, $localZ, $obsState);
                    }
                }
            }
        }
    }
}
