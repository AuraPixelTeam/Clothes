<?php
/*
*    /$$   /$$  /$$$$$$  /$$$$$$$$ /$$   /$$
*   | $$  /$$/ /$$__  $$|__  $$__/| $$  | $$
*   | $$ /$$/ | $$  \ $$   | $$   | $$  | $$
*   | $$$$$/  | $$  | $$   | $$   | $$$$$$$$
*   | $$  $$  | $$  | $$   | $$   | $$__  $$
*   | $$\  $$ | $$  | $$   | $$   | $$  | $$
*   | $$ \  $$|  $$$$$$/   | $$   | $$  | $$
*   |__/  \__/ \______/    |__/   |__/  |__/
*
*   Copyright (C) 2019-2020 JaxkDev
*
*   This program is free software: you can redistribute it and/or modify
*   it under the terms of the GNU General Public License as published by
*   the Free Software Foundation, either version 3 of the License, or
*   any later version.
*
*   This program is distributed in the hope that it will be useful,
*   but WITHOUT ANY WARRANTY; without even the implied warranty of
*   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*   GNU General Public License for more details.
*
*   You should have received a copy of the GNU General Public License
*   along with this program.  If not, see <https://www.gnu.org/licenses/>.
*
*   Twitter :: @JaxkDev
*   Discord :: JaxkDev#8860
*   Email   :: JaxkDev@gmail.com
*/

namespace TungstenVn\Clothes\updater;

use TungstenVn\Clothes\Clothes;
use pocketmine\scheduler\AsyncTask;

class GetUpdateInfo extends AsyncTask
{
    protected string $url;

    public function __construct(Clothes $plugin, string $url)
    {
        $this->url = $url;
        $this->storeLocal("key", $plugin); //4.0 compatible.
    }
    public function onRun(): void
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($curl);
        $curlerror = curl_error($curl);
        $responseJson = json_decode($response, true);
        $error = '';
        if($curlerror != ""){
            $error = "Unknown error occurred, code:".curl_getinfo($curl, CURLINFO_HTTP_CODE);
        }
        elseif (curl_getinfo($curl, CURLINFO_HTTP_CODE) != 200) {
            $error = $responseJson['message'];
        }
        $result = ["Response" => $responseJson, "Error" => $error, "httpCode" => curl_getinfo($curl, CURLINFO_HTTP_CODE)];
        $this->setResult($result);
    }
    public function onCompletion(): void
    {
        $plugin = $this->fetchLocal("key");
        Utils::handleUpdateInfo($this->getResult());
    }
}