<?php

class ContentRelationshipsCest
{
    public function _before(AcceptanceTester $I)
    {
        $I->amOnPage('/wp-login.php');
        $I->fillField(['name' => 'log'], 'admin');
        $I->fillField(['name' => 'pwd'], 'password');
        $I->click('#wp-submit');
    }

    public function twoSitesDefaultWordpressContent(AcceptanceTester $I)
    {
        // create a new site based on site one
        $I->amOnPage('/wp-admin/network/site-new.php');
        $I->fillField('#site-address', 'test');
        $I->fillField('#site-title', 'test');
        $I->fillField('#admin-email', 'admin@example.com');
        $I->selectOption('#mlp-site-language', 'de-DE');
        $I->checkOption('#related_blog_1');
        $I->selectOption('#mlp-base-site-id', '1');
        $I->click('Add Site');

        // create a term relationship
        $I->amOnPage('/test/wp-admin/term.php?taxonomy=category&tag_ID=1&post_type=post');
        $I->selectOption('#mlpterm_translation1', 'Uncategorized');
        $I->click('.button-primary');
        $I->see('Category updated.');

        // deactivate MLP2 and activate MLP3
        $I->amOnPage('/wp-admin/network/plugins.php');
        $I->click('[data-plugin="multilingual-press/multilingual-press.php"] .deactivate a');
        $I->click('[data-plugin="multilingualpress/multilingualpress.php"] .activate a');

        // run the tool
        $I->runShellCommand('wp mlp2to3 relationships --path=wordpress-site');
        $I->seeInShellOutput('Success: Migrated');

        // check mlp3 wp_mlp_content_relations table
        $I->seeInDatabase('wp_mlp_content_relations', [
            'relationship_id' => '1',
            'site_id' => '1',
            'content_id' => '3',
        ]);
        $I->seeInDatabase('wp_mlp_content_relations', [
            'relationship_id' => '1',
            'site_id' => '4',
            'content_id' => '3',
        ]);
        $I->seeInDatabase('wp_mlp_content_relations', [
            'relationship_id' => '2',
            'site_id' => '1',
            'content_id' => '2',
        ]);
        $I->seeInDatabase('wp_mlp_content_relations', [
            'relationship_id' => '2',
            'site_id' => '4',
            'content_id' => '2',
        ]);
        $I->seeInDatabase('wp_mlp_content_relations', [
            'relationship_id' => '3',
            'site_id' => '1',
            'content_id' => '1',
        ]);
        $I->seeInDatabase('wp_mlp_content_relations', [
            'relationship_id' => '3',
            'site_id' => '4',
            'content_id' => '1',
        ]);
        $I->seeInDatabase('wp_mlp_content_relations', [
            'relationship_id' => '4',
            'site_id' => '1',
            'content_id' => '1',
        ]);
        $I->seeInDatabase('wp_mlp_content_relations', [
            'relationship_id' => '4',
            'site_id' => '4',
            'content_id' => '1',
        ]);

        // check mlp3 wp_mlp_relationships table
        $I->seeInDatabase('wp_mlp_relationships', [
            'id' => '1',
            'type' => 'post',
        ]);
        $I->seeInDatabase('wp_mlp_relationships', [
            'id' => '2',
            'type' => 'post',
        ]);
        $I->seeInDatabase('wp_mlp_relationships', [
            'id' => '3',
            'type' => 'post',
        ]);
        $I->seeInDatabase('wp_mlp_relationships', [
            'id' => '4',
            'type' => 'term',
        ]);
    }
}
