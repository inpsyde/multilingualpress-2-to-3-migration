<?php # -*- coding: utf-8 -*-
declare(strict_types=1);

class SampleCest
{
    public function seeWordpressSite(AcceptanceTester $I)
    {
        $I->amOnPage('/');
        $I->see('Just another WordPress site');
    }

    public function seeInDatabase(AcceptanceTester $I)
    {
        $I->seeInDatabase('wp_options', ['option_name' => 'siteurl']);
    }

    public function seeWpCli(AcceptanceTester $I)
    {
        $I->runShellCommand('wp --allow-root --version');
        $I->seeInShellOutput('WP-CLI');
    }

    public function seeDatabase(AcceptanceTester $I)
    {
        $I->runShellCommand('wp post list --allow-root --path=wordpress-site');
        $I->seeInShellOutput('hello-world');
    }

    public function seeMlp2To3Command(AcceptanceTester $I)
    {
        $I->runShellCommand('wp mlp2to3 --help --allow-root --path=wordpress-site');
        $I->seeInShellOutput('wp mlp2to3');
    }
}
