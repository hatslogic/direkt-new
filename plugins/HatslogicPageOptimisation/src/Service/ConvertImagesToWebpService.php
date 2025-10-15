<?php

namespace HatslogicPageOptimisation\Service;

use HatslogicPageOptimisation\Helper\DirAndFileHelper;
use Monolog\Logger;

class ConvertImagesToWebpService
{
    protected DirAndFileHelper $helper;
    protected Logger $logger;

    public function __construct(
        DirAndFileHelper $helper,
        Logger $logger,

    ) {
        $this->helper = $helper;
        $this->logger = $logger;

    }

    public function run($isCommand = false)
    {
        $this->logger->debug("Started");
        foreach ($this->helper->decomposePath($isCommand) as $items) {
            foreach ($items as $item) {
                $path  = $item['path'];
                $fname = $item['fname'];
                
                // moved out to show before completing which file is taking longer
                if ($isCommand) {
                    echo "Processing: " . $path . DIRECTORY_SEPARATOR . $fname . "\n";
                }

                $errorOnConverting = $this->helper->convertImage($item);

                // echo only for command and not service
                if ($isCommand) {
                    if (!$errorOnConverting) {
                        echo "[Ok]: Converted \n";
                    } else {
                        echo "[Error]: Could not convert this file $errorOnConverting \n\n";
                    }
                } else {
                    $this->logger->debug("Processing: " . $path . DIRECTORY_SEPARATOR . $fname);
                    if (!$errorOnConverting) {
                        $this->logger->debug("[Ok]: Converted");
                    } else {
                        $this->logger->error("[Error]: Could not convert this file $errorOnConverting \n\n");
                    }
                }
            }

            if ($isCommand) {
                echo "\n\n";
                echo "Private plugin image compression done!\n";
                echo "\n\n\n";
            } else {
                $this->logger->debug("Finish!");
            }
        }
    }

    public function singleRun($path)
    {
        $response = $this->helper->processSingleImagePath($path);
        if ($response['status'] === 'pending') {
        } else {
            // Handle skipped or error cases
            $this->logger->error($response['message']);
            $this->logger->error($response['message']);
        }
        $item = $response['data'];
        $errorOnConverting = $this->helper->convertImage($item);
        $this->logger->debug("Processing: " . $path);
        if (!$errorOnConverting) {
            $this->logger->debug("[Ok]: Converted");
        } else {
            $this->logger->error("[Error]: Could not convert this file $errorOnConverting \n\n");
        }
                
    }
}
