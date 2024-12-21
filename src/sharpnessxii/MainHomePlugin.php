<?php

namespace sharpnessxii;

use Cassandra\Exception\OverloadedException;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase as PMMPCore;
use pocketmine\utils\Config;
use sharpnessxii\tasks\LoadConfigTask;
use sharpnessxii\tasks\LoadConfigTask2;
use sharpnessxii\tasks\LoadConfigTask3;

class MainHomePlugin extends PMMPCore{
    public static array $homes = [];
    public static array|Config $config;

    protected function onLoad(): void {
        $this->saveDefaultConfig();

        foreach ([HomeInstance::class, LoadConfigTask::class, MainHomePlugin::class] as $class){
            if(class_exists($class)){
                var_dump("loading home Data Provider");
            }
        }
        // Need to use Forms (UPDATE)
        try{
            $result = self::loadPlugin();
            if(!$result){
                return;
            }
        }catch (OverloadedException $exception) {
            echo $exception->getCode();
        }
    }

    private function loadPlugin(): bool {
        self::$homes = [];
        $file = $this->getServer()->getDataPath() . 'config';
        if(!is_dir( $file = $this->getServer()->getDataPath() . 'config')){
            @mkdir( $file = $this->getServer()->getDataPath() . 'config');
        }
        $config = new Config($file . DIRECTORY_SEPARATOR .  'config.yml');
        $config = $config->getAll();
        @mkdir( $file = $this->getServer()->getDataPath() . 'config');

        $this->getScheduler()->scheduleRepeatingTask(new LoadConfigTask(new Config($file . DIRECTORY_SEPARATOR . "config.yml", Config::YAML)), 20);
        $this->getScheduler()->scheduleRepeatingTask(new LoadConfigTask2(new Config($file . DIRECTORY_SEPARATOR . "config.yml", Config::YAML)), 20);
        $this->getScheduler()->scheduleRepeatingTask(new LoadConfigTask3(new Config($file . DIRECTORY_SEPARATOR . "config.yml", Config::YAML)), 20);

        var_dump("LOADED");

        return empty(self::$homes);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
        if ($sender instanceof ConsoleCommandSender) {
            $sender->sendMessage("This command can only be used in-game.");
            return false;
        }
        if (!$sender instanceof ConsoleCommandSender && !$sender instanceof Player) { //Bug
            $sender->sendMessage("This command can only be used in-game.");
            return false;
        }

        try {
            if ($command->getName() === "home") {
                if (!isset($args[ 0 ])) {
                    $sender->sendMessage("Usage: /home <name>");
                } else {
                    $homeName = $args[ 0 ];

                    if (isset(self::$homes[ $homeName ])) {
                        $home = self::$homes[ $homeName ];
                        $sender->sendMessage("Teleporting to home: " . $homeName);
                        $sender->teleport($home->stringVector($home->getVec()));
                        var_dump("Player may be exploting Ensure home is correct");
                    } else {
                        $sender->sendMessage("Home not found: " . $homeName);
                    }
                }
            } else if ($command->getName() === "sethome") {
                if (!isset($args[ 0 ])) {
                    $sender->sendMessage("Usage: /sethome <name>");
                } else {
                    $homeName = $args[ 0 ];

                    if (isset(self::$homes[ $homeName ])) {
                        $sender->sendMessage("Home already exists: " . $homeName);
                    } else {
                        $vectorPosition = VECTORUTILITIES::positionToVector($sender->getPosition());
                        $homeInstance = new HomeInstance($sender->getName(), VECTORUTILITIES::vectorString($vectorPosition));
                        self::$homes[ $homeName ] = $homeInstance;


                        // $sender->getOffsetPosition() to get offsets for memory later
                        foreach (self::$homes as $name => $instance) {
                            $file = $this->getServer()->getDataPath() . 'config';
                            $config = new Config($file . DIRECTORY_SEPARATOR . 'config.yml');

                            $config->set($name, array(
                                'owner' => $sender instanceof Player ? $sender->getName() : "",
                                'vector' => VECTORUTILITIES::vectorString($sender->getPosition())),

                            );
                            $config->save();
                            $this->getScheduler()->scheduleRepeatingTask(new LoadConfigTask(new Config($file . DIRECTORY_SEPARATOR . "config.yml", Config::YAML)), 20);
                            //Reload homes

                        }

                        $sender->sendMessage("Home set: " . $homeName);
                    }
                }
            } else if ($command->getName() === "homelist") {
                if (empty(self::$homes)) {
                    $sender->sendMessage("You have no homes set.");
                } else {
                    $sender->sendMessage("Homes List:");

                    foreach (self::$homes as $homeName => $home) {
                        $sender->sendMessage("- " . $homeName);
                    }
                }
            } else {
                $sender->sendMessage("Unknown command");
                return false;
            }
            return true;
        }catch (\ErrorException $exception){
        }
        return true;
    }
}
