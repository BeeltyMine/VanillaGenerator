# VanillaGenerator

VanillaGenerator is a simple "vanilla world" generator and helper plugin for BeeltyMine / PocketMine-based servers. It is designed for server administrators who want to quickly create new vanilla Minecraft-style worlds, configure basic settings, and automate generation tasks.

## Features
- Create new vanilla worlds (with a provided seed or randomly generated)
- Quick setup — drop the plugin into the `plugins/` folder and restart the server
- Basic configuration to change default behavior
- Informational logging during generation and setup

> Note: This README covers general usage. For advanced options or additional commands, check the plugin's `plugin.yml` or source code.

## Requirements
- BeeltyMine (targeting the `stable` branch)
- PHP 8.x

## Installation
1. Copy the `VanillaGenerator` folder to your server's `plugins/` directory (or install its PHAR/ZIP build if available).
2. Restart the server (or start it if it's currently down).
3. The plugin will load on startup and log its status to the server console.

## Quick Usage
This plugin can be configured to generate worlds automatically via your server's `pocketmine.yml` — you do not need to use plugin commands if you prefer configuration-based setup.

Add entries under the `worlds:` section of `pocketmine.yml` to select generator types for specific world folders. Example `pocketmine.yml` snippet:

```yaml
worlds:
  world:
    generator: vanilla_overworld # sets generator type of the world with folder name "world" to "vanilla_overworld"
  nether:
    generator: vanilla_nether
```

With the above, when the server loads or when the world is created by the server, the specified generator will be used for those world folders. If you want command-based generation instead, check `plugin.yml` for available commands; otherwise, configure `pocketmine.yml` as shown.

## Configuration
If the plugin provides a configuration file it will typically be located at `plugins/VanillaGenerator/config.yml`. Edit the file and restart the server to apply changes.

Example (hypothetical) `config.yml`:

```
default_world_type: "vanilla"
generate_structures: true
default_seed: null # null => random
```

## Logs & Debugging
- Generation and setup messages are written to the server console and (if present) to `plugins/VanillaGenerator/logs/`.
- If world generation fails:
  1. Check the server console for error messages.
 2. Inspect `plugins/VanillaGenerator` logs.
 3. Ensure there is enough disk space and proper filesystem permissions.

## Safety & Best Practices
- Always back up worlds before running destructive commands such as `/vdelete`.
- This plugin runs server-side only and does not modify clients.

## Contributing
- Issues and pull requests welcome. Please open small, testable changes and include a description and reproduction steps for bugs.

## License
- The plugin follows the project's license — see the repository `LICENSE` file in the repo root for details.

---
If you want me to include the exact command names and config keys, I can read `plugins/VanillaGenerator/plugin.yml` and the plugin source and update this English README to match the actual implementation.
