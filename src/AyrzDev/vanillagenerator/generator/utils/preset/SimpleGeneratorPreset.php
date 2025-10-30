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

namespace AyrzDev\vanillagenerator\generator\utils\preset;

use InvalidArgumentException;

final class SimpleGeneratorPreset implements GeneratorPreset{

	public static function empty() : self{
		return new self([]);
	}

	/**
	 * Parses a configured preset string.
	 * Example of valid strings:
	 *   * worldtype=normal
	 *   * worldtype=amplified
	 *   * worldtype=largebiomes,environment=overworld
	 *
	 * @param string $preset
	 * @return self
	 */
	public static function parse(string $preset) : self{
		if($preset === ""){
			return self::empty();
		}

		$data = [];
		foreach(explode(",", $preset) as $entry){
			if(!str_contains($entry, "=")){
				throw new InvalidArgumentException("Preset is invalid: {$entry} must contain an '=' symbol");
			}

			[$key, $value] = explode("=", $entry);
			if(is_numeric($value)){
				$value = (float) $value;
				if($value - floor($value) < PHP_FLOAT_EPSILON){
					$value = (int) $value;
				}
			}

			$data[$key] = $value;
		}

		return new self($data);
	}

	/**
	 * @param array<string, mixed> $data
	 */
	public function __construct(
		private array $data
	){}

	public function exists(string $property) : bool{
		return array_key_exists($property, $this->data);
	}

	public function get(string $property) : mixed{
		return $this->data[$property] ?? null;
	}

	public function getInt(string $property) : int{
		$value = $this->get($property);
		if(!is_int($value)){
			throw new InvalidArgumentException("{$property} is not an integer");
		}
		return $value;
	}

	public function getFloat(string $property) : float{
		$value = $this->get($property);
		if(!is_float($value)){
			throw new InvalidArgumentException("{$property} is not a float");
		}
		return $value;
	}

	public function getString(string $property) : string{
		$value = $this->get($property);
		if(!is_string($value)){
			throw new InvalidArgumentException("{$property} is not a string");
		}
		return $value;
	}

	public function toString() : string{
		$string = "";
		foreach($this->data as $property => $value){
			$string .= "{$property}={$value},";
		}
		return rtrim($string, ",");
	}
}