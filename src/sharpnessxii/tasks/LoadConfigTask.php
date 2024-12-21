<?php

namespace sharpnessxii\tasks;

use pocketmine\scheduler\CancelTaskException;
use pocketmine\scheduler\Task;
use pocketmine\scheduler\TaskHandler;
use pocketmine\utils\Config;
use sharpnessxii\HomeInstance;
use sharpnessxii\MainHomePlugin;

class LoadConfigTask extends Task {
    protected ?Config $config = null;
    protected ?TaskHandler $task = null;
    public static array $homeParserToStringMap = [];
    public function __construct(Config $config) {
        $this->config = $config;
        $this->task = $this->getHandler();
    }

    public function onRun(): void {
        MainHomePlugin::$config = $this->config;

         array_map(function (): void{
            foreach (MainHomePlugin::$config->getAll() as $key => $data){
                var_dump("loading: $key");
            }
        }, MainHomePlugin::$config->getAll());

         foreach (MainHomePlugin::$config->getAll() as $hname => $hdata){
             if(isset($hname)){
                 if(is_array($hdata)){
                     if(isset($hdata["vector"])){
                         if(isset($hdata['owner'])){
                             if(isset($hdata["owner"])){
                                 self::$homeParserToStringMap[] = new HomeInstance(
                                     $hdata['owner'],
                                     $hdata['vector']
                                 );
                                 // Save CPU TIME
                                 usleep(1000);
                             }
                         }
                     }
                 }

             }
         }
        if (count(self::$homeParserToStringMap) > 0) {
            MainHomePlugin::$homes = self::$homeParserToStringMap;
            $this->getHandler()?->getTask()?->onCancel();
            throw new CancelTaskException();
        }
    }
}