<?php

declare(strict_types=1);

namespace ZweiPunktOrderRating\CustomFields;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\ContainsFilter;
use Shopware\Core\Framework\Uuid\Uuid;

/**
 * class CustomFieldHandler
 *
 * Handles the custom fields
 */
class CustomFieldHandler
{
    /**
     * @var EntityRepository
     */
    private EntityRepository $customFieldSetRepo;

    /**
     * @var EntityRepository
     */
    private EntityRepository $customFieldRepo;

    /**
     * @var string
     */
    private string $pluginPath;

    /**
     * @var array<mixed>
     */
    private array $customFieldSets;

    public function __construct(
        EntityRepository $customFieldSetRepo,
        EntityRepository $customFieldRepo,
        string $pluginPath
    ) {
        $this->customFieldSetRepo = $customFieldSetRepo;
        $this->customFieldRepo = $customFieldRepo;
        $this->pluginPath = $pluginPath;
        $this->customFieldSets = $this->getCustomFieldSets();
    }

    /**
     * @return array<mixed>
     *
     * Fetches the data of the custom fields from the files
     */
    private function getCustomFieldSets(): array
    {
        $customFieldSets = [];

        // Reads the files that are located in the same folder.
        // Each file is a custom field set
        // and contains the information about it and the corresponding fields
        $customFieldSetsFiles = glob(
            "{$this->pluginPath}/CustomFields/customFields*.json"
        );

        // Saves the data from the files into an array
        if ($customFieldSetsFiles) {
            foreach ($customFieldSetsFiles as $customFieldSetFile) {
                $customFieldSets[] = json_decode(file_get_contents($customFieldSetFile), true);
            }
        }

        return $customFieldSets;
    }

    /**
     * Creates the custom field sets and its fields
     */
    public function create(): void
    {
        // For each entity from the files the sets are run through
        foreach ($this->customFieldSets as $customFieldSet) {
            // The current set is created
            $setId = $this->createCustomFieldSet($customFieldSet);

            // If the set could not be created,
            // the fields cannot be created either
            // and nothing else needs to be done
            if (null == $setId) {
                return;
            }

            // The fields of the current set are created
            foreach ($customFieldSet['customFields'] as $customFieldData) {
                $this->createCustomField($customFieldData, $setId);
            }
        }
    }

    /**
     * Creates the individual fields of a set
     */
    private function createCustomField(
        array $customField,
        string $setId
    ): void {
        // If the required data does not exist, it is possible to go back
        if (
            !$customField['name'] ||
            !$customField['config']
        ) {
            return;
        }

        // Entries are made for the Custom Field
        $this->customFieldRepo->create([
            [
                'id' => Uuid::randomHex(),
                'name' => $customField['name'],
                'type' => $customField['type'],
                'config' => $customField['config'],
                'customFieldSetId' => $setId
            ]
        ], Context::createDefaultContext());
    }

    /**
     * Create the set of custom fields
     */
    private function createCustomFieldSet(
        array $customFieldSet
    ): ?string {
        // Checks if all required data is available.
        // If not it returns null
        if (
            !$customFieldSet['name'] ||
            !$customFieldSet['config'] ||
            !$customFieldSet['relations']
        ) {
            return null;
        }

        // Creates the ID of the set
        $setId = Uuid::randomHex();
        // Creates the set with it
        $this->customFieldSetRepo->create([
            [
                'id' => $setId,
                'name' => $customFieldSet['name'],
                'config' => $customFieldSet['config'],
                'relations' => $customFieldSet['relations']
            ]
        ], Context::createDefaultContext());

        // Returns the Id for the individual fields
        return $setId;
    }

    /**
     * Deleted the Custom Field again
     */
    public function remove(): void
    {
        // Determines the existing custom fields based on the prefix name
        $criteria = (new Criteria())
            ->addFilter(new ContainsFilter('name', 'custom_rating_order_'));
        $customFields = $this->customFieldRepo->search($criteria, Context::createDefaultContext());

        // First the individual custom fields of the sets must be deleted and only then the sets.
        // If there are custom fields that can be deleted, they will be deleted.
        if ($customFields instanceof EntitySearchResult) {
            foreach ($customFields as $customField) {
                $this->customFieldRepo->delete([
                    ['id' => $customField->getId()]
                ], Context::createDefaultContext());
            }
        }

        // Determines the existing custom field sets based on the prefix name
        $criteria = (new Criteria())
            ->addFilter(new ContainsFilter('name', 'custom_rating_order'));
        $customFieldSets = $this->customFieldSetRepo->search($criteria, Context::createDefaultContext());

        // After the individual fields of the sets have been deleted,
        // the sets can now also be deleted
        foreach ($customFieldSets as $customFieldSet) {
            $this->customFieldSetRepo->delete([
                ['id' => $customFieldSet->getId()]
            ], Context::createDefaultContext());
        }
    }
}
