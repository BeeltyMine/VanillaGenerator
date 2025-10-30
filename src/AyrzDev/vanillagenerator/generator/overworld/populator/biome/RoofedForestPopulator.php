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

use AyrzDev\vanillagenerator\generator\object\tree\BirchTree;
use AyrzDev\vanillagenerator\generator\object\tree\BrownMushroomTree;
use AyrzDev\vanillagenerator\generator\object\tree\DarkOakTree;
use AyrzDev\vanillagenerator\generator\object\tree\GenericTree;
use AyrzDev\vanillagenerator\generator\object\tree\RedMushroomTree;
use AyrzDev\vanillagenerator\generator\overworld\biome\BiomeIds;
use AyrzDev\vanillagenerator\generator\overworld\decorator\types\TreeDecoration;

class RoofedForestPopulator extends ForestPopulator{

	private const BIOMES = [BiomeIds::ROOFED_FOREST, BiomeIds::ROOFED_FOREST_MUTATED];

	/** @var TreeDecoration[] */
	protected static array $TREES;

	protected static function initTrees() : void{
		self::$TREES = [
			new TreeDecoration(GenericTree::class, 20),
			new TreeDecoration(BirchTree::class, 5),
			new TreeDecoration(RedMushroomTree::class, 2),
			new TreeDecoration(BrownMushroomTree::class, 2),
			new TreeDecoration(DarkOakTree::class, 50)
		];
	}

	protected function initPopulators() : void{
		$this->tree_decorator->setAmount(50);
		$this->tree_decorator->setTrees(...self::$TREES);
		$this->tall_grass_decorator->setAmount(4);
	}

	public function getBiomes() : ?array{
		return self::BIOMES;
	}
}

RoofedForestPopulator::init();