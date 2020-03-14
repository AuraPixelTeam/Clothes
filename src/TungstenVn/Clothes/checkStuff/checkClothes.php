<?php

namespace TungstenVn\Clothes\checkStuff;

use TungstenVn\Clothes\Clothes;

class checkClothes {
    public function checkClothes(){
        $main = Clothes::$instance;
        //allDirs1 is the folder for button in the main form
        //allDirs2 is the folder of clothes to chose in the deeper form
        $checkFileAvailable = [];
        $path = $main->getDataFolder();
        $allDirs = scandir($path);
        foreach ($allDirs as $foldersName) {
            if(is_dir($path.$foldersName)){
                array_push($main->clothesTypes,$foldersName);
                $allFiles = scandir($path.$foldersName);
                foreach ($allFiles as $allFilesName) {
                    if(strpos($allFilesName, ".json")) {
                      array_push($checkFileAvailable, str_replace('.json', '', $allFilesName));
                    }
                }
                foreach ($checkFileAvailable as $value) {
                    if(!in_array($value.".png", $allFiles)) {
                       unset($checkFileAvailable[array_search($value, $checkFileAvailable)]);
                    }
                }
                $main->clothesDetails[$foldersName] = $checkFileAvailable;
                sort($main->clothesDetails[$foldersName]);
                $checkFileAvailable = [];
            }
        }
        unset($main->clothesTypes[0]);
        unset($main->clothesTypes[1]);
        unset($main->clothesTypes[array_search("saveskin",$main->clothesTypes)]);
        unset($main->clothesDetails["."]);
        unset($main->clothesDetails[".."]);
        unset($main->clothesDetails["saveskin"]);
        sort($main->clothesTypes);
    }
}