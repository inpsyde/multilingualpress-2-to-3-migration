<?php # -*- coding: utf-8 -*-
declare(strict_types=1);

class RedirectsCest
{
    public function _before(AcceptanceTester $I)
    {
        $I->amOnPage('/wp-login.php');
        $I->fillField(['name' => 'log'], 'admin');
        $I->fillField(['name' => 'pwd'], 'password');
        $I->click('#wp-submit');
    }

    public function migrateRedirects(AcceptanceTester $I)
    {
        // MLP2 enable redirection in sites 1 and 3
        $I->amOnPage('/wp-admin/network/site-settings.php?id=1&extra=mlp-site-settings');
        $I->checkOption('#inpsyde_multilingual_redirect_id');
        $I->click('Save Changes');
        $I->amOnPage('/wp-admin/network/site-settings.php?id=3&extra=mlp-site-settings');
        $I->checkOption('#inpsyde_multilingual_redirect_id');
        $I->click('Save Changes');

        // run the tool
        $this->runTheTool($I);

        // MLP3 enable redirection module
        $I->amOnPage('/wp-admin/network/admin.php?page=multilingualpress');
        $I->checkOption('#multilingualpress-module-redirect');
        $I->click('Save Changes');

        // check redirection in MLP3
        $I->amOnPage('/wp-admin/network/sites.php?page=multilingualpress-site-settings&id=1');
        $I->seeCheckboxIsChecked('#multilingualpress_redirect');
        $I->amOnPage('/wp-admin/network/sites.php?page=multilingualpress-site-settings&id=3');
        $I->seeCheckboxIsChecked('#multilingualpress_redirect');
    }

    private function runTheTool(AcceptanceTester $I)
    {
        // deactivate MLP2 and activate MLP3
        $I->amOnPage('/wp-admin/network/plugins.php');
        $I->click('[data-plugin="multilingual-press/multilingual-press.php"] .deactivate a');
        $I->click('[data-plugin="multilingualpress/multilingualpress.php"] .activate a');

        // run the tool
        $I->runShellCommand('wp mlp2to3 redirects --path=wordpress-site');
        $I->seeInShellOutput('Success:');
    }
}
