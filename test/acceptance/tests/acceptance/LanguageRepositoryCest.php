<?php # -*- coding: utf-8 -*-
declare(strict_types=1);

class LanguageRepositoryCest
{
    public function _before(AcceptanceTester $I)
    {
        $I->amOnPage('/wp-login.php');
        $I->fillField(['name' => 'log'], 'admin');
        $I->fillField(['name' => 'pwd'], 'password');
        $I->click('#wp-submit');
    }

    public function importLanguageRepository(AcceptanceTester $I)
    {
        // MLP2 go to language manager and create a new language editing an existing one
        $I->amOnPage('/wp-admin/network/settings.php?page=language-manager');
        $I->fillField(['name' => 'languages[1][native_name]'], 'Invented');
        $I->fillField(['name' => 'languages[1][english_name]'], 'Inv');
        $I->fillField(['name' => 'languages[1][http_name]'], 'inv');
        $I->fillField(['name' => 'languages[1][iso_639_1]'], 'in');
        $I->fillField(['name' => 'languages[1][priority]'], '2');
        $I->click('#save');

        // run the tool
        $this->runTheTool($I);

        // MLP3 Enable language manager
        $I->amOnPage('/wp-admin/network/admin.php?page=multilingualpress');
        $I->checkOption('#multilingualpress-module-language-manager');
        $I->click('#submit');

        // MLP3 check language manager
        $I->amOnPage('/wp-admin/network/admin.php?page=language-manager');

        // check if custom language exists
        $I->seeInField(['name' => 'languages[1][native_name]'], 'Invented');
        $I->seeInField(['name' => 'languages[1][english_name]'], 'Inv');
        $I->seeInField(['name' => 'languages[1][iso_639_1]'], 'in');
        $I->seeInField(['name' => 'languages[1][iso_639_2]'], 'inv');

        // check that default language does not exist
        $I->dontSeeInField(['name' => 'languages[55][english_name]'], 'Spanish (Spain)');
        $I->dontSeeInField(['name' => 'languages[55][iso_639_1]'], 'es');
    }

    private function runTheTool(AcceptanceTester $I)
    {
        // deactivate MLP2 and activate MLP3
        $I->amOnPage('/wp-admin/network/plugins.php');
        $I->click('[data-plugin="multilingual-press/multilingual-press.php"] .deactivate a');
        $I->click('[data-plugin="multilingualpress/multilingualpress.php"] .activate a');

        // run the tool
        $I->runShellCommand('wp mlp2to3 languages --path=wordpress-site');
        $I->seeInShellOutput('Success:');
    }
}
