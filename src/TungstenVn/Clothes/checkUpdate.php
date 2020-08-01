<?php

namespace TungstenVn\Clothes;

use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;

class checkUpdate extends AsyncTask
{

    /**
     *
     */
    public function onRun(): void
    {

        $link = 'https://raw.githubusercontent.com/TungstenVn/TungstenVn_poggit_news/master/update.yml';
        $file = @fopen($link, "rb");
        if ($file == false) {
            return;
        }
        $content = "";
        while (!feof($file)) {
            $line_of_text = fgets($file);
            $content = $content . " " . $line_of_text;
        }
        fclose($file);

        $content = yaml_parse($content);
        $this->setResult($content);
    }

    /**
     * @param Server $server
     */
    public function onCompletion(Server $server): void
    {
        if (is_null($clo = Clothes::$instance)) {
            return;
        }

        $content = $this->getResult();
        if (!isset($content)) {
            $clo->getServer()->getLogger()->info("§6[§bClothes§6]§f Cant get updated information (timed out?)");
            return;
        }

        if(!isset($content["clothes_version"])) return;
        $version = $content["clothes_version"];
        if (version_compare($clo->getDescription()->getVersion(), $version) < 0) {
            $clo->getServer()->getLogger()->info("§6[§bClothes§6]§f New version §b$version §fhas been released, download at: https://poggit.pmmp.io/p/Clothes/");
        }
    }
}
