<?php

declare(strict_types=1);

namespace AyrzDev\vanillagenerator\generator\end\populator;

use AyrzDev\vanillagenerator\generator\Decorator;
use AyrzDev\vanillagenerator\generator\Populator;
use AyrzDev\vanillagenerator\generator\end\decorator\EndSpikeDecorator;
use pocketmine\utils\Random;
use pocketmine\world\ChunkManager;
use pocketmine\world\format\Chunk;

class EndPopulator implements Populator{

    /** @var Populator[] */
    private array $on_ground_populators = [];

    public function __construct(){
        // spawn decorator should run first so it can lay the large spawn island
        $spawn = new \AyrzDev\vanillagenerator\generator\end\decorator\EndSpawnDecorator();
        $spawn->setAmount(1);
        $this->on_ground_populators[] = $spawn;

        // small number of spikes per chunk, tuned to feel "End-y" but not overwhelming
        $spike = new EndSpikeDecorator();
        $spike->setAmount(3);
        $this->on_ground_populators[] = $spike;
    }

    public function populate(ChunkManager $world, Random $random, int $chunk_x, int $chunk_z, Chunk $chunk) : void{
        foreach($this->on_ground_populators as $populator){
            $populator->populate($world, $random, $chunk_x, $chunk_z, $chunk);
        }
    }
}
