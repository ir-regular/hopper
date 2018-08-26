<?php
declare(strict_types=1);

namespace IrRegular\Examples\Hopper\Json;

/*
 * Convenience functions for file download, reading file contents etc.
 *
 * Extracted to a separate file, in order to keep the main example clean.
 * Normally I'd use a convenient library - like Guzzle for curl.
 * Since this is an example, I chose to keep it dependency free.
 */

/**
 * @param string $url
 * @param string $cacheFile
 * @return bool
 */
function download_and_cache($url, $cacheFile): bool
{
    $success = true;

    if (!is_file($cacheFile)) {
        $fp = fopen($cacheFile, "w");
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, false);

        $success = curl_exec($ch);
        curl_close($ch);
        fclose($fp);

        if (!$success && is_file($cacheFile)) {
            unlink($cacheFile);
        }
    }

    return $success;
}

/**
 * Read a file and split into an array by line, filtering out empty lines.
 *
 * @param string $file
 * @return string[]
 */
function read_lines($file): array
{
    if (!is_file($file)) {
        throw new \InvalidArgumentException("File $file not found");
    }

    $contents = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    if ($contents === false) {
        throw new \RuntimeException("Could not read from $file");
    }

    return $contents;
}
