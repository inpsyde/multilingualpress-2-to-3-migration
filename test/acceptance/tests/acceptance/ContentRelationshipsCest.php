<?php # -*- coding: utf-8 -*-
declare(strict_types=1);

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

        // run the tool
        $this->runTheTool($I);

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
    }

    public function postConnectedIn3SitesInATwoSiteRelationship(AcceptanceTester $I)
    {
        // 3 sites connected in this way: 1 -> 2 and 3 -> 1
        $I->amOnPage('/wp-admin/network/sites.php?page=mlp-site-settings&id=1');
        $I->checkOption('#related_blog_2');
        $I->click('Save Changes');
        $I->amOnPage('/wp-admin/network/sites.php?page=mlp-site-settings&id=3');
        $I->checkOption('#related_blog_1');
        $I->click('Save Changes');

        // Go to post in site A and connect it to sites B and C
        $I->amOnPage('/wp-admin/post.php?post=1&action=edit');
        $I->fillField('#mlp-translation-data-2-title', 'Post Site B');
        $I->fillField('#mlp-translation-data-3-title', 'Post Site C');
        $I->click('#publish');

        // run the tool
        $this->runTheTool($I);

        // check mlp3 wp_mlp_content_relations table
        $I->seeInDatabase('wp_mlp_content_relations', [
            'relationship_id' => '1',
            'site_id' => '1',
            'content_id' => '1',
        ]);
        $I->seeInDatabase('wp_mlp_content_relations', [
            'relationship_id' => '1',
            'site_id' => '2',
            'content_id' => '4',
        ]);
        $I->seeInDatabase('wp_mlp_content_relations', [
            'relationship_id' => '1',
            'site_id' => '3',
            'content_id' => '3',
        ]);

        // check mlp3 wp_mlp_relationships table
        $I->seeInDatabase('wp_mlp_relationships', [
            'id' => '1',
            'type' => 'post',
        ]);
    }

    public function multiplePostAndTermRelationships(AcceptanceTester $I)
    {
        // connect 3 sites together
        $I->amOnPage('/wp-admin/network/sites.php?page=mlp-site-settings&id=1');
        $I->checkOption('#related_blog_2');
        $I->checkOption('#related_blog_3');
        $I->click('Save Changes');
        $I->amOnPage('/wp-admin/network/sites.php?page=mlp-site-settings&id=2');
        $I->checkOption('#related_blog_3');
        $I->click('Save Changes');

        // create category terms
        $I->amOnPage('/wp-admin/edit-tags.php?taxonomy=category');
        $I->fillField('#tag-name', 'A');
        $I->click('#submit');
        $I->fillField('#tag-name', 'B');
        $I->click('#submit');
        $I->fillField('#tag-name', 'C');
        $I->click('#submit');
        $I->amOnPage('/es/wp-admin/edit-tags.php?taxonomy=category');
        $I->fillField('#tag-name', 'D');
        $I->click('#submit');
        $I->fillField('#tag-name', 'E');
        $I->click('#submit');
        $I->fillField('#tag-name', 'F');
        $I->click('#submit');
        $I->amOnPage('/it/wp-admin/edit-tags.php?taxonomy=category');
        $I->fillField('#tag-name', 'G');
        $I->click('#submit');
        $I->fillField('#tag-name', 'H');
        $I->click('#submit');
        $I->fillField('#tag-name', 'I');
        $I->click('#submit');

        // create relations like so:
        // A -> D -> G
        // B -> E -> H
        // C -> F -> I
        $I->amOnPage('/wp-admin/edit-tags.php?taxonomy=category');
        $I->click('A');
        $I->selectOption('#mlpterm_translation2', 'D');
        $I->selectOption('#mlpterm_translation3', 'G');
        $I->click('.button-primary');

        $I->amOnPage('/wp-admin/post-new.php');
        $I->fillField('#title', 'A');
        $I->fillField('#mlp-translation-data-2-title', 'D');
        $I->click('#publish');
        $I->amOnPage('/wp-admin/post-new.php');
        $I->fillField('#title', 'B');
        $I->fillField('#mlp-translation-data-2-title', 'E');
        $I->click('#publish');

        $I->amOnPage('/wp-admin/edit-tags.php?taxonomy=category');
        $I->click('B');
        $I->selectOption('#mlpterm_translation3', 'H');
        $I->click('.button-primary');

        $I->amOnPage('/wp-admin/post-new.php');
        $I->fillField('#title', 'C');
        $I->fillField('#mlp-translation-data-2-title', 'F');
        $I->click('#publish');

        $I->amOnPage('/wp-admin/edit-tags.php?taxonomy=category');
        $I->click('B');
        $I->selectOption('#mlpterm_translation2', 'E');
        $I->click('.button-primary');

        $I->amOnPage('/es/wp-admin/edit.php');
        $I->click('D');
        $I->fillField('#mlp-translation-data-3-title', 'G');
        $I->click('#publish');
        $I->amOnPage('/es/wp-admin/edit.php');
        $I->click('E');
        $I->fillField('#mlp-translation-data-3-title', 'H');
        $I->click('#publish');

        $I->amOnPage('/wp-admin/edit-tags.php?taxonomy=category');
        $I->click('C');
        $I->selectOption('#mlpterm_translation2', 'F');
        $I->selectOption('#mlpterm_translation3', 'I');
        $I->click('.button-primary');

        $I->amOnPage('/es/wp-admin/edit.php');
        $I->click('F');
        $I->fillField('#mlp-translation-data-3-title', 'I');
        $I->click('#publish');

        // run the tool
        $this->runTheTool($I);

        $I->amOnPage('/wp-admin/edit.php');
        $I->click('A');
        $I->see('Currently connected with "D"');
        $I->see('Currently connected with "G"');
        $I->amOnPage('/wp-admin/edit.php');
        $I->click('B');
        $I->see('Currently connected with "E"');
        $I->see('Currently connected with "H"');
        $I->amOnPage('/wp-admin/edit.php');
        $I->click('C');
        $I->see('Currently connected with "F"');
        $I->see('Currently connected with "I"');

        $I->amOnPage('/es/wp-admin/edit.php');
        $I->click('D');
        $I->see('Currently connected with "A"');
        $I->see('Currently connected with "G"');
        $I->amOnPage('/es/wp-admin/edit.php');
        $I->click('E');
        $I->see('Currently connected with "B"');
        $I->see('Currently connected with "H"');
        $I->amOnPage('/es/wp-admin/edit.php');
        $I->click('F');
        $I->see('Currently connected with "C"');
        $I->see('Currently connected with "I"');

        $I->amOnPage('/it/wp-admin/edit.php');
        $I->click('G');
        $I->see('Currently connected with "D"');
        $I->see('Currently connected with "A"');
        $I->amOnPage('/it/wp-admin/edit.php');
        $I->click('H');
        $I->see('Currently connected with "E"');
        $I->see('Currently connected with "B"');
        $I->amOnPage('/it/wp-admin/edit.php');
        $I->click('I');
        $I->see('Currently connected with "F"');
        $I->see('Currently connected with "C"');
    }

    private function runTheTool(AcceptanceTester $I)
    {
        // deactivate MLP2 and activate MLP3
        $I->amOnPage('/wp-admin/network/plugins.php');
        $I->click('[data-plugin="multilingual-press/multilingual-press.php"] .deactivate a');
        $I->click('[data-plugin="multilingualpress/multilingualpress.php"] .activate a');

        // run the tool
        $I->runShellCommand('wp mlp2to3 relationships --allow-root --path=wordpress-site');
        $I->seeInShellOutput('Success:');
    }
}
