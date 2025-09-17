<?php

declare(strict_types=1);

namespace ZweiPunktOrderRating;

use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;
use Shopware\Core\Framework\Plugin\Context\DeactivateContext;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use ZweiPunktOrderRating\CustomFields\CustomFieldHandler;

/**
 * class ZweiPunktOrderRating
 */
class ZweiPunktOrderRating extends Plugin
{
    public const PLUGIN_NAME = 'ZweiPunktOrderRating';

    public function install(
        InstallContext $installContext
    ): void {
        parent::install($installContext);

        // Creates the custom fields during installation
        $this->getCustomFieldHandler()->create();
    }

    public function uninstall(
        UninstallContext $uninstallContext
    ): void {
        parent::uninstall($uninstallContext);

        // Deletes the custom fields during uninstallation
        $this->getCustomFieldHandler()->remove();
    }

    public function activate(
        ActivateContext $activateContext
    ): void {
        parent::activate($activateContext);
    }

    public function deactivate(
        DeactivateContext $deactivateContext
    ): void {
        parent::deactivate($deactivateContext);
    }

    private function getCustomFieldHandler(): CustomFieldHandler
    {
        // Determines the custom field set repository from the container
        $customFieldSetRepo = $this->container->get('custom_field_set.repository');
        // Determines the custom field repository from the container
        $customFieldRepo = $this->container->get('custom_field.repository');

        // Returns a custom field handler to create or delete the custom fields.
        return new CustomFieldHandler(
            $customFieldSetRepo,
            $customFieldRepo,
            $this->path
        );
    }
}
