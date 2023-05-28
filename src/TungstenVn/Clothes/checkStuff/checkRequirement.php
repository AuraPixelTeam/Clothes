<?php

declare(strict_types=1);

namespace TungstenVn\Clothes\checkStuff;

use TungstenVn\Clothes\Clothes;
use Vecnavium\FormsUI\SimpleForm;
use TungstenVn\Clothes\copyResource\copyResource;

class checkRequirement
{
    public function checkRequirement(): void
    {
        $main = Clothes::getInstance();
        $logger = $main->getServer()->getLogger();
        $pluginManager = $main->getServer()->getPluginManager();
        $dataFolder = $main->getDataFolder();

        if (!extension_loaded("gd")) {
            $logger->info("§6Clothes: Uncomment gd2.dll (remove symbol ';' in ';extension=php_gd2.dll') in bin/php/php.ini to make the plugin work");
            $pluginManager->disablePlugin($main);
            return;
        }

        if (!class_exists(SimpleForm::class)) {
            $logger->info("§6Clothes: FormAPI class is missing. Please use the .phar from poggit!");
            $pluginManager->disablePlugin($main);
            return;
        }

        $requiredFiles = ["steve.png", "steve.json", "config.yml"];
        $missingFiles = array_filter($requiredFiles, function ($file) use ($dataFolder) {
            return !file_exists($dataFolder . $file);
        });

        if (!empty($missingFiles)) {
            $resourcesPath = str_replace("config.yml", "", $main->getResources()["config.yml"]);

            if (file_exists($resourcesPath)) {
                $copyResource = new CopyResource();
                $copyResource->recurse_copy($resourcesPath, $dataFolder);
            } else {
                $logger->info("§6Clothes: Something is wrong with the resources");
                $pluginManager->disablePlugin($main);
            }
        }
    }
}
