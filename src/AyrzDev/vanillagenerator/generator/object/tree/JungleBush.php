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

namespace AyrzDev\vanillagenerator\generator\object\tree;

use pocketmine\block\Block;
use pocketmine\block\BlockTypeIds;
use pocketmine\block\Leaves;
use pocketmine\block\VanillaBlocks;
use pocketmine\utils\Random;
use pocketmine\world\BlockTransaction;
use pocketmine\world\ChunkManager;

class JungleBush extends GenericTree
{

	/**
	 * Initializes this bush, preparing it to attempt to generate.
	 * @param Random $random
	 * @param BlockTransaction $transaction
	 */
	public function __construct(Random $random, BlockTransaction $transaction)
	{
		parent::__construct($random, $transaction);
		$this->setType(VanillaBlocks::JUNGLE_LOG(), VanillaBlocks::JUNGLE_LEAVES());
	}

	public function canPlaceOn(Block $soil): bool
	{
		$id = $soil->getTypeId();
		return $id === BlockTypeIds::GRASS || $id === BlockTypeIds::DIRT;
	}

	public function generate(ChunkManager $world, Random $random, int $source_x, int $source_y, int $source_z): bool
	{
		while ((
			($block = $world->getBlockAt($source_x, $source_y, $source_z))->getTypeId() === BlockTypeIds::AIR ||
			$block instanceof Leaves
		) && $source_y > 0) {
			--$source_y;
		}

		// check only below block
		if (!$this->canPlaceOn($world->getBlockAt($source_x, $source_y - 1, $source_z))) {
			return false;
		}

		// generates the trunk
		$adjust_y = $source_y;
		$this->transaction->addBlockAt($source_x, $adjust_y + 1, $source_z, $this->log_type);

		// generates the leaves
		for ($y = $adjust_y + 1; $y <= $adjust_y + 3; ++$y) {
			$radius = 3 - ($y - $adjust_y);

			for ($x = $source_x - $radius; $x <= $source_x + $radius; ++$x) {
				for ($z = $source_z - $radius; $z <= $source_z + $radius; ++$z) {
					if (
						!$this->transaction->fetchBlockAt($x, $y, $z)->isSolid() &&
						(
							abs($x - $source_x) !== $radius ||
							abs($z - $source_z) !== $radius ||
							$random->nextBoolean()
						)
					) {
						$this->transaction->addBlockAt($x, $y, $z, $this->leaves_type);
					}
				}
			}
		}

		return true;
	}
}
