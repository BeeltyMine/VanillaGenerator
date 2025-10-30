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

namespace AyrzDev\vanillagenerator\generator\utils;

use AyrzDev\vanillagenerator\generator\noise\bukkit\OctaveGenerator;

/**
 * @template T of OctaveGenerator
 * @template U of OctaveGenerator
 * @template V of OctaveGenerator
 * @template W of OctaveGenerator
 */
class WorldOctaves{

	/**
	 * @param T $height
	 * @param U $roughness
	 * @param U $roughness_2
	 * @param V $detail
	 * @param W $surface
	 */
	public function __construct(
		public OctaveGenerator $height,
		public OctaveGenerator $roughness,
		public OctaveGenerator $roughness_2,
		public OctaveGenerator $detail,
		public OctaveGenerator $surface
	){}
}