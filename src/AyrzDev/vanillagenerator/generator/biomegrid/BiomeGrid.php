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

namespace AyrzDev\vanillagenerator\generator\biomegrid;

interface BiomeGrid
{

	/**
	 * Get biome at x, z within chunk being generated
	 *
	 * @param int $x - 0-15
	 * @param int $z - 0-15
	 * @return int|null
	 */
	public function getBiome(int $x, int $z): ?int;

	/**
	 * Set biome at x, z within chunk being generated
	 *
	 * @param int $x - 0-15
	 * @param int $z - 0-15
	 * @param int $biome_id
	 */
	public function setBiome(int $x, int $z, int $biome_id): void;

	/**
	 * Set biome at x, z within chunk being generated
	 * Get biome at x, y, z within chunk being generated (3D biome support for 1.18+)
	 *
	 * @param int $x - 0-15
	 * @param int $y - world min to world max
	 * @param int $z - 0-15
	 * @return int|null
	 */
	public function getBiome3D(int $x, int $y, int $z) : ?int;

	
	/**
	 * Set biome at x, y, z within chunk being generated (3D biome support for 1.18+)
	 *
	 * @param int $x - 0-15
	 * @param int $y - world min to world max
	 * @param int $z - 0-15
	 * @param int $biome_id
	 */
	public function setBiome3D(int $x, int $y, int $z, int $biome_id) : void;
}
