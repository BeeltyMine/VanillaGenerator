<?php

declare(strict_types=1);

namespace AyrzDev\vanillagenerator\command;

use AyrzDev\vanillagenerator\Loader;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginOwned;
use pocketmine\utils\TextFormat;
use pocketmine\world\WorldCreationOptions;
use pocketmine\world\generator\GeneratorManager;

class VanillaGeneratorCommand extends Command implements PluginOwned
{
    /** @var Loader */
    private Loader $owningPlugin;

    public function __construct(Loader $plugin)
    {
        parent::__construct("vg");
        $this->owningPlugin = $plugin;
        $this->setDescription("VanillaGenerator helper commands");
        $this->setUsage("/vg create <name> <type:overworld|nether|end> [seed] [preset]");
        $this->setPermission("vanillagenerator.command.vg");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool
    {

        if (!isset($args[0])) {
            $this->sendHelp($sender);
            return true;
        }

        switch (strtolower($args[0])) {
            case "create":
                return $this->handleCreate($sender, array_slice($args, 1));
            case "info":
                return $this->handleInfo($sender);
            case "tp":
                return $this->handleTp($sender, array_slice($args, 1));
            default:
                $sender->sendMessage(TextFormat::RED . "Unknown subcommand. Usage: " . $this->getUsage());
                return false;
        }
    }

    /**
     * Teleport subcommand: /vg tp <world>
     * @param string[] $args
     */
    private function handleTp(CommandSender $sender, array $args): bool
    {
        if (!($sender instanceof \pocketmine\player\Player)) {
            $sender->sendMessage(TextFormat::RED . "Only in-game players can be teleported with /vg tp.");
            return true;
        }

        if (count($args) < 1) {
            $sender->sendMessage(TextFormat::YELLOW . "Usage: /vg tp <world>");
            return false;
        }

        $worldName = $args[0];
        $server = $this->owningPlugin->getServer();
        $worldManager = $server->getWorldManager();

        if (!$worldManager->isWorldGenerated($worldName)) {
            $sender->sendMessage(TextFormat::RED . "World '$worldName' does not exist. Use /vg create to generate it.");
            return true;
        }

        $world = $worldManager->getWorldByName($worldName);
        if ($world === null) {
            // try to load the world
            if (!$worldManager->loadWorld($worldName, true)) {
                $sender->sendMessage(TextFormat::RED . "Failed to load world '$worldName'. Try again or generate it with /vg create.");
                return true;
            }
            $world = $worldManager->getWorldByName($worldName);
            if ($world === null) {
                $sender->sendMessage(TextFormat::RED . "World '$worldName' could not be loaded.");
                return true;
            }
        }

        // Request a safe spawn and teleport when ready
            $playerName = $sender instanceof \pocketmine\player\Player ? $sender->getName() : null;
            $world->requestSafeSpawn()->onCompletion(
                function (\pocketmine\world\Position $pos) use ($playerName, $worldName): void {
                    if ($playerName === null) return;
                    $player = $this->owningPlugin->getServer()->getPlayerExact($playerName);
                    if ($player instanceof \pocketmine\player\Player && $player->isConnected()) {
                        $player->teleport($pos);
                        $player->sendMessage(TextFormat::GREEN . "Teleported to world '{$worldName}'.");
                    }

                    // Process any queued EndCrystal spawns now that spawn is ready
                    try{
                        $queued = $this->owningPlugin->drainCrystalQueue();
                        foreach($queued as [$w, $x, $y, $z, $show]){
                            $wobj = $this->owningPlugin->getServer()->getWorldManager()->getWorldByName($w);
                            if($wobj instanceof \pocketmine\world\World){
                                $loc = \pocketmine\entity\Location::fromObject(new \pocketmine\math\Vector3($x + 0.5, $y, $z + 0.5), $wobj, 0.0, 0.0);
                                $ent = new \pocketmine\entity\object\EndCrystal($loc);
                                $ent->setShowBase((bool)$show);
                                $wobj->addEntity($ent);
                            }
                        }
                    }catch(\Throwable $_){
                        // best-effort
                    }
                },
                        function () use ($playerName, $worldName): void {
                if ($playerName === null) return;
                $player = $this->owningPlugin->getServer()->getPlayerExact($playerName);
                if ($player instanceof \pocketmine\player\Player && $player->isConnected()) {
                    $player->sendMessage(TextFormat::YELLOW . "Failed to teleport to world '{$worldName}' (world unloaded or generation interrupted). Please try /tp or rejoin.");
                }
            }
            );

        $sender->sendMessage(TextFormat::GOLD . "Teleport to '{$worldName}' requested — teleport will occur when spawn is ready.");
        return true;
    }

    private function sendHelp(CommandSender $sender): void
    {
        $sender->sendMessage(TextFormat::GOLD . "VanillaGenerator commands:");
        $sender->sendMessage(TextFormat::YELLOW . "/vg create <name> <type:overworld|nether|end> [seed] [preset] " . TextFormat::WHITE . "- Create a new world");
        $sender->sendMessage(TextFormat::YELLOW . "/vg info" . TextFormat::WHITE . " - Show plugin/generator info");
    }

    private function handleInfo(CommandSender $sender): bool
    {
        $sender->sendMessage(TextFormat::GOLD . "VanillaGenerator info:");
        $sender->sendMessage(TextFormat::WHITE . "Version: 0.0.4");
        $gm = GeneratorManager::getInstance();
        $gens = $gm->getGeneratorList();
        $sender->sendMessage(TextFormat::GOLD . "Available generator aliases: " . TextFormat::WHITE . implode(", ", $gens));
        return true;
    }

    /**
     * @param string[] $args
     */
    private function handleCreate(CommandSender $sender, array $args): bool
    {
        if (count($args) < 2) {
            $sender->sendMessage(TextFormat::YELLOW . "Usage: /vg create <name> <type> [seed] [preset]");
            return false;
        }

        $name = $args[0];
        $type = strtolower($args[1]);
        $seed = null;
        $preset = "";
        if (isset($args[2])) {
            if (is_numeric($args[2])) {
                $seed = (int)$args[2];
            } else {
                $preset = $args[2];
            }
        }
        if (isset($args[3])) {
            $preset = $args[3];
        }

        $server = $this->owningPlugin->getServer();
        $worldManager = $server->getWorldManager();

        // Debug/log info to help troubleshoot when commands are run from console
        $this->owningPlugin->getLogger()->info("VanillaGenerator: create request - name={$name}, type={$type}, seed=" . ($seed === null ? 'null' : $seed) . ", preset={$preset}");

        if ($worldManager->isWorldGenerated($name)) {
            $sender->sendMessage(TextFormat::RED . "World '$name' already exists.");
            return true;
        }

        $options = WorldCreationOptions::create();
        if ($seed !== null) {
            $options->setSeed($seed);
        }

        // Map type to generator class name registered in GeneratorManager
        switch ($type) {
            case "overworld":
                $gen = \AyrzDev\vanillagenerator\generator\overworld\OverworldGenerator::class;
                break;
            case "nether":
                $gen = \AyrzDev\vanillagenerator\generator\nether\NetherGenerator::class;
                break;
            case "end":
                $gen = \AyrzDev\vanillagenerator\generator\end\EndGeneratorV2::class;
                break;
            default:
                $sender->sendMessage(TextFormat::RED . "Unknown generator type: $type");
                return false;
        }

        // set generator class and options
        $options->setGeneratorClass($gen);
        $options->setGeneratorOptions($preset ?? "");

        $success = $worldManager->generateWorld($name, $options, true);
        if ($success) {
            $msg = "World '{$name}' generation started (background).";
            $sender->sendMessage(TextFormat::GREEN . $msg . " Look at server logs for progress.");
            $this->owningPlugin->getLogger()->info("VanillaGenerator: {$msg} generator={$gen}");
            // Debug: log resolved generator alias/class to help diagnose "no biome/atmosphere change" reports
            try{
                $gm = \pocketmine\world\generator\GeneratorManager::getInstance();
                $alias = null;
                try{
                    $alias = $gm->getGeneratorName($gen);
                }catch(\Throwable $_){
                    // not registered or unknown; leave alias null
                }
                $this->owningPlugin->getLogger()->info("VanillaGenerator-debug: requested generator class={$gen} alias=" . ($alias ?? 'null'));
            }catch(\Throwable $_){
                // best-effort logging
            }
            // Attempt to teleport the command sender to the world's safe spawn once it's ready
            $world = $worldManager->getWorldByName($name);
            if ($sender instanceof \pocketmine\player\Player) {
                if ($world instanceof \pocketmine\world\World) {
                    // requestSafeSpawn returns a Promise-like object with onCompletion callbacks
                        $playerName = $sender->getName();
                        $world->requestSafeSpawn()->onCompletion(
                            function (\pocketmine\world\Position $pos) use ($playerName, $name): void {
                            if ($playerName === null) return;
                            $player = $this->owningPlugin->getServer()->getPlayerExact($playerName);
                            if ($player instanceof \pocketmine\player\Player && $player->isConnected()) {
                                // Teleport the player when the spawn is ready
                                $player->teleport($pos);
                                $player->sendMessage(TextFormat::GREEN . "Teleported to world '{$name}'.");
                            }

                            // Process queued EndCrystal spawns now that generation is complete for this world
                            try{
                                $queued = $this->owningPlugin->drainCrystalQueue();
                                foreach($queued as [$w, $x, $y, $z, $show]){
                                    $wobj = $this->owningPlugin->getServer()->getWorldManager()->getWorldByName($w);
                                    if($wobj instanceof \pocketmine\world\World){
                                        $loc = \pocketmine\entity\Location::fromObject(new \pocketmine\math\Vector3($x + 0.5, $y, $z + 0.5), $wobj, 0.0, 0.0);
                                        $ent = new \pocketmine\entity\object\EndCrystal($loc);
                                        $ent->setShowBase((bool)$show);
                                        $wobj->addEntity($ent);
                                    }
                                }
                            }catch(\Throwable $_){
                                // best-effort
                            }
                            // Additional debug: report which generator the world provider recorded
                            try{
                                $wobj = $this->owningPlugin->getServer()->getWorldManager()->getWorldByName($name);
                                if($wobj instanceof \pocketmine\world\World){
                                    $wProviderGen = $wobj->getProvider()->getWorldData()->getGenerator();
                                    $this->owningPlugin->getLogger()->info("VanillaGenerator-debug: world provider recorded generator={$wProviderGen}");
                                }
                            }catch(\Throwable $_){
                                // ignore
                            }
                        },
                            function () use ($playerName, $name): void {
                                if ($playerName === null) return;
                                $player = $this->owningPlugin->getServer()->getPlayerExact($playerName);
                                if ($player instanceof \pocketmine\player\Player && $player->isConnected()) {
                                    $player->sendMessage(TextFormat::YELLOW . "World '{$name}' was generated but teleport failed (world unloaded or generation interrupted). Please try /tp or rejoin.");
                                }
                            }
                        );
                } else {
                    // World not loaded for some reason; inform the player
                    $sender->sendMessage(TextFormat::YELLOW . "World '{$name}' generation started but world is not yet loaded. You can teleport once it's ready.");
                }
            } else {
                // Console sender: advise how to teleport manually
                $sender->sendMessage(TextFormat::GOLD . "World '{$name}' generation started. Use an in-game player or server commands to teleport to the world when ready.");
            }
        } else {
            $msg = "Failed to start world generation for '{$name}'.";
            $sender->sendMessage(TextFormat::RED . $msg);
            $this->owningPlugin->getLogger()->warning("VanillaGenerator: {$msg} generator={$gen}");
        }

        return $success;
    }

    public function getOwningPlugin(): Loader
    {
        return $this->owningPlugin;
    }
}
