<?php
declare(strict_types=1);

namespace Inpsyde\MultilingualPress2to3\Migration;

use Dhii\I18n\FormatTranslatorInterface;
use Dhii\I18n\StringTranslatingTrait;
use Dhii\I18n\StringTranslatorAwareTrait;
use Exception;
use Inpsyde\MultilingualPress2to3\Db\DatabaseWpdbTrait;
use Inpsyde\MultilingualPress2to3\Event\WpTriggerCapableTrait;
use Throwable;
use UnexpectedValueException;
use WP_CLI;
use wpdb as Wpdb;

/**
 * Migrates a single MLP2 site language to MLP3.
 *
 * @package MultilingualPress2to3
 */
class SiteLanguageMigrator
{
    use WpTriggerCapableTrait;

    use DatabaseWpdbTrait;

    use StringTranslatingTrait;

    use StringTranslatorAwareTrait;

    protected $db;
    protected $translator;
    /**
     * @var int
     */
    protected $mainSiteId;
    /**
     * @var string
     */
    protected $siteSettingsOptionName;

    /**
     * @param Wpdb $wpdb The database driver to use for DB operations.
     * @param FormatTranslatorInterface $translator The translator to use for i18n.
     */
    public function __construct(
        Wpdb $wpdb,
        FormatTranslatorInterface $translator,
        int $mainSiteId,
        string $siteSettingsOptionName
    )
    {
        $this->db = $wpdb;
        $this->translator = $translator;
        $this->mainSiteId = $mainSiteId;
        $this->siteSettingsOptionName = $siteSettingsOptionName;
    }

    /**
     * Migrates an MLP2 site language to MLP3.
     *
     * @param object $mlp2Language Data of an MLP2 site language. Properties
     * - `site_id` - If of the site to which the language belongs.
     * - `locale` - Code of the language's locale.
     * - `title` - The human-readable name of the language.
     *
     * @throws Throwable If problem migrating.
     */
    public function migrate($mlp2Language)
    {
        $optinNameAltTitle = 'multilingualpress_alt_language_title';

        $siteId = $mlp2Language->site_id ? (int) $mlp2Language->site_id : null;
        $locale = $mlp2Language->locale ? (string) $mlp2Language->locale : null;
        $altTitle = $mlp2Language->title ? (string) $mlp2Language->title : '';

        if (empty($siteId)) {
            throw new UnexpectedValueException($this->__('Site ID is required'));
        }

        if (empty($locale)) {
            throw new UnexpectedValueException($this->__('Locale is required for site #%1$d language', [$siteId]));
        }

        try {
            $siteSettings = $this->_getSiteSettings($siteId);
        } catch (UnexpectedValueException $e) {
            $siteSettings = [];
            $siteSettings[$siteId] = [
                'lang'              => $this->_transformLanguageCode($locale),
            ];

            $this->_setSiteSettings($siteId, $siteSettings);
            $this->_setSiteOption($siteId, $optinNameAltTitle, $altTitle);
        }
    }

    /**
     * Retrieves an option of a particular network.
     *
     * @param int $networkId The ID of the network, to which the option belongs.
     * @param string $optionName The name of the option to retrieve.
     * @return mixed The option value.
     *
     * @throws UnexpectedValueException If option could not be retrieved.
     * @throws Exception If problem retrieving.
     * @throws Throwable If problem running.
     */
    protected function _getSiteOption(int $networkId, string $optionName)
    {
        $default = uniqid('default-network-option-value-');
        $value = get_network_option($networkId, $optionName, $default);

        if ($value === $default) {
            throw new UnexpectedValueException($this->__('Could not retrieve option "%1$s" for network #%2$d', [$optionName, $networkId]));
        }

        return $value;
    }

    /**
     * Assigns a value to a network option.
     *
     * @param int $siteId The ID of the network to which the option belongs.
     * @param string $optionName Name of the option to set.
     * @param mixed $value The option value.
     *
     * @throws UnexpectedValueException If option could not be set.
     */
    protected function _setSiteOption(int $siteId, string $optionName, $value)
    {
        $result = update_network_option($siteId, $optionName, $value);
        $default = uniqid('default-site-option-value-');
        $newValue = get_network_option($siteId, $optionName, $default);

        if ((!$result) && ($value !== $newValue)) {
            throw new UnexpectedValueException($this->__('Could not update option "%1$s" for network #%2$d', [$optionName, $siteId]));
        }
    }

    /**
     * Retrieves settings for a site.
     *
     * @return array<string, mixed> A map of site info keys to values.
     *
     * @throws UnexpectedValueException If no settings found for specified site.
     * @throws Exception If problem retrieving.
     * @throws Throwable If problem running.
     */
    protected function _getSiteSettings(int $siteId): array
    {
        $allSettings = $this->_getAllSiteSettings();

        if (!array_key_exists($siteId, $allSettings)) {
            throw new UnexpectedValueException($this->__('No settings for site #%1$d', [$siteId]));
        }

        $siteSettings = $allSettings[$siteId];

        return $siteSettings;
    }

    /**
     * Assigns settings for a site.
     *
     * @param int $siteId
     * @param $value
     *
     * @throws Exception If problem assigning.
     * @throws Throwable If problem running.
     */
    protected function _setSiteSettings(int $siteId, $value)
    {
        try {
            $allSettings = $this->_getAllSiteSettings();
        } catch (UnexpectedValueException $e) {
            $allSettings = [];
        }

        $allSettings[$siteId] = $value;

        $this->_setAllSiteSettings($allSettings);
    }

    /**
     * Assigns the settings for all sites.
     *
     * @param array<int, array<string, mixed>> $settings A map of site ID to site settings.
     *
     * @throws UnexpectedValueException If could not set settings.
     * @throws Exception If problem setting.
     * @throws Throwable If problem running.
     */
    protected function _setAllSiteSettings(array $settings)
    {
        $siteId = $this->mainSiteId;
        $optionName = $this->siteSettingsOptionName;

        $this->_setSiteOption($siteId, $optionName, $settings);
    }

    /**
     * Retrieves the settings for all sites.
     *
     * @return array<int, array<string, mixed>> $settings A map of site ID to site settings.
     *
     * @throws UnexpectedValueException If settings value could not be retrieved.
     * @throws Exception If problem retrieving.
     * @throws Throwable If problem running.
     */
    protected function _getAllSiteSettings(): array
    {
        $siteId = $this->mainSiteId;
        $optionName = $this->siteSettingsOptionName;
        $siteSettings = $this->_getSiteOption($siteId, $optionName);

        return $siteSettings;
    }

    /**
     * Transforms a source language code into a destination language code.
     *
     * @param string $code The code to transform.
     *
     * @return string The transformed code.
     * Usually in BCP47 format.
     */
    protected function _transformLanguageCode(string $code): string
    {
        $code = str_replace('_', '-', $code);

        return $code;
    }

    /**
     * {@inheritdoc}
     */
    protected function _getDb()
    {
        return $this->db;
    }

    /**
     * {@inheritdoc}
     */
    protected function _getTranslator()
    {
        return $this->translator;
    }
}
