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

use AyrzDev\vanillagenerator\generator\object\tree\SwampTree;
use AyrzDev\vanillagenerator\generator\overworld\biome\BiomeIds;
use AyrzDev\vanillagenerator\generator\overworld\decorator\MushroomDecorator;
use AyrzDev\vanillagenerator\generator\overworld\decorator\types\FlowerDecoration;
use AyrzDev\vanillagenerator\generator\overworld\decorator\types\TreeDecoration;
use AyrzDev\vanillagenerator\generator\overworld\decorator\WaterLilyDecorator;
use pocketmine\block\VanillaBlocks;
use pocketmine\utils\Random;
use pocketmine\world\ChunkManager;
use pocketmine\world\format\Chunk;

class SwamplandPopulator extends BiomePopulator{

	/** @var TreeDecoration[] */
	protected static array $TREES;

	/** @var FlowerDecoration[] */
	protected static array $FLOWERS;

	protected static function initTrees() : void{
		self::$TREES = [
			new TreeDecoration(SwampTree::class, 1)
		];
	}

	protected static function initFlowers() : void{
		self::$FLOWERS = [
			new FlowerDecoration(VanillaBlocks::BLUE_ORCHID(), 1)
		];
	}

	private MushroomDecorator $swampland_brown_mushroom_decorator;
	private MushroomDecorator $swampland_red_mushroom_decorator;
	private WaterLilyDecorator $waterlily_decorator;

	public function __construct(){
		$this->swampland_brown_mushroom_decorator = new MushroomDecorator(VanillaBlocks::BROWN_MUSHROOM());
		$this->swampland_red_mushroom_decorator = new MushroomDecorator(VanillaBlocks::RED_MUSHROOM());
		$this->waterlily_decorator = new WaterLilyDecorator();
		parent::__construct();
	}

	protected function initPopulators() : void{
		$this->sand_patch_decorator->setAmount(0);
		$this->gravel_patch_decorator->setAmount(0);
		$this->tree_decorator->setAmount(2);
		$this->tree_decorator->setTrees(...self::$TREES);
		$this->flower_decorator->setAmount(1);
		$this->flower_decorator->setFlowers(...self::$FLOWERS);
		$this->tall_grass_decorator->setAmount(5);
		$this->dead_bush_decorator->setAmount(1);
		$this->sugar_cane_decorator->setAmount(20);
		$this->swampland_brown_mushroom_decorator->setAmount(8);
		$this->swampland_red_mushroom_decorator->setAmount(8);
		$this->waterlily_decorator->setAmount(4);
	}

	public function getBiomes() : ?array{
		return [BiomeIds::SWAMPLAND, BiomeIds::SWAMPLAND_MUTATED];
	}

	protected function populateOnGround(ChunkManager $world, Random $random, int $chunk_x, int $chunk_z, Chunk $chunk) : void{
		parent::populateOnGround($world, $random, $chunk_x, $chunk_z, $chunk);
		$this->swampland_brown_mushroom_decorator->populate($world, $random, $chunk_x, $chunk_z, $chunk);
		$this->swampland_red_mushroom_decorator->populate($world, $random, $chunk_x, $chunk_z, $chunk);
		$this->waterlily_decorator->populate($world, $random, $chunk_x, $chunk_z, $chunk);
	}
}

SwamplandPopulator::init();