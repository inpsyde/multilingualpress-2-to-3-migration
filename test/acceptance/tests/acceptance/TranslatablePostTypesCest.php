<?php # -*- coding: utf-8 -*-
declare(strict_types=1);

class TranslatablePostTypesCest
{
    public function _before(AcceptanceTester $I)
    {
        $I->amOnPage('/wp-login.php');
        $I->fillField(['name' => 'log'], 'admin');
        $I->fillField(['name' => 'pwd'], 'password');
        $I->click('#wp-submit');
    }

    public function migrateCustomPostTypes(AcceptanceTester $I)
    {
        // go to MLP2 settings page
        $I->amOnPage('/wp-admin/network/settings.php?page=mlp');

        // check both Fake Post Type and Use dynamic permalinks checkboxes in Custom Post Type Translator Settings
        $I->checkOption('#mlp_cpt_fake');

        // TODO is not currently possible to find this element name="mlp_cpts[fake|links]" or id="mlp_cpt_fake|links"
        //$I->click('input[name=mlp_cpts[fake|links]]');

        $I->click('#submit');

        // run the tool
        $this->runTheTool($I);

        // MLP3 check that post type is enabled in
        $I->amOnPage('/wp-admin/network/admin.php?page=multilingualpress&tab=post-types');
        $I->seeCheckboxIsChecked('#mlp-post-type-fake');

        // TODO current not testeable because of issue in MLP2 naming element (#mlp_cpt_fake|links)
        //$I->seeCheckboxIsChecked('#mlp-post-type-fake-permalinks');
    }

    private function runTheTool(AcceptanceTester $I)
    {
        // deactivate MLP2 and activate MLP3
        $I->amOnPage('/wp-admin/network/plugins.php');
        $I->click('[data-plugin="multilingual-press/multilingual-press.php"] .deactivate a');
        $I->click('[data-plugin="multilingualpress/multilingualpress.php"] .activate a');

        // run the tool
        $I->runShellCommand('wp mlp2to3 translatable_post_types --path=wordpress-site');
        $I->seeInShellOutput('Success:');
    }
}
