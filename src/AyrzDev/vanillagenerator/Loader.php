<?php

declare(strict_types=1);

namespace AyrzDev\vanillagenerator;

use AyrzDev\vanillagenerator\generator\nether\NetherGenerator;
use AyrzDev\vanillagenerator\generator\overworld\OverworldGenerator;
use AyrzDev\vanillagenerator\generator\end\EndGeneratorV2;
use AyrzDev\vanillagenerator\command\VanillaGeneratorCommand;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\world\generator\GeneratorManager;
use AyrzDev\vanillagenerator\task\SpawnCrystalTask;

final class Loader extends PluginBase
{

	/** @var array<int, array{string,int,int,int,bool}> */
	private array $crystalQueue = [];

	/**
	 * Add a queued EndCrystal spawn position. Stored as [worldName, x, y, z, showBase]
	 */
	public function queueCrystal(string $worldName, int $x, int $y, int $z, bool $showBase = false) : void{
		$this->crystalQueue[] = [$worldName, $x, $y, $z, $showBase];
	}

	/**
	 * Consume and return the queued crystal positions (drains the queue)
	 * @return array<int, array{string,int,int,int,bool}>
	 */
	public function drainCrystalQueue() : array{
		$items = $this->crystalQueue;
		$this->crystalQueue = [];
		return $items;
	}


	public function onLoad(): void
	{
		$generator_manager = GeneratorManager::getInstance();
		Server::getInstance()->getCommandMap()->register("vg", new VanillaGeneratorCommand($this));
		$generator_manager->addGenerator(NetherGenerator::class, "vanilla_nether", fn() => null);
		$generator_manager->addGenerator(OverworldGenerator::class, "vanilla_overworld", fn() => null);
	$generator_manager->addGenerator(EndGeneratorV2::class, "vanilla_end", fn() => null);
	}

	public function onEnable() : void{
		// schedule a repeating task to process queued EndCrystal spawns on the main thread
		$this->getScheduler()->scheduleRepeatingTask(new SpawnCrystalTask($this), 20);
	}
}
