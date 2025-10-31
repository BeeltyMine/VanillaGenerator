<?php

declare(strict_types=1);

namespace AyrzDev\vanillagenerator\generator\test;

use AyrzDev\vanillagenerator\generator\overworld\OverworldGenerator;
use AyrzDev\vanillagenerator\generator\VanillaBiomeGrid;
use AyrzDev\vanillagenerator\generator\utils\preset\SimpleGeneratorPreset;
use pocketmine\world\ChunkManager;

/**
 * A simple test generator that behaves like the OverworldGenerator but only
 * generates blocks within a fixed chunk radius around a center. Outside this
 * area, chunks remain void (air) and are not populated.
 *
 * Preset options (all optional):
 * - radius=<int>   Default: 1 (3x3 chunks visible)
 * - centerx=<int>  Default: 0 (chunk coordinate)
 * - centerz=<int>  Default: 0 (chunk coordinate)
 *
 * You can still pass other Overworld preset options like environment/worldtype.
 */
final class TestGenerator extends OverworldGenerator{

    private int $centerChunkX = 0;
    private int $centerChunkZ = 0;
    private int $radius = 5; // radius in chunks; 1 => 3x3 area

    public function __construct(int $seed, string $preset_string){
        parent::__construct($seed, $preset_string);

        // Parse custom options from preset string
        $preset = SimpleGeneratorPreset::parse($preset_string);
        if($preset->exists("centerx")){
            $this->centerChunkX = (int) $preset->getString("centerx");
        }
        if($preset->exists("centerz")){
            $this->centerChunkZ = (int) $preset->getString("centerz");
        }
        if($preset->exists("radius")){
            $r = (int) $preset->getString("radius");
            $this->radius = $r < 0 ? 0 : $r;
        }
    }

    private function isWithinAllowedArea(int $chunk_x, int $chunk_z) : bool{
        return (abs($chunk_x - $this->centerChunkX) <= $this->radius)
            && (abs($chunk_z - $this->centerChunkZ) <= $this->radius);
    }

    protected function generateChunkData(ChunkManager $world, int $chunk_x, int $chunk_z, VanillaBiomeGrid $biomes) : void{
        if(!$this->isWithinAllowedArea($chunk_x, $chunk_z)){
            // Leave chunk as all air outside the allowed area
            return;
        }
        parent::generateChunkData($world, $chunk_x, $chunk_z, $biomes);
    }

    public function populateChunk(ChunkManager $world, int $chunk_x, int $chunk_z) : void{
        if(!$this->isWithinAllowedArea($chunk_x, $chunk_z)){
            // Skip populators outside the allowed area
            return;
        }
        parent::populateChunk($world, $chunk_x, $chunk_z);
    }
}