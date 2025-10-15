<?php declare(strict_types=1);

namespace HatslogicPageOptimisation\Listener;

use HatslogicPageOptimisation\Service\MinifyService;
use HatslogicPageOptimisation\Service\PreloadLinkService;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class ResponseListener
{
    public function __construct(
        private readonly string $environment,
        private readonly MinifyService $minifyService,
        private readonly PreloadLinkService $preloadLinkService
    ) {
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        if ($this->environment !== 'dev') {
            return;
        }

        if (!$event->isMainRequest()) {
            return;
        }

        $response = $event->getResponse();

        if ($response instanceof BinaryFileResponse
            || $response instanceof StreamedResponse) {
            return;
        }

        if ($response->getStatusCode() === Response::HTTP_NO_CONTENT) {
            return;
        }

        if (!str_contains($response->headers->get('Content-Type', ''), 'text/html')) {
            return;
        }

        // $this->minifyService
        //     ->setResponse($response)
        //     ->minify();

        $this->preloadLinkService
            ->setResponse($response)
            ->optimize();
    }
}
