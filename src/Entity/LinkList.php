<?php

declare(strict_types = 1);

namespace Drupal\oe_link_lists\Entity;

use Drupal\Core\Entity\EditorialContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Defines the LinkList entity.
 *
 * @ingroup oe_link_lists
 *
 * @ContentEntityType(
 *   id = "link_list",
 *   label = @Translation("Link list"),
 *   bundle_label = @Translation("Link list type"),
 *   handlers = {
 *     "view_builder" = "Drupal\oe_link_lists\LinkListViewBuilder",
 *     "list_builder" = "Drupal\oe_link_lists\LinkListListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "translation" = "Drupal\content_translation\ContentTranslationHandler",
 *     "form" = {
 *       "default" = "Drupal\oe_link_lists\Form\LinkListForm",
 *       "add" = "Drupal\oe_link_lists\Form\LinkListForm",
 *       "edit" = "Drupal\oe_link_lists\Form\LinkListForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "link_list",
 *   data_table = "link_list_field_data",
 *   revision_table = "link_list_revision",
 *   revision_data_table = "link_list_field_revision",
 *   translatable = TRUE,
 *   admin_permission = "administer link_lists",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "vid",
 *     "uuid" = "uuid",
 *     "label" = "administrative_title",
 *     "bundle" = "bundle",
 *     "langcode" = "langcode",
 *     "published" = "status",
 *     "created" = "created",
 *     "changed" = "changed",
 *   },
 *   revision_metadata_keys = {
 *     "revision_user" = "revision_uid",
 *     "revision_created" = "revision_timestamp",
 *     "revision_log_message" = "revision_log"
 *   },
 *   links = {
 *     "add-form" = "/link_list/add/{link_list_type}",
 *     "add-page" = "/link_list/add",
 *     "canonical" = "/link_list/{link_list}",
 *     "collection" = "/admin/content/link_lists",
 *     "edit-form" = "/link_list/{link_list}/edit",
 *     "delete-form" = "/link_list/{link_list}/delete",
 *     "delete-multiple-form" = "/admin/content/link_list/delete",
 *   },
 *   bundle_entity_type = "link_list_type",
 *   field_ui_base_route = "entity.link_list_type.edit_form"
 * )
 */
class LinkList extends EditorialContentEntityBase implements LinkListInterface {

  /**
   * {@inheritdoc}
   */
  public function getAdministrativeTitle(): string {
    return (string) $this->get('administrative_title')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setAdministrativeTitle(string $administrative_title): LinkListInterface {
    $this->set('administrative_title', $administrative_title);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getConfiguration(): array {
    return !$this->get('configuration')->isEmpty() ? unserialize($this->get('configuration')->value) : [];
  }

  /**
   * {@inheritdoc}
   */
  public function setConfiguration(array $configuration): LinkListInterface {
    $this->set('configuration', serialize($configuration));
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getTitle(): ?string {
    return $this->get('title')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setTitle(string $title): LinkListInterface {
    $this->set('title', $title);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime(): int {
    return (int) $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime(int $timestamp): LinkListInterface {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function save() {
    parent::save();

    // Invalidate the block cache to update the derivatives.
    \Drupal::service('plugin.manager.block')->clearCachedDefinitions();
  }

  /**
   * {@inheritdoc}
   */
  public function delete() {
    parent::delete();

    // Invalidate the block cache to update the derivatives.
    \Drupal::service('plugin.manager.block')->clearCachedDefinitions();
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['administrative_title'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Administrative title'))
      ->setRequired(TRUE)
      ->setRevisionable(TRUE)
      ->setSetting('max_length', 255)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -5,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['title'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Title'))
      ->setTranslatable(TRUE)
      ->setRevisionable(TRUE)
      ->setSetting('max_length', 255)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -5,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['status']
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'settings' => [
          'display_label' => TRUE,
        ],
        'weight' => 120,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['configuration'] = BaseFieldDefinition::create('link_list_configuration')
      ->setLabel(t('Configuration'))
      ->setDescription(t('The list configuration.'))
      ->setRevisionable(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'link_list_configuration',
        'weight' => 4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDefaultValue(serialize([]));

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Authored on'))
      ->setDescription(t('The time that the list was created.'))
      ->setRevisionable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'timestamp',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'datetime_timestamp',
        'weight' => 10,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the list was last edited.'))
      ->setRevisionable(TRUE);

    return $fields;
  }

}
