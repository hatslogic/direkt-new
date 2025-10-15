<?php

declare(strict_types=1);

namespace WebLa_ContactFormFileUpload\Storefront\Controller;

use Shopware\Core\Content\Flow\Dispatching\Action\SendMailAction;
use Shopware\Core\Content\MailTemplate\Subscriber\MailSendSubscriberConfig;
use Shopware\Core\Content\Media\File\FileNameProvider;
use Shopware\Core\Content\Media\File\FileSaver;
use Shopware\Core\Content\Media\File\MediaFile;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Shopware\Core\Framework\Util\Random;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Swag\CmsExtensions\Storefront\Controller\CustomFormController;

#[Route(defaults: ['_routeScope' => ['storefront']])]
class CustomFormControllerDecorator extends StorefrontController
{

    private $mediaUpdater;
    private $fileNameProvider;
    private $systemConfigService;
    private $decorated;

    public function __construct(
        CustomFormController $decorated,
        FileSaver $mediaUpdater,
        FileNameProvider $fileNameProvider,
        SystemConfigService $systemConfigService
    ) {
        $this->decorated = $decorated;
        $this->mediaUpdater = $mediaUpdater;
        $this->fileNameProvider = $fileNameProvider;
        $this->systemConfigService = $systemConfigService;
    }

    public function getDecorated(): CustomFormController
    {
        return $this->decorated;
    }

    #[Route(path: '/swag/cms-extensions/form', name: 'frontend.swag.cms-extensions.form.send', methods: ['POST'], defaults: ['XmlHttpRequest' => true, '_captcha' => true])]
    public function sendForm(Request $request, RequestDataBag $data, SalesChannelContext $context): JsonResponse
    {
        $response = [];
        $filesFields = $request->files;

        $allowedExtenstions = $this->systemConfigService->get('WebLa_ContactFormFileUpload.config.allowedExts', $context->getSalesChannelId());
        $maxSize = $this->systemConfigService->get('WebLa_ContactFormFileUpload.config.filesize', $context->getSalesChannelId());
        $allowedExtenstions = array_map('trim', explode(',', $allowedExtenstions));
        $mediaRepository = $this->container->get('media.repository');
        $mediaIds = [];

        foreach ($filesFields as $field => $filesField) {
            $fieldValues = [];
            if (count($filesField) > 0) {
                $size = 0;
                $files = $filesField;
                /** @var \Symfony\Component\HttpFoundation\File\UploadedFile */
                foreach ($files as $key => $file) {
                    if (!$file) {
                        continue;
                    }
                    $fileName = $file->getClientOriginalName();
                    $size += $file->getSize();
                    if (intval($maxSize) * 1048576 < $size) {
                        $response[] = [
                            'type' => 'danger',
                            'alert' => $this->renderView('@Storefront/storefront/utilities/alert.html.twig', [
                                'type' => 'danger',
                                'content' => $this->trans('form.tooBig', ['%maxSize%' => $maxSize]),
                            ]),
                        ];
                        continue;
                    }
                    $ext = pathinfo($fileName, PATHINFO_EXTENSION);
                    if (!in_array($ext, $allowedExtenstions)) {
                        $response[] = [
                            'type' => 'danger',
                            'alert' => $this->renderView('@Storefront/storefront/utilities/alert.html.twig', [
                                'type' => 'danger',
                                'content' => $this->trans('form.invalidExtension', ['%ext%' => $ext]),
                            ]),
                        ];
                    } else {
                        $fileName = pathinfo($fileName, PATHINFO_FILENAME) . '_' . Random::getInteger(100, 1000);

                        $mediaId = Uuid::randomHex();
                        $media = [
                            [
                                'id' => $mediaId,
                                'name' => $fileName,
                                'fileName' => $fileName,
                                'mimeType' => $file->getClientMimeType(),
                                'fileExtension' => $file->guessExtension(),
                                'mediaFolderId' => $this->systemConfigService->get('WebLa_ContactFormFileUpload.config.mediaFolderId', $context->getSalesChannelId())
                            ]
                        ];

                        $mediaId = $mediaRepository->create($media, Context::createDefaultContext())->getEvents()->getElements()[1]->getIds()[0];
                        if (is_array($mediaId)) {
                            $mediaId = $mediaId['mediaId'];
                        }
                        try {
                            $this->upload($file, $fileName, $mediaId, Context::createDefaultContext());
                            $mediaIds[] = $mediaId;
                        } catch (\Exception $exception) {
                            try {
                                $fileName = $fileName . Random::getInteger(100, 1000);
                                $this->upload($file, $fileName, $mediaId, Context::createDefaultContext());
                            } catch (\Exception $e) {
                                $response[] = [
                                    'type' => 'danger',
                                    'alert' => $this->renderView('@Storefront/storefront/utilities/alert.html.twig', [
                                        'type' => 'danger',
                                        'content' => $e->getMessage()
                                    ]),
                                ];
                            }
                        }
                    }
                    $fieldValues[] = $fileName;
                }
            }
            $data->set((string)$field, \implode(', ', $fieldValues));
        }

        $context->getContext()->addExtension(
            SendMailAction::MAIL_CONFIG_EXTENSION,
            new MailSendSubscriberConfig(false, [], $mediaIds)
        );

        if (count($response) > 0) {
            return new JsonResponse($response);
        }

        return $this->getDecorated()->sendForm($data, $context);
    }

    private function upload($file, $fileName, $mediaId, $context)
    {
        return $this->mediaUpdater->persistFileToMedia(
            new MediaFile(
                $file->getRealPath(),
                $file->getMimeType(),
                $file->guessExtension(),
                $file->getSize()
            ),
            $this->fileNameProvider->provide(
                $fileName,
                $file->getExtension(),
                $mediaId,
                $context
            ),
            $mediaId,
            $context
        );
    }
}
