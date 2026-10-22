<?php
    foreach (["javascript", "styles"] as $type) {
        $file = file_get_contents("{$type}/versions.json");
        $versions = json_decode($file, true);
        $versionHashes = [];
        foreach ($versions as $file => $version) {
            if (file_exists(getcwd().$file)) {
                $versionHashes[$file] = md5_file(getcwd().$file);
            }
        }
        file_put_contents("{$type}/versions.json", json_encode($versionHashes, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . "\n");
    }
