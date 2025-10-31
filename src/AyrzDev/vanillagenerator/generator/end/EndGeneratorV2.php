<?php

declare(strict_types=1);

namespace AyrzDev\vanillagenerator\generator\end;

use AyrzDev\vanillagenerator\generator\VanillaGenerator;
use AyrzDev\vanillagenerator\generator\utils\preset\SimpleGeneratorPreset;
use AyrzDev\vanillagenerator\generator\utils\NetherWorldOctaves;
use AyrzDev\vanillagenerator\generator\noise\glowstone\PerlinOctaveGenerator;
use AyrzDev\vanillagenerator\generator\end\populator\EndPopulator;
use AyrzDev\vanillagenerator\generator\Environment;
use AyrzDev\vanillagenerator\generator\overworld\WorldType;
use pocketmine\utils\Random;
use pocketmine\world\ChunkManager;
use pocketmine\world\format\Chunk;
use pocketmine\block\VanillaBlocks;

class EndGeneratorV2 extends VanillaGenerator{

    protected const SURFACE_SCALE = 0.0625;

    public function __construct(int $seed, string $preset_string){
        $preset = SimpleGeneratorPreset::parse($preset_string);
        parent::__construct(
            $seed,
            $preset->exists("environment") ? Environment::fromString($preset->getString("environment")) : Environment::THE_END,
            $preset->exists("worldtype") ? WorldType::fromString($preset->getString("worldtype")) : null,
            $preset
        );
    }

    protected function createWorldOctaves(): NetherWorldOctaves{
        $seed = new Random($this->random->getSeed());

        $height = PerlinOctaveGenerator::fromRandomAndOctaves($seed, 16, 5, 1, 5);
        $roughness = PerlinOctaveGenerator::fromRandomAndOctaves($seed, 16, 5, 17, 5);
        $roughness2 = PerlinOctaveGenerator::fromRandomAndOctaves($seed, 16, 5, 17, 5);
        $detail = PerlinOctaveGenerator::fromRandomAndOctaves($seed, 8, 5, 17, 5);
        $surface = PerlinOctaveGenerator::fromRandomAndOctaves($seed, 4, 16, 16, 1);
        $surface->setScale(self::SURFACE_SCALE);

        return new NetherWorldOctaves($height, $roughness, $roughness2, $detail, $surface, $surface, $surface);
    }

    protected function generateChunkData(ChunkManager $world, int $chunk_x, int $chunk_z, \AyrzDev\vanillagenerator\generator\VanillaBiomeGrid $biomes): void{
        $chunk = $world->getChunk($chunk_x, $chunk_z);

        $min_y = $world->getMinY();
        $max_y = $world->getMaxY();

        $end_stone = VanillaBlocks::END_STONE()->getStateId();
        $air = VanillaBlocks::AIR()->getStateId();

        // Fill with air
        for($x = 0; $x < 16; ++$x){
            for($z = 0; $z < 16; ++$z){
                for($y = $min_y; $y < $max_y; ++$y){
                    $chunk->setBlockStateId($x, $y, $z, $air);
                }
            }
        }

        $chunkBaseX = $chunk_x << Chunk::COORD_BIT_SIZE;
        $chunkBaseZ = $chunk_z << Chunk::COORD_BIT_SIZE;

        // distances in blocks from world origin
        $spawnRadius = 500; // main island radius in blocks (≈1000 diameter)
        $outerStart = 1000; // outer islands begin beyond this distance

        // Determine whether this chunk is within main island or outer island region
        $inMain = false;
        // Check center of chunk
        $centerX = $chunkBaseX + 8;
        $centerZ = $chunkBaseZ + 8;
        $distCenter = sqrt($centerX * $centerX + $centerZ * $centerZ);
        if($distCenter <= $spawnRadius + 16){
            $inMain = true;
        }

        if($inMain){
            // build a big rounded main island (ellipsoid-ish)
            $base_top = 70;
            for($x = 0; $x < 16; ++$x){
                for($z = 0; $z < 16; ++$z){
                    $bx = $chunkBaseX + $x;
                    $bz = $chunkBaseZ + $z;
                    $d = sqrt($bx * $bx + $bz * $bz);
                    if($d > $spawnRadius) continue;

                    $factor = 1.0 - ($d / $spawnRadius);
                    $top = $base_top + (int) (pow($factor, 1.6) * $this->random->nextBoundedInt(40));
                    $thickness = 6 + (int) ($factor * 24);
                    for($y = $top - $thickness + 1; $y <= $top; ++$y){
                        if($y >= $min_y && $y < $max_y){
                            $chunk->setBlockStateId($x, $y, $z, $end_stone);
                        }
                    }
                }
            }
        }else{
            // outer islands: only generate when beyond outerStart using surface noise
            $distEdge = $distCenter;
            if($distEdge >= $outerStart){
                /** @var \AyrzDev\vanillagenerator\generator\noise\glowstone\PerlinOctaveGenerator $octave */
                $octave = $this->getWorldOctaves()->surface;
                $surface_noise = $octave->getFractalBrownianMotion($chunkBaseX, 0.0, $chunkBaseZ, 0.5, 0.5);

                $threshold = 0.2;
                $base_top = 58;

                for($x = 0; $x < 16; ++$x){
                    for($z = 0; $z < 16; ++$z){
                        $index = $x | ($z << Chunk::COORD_BIT_SIZE);
                        $n = $surface_noise[$index] ?? 0.0;
                        if($n > $threshold){
                            $top = $base_top + (int)(($n - $threshold) / (1.0 - $threshold) * 20.0);
                            $thickness = 2 + ($this->random->nextBoundedInt(4));
                            for($y = $top - $thickness + 1; $y <= $top; ++$y){
                                if($y >= $min_y && $y < $max_y){
                                    $chunk->setBlockStateId($x, $y, $z, $end_stone);
                                }
                            }
                        }
                    }
                }
            }
        }

        $populator = new EndPopulator();
        $populator->populate($world, $this->random, $chunk_x, $chunk_z, $chunk);
    }
}
