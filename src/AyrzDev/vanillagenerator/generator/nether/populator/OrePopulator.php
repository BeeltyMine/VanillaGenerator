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

namespace AyrzDev\vanillagenerator\generator\nether\populator;

use AyrzDev\vanillagenerator\generator\object\OreType;
use AyrzDev\vanillagenerator\generator\overworld\populator\biome\OrePopulator as OverworldOrePopulator;
use pocketmine\block\BlockTypeIds;
use pocketmine\block\VanillaBlocks;
use pocketmine\world\World;

class OrePopulator extends OverworldOrePopulator
{

	/**
	 * @noinspection MagicMethodsValidityInspection
	 * @noinspection PhpMissingParentConstructorInspection
	 * @param int $world_height
	 */
	public function __construct(int $world_height = World::Y_MAX)
	{
		$this->addOre(new OreType(VanillaBlocks::NETHER_QUARTZ_ORE(), 10, $world_height - (10 * ($world_height >> 7)), 13, BlockTypeIds::NETHERRACK), 16);
		$this->addOre(new OreType(VanillaBlocks::MAGMA(), 26, 32 + (5 * ($world_height >> 7)), 32, BlockTypeIds::NETHERRACK), 16);
	}
}
