<?php

namespace HatslogicPageOptimisation\Helper;

use Generator;
use Monolog\Logger;

class DirAndFileHelper
{

    protected Logger $logger;

    public function __construct(
        Logger $logger
    )
    {
        $this->logger = $logger;
    }

    private static string $mediaPath = "public/media/*/*/*/*/*.*";
    private static string $thumbnailPath = "public/thumbnail/*/*/*/*/*.*";
    private static int $effortQuality = 80; // Range from 0->lowest to 100->highest
    private static array $allowedMime = array(
        "image/gif",     // [] gif
        "image/jpeg",    // [] jpg / jpeg / jpe
        "image/png",     // [] png
        "image/bmp",     // [] bmp
        "image/x-ms-bmp" // [] bmp (Windows)

    );


    /**
     * @return array
     * Get containing all images stored in $mediaPath & $thumbnailPath. Uses Glob.
     */
    private function getFilePathsArray(): array
    {

        // retrieve shopware root path to build abolute path for gatheting images
        $sw6root = rtrim(explode("custom/plugins", __DIR__)[0], "/");
        $this->logger->debug("sw6root" . $sw6root);
        $absoluteMediaPath = $sw6root . DIRECTORY_SEPARATOR . self::$mediaPath;
        $this->logger->debug("absoluteMediaPath" . $absoluteMediaPath);
        $absoluteThumbnailPath = $sw6root . DIRECTORY_SEPARATOR . self::$thumbnailPath;
        $this->logger->debug("absoluteThumbnailPath" . $absoluteThumbnailPath);

        return array_merge(glob($absoluteMediaPath), glob($absoluteThumbnailPath));
    }


    /**
     * @param $isCommand
     * @return Generator
     * Decompose the path to get path, filename, mime type and string extension
     * yields it in a Generator only if matches the statically declared "allowedMime"
     */
    public function decomposePath($isCommand): Generator
    {

        $pendingList = [];
        foreach ($this->getFilePathsArray() as $path) {
            $temp = [];

            // Get path, name and extension (mime_type)
            $temp["path"] = explode(DIRECTORY_SEPARATOR, $path);
            $temp["fname"] = (string)array_pop($temp["path"]);
            $temp["mime_type"] = mime_content_type($path);


            // get the extension in string format for building the fname in webp
            $explodedFname = explode(".", $temp["fname"]);
            $temp["ext"] = (string)end($explodedFname);

            // join path together once removed the fname
            $temp["path"] = (string)implode(DIRECTORY_SEPARATOR, $temp["path"]);

            // Store the element before to check if already converted
            if (count($pendingList)) {
                $lastItemInserted = end($pendingList);
            } else {
                $lastItemInserted["fname"] = "";
                $lastItemInserted["path"] = "";
            }

            // Convert only image that has not been converted before and skip webp images
            $isWebp = $temp["mime_type"] === "image/webp";
            $currentName = $this->removeExt($temp["fname"]);
            $lastInsertedName = $this->removeExt($lastItemInserted["fname"]);
            $alreadyConverted = $currentName === $lastInsertedName && $temp["path"] === $lastItemInserted["path"];


            if ($alreadyConverted) {
                // remove the previous item if it finds a webp image
                $alreadyProcessed = array_pop($pendingList);
                $msg = "└ [Skip] " . $alreadyProcessed["fname"] . " has already been converted.\n";
                if ($isCommand) {
                    echo $msg . "\n";
                } else {
                    $this->logger->debug($msg);
                }

                // if the image is already in webp format skips
            } elseif ($isWebp) {
                $msg = "└ [Skip] Exist already...\n";
                if ($isCommand) {
                    echo $msg . "\n";
                } else {
                    $this->logger->debug($msg);
                }

                // Normal case, add image to pending list so can be used later
            } else {
                $msg = "Found Image: " . $temp["path"] . DIRECTORY_SEPARATOR . $temp["fname"] . "\n";
                if ($isCommand) {
                    echo $msg;
                } else {
                    $this->logger->debug($msg);
                }

                if (in_array($temp["mime_type"], self::$allowedMime)) {
                    $pendingList[] = $temp;
                }
            }
        }
        yield $pendingList;
    }

    public function processSingleImagePath(string $path): array
    {
        $imageData = [];
    
        // Extract path, name, and extension
        $imageData["path"] = explode(DIRECTORY_SEPARATOR, $path);
        $imageData["fname"] = (string)array_pop($imageData["path"]);
        $imageData["mime_type"] = mime_content_type($path);
    
        // Get the file extension
        $explodedFname = explode(".", $imageData["fname"]);
        $imageData["ext"] = (string)end($explodedFname);
    
        // Join the remaining path back together
        $imageData["path"] = (string)implode(DIRECTORY_SEPARATOR, $imageData["path"]);
    
        // Check if the image is already a WebP
        $isWebp = $imageData["mime_type"] === "image/webp";
    
        if ($isWebp) {
            $msg = "└ [Skip] The image is already in WebP format: " . $imageData["path"] . DIRECTORY_SEPARATOR . $imageData["fname"] . "\n";
            $this->logger->debug($msg);
            return ["status" => "skipped", "message" => $msg, "data" => $imageData];
        }
    
        // Check if the image's MIME type is allowed for conversion
        if (!in_array($imageData["mime_type"], self::$allowedMime)) {
            $msg = "└ [Error] Unsupported MIME type: " . $imageData["mime_type"] . " for file " . $imageData["fname"] . "\n";
            $this->logger->debug($msg);
            return ["status" => "error", "message" => $msg, "data" => $imageData];
        }
    
        // Log the image found for processing
        $msg = "Found Image: " . $imageData["path"] . DIRECTORY_SEPARATOR . $imageData["fname"] . "\n";
        $this->logger->debug($msg);
    
        return ["status" => "pending", "message" => $msg, "data" => $imageData];
    }
    


    /**
     * @param array $item
     * @return string|null
     * Convert image to WEBP if mime type in $allowedMime and GIF don't have 2 or more frames .
     */
    public function convertImage(array $item): ?string
    {
        // initialize the error to show why is failing in the console
        $error = null;

        // Prepare all the needed variables 
        $path = $item["path"];
        $name = $item["fname"];
        $mimeType = $item["mime_type"];
        $completePath = $path . DIRECTORY_SEPARATOR . $item["fname"];
        $newName = $this->removeExt($name) . ".webp";


        switch ($mimeType) {
            case "image/gif":
                // We don't want to convert animated gifs
                if (self::isAnimatedGif($completePath)) {
                    return "Image is a GIF with more than 1 frame, cannot be converted";
                }
                $img = imageCreateFromGif($completePath);
                break;
            case "image/jpeg":
                $img = imageCreateFromJpeg($completePath);
                break;
            case "image/png":
                $img = imageCreateFromPng($completePath);
                break;
            // Windows bmp are different
            case "image/bmp":
            case "image/x-ms-bmp":
                $img = imageCreateFromBmp($completePath);
                break;
            default:
                return sprintf("Mime type not one of the following: %s", implode("\n└ ", $mimeType));
        }



        imagepalettetotruecolor($img);
        imagealphablending($img, true);
        imagesavealpha($img, true);
        $savePath = $path . DIRECTORY_SEPARATOR . $newName;
        if (!imagewebp($img, $savePath, self::$effortQuality)) {
            $error = "Image cannot be converted in Webp";
        }
        imagedestroy($img);

        return $error;
    }


    /**
     * Check if the provided file is an animated gif.
     *
     * @param string $fileName
     * @return bool
     * For reference https://www.php.net/manual/en/function.imagecreatefromgif.php#119564
     */
    private static function isAnimatedGif(string $fileName): bool
    {
        $fh = fopen($fileName, 'rb');

        if (!$fh) {
            return false;
        }

        $totalCount = 0;
        $chunk = '';

        // An animated gif contains multiple "frames", with each frame having a header made up of:
        // * a static 4-byte sequence (\x00\x21\xF9\x04)
        // * 4 variable bytes
        // * a static 2-byte sequence (\x00\x2C) (some variants may use \x00\x21 ?)

        // We read through the file until we reach the end of it, or we've found at least 2 frame headers.
        while (!feof($fh) && $totalCount < 2) {
            // Read 100kb at a time and append it to the remaining chunk.
            $chunk .= fread($fh, 1024 * 100);
            $count = preg_match_all('#\x00\x21\xF9\x04.{4}\x00(\x2C|\x21)#s', $chunk, $matches);
            $totalCount += $count;

            // Execute this block only if we found at least one match,
            // and if we did not reach the maximum number of matches needed.
            if ($count > 0 && $totalCount < 2) {
                // Get the last full expression match.
                $lastMatch = end($matches[0]);
                // Get the string after the last match.
                $end = strrpos($chunk, $lastMatch) + strlen($lastMatch);
                $chunk = substr($chunk, $end);
            }
        }

        fclose($fh);

        return $totalCount > 1;
    }


    /**
     * @param $fname
     * @return string
     * Remove the extension from the filename.
     */
    private function removeExt($fname): string
    {
        $exploded = explode(".", $fname);
        array_pop($exploded);
        return implode(".", $exploded);
    }
}
