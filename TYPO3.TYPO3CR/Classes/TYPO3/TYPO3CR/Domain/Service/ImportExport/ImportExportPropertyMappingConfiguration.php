<?php
namespace TYPO3\TYPO3CR\Domain\Service\ImportExport;

/*
 * This file is part of the TYPO3.TYPO3CR package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */


use TYPO3\Flow\Property\PropertyMappingConfigurationInterface;
use TYPO3\Flow\Property\TypeConverter\ArrayConverter;
use TYPO3\Flow\Property\TypeConverter\PersistentObjectConverter;
use TYPO3\Flow\Property\TypeConverter\StringConverter;
use TYPO3\Flow\Resource\ResourceTypeConverter;

/**
 * Property mapping configuration which is used for import / export:
 *
 * - works for all levels of the PropertyMapping (recursively)
 * - sets the correct export and import configuration for the type converters
 */
class ImportExportPropertyMappingConfiguration implements PropertyMappingConfigurationInterface
{
    /**
     * @var string the resource-load-save-path, or NULL if it does not exist.
     */
    protected $resourceLoadSavePath;

    /**
     * @param $resourceLoadSavePath
     */
    public function __construct($resourceLoadSavePath)
    {
        $this->resourceLoadSavePath = $resourceLoadSavePath;
    }

    /**
     * The sub-configuration to be used is the current one.
     *
     * @param string $propertyName
     * @return PropertyMappingConfigurationInterface the property mapping configuration for the given $propertyName.
     * @api
     */
    public function getConfigurationFor($propertyName)
    {
        return $this;
    }

    /**
     * @param string $typeConverterClassName
     * @param string $key
     * @return mixed configuration value for the specific $typeConverterClassName. Can be used by Type Converters to fetch converter-specific configuration
     * @api
     */
    public function getConfigurationValue($typeConverterClassName, $key)
    {
        // needed in EXPORT
        if ($typeConverterClassName === 'TYPO3\Flow\Property\TypeConverter\StringConverter' && $key === StringConverter::CONFIGURATION_ARRAY_FORMAT) {
            return StringConverter::ARRAY_FORMAT_JSON;
        }

        if ($typeConverterClassName === 'TYPO3\Flow\Property\TypeConverter\ArrayConverter' && $key === ArrayConverter::CONFIGURATION_RESOURCE_EXPORT_TYPE) {
            return ArrayConverter::RESOURCE_EXPORT_TYPE_FILE;
        }

        if ($typeConverterClassName === 'TYPO3\Flow\Property\TypeConverter\ArrayConverter' && $key === ArrayConverter::CONFIGURATION_RESOURCE_SAVE_PATH) {
            return $this->resourceLoadSavePath;
        }


        // needed in IMPORT
        if ($typeConverterClassName === 'TYPO3\Flow\Property\TypeConverter\PersistentObjectConverter' && $key === PersistentObjectConverter::CONFIGURATION_CREATION_ALLOWED) {
            return true;
        }

        if ($typeConverterClassName === 'TYPO3\Flow\Property\TypeConverter\PersistentObjectConverter' && $key === PersistentObjectConverter::CONFIGURATION_IDENTITY_CREATION_ALLOWED) {
            return true;
        }

        if ($typeConverterClassName === 'TYPO3\Flow\Property\TypeConverter\ArrayConverter' && $key === ArrayConverter::CONFIGURATION_STRING_FORMAT) {
            return ArrayConverter::STRING_FORMAT_JSON;
        }

        if ($typeConverterClassName === 'TYPO3\Flow\Resource\ResourceTypeConverter' && $key === ResourceTypeConverter::CONFIGURATION_IDENTITY_CREATION_ALLOWED) {
            return true;
        }

        if ($typeConverterClassName === 'TYPO3\Flow\Resource\ResourceTypeConverter' && $key === ResourceTypeConverter::CONFIGURATION_RESOURCE_LOAD_PATH) {
            return $this->resourceLoadSavePath;
        }

        // fallback
        return null;
    }


    // starting from here, we just implement the interface in the "default" way without modifying things
    /**
     * @param string $propertyName
     * @return boolean TRUE if the given propertyName should be mapped, FALSE otherwise.
     * @api
     */
    public function shouldMap($propertyName)
    {
        return true;
    }

    /**
     * Check if the given $propertyName should be skipped during mapping.
     *
     * @param string $propertyName
     * @return boolean
     * @api
     */
    public function shouldSkip($propertyName)
    {
        return false;
    }

    /**
     * Whether unknown (unconfigured) properties should be skipped during
     * mapping, instead if causing an error.
     *
     * @return boolean
     * @api
     */
    public function shouldSkipUnknownProperties()
    {
        return false;
    }

    /**
     * Maps the given $sourcePropertyName to a target property name.
     * Can be used to rename properties from source to target.
     *
     * @param string $sourcePropertyName
     * @return string property name of target
     * @api
     */
    public function getTargetPropertyName($sourcePropertyName)
    {
        return $sourcePropertyName;
    }

    /**
     * This method can be used to explicitely force a TypeConverter to be used for this Configuration.
     *
     * @return \TYPO3\Flow\Property\TypeConverterInterface The type converter to be used for this particular PropertyMappingConfiguration, or NULL if the system-wide configured type converter should be used.
     * @api
     */
    public function getTypeConverter()
    {
        return null;
    }
}
