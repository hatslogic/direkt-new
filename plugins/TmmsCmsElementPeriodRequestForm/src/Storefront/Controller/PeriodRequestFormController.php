<?php
declare(strict_types=1);

namespace Tmms\CmsElementPeriodRequestForm\Storefront\Controller;

use Shopware\Core\Content\Mail\Service\AbstractMailService;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Core\Framework\Log\Package;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(defaults: ['_routeScope' => ['storefront']])]
#[Package('storefront')]
class PeriodRequestFormController extends StorefrontController
{
    /**
     * @var SystemConfigService
     */
    private $systemConfigService;

    /**
     * @var AbstractMailService
     */
    private AbstractMailService $mailService;

    /**
     * @var EntityRepository
     */
    private EntityRepository $mailTemplateRepository;

    /**
     * @var EntityRepository
     */
    private EntityRepository $cmsSlotRepository;

    public function __construct(
        SystemConfigService $systemConfigService,
        AbstractMailService $mailService,
        EntityRepository $mailTemplateRepository,
        EntityRepository $cmsSlotRepository
    )
    {
        $this->systemConfigService = $systemConfigService;
        $this->mailService = $mailService;
        $this->mailTemplateRepository = $mailTemplateRepository;
        $this->cmsSlotRepository = $cmsSlotRepository;
    }

    #[Route(path: '/periodrequestform/getdisableddates', name: 'frontend.periodrequestform.getdisableddates', defaults: ['id' => null, 'XmlHttpRequest' => true, '_routeScope' => ['storefront'], '_captcha' => false], methods: ['GET', 'POST'])]
    public function getdisableddates(Request $request, Context $context): Response
    {
        $placeholder = $request->query->get('placeholder');
        $origin = $request->query->get('origin');
        $originId = $request->query->get('originid');
        $dateisrequired = $request->query->get('dateisrequired');
        $defaultdatevalue = $request->query->get('defaultdatevalue');
        $mindatevalue = $request->query->get('mindatevalue');
        $maxdatevalue = $request->query->get('maxdatevalue');
        $inputdisableddates = $request->query->get('inputdisableddates');
        $dateformat = $request->query->get('dateformat');
        $showweeksnumbers = $request->query->get('showweeksnumbers');
        $locale = $request->query->get('locale');
        $showperiodselection = $request->query->get('showperiodselection');
        $showcalendarpermanentlyopen = $request->query->get('showcalendarpermanentlyopen');
        $showselectfieldformonth = $request->query->get('showselectfieldformonth');
        $showtwomonthssidebyside = $request->query->get('showtwomonthssidebyside');
        $formelementid = $request->query->get('formelementid');

        $periodrequestform = $this->container->get('periodrequestform.repository');

        $criteria = $this->createCriteria($origin, $originId);

        $periodrequestformEntries = $periodrequestform->search($criteria, $context);

        return $this->renderStorefront('storefront/custom/period-request-form/getdisableddates.html.twig', [
            'placeholder' => $placeholder,
            'origin' => $origin,
            'originId' => $originId,
            'dateisrequired' => $dateisrequired,
            'defaultdatevalue' => $defaultdatevalue,
            'mindatevalue' => $mindatevalue,
            'maxdatevalue' => $maxdatevalue,
            'inputdisableddates' => $inputdisableddates,
            'dateformat' => $dateformat,
            'showweeksnumbers' => $showweeksnumbers,
            'locale' => $locale,
            'showperiodselection' => $showperiodselection,
            'showcalendarpermanentlyopen' => $showcalendarpermanentlyopen,
            'showselectfieldformonth' => $showselectfieldformonth,
            'showtwomonthssidebyside' => $showtwomonthssidebyside,
            'formelementid' => $formelementid,
            'periodrequestformEntries' => $periodrequestformEntries,
            'context' => $context,
        ]);
    }

    #[Route(path: '/periodrequestform/save', name: 'frontend.periodrequestform.save', options: ['seo' => false], defaults: ['id' => null, 'XmlHttpRequest' => true, '_routeScope' => ['storefront'], '_captcha' => true], methods: ['GET', 'POST'])]
    public function save(Request $request, SalesChannelContext $context, Context $criteriaContext): Response
    {
        $formElementId = ($request->request->get('formElementId') ? $request->request->get('formElementId') : '');

        if ($formElementId !== '') {
            $cmsSlot = $this->cmsSlotRepository->search(new Criteria([$formElementId]), $context->getContext())->first();
            $cmsSlotConfig = $cmsSlot->getConfig();

            $sendMail = $cmsSlotConfig['periodRequestFormSendMail']['value'];
            $mailReceiverName = $cmsSlotConfig['periodRequestFormMailReceiverName']['value'];
            $mailReceiver = $cmsSlotConfig['periodRequestFormMailReceiver']['value'];
            $mailReceiver = str_replace(" ", "", $mailReceiver);
            $sendMailToRequester = $cmsSlotConfig['periodRequestFormSendMailToRequester']['value'];
            $saveData = $cmsSlotConfig['periodRequestFormSaveData']['value'];
            $setOriginOfRequestManually = $cmsSlotConfig['setOriginOfRequestManually']['value'];
            $formTitle = $cmsSlotConfig['periodRequestFormTitle']['value'];
            $origin = $cmsSlotConfig['originValue']['value'] ?: '';
            $originId = $cmsSlotConfig['originIdValue']['value'] ?: '';
            $originName = $cmsSlotConfig['originNameValue']['value'] ?: '';

            if ($setOriginOfRequestManually != 1) {
                $origin = ($request->request->get('origin') ? $request->request->get('origin') : '');
                $originId = ($request->request->get('originId') ? $request->request->get('originId') : '');
                $originName = ($request->request->get('originName') ? $request->request->get('originName') : '');
            }

            $periodRequestFormData = [
                'id' => ($request->request->get('formElementId') ? $request->request->get('formElementId') : ''),
                'salutation' => ($request->request->get('salutationId') ? $request->request->get('salutationId') : ''),
                'firstname' => ($request->request->get('firstName') ? $request->request->get('firstName') : ''),
                'lastname' => ($request->request->get('lastName') ? $request->request->get('lastName') : ''),
                'street' => ($request->request->get('street') ? $request->request->get('street') : ''),
                'zipcode' => ($request->request->get('zipcode') ? $request->request->get('zipcode') : ''),
                'city' => ($request->request->get('city') ? $request->request->get('city') : ''),
                'country' => ($request->request->get('country') ? $request->request->get('country') : ''),
                'email' => ($request->request->get('email') ? $request->request->get('email') : ''),
                'phone' => ($request->request->get('phone') ? $request->request->get('phone') : ''),
                'comment' => ($request->request->get('comment') ? $request->request->get('comment') : ''),
                'date' => ($request->request->get('date') ? $request->request->get('date') : ''),
                'labelDate' => ($request->request->get('labelDate') ? $request->request->get('labelDate') : ''),
                'freeInputLabel' => ($request->request->get('freeInputLabel') ? $request->request->get('freeInputLabel') : ''),
                'freeInput' => ($request->request->get('freeInput') ? $request->request->get('freeInput') : ''),
                'freeInput2Label' => ($request->request->get('freeInput2Label') ? $request->request->get('freeInput2Label') : ''),
                'freeInput2' => ($request->request->get('freeInput2') ? $request->request->get('freeInput2') : ''),
                'freeInput3Label' => ($request->request->get('freeInput3Label') ? $request->request->get('freeInput3Label') : ''),
                'freeInput3' => ($request->request->get('freeInput3') ? $request->request->get('freeInput3') : ''),
                'freeInput4Label' => ($request->request->get('freeInput4Label') ? $request->request->get('freeInput4Label') : ''),
                'freeInput4' => ($request->request->get('freeInput4') ? $request->request->get('freeInput4') : ''),
                'freeInput5Label' => ($request->request->get('freeInput5Label') ? $request->request->get('freeInput5Label') : ''),
                'freeInput5' => ($request->request->get('freeInput5') ? $request->request->get('freeInput5') : ''),
                'freeInput6Label' => ($request->request->get('freeInput6Label') ? $request->request->get('freeInput6Label') : ''),
                'freeInput6' => ($request->request->get('freeInput6') ? $request->request->get('freeInput6') : ''),
                'freeInput7Label' => ($request->request->get('freeInput7Label') ? $request->request->get('freeInput7Label') : ''),
                'freeInput7' => ($request->request->get('freeInput7') ? $request->request->get('freeInput7') : ''),
                'freeInput8Label' => ($request->request->get('freeInput8Label') ? $request->request->get('freeInput8Label') : ''),
                'freeInput8' => ($request->request->get('freeInput8') ? $request->request->get('freeInput8') : ''),
                'freeInput9Label' => ($request->request->get('freeInput9Label') ? $request->request->get('freeInput9Label') : ''),
                'freeInput9' => ($request->request->get('freeInput9') ? $request->request->get('freeInput9') : ''),
                'freeInput10Label' => ($request->request->get('freeInput10Label') ? $request->request->get('freeInput10Label') : ''),
                'freeInput10' => ($request->request->get('freeInput10') ? $request->request->get('freeInput10') : ''),
                'formTitle' => $formTitle ?: '',
                'origin' => $origin,
                'originId' => $originId,
                'originName' => $originName,
            ];

            if ($saveData == 1) {
                $this->insertPeriodRequestFormEntry($context, $periodRequestFormData);
            }

            if (($sendMail == 1) && $mailReceiver) {
                $mailReceiverNameText = "";

                if ($mailReceiver) {
                    $mailReceiversArray = explode(",", $mailReceiver);
                }

                foreach ($mailReceiversArray as $key => $mailReceiverText) {
                    if ($mailReceiverName) {
                        $mailReceiverNamesArray = explode(",", $mailReceiverName);
                        $mailReceiverNameText = ((isset($mailReceiverNamesArray[$key])) ? $mailReceiverNamesArray[$key] : $mailReceiverNamesArray[0]);
                    }

                    $this->sendMailTemplate($context, $periodRequestFormData, $mailReceiverNameText, $mailReceiverText, 'receiver');
                }
            }

            if (($sendMailToRequester == 1) && $request->request->get('email')) {
                $mailReceiverNameText = (($request->request->get('firstName')) ? $request->request->get('firstName') . " " : "") . (($request->request->get('lastName')) ? $request->request->get('lastName') : "");

                $this->sendMailTemplate($context, $periodRequestFormData, $mailReceiverNameText, $request->request->get('email'), 'requester');
            }

            return $this->renderStorefront('storefront/custom/period-request-form/alert.html.twig', [
                'savePeriodRequestFormSuccess' => true,
                'periodRequestFormData' => $periodRequestFormData,
            ]);
        }

        return $this->renderStorefront('storefront/custom/period-request-form/alert.html.twig', [
            'savePeriodRequestFormSuccess' => false,
            'periodRequestFormData' => null,
        ]);
    }

    private function createCriteria($origin, $originId): Criteria
    {
        $ignoreConfirmedStatusOfRequests = $this->systemConfigService->get('TmmsCmsElementPeriodRequestForm.config.ignoreConfirmedStatusOfRequests');
        $ignoreOriginOfRequests = $this->systemConfigService->get('TmmsCmsElementPeriodRequestForm.config.ignoreOriginOfRequests');

        $criteria = new Criteria();

        if ($ignoreOriginOfRequests) {
            if ($ignoreConfirmedStatusOfRequests) {
                $criteria->addFilter(
                    new EqualsFilter('originid', $originId)
                );
            } else {
                $criteria->addFilter(
                    new EqualsFilter('originid', $originId),
                    new EqualsFilter('confirmed', true)
                );
            }
        } else {
            if ($ignoreConfirmedStatusOfRequests) {
                $criteria->addFilter(
                    new EqualsFilter('origin', $origin),
                    new EqualsFilter('originid', $originId)
                );
            } else {
                $criteria->addFilter(
                    new EqualsFilter('origin', $origin),
                    new EqualsFilter('originid', $originId),
                    new EqualsFilter('confirmed', true)
                );
            }
        }

        $field = 'createdAt';
        $direction = 'ASC';

        $criteria->addSorting((new FieldSorting($field, $direction)));

        return $criteria;
    }

    public function insertPeriodRequestFormEntry(SalesChannelContext $context, array $periodRequestFormData): void
    {
        $periodrequestform = $this->container->get('periodrequestform.repository');

        $frameworkContext = Context::createDefaultContext();

        $periodRequestFormEntryId = Uuid::randomHex();

        $periodrequestform->create(
            [
                [
                    'id' => $periodRequestFormEntryId,
                    'salutation' => $periodRequestFormData['salutation'],
                    'firstname' => $periodRequestFormData['firstname'],
                    'lastname' => $periodRequestFormData['lastname'],
                    'street' => $periodRequestFormData['street'],
                    'zipcode' => $periodRequestFormData['zipcode'],
                    'city' => $periodRequestFormData['city'],
                    'country' => $periodRequestFormData['country'],
                    'email' => $periodRequestFormData['email'],
                    'phone' => $periodRequestFormData['phone'],
                    'comment' => $periodRequestFormData['comment'],
                    'date' => $periodRequestFormData['date'],
                    'freeinputlabel' => $periodRequestFormData['freeInputLabel'],
                    'freeinput' => $periodRequestFormData['freeInput'],
                    'freeinput2label' => $periodRequestFormData['freeInput2Label'],
                    'freeinput2' => $periodRequestFormData['freeInput2'],
                    'freeinput3label' => $periodRequestFormData['freeInput3Label'],
                    'freeinput3' => $periodRequestFormData['freeInput3'],
                    'freeinput4label' => $periodRequestFormData['freeInput4Label'],
                    'freeinput4' => $periodRequestFormData['freeInput4'],
                    'freeinput5label' => $periodRequestFormData['freeInput5Label'],
                    'freeinput5' => $periodRequestFormData['freeInput5'],
                    'freeinput6label' => $periodRequestFormData['freeInput6Label'],
                    'freeinput6' => $periodRequestFormData['freeInput6'],
                    'freeinput7label' => $periodRequestFormData['freeInput7Label'],
                    'freeinput7' => $periodRequestFormData['freeInput7'],
                    'freeinput8label' => $periodRequestFormData['freeInput8Label'],
                    'freeinput8' => $periodRequestFormData['freeInput8'],
                    'freeinput9label' => $periodRequestFormData['freeInput9Label'],
                    'freeinput9' => $periodRequestFormData['freeInput9'],
                    'freeinput10label' => $periodRequestFormData['freeInput10Label'],
                    'freeinput10' => $periodRequestFormData['freeInput10'],
                    'origin' => $periodRequestFormData['origin'],
                    'originid' => $periodRequestFormData['originId'],
                    'originname' => $periodRequestFormData['originName'],
                    'confirmed' => false,
                    'answered' => false,
                ],
            ],
            $frameworkContext::createDefaultContext()
        );
    }

    public function sendMailTemplate(SalesChannelContext $salesChannelContext, array $periodRequestFormData, $mailReceiverName, $mailReceiver, $mailReceiverType): void
    {
        $technicalName = 'tmms_request_form';

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('mailTemplateType.technicalName', $technicalName));
        $criteria->setLimit(1);

        $mailTemplate = $this->mailTemplateRepository->search($criteria, $salesChannelContext->getContext())->first();

        $data = new ParameterBag();

        if ($mailReceiverName == '') {
            $mailReceiverName = $mailReceiver;
        }

        $data->set(
            'recipients',
            [
                $mailReceiver => $mailReceiverName,
            ]
        );

        $data->set('senderName', $mailTemplate->getTranslation('senderName'));
        $data->set('salesChannel', $salesChannelContext->getSalesChannel());
        $data->set('salesChannelId', $salesChannelContext->getSalesChannel()->getId());

        if($mailReceiverType == "receiver" && $periodRequestFormData['email'] != "") {
            $data->set('replyTo', $periodRequestFormData['email']);
        }

        $data->set('templateId', $mailTemplate->getId());
        $data->set('customFields', $mailTemplate->getCustomFields());
        $data->set('contentPlain', $mailTemplate->getTranslation('contentPlain'));
        $data->set('contentHtml', $mailTemplate->getTranslation('contentHtml'));
        $data->set('subject', $mailTemplate->getTranslation('subject'));

        $this->mailService->send(
            $data->all(),
            $salesChannelContext->getContext(),
            [
                'salesChannelId' => $salesChannelContext->getSalesChannel()->getId(),
                'periodRequestFormData' => $periodRequestFormData,
            ]
        );
    }
}
