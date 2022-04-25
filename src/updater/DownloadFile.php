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

use Error;
use pocketmine\scheduler\AsyncTask;
use TungstenVn\Clothes\Clothes;

class DownloadFile extends AsyncTask
{
    protected string $url;
    protected string $path;

    public function __construct(Clothes $plugin, string $url, string $path)
    {
        $this->url = $url;
        $this->path = $path;
        $this->storeLocal("idk", $plugin); //4.0 compatible.
    }
    public function onRun(): void
    {
        $file = fopen($this->path, 'w+');
        if($file === false){
            throw new Error('Could not open: ' . $this->path);
        }
        $ch = curl_init($this->url);
        curl_setopt($ch, CURLOPT_FILE, $file);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60); //give it 1 minute.
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_exec($ch);
        if(curl_errno($ch)){
            throw new Error(curl_error($ch));
        }
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        fclose($file);
        $this->setResult($statusCode);
    }
    public function onCompletion(): void
    {
        $plugin = $this->fetchLocal("idk");
        Utils::handleDownload($this->path, $this->getResult());
    }
}
