<?php declare(strict_types=1);

namespace HatslogicPageOptimisation\Core\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Shopware\Core\Framework\Context;
use HatslogicPageOptimisation\Service\CriticalCssGenerationService;
use HatslogicPageOptimisation\Service\NodeFinder;

#[Route(defaults: ['_routeScope' => ['api']])]
class CriticalCssGenerationController extends AbstractController
{
    public function __construct(
        private CriticalCssGenerationService $criticalCssGenerationService,
        private NodeFinder $nodeFinder
    ) {
    }

    #[Route(path: '/api/_action/hatslogic/generate/critical-css', name: 'api.action.hatslogic.generate.critical-css', methods: ['GET'])]
    public function getList(Context $context): Response
    {
        try {

            $urls = $this->criticalCssGenerationService->generate($context);

            if ($urls === null) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Failed generating critical CSS.',
                ]);
            }
        } catch (\Throwable $e) {
            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
        return new JsonResponse([
            'success' => true,
            'message' => 'Successfully generated critical CSS.',
        ]);
    }
}
