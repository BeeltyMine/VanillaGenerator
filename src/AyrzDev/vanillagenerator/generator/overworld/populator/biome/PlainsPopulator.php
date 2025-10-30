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

namespace AyrzDev\vanillagenerator\generator\overworld\populator\biome;

use AyrzDev\vanillagenerator\generator\noise\bukkit\OctaveGenerator;
use AyrzDev\vanillagenerator\generator\noise\glowstone\SimplexOctaveGenerator;
use AyrzDev\vanillagenerator\generator\object\DoubleTallPlant;
use AyrzDev\vanillagenerator\generator\object\Flower;
use AyrzDev\vanillagenerator\generator\object\TallGrass;
use AyrzDev\vanillagenerator\generator\overworld\biome\BiomeIds;
use pocketmine\block\Block;
use pocketmine\block\VanillaBlocks;
use pocketmine\utils\Random;
use pocketmine\world\ChunkManager;
use pocketmine\world\format\Chunk;

class PlainsPopulator extends BiomePopulator{

	/** @var Block[] */
	protected static array $PLAINS_FLOWERS;

	/** @var Block[] */
	protected static array $PLAINS_TULIPS;

	public static function init() : void{
		parent::init();

		self::$PLAINS_FLOWERS = [
			VanillaBlocks::POPPY(),
			VanillaBlocks::AZURE_BLUET(),
			VanillaBlocks::OXEYE_DAISY()
		];

		self::$PLAINS_TULIPS = [
			VanillaBlocks::RED_TULIP(),
			VanillaBlocks::ORANGE_TULIP(),
			VanillaBlocks::WHITE_TULIP(),
			VanillaBlocks::PINK_TULIP()
		];
	}

	private OctaveGenerator $noise_gen;

	public function __construct(){
		parent::__construct();
		$this->noise_gen = SimplexOctaveGenerator::fromRandomAndOctaves(new Random(2345), 1, 0, 0, 0);
		$this->noise_gen->setScale(1 / 200.0);
	}

	protected function initPopulators() : void{
		$this->flower_decorator->setAmount(0);
		$this->tall_grass_decorator->setAmount(0);
	}

	public function getBiomes() : ?array{
		return [BiomeIds::PLAINS];
	}

	public function populateOnGround(ChunkManager $world, Random $random, int $chunk_x, int $chunk_z, Chunk $chunk) : void{
		$source_x = $chunk_x << Chunk::COORD_BIT_SIZE;
		$source_z = $chunk_z << Chunk::COORD_BIT_SIZE;

		$flower_amount = 15;
		$tall_grass_amount = 5;
		if($this->noise_gen->noise($source_x + 8, $source_z + 8, 0, 0.5, 2.0, false) >= -0.8){
			$flower_amount = 4;
			$tall_grass_amount = 10;
			for($i = 0; $i < 7; ++$i){
				$x = $random->nextBoundedInt(16);
				$z = $random->nextBoundedInt(16);
				$y = $random->nextBoundedInt($chunk->getHighestBlockAt($x, $z) + 32);
				(new DoubleTallPlant(VanillaBlocks::DOUBLE_TALLGRASS()))->generate($world, $random, $source_x + $x, $y, $source_z + $z);
			}
		}

		$flower = match(true){
			$this->noise_gen->noise($source_x + 8, $source_z + 8, 0, 0.5, 2.0, false) < -0.8 => self::$PLAINS_TULIPS[$random->nextBoundedInt(count(self::$PLAINS_TULIPS))],
			$random->nextBoundedInt(3) > 0 => self::$PLAINS_FLOWERS[$random->nextBoundedInt(count(self::$PLAINS_FLOWERS))],
			default => VanillaBlocks::DANDELION()
		};

		for($i = 0; $i < $flower_amount; ++$i){
			$x = $random->nextBoundedInt(16);
			$z = $random->nextBoundedInt(16);
			$y = $random->nextBoundedInt($chunk->getHighestBlockAt($x, $z) + 32);
			(new Flower($flower))->generate($world, $random, $source_x + $x, $y, $source_z + $z);
		}

		for($i = 0; $i < $tall_grass_amount; ++$i){
			$x = $random->nextBoundedInt(16);
			$z = $random->nextBoundedInt(16);
			$y = $random->nextBoundedInt($chunk->getHighestBlockAt($x, $z) << 1);
			(new TallGrass(VanillaBlocks::TALL_GRASS()))->generate($world, $random, $source_x + $x, $y, $source_z + $z);
		}

		parent::populateOnGround($world, $random, $chunk_x, $chunk_z, $chunk);
	}
}

PlainsPopulator::init();