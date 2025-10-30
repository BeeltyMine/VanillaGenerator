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

use AyrzDev\vanillagenerator\generator\object\tree\MegaPineTree;
use AyrzDev\vanillagenerator\generator\object\tree\MegaSpruceTree;
use AyrzDev\vanillagenerator\generator\object\tree\RedwoodTree;
use AyrzDev\vanillagenerator\generator\object\tree\TallRedwoodTree;
use AyrzDev\vanillagenerator\generator\overworld\biome\BiomeIds;
use AyrzDev\vanillagenerator\generator\overworld\decorator\StoneBoulderDecorator;
use AyrzDev\vanillagenerator\generator\overworld\decorator\types\TreeDecoration;
use pocketmine\utils\Random;
use pocketmine\world\ChunkManager;
use pocketmine\world\format\Chunk;

class MegaTaigaPopulator extends TaigaPopulator{

	/** @var TreeDecoration[] */
	protected static array $TREES;

	protected static function initTrees() : void{
		self::$TREES = [
			new TreeDecoration(RedwoodTree::class, 52),
			new TreeDecoration(TallRedwoodTree::class, 26),
			new TreeDecoration(MegaPineTree::class, 36),
			new TreeDecoration(MegaSpruceTree::class, 3)
		];
	}

	public function getBiomes() : ?array{
		return [BiomeIds::MEGA_TAIGA, BiomeIds::MEGA_TAIGA_HILLS];
	}

	protected StoneBoulderDecorator $stone_boulder_decorator;

	public function __construct(){
		parent::__construct();
		$this->stone_boulder_decorator = new StoneBoulderDecorator();
	}

	protected function initPopulators() : void{
		$this->tree_decorator->setTrees(...self::$TREES);
		$this->tall_grass_decorator->setAmount(7);
		$this->dead_bush_decorator->setAmount(0);
		$this->taiga_brown_mushroom_decorator->setAmount(3);
		$this->taiga_red_mushroom_decorator->setAmount(3);
	}

	protected function populateOnGround(ChunkManager $world, Random $random, int $chunk_x, int $chunk_z, Chunk $chunk) : void{
		$this->stone_boulder_decorator->populate($world, $random, $chunk_x, $chunk_z, $chunk);
		parent::populateOnGround($world, $random, $chunk_x, $chunk_z, $chunk);
	}
}

MegaTaigaPopulator::init();