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

use Exception;
use AyrzDev\vanillagenerator\generator\Decorator;
use AyrzDev\vanillagenerator\generator\object\tree\GenericTree;
use AyrzDev\vanillagenerator\generator\overworld\decorator\types\TreeDecoration;
use pocketmine\utils\Random;
use pocketmine\world\BlockTransaction;
use pocketmine\world\ChunkManager;
use pocketmine\world\format\Chunk;

class TreeDecorator extends Decorator
{

	/**
	 * @param Random $random
	 * @param TreeDecoration[] $decorations
	 * @return class-string<GenericTree>|null
	 */
	private static function getRandomTree(Random $random, array $decorations): ?string
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
					return $decoration->class;
				}
			}
		}

		return null;
	}

	/** @var TreeDecoration[] */
	private array $trees = [];

	final public function setTrees(TreeDecoration ...$trees): void
	{
		$this->trees = $trees;
	}

	public function populate(ChunkManager $world, Random $random, int $chunk_x, int $chunk_z, Chunk $chunk): void
	{
		$treeAmount = $this->amount;
		if ($random->nextBoundedInt(10) === 0) {
			++$treeAmount;
		}

		for ($i = 0; $i < $treeAmount; ++$i) {
			$this->decorate($world, $random, $chunk_x, $chunk_z, $chunk);
		}
	}

	public function decorate(ChunkManager $world, Random $random, int $chunk_x, int $chunk_z, Chunk $chunk): void
	{
		$x = $random->nextBoundedInt(16);
		$z = $random->nextBoundedInt(16);
		$source_y = $chunk->getHighestBlockAt($x, $z);

		$class = self::getRandomTree($random, $this->trees);
		if ($class !== null) {
			$txn = new BlockTransaction($world);
			try {
				/** @var GenericTree $tree */
				$tree = new $class($random, $txn);
			} catch (Exception $ex) {
				$tree = new GenericTree($random, $txn);
			}
			if ($tree->generate($world, $random, ($chunk_x << Chunk::COORD_BIT_SIZE) + $x, $source_y, ($chunk_z << Chunk::COORD_BIT_SIZE) + $z)) {
				$txn->apply();
			}
		}
	}
}
