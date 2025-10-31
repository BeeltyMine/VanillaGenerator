<?php

/*
 *    $$$$$$$\                      $$\   $$\               $$\      $$\ $$\                     
 *    $$  __$$\                     $$ |  $$ |              $$$\    $$$ |\__|                         
 *    $$ |  $$ | $$$$$$\   $$$$$$\  $$ |$$$$$$\   $$\   $$\ $$$$\  $$$$ |$$\ $$$$$$$\   $$$$$$\        
 *    $$$$$$$\ |$$  __$$\ $$  __$$\ $$ |\_$$  _|  $$ |  $$ |$$\$$\$$ $$ |$$ |$$  __$$\ $$  __$$\ 
 *    $$  __$$\ $$$$$$$$ |$$$$$$$$ |$$ |  $$ |    $$ |  $$ |$$ \$$$  $$ |$$ |$$ |  $$ |$$$$$$$$ |
 *    $$ |  $$ |$$   ____|$$   ____|$$ |  $$ |$$\ $$ |  $$ |$$ |\$  /$$ |$$ |$$ |  $$ |$$   ____|        
 *    $$$$$$$  |\$$$$$$$\ \$$$$$$$\ $$ |  \$$$$  |\$$$$$$$ |$$ | \_/ $$ |$$ |$$ |  $$ |\$$$$$$$\       
 *    \_______/  \_______| \_______|\__|   \____/  \____$$ |\__|     \__|\__|\__|  \__| \_______|     
 *                                                $$\   $$ |                                                                    
 *                                                \$$$$$$  |                                                                    
 *                                                 \______/           
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * @author AyrzDev
 * @project BeeltyMine VanillaGenerator
 * @link https://dephts.com
 * 
 *                                   
 */

declare(strict_types=1);

namespace AyrzDev\vanillagenerator\generator;

use AyrzDev\vanillagenerator\generator\biomegrid\BiomeGrid;
use pocketmine\world\format\Chunk;
use function array_key_exists;

class VanillaBiomeGrid implements BiomeGrid
{

	/** @var int[] */
	public array $biomes = [];

	/** @var int[] */
	private array $biomes_3d = [];

	public function getBiome(int $x, int $z): ?int
	{
		// upcasting is very important to get extended biomes
		return array_key_exists($hash = $x | $z << Chunk::COORD_BIT_SIZE, $this->biomes) ? $this->biomes[$hash] & 0xFF : null;
	}

	public function getBiome3D(int $x, int $y, int $z): ?int
	{
		// For 3D biomes, we use a different hash that includes Y coordinate
		// We sample biomes every 4 blocks in Y (like 1.18+) to save memory
		$sample_y = $y >> 2; // Sample every 4 blocks in Y direction
		$hash = ($x) | ($z << 4) | ($sample_y << 8);

		if (array_key_exists($hash, $this->biomes_3d)) {
			return $this->biomes_3d[$hash] & 0xFF;
		}

		// Fall back to surface biome if no 3D biome is set
		return $this->getBiome($x, $z);
	}
	public function setBiome(int $x, int $z, int $biome_id): void
	{
		$this->biomes[$x | $z << Chunk::COORD_BIT_SIZE] = $biome_id;
	}
	public function setBiome3D(int $x, int $y, int $z, int $biome_id): void
	{
		$sample_y = $y >> 2;
		$hash = ($x) | ($z << 4) | ($sample_y << 8);
		$this->biomes_3d[$hash] = $biome_id;
	}
}
