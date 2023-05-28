<?php

declare(strict_types=1);

namespace TungstenVn\Clothes\checkStuff;

use TungstenVn\Clothes\Clothes;

class checkClothes
{
    public function checkClothes(): void
    {
        $main = Clothes::getInstance();
        $checkFileAvailable = [];
        $clothesDir = $main->getDataFolder() . "clothes/";

        if (!file_exists($clothesDir)) {
            mkdir($clothesDir);
        }

        $allDirs = scandir($clothesDir);

        foreach ($allDirs as $folderName) {
            $folderPath = $clothesDir . $folderName;

            if (is_dir($folderPath)) {
                $main->clothesTypes[] = $folderName;
                $allFiles = scandir($folderPath);

                foreach ($allFiles as $fileName) {
                    if (strpos($fileName, ".json")) {
                        $checkFileAvailable[] = str_replace('.json', '', $fileName);
                    }
                }

                foreach ($checkFileAvailable as $value) {
                    if (!in_array($value . ".png", $allFiles)) {
                        unset($checkFileAvailable[array_search($value, $checkFileAvailable)]);
                    }
                }

                $main->clothesDetails[$folderName] = $checkFileAvailable;
                sort($main->clothesDetails[$folderName]);
                $checkFileAvailable = [];
            }
        }

        unset($main->clothesTypes[0]);
        unset($main->clothesTypes[1]);
        unset($main->clothesTypes[array_search("saveskin", $main->clothesTypes)]);
        unset($main->clothesDetails["."]);
        unset($main->clothesDetails[".."]);
        unset($main->clothesDetails["saveskin"]);
        sort($main->clothesTypes);
    }

    public function checkCos(): void
    {
        $main = Clothes::getInstance();
        $checkFileAvailable = [];
        $cosplaysDir = $main->getDataFolder() . "cosplays/";

        if (!file_exists($cosplaysDir)) {
            mkdir($cosplaysDir);
        }

        $allDirs = scandir($cosplaysDir);

        foreach ($allDirs as $folderName) {
            $folderPath = $cosplaysDir . $folderName;

            if (is_dir($folderPath)) {
                $main->cosplaysTypes[] = $folderName;
                $allFiles = scandir($folderPath);

                foreach ($allFiles as $fileName) {
                    if (strpos($fileName, ".json")) {
                        $checkFileAvailable[] = str_replace('.json', '', $fileName);
                    }
                }

                foreach ($checkFileAvailable as $value) {
                    if (!in_array($value . ".png", $allFiles)) {
                        unset($checkFileAvailable[array_search($value, $checkFileAvailable)]);
                    }
                }

                $main->cosplaysDetails[$folderName] = $checkFileAvailable;
                sort($main->cosplaysDetails[$folderName]);
                $checkFileAvailable = [];
            }
        }

        unset($main->cosplaysTypes[0]);
        unset($main->cosplaysTypes[1]);
        unset($main->cosplaysTypes[array_search("saveskin", $main->cosplaysTypes)]);
        unset($main->cosplaysDetails["."]);
        unset($main->cosplaysDetails[".."]);
        unset($main->cosplaysDetails["saveskin"]);
        sort($main->cosplaysTypes);
    }
}