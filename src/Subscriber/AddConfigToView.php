<?php

declare(strict_types=1);

namespace ZweiPunktOrderRating\Subscriber;

use Shopware\Storefront\Page\Checkout\Finish\CheckoutFinishPageLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use ZweiPunktOrderRating\ZweiPunktOrderRating;

/**
 * class AddConfigToView
 *
 * Delivers the plugin configration to the view
 */
class AddConfigToView implements EventSubscriberInterface
{
    /**
     * @var SystemConfigService
     */
    private SystemConfigService $systemConfigService;

    public function __construct(
        SystemConfigService $systemConfigService
    ) {
        // Get system config
        $this->systemConfigService = $systemConfigService;
    }

    /**
     * @return array<mixed>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            CheckoutFinishPageLoadedEvent::class => 'onPageLoaded'
        ];
    }

    /**
     * When the finish page of the checkout is loaded,
     * the plugin configuration for the order rating is loaded
     */
    public function onPageLoaded(
        CheckoutFinishPageLoadedEvent $event
    ): void {
        // Get sales channel id
        $salesChannelId = $event->getSaleschannelContext()->getSalesChannelId();

        // Get plugin configuration
        $pluginConfig = $this->systemConfigService
            ->get(ZweiPunktOrderRating::PLUGIN_NAME . '.config', $salesChannelId);

        // Assigns the plugin configuration to the view and can be found under page.showOrderReview
        $event->getPage()->assign(['showOrderReview' => $pluginConfig]);
    }
}
