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
use AyrzDev\vanillagenerator\generator\object\DoubleTallPlant;
use AyrzDev\vanillagenerator\generator\overworld\decorator\types\DoublePlantDecoration;
use pocketmine\block\DoublePlant;
use pocketmine\utils\Random;
use pocketmine\world\ChunkManager;
use pocketmine\world\format\Chunk;

class DoublePlantDecorator extends Decorator
{

	/**
	 * @param Random $random
	 * @param DoublePlantDecoration[] $decorations
	 * @return DoublePlant|null
	 */
	private static function getRandomDoublePlant(Random $random, array $decorations): ?DoublePlant
	{
		$totalWeight = 0;
		foreach ($decorations as $decoration) {
			$totalWeight += $decoration->weight;
		}
		$weight = $random->nextBoundedInt($totalWeight);
		foreach ($decorations as $decoration) {
			$weight -= $decoration->weight;
			if ($weight < 0) {
				return $decoration->block;
			}
		}
		return null;
	}

	/** @var DoublePlantDecoration[] */
	private array $doublePlants = [];

	final public function setDoublePlants(DoublePlantDecoration ...$doublePlants): void
	{
		$this->doublePlants = $doublePlants;
	}

	public function decorate(ChunkManager $world, Random $random, int $chunk_x, int $chunk_z, Chunk $chunk): void
	{
		$x = $random->nextBoundedInt(16);
		$z = $random->nextBoundedInt(16);
		$source_y = $random->nextBoundedInt($chunk->getHighestBlockAt($x, $z) + 32);

		$species = self::getRandomDoublePlant($random, $this->doublePlants);
		(new DoubleTallPlant($species))->generate($world, $random, ($chunk_x << Chunk::COORD_BIT_SIZE) + $x, $source_y, ($chunk_z << Chunk::COORD_BIT_SIZE) + $z);
	}
}
