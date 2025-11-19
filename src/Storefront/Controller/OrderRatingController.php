<?php

declare(strict_types=1);

namespace ZweiPunktOrderRating\Storefront\Controller;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\Language\LanguageEntity;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Saves the rating at the order
 */
#[Route(defaults: ['_routeScope' => ['storefront']])]
class OrderRatingController extends StorefrontController
{
    /**
     * @var EntityRepository
     */
    private EntityRepository $orderRepository;

    /**
     * @var EntityRepository
     */
    private EntityRepository $languageRepository;

    /**
     * @var TranslatorInterface
     */
    private TranslatorInterface $translator;

    public function __construct(
        EntityRepository $orderRepository,
        EntityRepository $languageRepository,
        TranslatorInterface $translator
    ) {
        $this->orderRepository = $orderRepository;
        $this->languageRepository = $languageRepository;
        $this->translator = $translator;
    }

    /**
     * Stores the number of stars of the rating from the order in the free text field:
     * custom_rating_order_review
     */
    #[Route(
        path: '/order/rating',
        name: 'frontend.order.rating',
        defaults: ['XmlHttpRequest' => true],
        methods: ['POST']
    )]
    public function saveRating(
        Request $request,
        SalesChannelContext $salesChannelContext
    ): JsonResponse {
        // Determines the data passed in the request
        // and the context from the Saleschannel Context
        $data = $request->request->all();
        $context = $salesChannelContext->getContext();
        
        // Get the language ID - try multiple sources to ensure we get the user's selected language
        // 1. From request data (sent by JavaScript)
        // 2. From request headers (sw-language-id header)
        // 3. From sales channel context (fallback)
        $languageId = $data['languageId'] 
            ?? $request->headers->get('sw-language-id')
            ?? $salesChannelContext->getLanguageId();
        
        // Get the language code from the language ID
        $languageCode = $this->getLanguageCode($languageId, $context);
        // Specifies the search criteria, based on which the Id of the order will be searched.
        // The search is done using the order number available from the finish page.
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('orderNumber', $data['ordernumber']));
        // The Id of the order is determined
        $orderId = $this->orderRepository->searchIds($criteria, $context)->firstId();

        // If no id was found, the rating cannot be saved
        // and an error message is returned to be displayed on the page.
        // The error message comes from the freetext fields, so this is translated accordingly.
        if (empty($orderId)) {
            return new JsonResponse([
                'success' => false,
                'message' => $this->translator->trans('zweipunkt.review.errorMessage', [], null, $languageCode)
            ]);
        }

        // If there is an Id, then the free text field for the rating is filled with the number of stars
        $this->orderRepository->update([
            [
                'id' => $orderId,
                'customFields' => [
                    'custom_rating_order_review' => $data['reviewCount'],
                    'custom_rating_order_review_comment' => $data['comment']
                ]
            ]
        ], $context);

        // A success message is returned and output, which is also taken from the freetext fields
        return new JsonResponse([
            'success' => true,
            'message' => $this->translator->trans('zweipunkt.review.successMessage', [], null, $languageCode)
        ]);
    }

    /**
     * Gets the language code (locale) from the language ID
     */
    private function getLanguageCode(string $languageId, Context $context): string
    { 
        $criteria = new Criteria([$languageId]);
        $criteria->addAssociation('locale');
        $criteria->setLimit(1);

        /** @var LanguageEntity|null $language */
        $language = $this->languageRepository->search($criteria, $context)->first();

        if ($language === null || $language->getLocale() === null) {
            // Fallback to default locale if language not found
            return 'en-GB';
        }

        return $language->getLocale()->getCode() ?? 'en-GB';
    }
}
