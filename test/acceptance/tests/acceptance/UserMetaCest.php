<?php # -*- coding: utf-8 -*-
declare(strict_types=1);

class UserMetaCest
{
    public function _before(AcceptanceTester $I)
    {
        $I->amOnPage('/wp-login.php');
        $I->fillField(['name' => 'log'], 'admin');
        $I->fillField(['name' => 'pwd'], 'password');
        $I->click('#wp-submit');
    }

    public function importUserLanguageRedirect(AcceptanceTester $I)
    {
        // go to user and check Language redirect checkbox
        $I->amOnPage('/wp-admin/network/profile.php?wp_http_referer=%2Fwp-admin%2Fnetwork%2Fusers.php');
        $I->checkOption('#mlp_redirect_id');
        $I->click('#submit');
        $I->see('Profile updated.');
        $I->seeCheckboxIsChecked('#mlp_redirect_id');

        // run the tool
        $this->runTheTool($I);

        // MLP3 enable redirection module
        $I->amOnPage('/wp-admin/network/admin.php?page=multilingualpress');
        $I->checkOption('#multilingualpress-module-redirect');
        $I->click('Save Changes');

        // MLP3 check user Language redirect checkbox
        $I->amOnPage('/wp-admin/network/profile.php?wp_http_referer=%2Fwp-admin%2Fnetwork%2Fusers.php');
        $I->seeCheckboxIsChecked('#multilingualpress_redirect');
    }

    private function runTheTool(AcceptanceTester $I)
    {
        // deactivate MLP2 and activate MLP3
        $I->amOnPage('/wp-admin/network/plugins.php');
        $I->click('[data-plugin="multilingual-press/multilingual-press.php"] .deactivate a');
        $I->click('[data-plugin="multilingualpress/multilingualpress.php"] .activate a');

        // run the tool
//        $I->runShellCommand('wp mlp2to3 unknown --path=wordpress-site');
//        $I->seeInShellOutput('Success: Migration complete');
    }
}
