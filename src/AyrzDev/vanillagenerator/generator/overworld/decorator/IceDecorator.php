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

namespace AyrzDev\vanillagenerator\generator\overworld\decorator;

use AyrzDev\vanillagenerator\generator\Decorator;
use AyrzDev\vanillagenerator\generator\object\BlockPatch;
use AyrzDev\vanillagenerator\generator\object\IceSpike;
use pocketmine\block\BlockTypeIds;
use pocketmine\block\VanillaBlocks;
use pocketmine\utils\Random;
use pocketmine\world\ChunkManager;
use pocketmine\world\format\Chunk;

class IceDecorator extends Decorator
{

	/** @var int[] */
	private static array $OVERRIDABLES;

	public static function init(): void
	{
		self::$OVERRIDABLES = [
			VanillaBlocks::DIRT()->getStateId(),
			VanillaBlocks::GRASS()->getStateId(),
			VanillaBlocks::SNOW()->getStateId(),
			VanillaBlocks::ICE()->getStateId()
		];
	}

	public function populate(ChunkManager $world, Random $random, int $chunk_x, int $chunk_z, Chunk $chunk): void
	{
		$source_x = $chunk_x << Chunk::COORD_BIT_SIZE;
		$source_z = $chunk_z << Chunk::COORD_BIT_SIZE;

		for ($i = 0; $i < 3; ++$i) {
			$x = $source_x + $random->nextBoundedInt(16);
			$z = $source_z + $random->nextBoundedInt(16);
			$y = $chunk->getHighestBlockAt($x & Chunk::COORD_MASK, $z & Chunk::COORD_MASK) - 1;
			while ($y > 2 && $world->getBlockAt($x, $y, $z)->getTypeId() === BlockTypeIds::AIR) {
				--$y;
			}
			if ($world->getBlockAt($x, $y, $z)->getTypeId() === BlockTypeIds::SNOW) {
				(new BlockPatch(VanillaBlocks::PACKED_ICE(), 4, 1, ...self::$OVERRIDABLES))->generate($world, $random, $x, $y, $z);
			}
		}

		for ($i = 0; $i < 2; ++$i) {
			$x = $source_x + $random->nextBoundedInt(16);
			$z = $source_z + $random->nextBoundedInt(16);
			$y = $chunk->getHighestBlockAt($x & Chunk::COORD_MASK, $z & Chunk::COORD_MASK);
			while ($y > 2 && $world->getBlockAt($x, $y, $z)->getTypeId() === BlockTypeIds::AIR) {
				--$y;
			}
			if ($world->getBlockAt($x, $y, $z)->getTypeId() === BlockTypeIds::SNOW) {
				(new IceSpike())->generate($world, $random, $x, $y, $z);
			}
		}
	}

	public function decorate(ChunkManager $world, Random $random, int $chunk_x, int $chunk_z, Chunk $chunk): void {}
}

IceDecorator::init();
