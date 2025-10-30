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
use AyrzDev\vanillagenerator\generator\object\Flower;
use AyrzDev\vanillagenerator\generator\overworld\decorator\types\FlowerDecoration;
use pocketmine\block\Block;
use pocketmine\utils\Random;
use pocketmine\world\ChunkManager;
use pocketmine\world\format\Chunk;

class FlowerDecorator extends Decorator
{

	/**
	 * @param Random $random
	 * @param FlowerDecoration[] $decorations
	 * @return Block|null
	 */
	private static function getRandomFlower(Random $random, array $decorations): ?Block
	{
		$total_weight = 0;
		foreach ($decorations as $decoration) {
			$total_weight += $decoration->weight;
		}

		if ($total_weight > 0) {
			$weight = $random->nextBoundedInt($total_weight);
			foreach ($decorations as $decoration) {
				$weight -= $decoration->weight;
				if ($weight < 0) {
					return $decoration->block;
				}
			}
		}

		return null;
	}

	/** @var FlowerDecoration[] */
	private array $flowers = [];

	final public function setFlowers(FlowerDecoration ...$flowers): void
	{
		$this->flowers = $flowers;
	}

	public function decorate(ChunkManager $world, Random $random, int $chunk_x, int $chunk_z, Chunk $chunk): void
	{
		$x = $random->nextBoundedInt(16);
		$z = $random->nextBoundedInt(16);
		$source_y = $random->nextBoundedInt($chunk->getHighestBlockAt($x & Chunk::COORD_MASK, $z & Chunk::COORD_MASK) + 32);

		// the flower can change on each decoration pass
		$flower = self::getRandomFlower($random, $this->flowers);
		if ($flower !== null) {
			(new Flower($flower))->generate($world, $random, ($chunk_x << Chunk::COORD_BIT_SIZE) + $x, $source_y, ($chunk_z << Chunk::COORD_BIT_SIZE) + $z);
		}
	}
}
