<?php

declare(strict_types=1);

namespace TungstenVn\Clothes\updater;

use pocketmine\scheduler\AsyncTask;
use pocketmine\utils\Internet;
use TungstenVn\Clothes\Clothes;
use function json_decode;
use function version_compare;
use function vsprintf;

class CheckUpdateTask extends AsyncTask{

    private const POGGIT_RELEASES_URL = "https://poggit.pmmp.io/releases.min.json?name=";

    /**
     * @param string $pluginName
     * @param string $pluginVersion
     */
    public function __construct(
        private string $pluginName,
        private string $pluginVersion
    ){}

    /**
     * @return void
     */
    public function onRun() : void{
        $json = Internet::getURL(self::POGGIT_RELEASES_URL . $this->pluginName, 10, [], $err);
        $highestVersion = $this->pluginVersion;
        $artifactUrl = "";
        $api = "";
        if($json !== null){
            $releases = json_decode($json->getBody(), true);
            foreach($releases as $release){
                if(version_compare($highestVersion, $release["version"], ">=")){
                    continue;
                }
                $highestVersion = $release["version"];
                $artifactUrl = $release["artifact_url"];
                $api = $release["api"][0]["from"] . " - " . $release["api"][0]["to"];
            }
        }

        $this->setResult([$highestVersion, $artifactUrl, $api, $err]);
    }

    /**
     * @return void
     */
    public function onCompletion() : void{
        $plugin = Clothes::getInstance();
        if(!$plugin instanceof Clothes){
            return;
        }

        [$highestVersion, $artifactUrl, $api, $err] = $this->getResult();
        if($err !== null){
            $plugin->getLogger()->error("Update notify error: $err");

            return;
        }

        if($highestVersion !== $this->pluginVersion){
            $artifactUrl = $artifactUrl . "/" . $this->pluginName . "_" . $highestVersion . ".phar";
            $plugin->getLogger()->notice(vsprintf("Version %s has been released for API %s. Download the new release at %s", [$highestVersion, $api, $artifactUrl]));
        }
    }
}