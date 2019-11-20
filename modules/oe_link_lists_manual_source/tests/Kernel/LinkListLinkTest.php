<?php

declare(strict_types = 1);

namespace Drupal\Tests\oe_link_lists_manual_source\Kernel;

use Drupal\KernelTests\Core\Entity\EntityKernelTestBase;

/**
 * Tests the Link list link entity.
 */
class LinkListLinkTest extends EntityKernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'oe_link_lists',
    'oe_link_lists_manual_source',
    'entity_reference_revisions',
    'inline_entity_form',
    'node',
    'user',
    'field',
    'system',
    'link',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installEntitySchema('user');
    $this->installEntitySchema('node');
    $this->installEntitySchema('link_list_link');
    $this->installSchema('node', 'node_access');
    $this->installConfig([
      'field',
      'node',
      'system',
      'oe_link_lists_manual_source',
      'entity_reference_revisions',
    ]);

  }

  /**
   * Tests Link list link entities.
   */
  public function testLinkListLink(): void {
    /** @var \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager */
    $entity_type_manager = $this->container->get('entity_type.manager');

    // Create a content type.
    $node_type_storage = $entity_type_manager->getStorage('node_type');
    $type = $node_type_storage->create(['name' => 'Test content type', 'type' => 'test_ct']);
    $type->save();

    $values = [
      'type' => 'test_ct',
      'title' => 'My node title',
    ];

    // Create a node.
    $node_storage = $entity_type_manager->getStorage('node');
    $node = $node_storage->create($values);
    $node->save();

    $link_storage = $entity_type_manager->getStorage('link_list_link');

    // Create an internal link.
    /** @var \Drupal\oe_link_lists_manual_source\Entity\LinkListLinkInterface $link_entity */
    $link_entity = $link_storage->create([
      'bundle' => 'internal',
      'target' => $node->id(),
      'status' => 1,
    ]);
    $link_entity->save();

    $link_entity = $link_storage->load($link_entity->id());
    // Asserts that the internal link was correctly saved.
    $this->assertEquals($node->id(), $link_entity->get('target')->target_id);

    // Create a valid external link.
    /** @var \Drupal\oe_link_lists_manual_source\Entity\LinkListLinkInterface $link_entity */
    $link_entity = $link_storage->create([
      'bundle' => 'external',
      'url' => 'http://example.com',
      'title' => 'Example title',
      'teaser' => 'Example teaser',
      'status' => 1,
    ]);
    $link_entity->save();

    $link_entity = $link_storage->load($link_entity->id());
    // Asserts that external link was correctly saved.
    $this->assertEquals('http://example.com', $link_entity->get('url')->uri);
    $this->assertEquals('Example title', $link_entity->getTitle());
    $this->assertEquals('Example teaser', $link_entity->getTeaser());

    // Create an invalid external link.
    /** @var \Drupal\oe_link_lists_manual_source\Entity\LinkListLinkInterface $link_entity */
    $link_entity = $link_storage->create([
      'bundle' => 'external',
      'url' => 'http://example.com',
      'status' => 1,
    ]);
    $link_entity->save();

    $link_entity = $link_storage->load($link_entity->id());
    // Assert that an external link needs a title and a teaser.
    /** @var \Drupal\Core\Entity\EntityConstraintViolationListInterface $violations */
    $violations = $link_entity->validate();
    $this->assertEquals(2, $violations->count());
    $violation = $violations->get(0);
    $this->assertEquals('Title field is required.', $violation->getMessage());
    $violation = $violations->get(1);
    $this->assertEquals('Teaser field is required.', $violation->getMessage());
  }

}
