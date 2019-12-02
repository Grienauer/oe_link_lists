<?php

declare(strict_types = 1);

namespace Drupal\Tests\oe_link_lists_internal_source\FunctionalJavascript;

/**
 * Tests the alterability of the internal link source plugin.
 *
 * This test should have been placed in the plugin test, but the internal source
 * test module provides also filters, so the expected configuration would be
 * changed.
 *
 * @group oe_link_lists
 */
class InternalLinkSourceAlterabilityTest extends InternalLinkSourceTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'oe_link_lists_internal_source_test',
    'node',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->drupalCreateContentType([
      'type' => 'page',
      'name' => 'Basic page',
    ]);
    $this->drupalCreateContentType([
      'type' => 'article',
      'name' => 'Article',
    ]);
  }

  /**
   * Test that the entity type select can be altered through an event.
   */
  public function testEntityTypeAlter(): void {
    $web_user = $this->drupalCreateUser(['administer link_lists']);
    $this->drupalLogin($web_user);

    $this->drupalGet('link_list/add');
    $this->getSession()->getPage()->fillField('Administrative title', 'Internal plugin test');
    $this->getSession()->getPage()->fillField('Title', 'Internal list');
    $this->getSession()->getPage()->selectFieldOption('Link display', 'Foo');
    $this->assertSession()->assertWaitOnAjaxRequest();

    $this->getSession()->getPage()->selectFieldOption('Link source', 'Internal');
    $this->assertSession()->assertWaitOnAjaxRequest();
    $select = $this->assertSession()->selectExists('Entity type');
    $this->assertEquals([
      '- Select -' => '- Select -',
      'link_list' => 'Link list',
      'node' => 'Content',
      'user' => 'User',
    ], $this->getOptions($select));

    // Select the user option and save the content.
    $this->getSession()->getPage()->selectFieldOption('Entity type', 'user');
    $this->assertSession()->assertWaitOnAjaxRequest();
    $this->getSession()->getPage()->pressButton('Save');

    // Disable the user bundle from the list of selectable entity types.
    \Drupal::service('state')->set('internal_source_allowed_entity_types', ['node', 'link_list']);

    // Go to the edit page of the link list.
    $link_list = $this->getLinkListByTitle('Internal list');
    $this->drupalGet($link_list->toUrl('edit-form'));

    // The user option should not be selectable anymore.
    $select = $this->assertSession()->selectExists('Entity type');
    $this->assertEquals('', $select->getValue());
    $this->assertEquals([
      '- Select -' => '- Select -',
      'link_list' => 'Link list',
      'node' => 'Content',
    ], $this->getOptions($select));

    // Leave only node enabled.
    \Drupal::service('state')->set('internal_source_allowed_entity_types', ['node']);

    $this->drupalGet($link_list->toUrl('edit-form'));
    $select = $this->assertSession()->selectExists('Entity type');
    $this->assertEquals([
      '- Select -' => '- Select -',
      'node' => 'Content',
    ], $this->getOptions($select));

    // Verify that the bundle select appears correctly.
    $select->selectOption('node');
    $this->assertSession()->assertWaitOnAjaxRequest();
    $select = $this->assertSession()->selectExists('Bundle');
    $this->assertEquals([
      '- Select -' => '- Select -',
      'page' => 'Basic page',
      'article' => 'Article',
    ], $this->getOptions($select));

    // Complete the selection to verify that no errors are triggered.
    $select->selectOption('article');
    $this->assertSession()->assertWaitOnAjaxRequest();
    $this->getSession()->getPage()->pressButton('Save');

    // Verify the configuration.
    $link_list = $this->getLinkListByTitle('Internal list', TRUE);
    $this->assertEquals([
      'entity_type' => 'node',
      'bundle' => 'article',
      'filters' => [],
    ], $link_list->getConfiguration()['source']['plugin_configuration']);
  }

}
