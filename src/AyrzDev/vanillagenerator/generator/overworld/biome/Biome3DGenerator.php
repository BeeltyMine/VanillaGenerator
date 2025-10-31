<?php

declare(strict_types=1);

namespace AyrzDev\vanillagenerator\generator\overworld\biome;

use AyrzDev\vanillagenerator\generator\VanillaBiomeGrid;
use pocketmine\utils\Random;

class Biome3DGenerator {

	/**
	 * Generate 3D biomes for a chunk based on surface biomes and depth
	 * 
	 * @param VanillaBiomeGrid $biome_grid
	 * @param Random $random
	 * @param int $chunk_x
	 * @param int $chunk_z
	 * @param int $world_min_y
	 * @param int $world_max_y
	 */
	public static function generate3DBiomes(VanillaBiomeGrid $biome_grid, Random $random, int $chunk_x, int $chunk_z, int $world_min_y, int $world_max_y): void {
		for($x = 0; $x < 16; $x += 4) {
			for($z = 0; $z < 16; $z += 4) {
				// Get surface biome
				$surface_biome = $biome_grid->getBiome($x, $z);
				if($surface_biome === null) continue;

				// Generate biomes for different depth layers
				for($y = $world_min_y; $y <= $world_max_y; $y += 4) {
					$depth_biome = self::getBiomeForDepth($surface_biome, $y, $random, $chunk_x * 16 + $x, $chunk_z * 16 + $z);

					// Set biome for 4x4x4 region
					for($dx = 0; $dx < 4 && $x + $dx < 16; $dx++) {
						for($dz = 0; $dz < 4 && $z + $dz < 16; $dz++) {
							for($dy = 0; $dy < 4 && $y + $dy <= $world_max_y; $dy++) {
								$biome_grid->setBiome3D($x + $dx, $y + $dy, $z + $dz, $depth_biome);
							}
						}
					}
				}
			}
		}
	}

	/**
	 * Determine appropriate biome based on surface biome and Y level
	 * 
	 * @param int $surface_biome
	 * @param int $y
	 * @param Random $random
	 * @param int $world_x
	 * @param int $world_z
	 * @return int
	 */
	private static function getBiomeForDepth(int $surface_biome, int $y, Random $random, int $world_x, int $world_z): int {
		// Underground biome generation based on depth and surface biome
		// IMPORTANT: Use deterministic, position-based randomness so results don't depend on PRNG state/order.
		$rand01 = static function(float $chance) use ($random, $world_x, $world_z, $y): bool {
			$seed = method_exists($random, 'getSeed') ? (string)$random->getSeed() : '0';
			// crc32 gives a stable 32-bit unsigned int; normalize to [0,1)
			$h = crc32($seed . '|b3d|' . $world_x . '|' . $world_z . '|' . $y);
			$val = $h / 4294967296.0; // 2^32
			return $val < $chance;
		};

		// Deep Dark biome (Y -64 to -10, rare)
		if($y >= -64 && $y <= -10){
			// 5% chance for Deep Dark in deep areas, higher chance near Y -52
			$deep_dark_chance = ($y <= -40 && $y >= -60) ? 0.08 : 0.02;
			if($rand01($deep_dark_chance)){
				return BiomeIds::DEEP_DARK;
			}
		}

		// Lush Caves (Y -20 to Y 40, forest and jungle areas)
		if($y >= -20 && $y <= 40){
			$is_lush_surface = in_array($surface_biome, [
				BiomeIds::FOREST, BiomeIds::BIRCH_FOREST, BiomeIds::FLOWER_FOREST,
				BiomeIds::JUNGLE, BiomeIds::JUNGLE_HILLS, BiomeIds::ROOFED_FOREST
			], true);

			if($is_lush_surface && $rand01(0.15)){
				return BiomeIds::LUSH_CAVES;
			}
		}

		// Dripstone Caves (Y -60 to Y 20, desert and mesa areas primarily)
		if($y >= -60 && $y <= 20){
			$is_dry_surface = in_array($surface_biome, [
				BiomeIds::DESERT, BiomeIds::DESERT_HILLS, BiomeIds::MESA,
				BiomeIds::MESA_PLATEAU, BiomeIds::MESA_PLATEAU_STONE, BiomeIds::SAVANNA
			], true);

			if($is_dry_surface && $rand01(0.2)){
				return BiomeIds::DRIPSTONE_CAVES;
			}

			// Lower chance in other biomes
			if($rand01(0.05)){
				return BiomeIds::DRIPSTONE_CAVES;
			}
		}
		return $surface_biome;
	}
}
