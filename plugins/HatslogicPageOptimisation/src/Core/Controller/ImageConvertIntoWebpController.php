<?php declare(strict_types=1);

namespace HatslogicPageOptimisation\Core\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Shopware\Core\Framework\Context;
use HatslogicPageOptimisation\Service\ConvertImagesToWebpService;
use Svg\Tag\Rect;

#[Route(defaults: ['_routeScope' => ['api']])]
class ImageConvertIntoWebpController extends AbstractController
{
    public function __construct(
        private ConvertImagesToWebpService $convertImagesToWebpService
    ) {
    }

    #[Route(path: '/api/_action/hatslogic/convert/singleimagestowebp', name: 'api.action.hatslogic.convert.singleimagestowebp', methods: ['POST'])]
    public function convertSingleImageToWebp(Context $context, Request $request): Response
    {
        try {
            $fileUrl = $request->get('url');
            // Assuming $data contains the incoming payload
            $projectDir = $this->getProjectDir(); // Get the base project directory
            $publicDir = $projectDir . '/public'; // Path to the public directory

            // Convert URL to absolute path
            $parsedUrl = parse_url($fileUrl);
            $filePath = $publicDir . $parsedUrl['path'];

            // Ensure file exists
            if (!file_exists($filePath)) {
                throw new \RuntimeException('File does not exist at path: ' . $filePath);
            }

            // Prepare new file data
            $fileInfo = pathinfo($filePath);

            $preparedData = [
                'path' => $fileInfo['dirname'],                   // Directory path
                'fname' => $fileInfo['filename'] . '.' . $fileInfo['extension'], // File name with extension
                'mime_type' => mime_content_type($filePath),      // MIME type
                'ext' => $fileInfo['extension'],                  // File extension
            ];
            $fullPath = $preparedData['path'] . '/' . $preparedData['fname'];
            $this->convertImagesToWebpService->singleRun($fullPath);
            return new JsonResponse([
                'success' => true,
                'message' => 'Successfull converted image',
            ]);
        } catch (\Throwable $e) {
            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    #[Route(path: '/api/_action/hatslogic/convert/imagestowebp', name: 'api.action.hatslogic.convert.imagestowebp', methods: ['GET'])]
    public function convertImageToWebp(Context $context): Response
    {
        try {
            $this->convertImagesToWebpService->run();
            return new JsonResponse([
                'success' => true,
                'message' => 'Image conversion completed successfully.',
            ]);
        } catch (\Throwable $e) {
            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    protected function getProjectDir(): string
    {
		 $currentDir = __DIR__;
			while (!file_exists($currentDir . '/vendor/shopware')) {
				// Move one level up
				$parentDir = dirname($currentDir);

				// If we've reached the top-level directory, break to avoid infinite loop
				if ($parentDir === $currentDir) {
					throw new \Exception("Project root directory not found (composer.json is missing).");
				}

				$currentDir = $parentDir;
			}

		return $currentDir;
    }
}
