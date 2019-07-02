<?php # -*- coding: utf-8 -*-
declare(strict_types=1);

class SiteLanguagesCest
{
    public function _before(AcceptanceTester $I)
    {
        $I->amOnPage('/wp-login.php');
        $I->fillField(['name' => 'log'], 'admin');
        $I->fillField(['name' => 'pwd'], 'password');
        $I->click('#wp-submit');
    }

    public function migrateSiteLanguages(AcceptanceTester $I)
    {
        // MLP2 check site selected languages
        $I->amOnPage('/wp-admin/network/sites.php?page=mlp-site-settings&id=1');
        $language = $I->grabValueFrom('#inpsyde_multilingual_lang');
        $I->assertSame(trim($language), 'en_US');
        $I->amOnPage('/wp-admin/network/sites.php?page=mlp-site-settings&id=2');
        $language = $I->grabValueFrom('#inpsyde_multilingual_lang');
        $I->assertSame(trim($language), 'es_ES');
        $I->amOnPage('/wp-admin/network/sites.php?page=mlp-site-settings&id=3');
        $language = $I->grabValueFrom('#inpsyde_multilingual_lang');
        $I->assertSame(trim($language), 'it_IT');

        // run the tool
        $this->runTheTool($I);

        // MLP3 check site selected languages
        $I->amOnPage('/wp-admin/network/sites.php?page=multilingualpress-site-settings&id=1');
        $language = $I->grabValueFrom('#mlp-site-language-tag');
        $I->assertSame($language, 'en-US');
        $I->amOnPage('/wp-admin/network/sites.php?page=multilingualpress-site-settings&id=2');
        $language = $I->grabValueFrom('#mlp-site-language-tag');
        $I->assertSame($language, 'es-ES');
        $I->amOnPage('/wp-admin/network/sites.php?page=multilingualpress-site-settings&id=3');
        $language = $I->grabValueFrom('#mlp-site-language-tag');
        $I->assertSame($language, 'it-IT');
    }

    public function migrateAlternativeLanguageTitle(AcceptanceTester $I)
    {
        // add alternative language title in sites 1 and 3
        $I->amOnPage('/wp-admin/network/sites.php?page=mlp-site-settings&id=1');
        $I->fillField('#inpsyde_multilingual_text', 'This is alternate title for site 1');
        $I->click('#submit');
        $I->amOnPage('/wp-admin/network/sites.php?page=mlp-site-settings&id=3');
        $I->fillField('#inpsyde_multilingual_text', 'This is alternate title for site 3');
        $I->click('#submit');

        // run the tool
        $this->runTheTool($I);

        // MLP3 enable alternative language title module
        $I->amOnPage('/wp-admin/network/admin.php?page=multilingualpress');
        $I->checkOption('#multilingualpress-module-alternative_language_title');
        $I->click('#submit');

        // MLP3 check alternative language title in sites 1 and 3
        $I->amOnPage('/wp-admin/network/sites.php?page=multilingualpress-site-settings&id=1');
        $I->seeInField('#multilingualpress_alt_language_title', 'This is alternate title for site 1');
        $I->amOnPage('/wp-admin/network/sites.php?page=multilingualpress-site-settings&id=3');
        $I->seeInField('#multilingualpress_alt_language_title', 'This is alternate title for site 3');
    }

    private function runTheTool(AcceptanceTester $I)
    {
        // deactivate MLP2 and activate MLP3
        $I->amOnPage('/wp-admin/network/plugins.php');
        $I->click('[data-plugin="multilingual-press/multilingual-press.php"] .deactivate a');
        $I->click('[data-plugin="multilingualpress/multilingualpress.php"] .activate a');

        // run the tool
        $I->runShellCommand('wp mlp2to3 site_languages --allow-root --path=wordpress-site');
        $I->seeInShellOutput('Success:');
    }
}
