<?php

declare(strict_types=1);

namespace TungstenVn\Clothes\copyResource;

class copyResource
{

    public function recurse_copy(string $src, string $dst): void
    {
        $dir = opendir($src);
        @mkdir($dst);

        while (($file = readdir($dir)) !== false) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $srcFilePath = $src . '/' . $file;
            $dstFilePath = $dst . '/' . $file;

            if (is_dir($srcFilePath)) {
                $this->recurse_copy($srcFilePath, $dstFilePath);
            } else {
                copy($srcFilePath, $dstFilePath);
            }
        }

        closedir($dir);
    }
}