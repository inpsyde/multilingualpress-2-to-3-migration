<?php

class FirstCest
{
    public function seeInWordpressHome(AcceptanceTester $I)
    {
        $I->amOnPage('/');
        $I->see('Just another WordPress site');
    }

    public function seeInWordpressDatabase(AcceptanceTester $I)
    {
        $I->seeInDatabase('wp_posts', [
            'ID' => '1',
            'post_title' => 'Hello World!',
        ]);
    }

    public function checkWpcliAvailableInHostMachine(AcceptanceTester $I)
    {
        $I->runShellCommand('wp --version');
        $I->seeInShellOutput('WP-CLI');
    }

    public function runWpcliCommandInWordpressPath(AcceptanceTester $I)
    {
        $I->runShellCommand('wp post list --path=wordpress-site');
        $I->seeInShellOutput('hello-world');
    }

    public function runCustomWpcliCommand(AcceptanceTester $I)
    {
        $I->runShellCommand('wp hello world --path=wordpress-site');
        $I->seeInShellOutput('Success: world');
    }
}
