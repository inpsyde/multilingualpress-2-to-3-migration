<?php # -*- coding: utf-8 -*-
declare(strict_types=1);

class ModulesCest
{
    public function _before(AcceptanceTester $I)
    {
        $I->amOnPage('/wp-login.php');
        $I->fillField(['name' => 'log'], 'admin');
        $I->fillField(['name' => 'pwd'], 'password');
        $I->click('#wp-submit');
    }

    public function migrateModules(AcceptanceTester $I)
    {
        // go to mLP2 settings page
        $I->amOnPage('/wp-admin/network/settings.php?page=mlp');

        // check Alternative Language Title checkbox
        $I->checkOption('#id_mlp_state_class-Mlp_Alternative_Language_Title_Module');
        $I->click('Save changes');

        // ensure modules are checked in MLP2
        $I->seeCheckboxIsChecked('#id_mlp_state_class-Mlp_Alternative_Language_Title_Module');
        $I->seeCheckboxIsChecked('#id_mlp_state_class-Mlp_Redirect_Registration');
        $I->seeCheckboxIsChecked('#id_mlp_state_class-Mlp_Trasher');

        // run the tool
        $this->runTheTool($I);

        // check modules are enabled in MLP3
        $I->amOnPage('/wp-admin/network/admin.php?page=multilingualpress');
        $I->seeCheckboxIsChecked('#multilingualpress-module-alternative_language_title');
        $I->seeCheckboxIsChecked('#multilingualpress-module-redirect');
        $I->seeCheckboxIsChecked('#multilingualpress-module-trasher');
    }

    private function runTheTool(AcceptanceTester $I)
    {
        // deactivate MLP2 and activate MLP3
        $I->amOnPage('/wp-admin/network/plugins.php');
        $I->click('[data-plugin="multilingual-press/multilingual-press.php"] .deactivate a');
        $I->click('[data-plugin="multilingualpress/multilingualpress.php"] .activate a');

        // run the tool
        $I->runShellCommand('wp mlp2to3 modules --allow-root --path=wordpress-site');
        $I->seeInShellOutput('Success:');
    }
}
