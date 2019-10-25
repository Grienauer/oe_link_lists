<?php

declare(strict_types = 1);

namespace Drupal\oe_link_lists_manual_source\Entity;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\EditorialContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Defines the LinkListLink entity.
 *
 * @ingroup oe_link_lists
 *
 * @ContentEntityType(
 *   id = "link_list_link",
 *   label = @Translation("Link list link"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\oe_link_lists_manual_source\LinkListLinkListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "translation" = "Drupal\content_translation\ContentTranslationHandler",
 *     "form" = {
 *       "default" = "Drupal\oe_link_lists_manual_source\Form\LinkListLinkForm",
 *       "add" = "Drupal\oe_link_lists_manual_source\Form\LinkListLinkForm",
 *       "edit" = "Drupal\oe_link_lists_manual_source\Form\LinkListLinkForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     },
 *     "form_builder" = "Drupal\oe_link_lists_manual_source\Form\LinkListLinkFormBuilder"
 *   },
 *   base_table = "link_list_link",
 *   data_table = "link_list_link_field_data",
 *   revision_table = "link_list_link_revision",
 *   revision_data_table = "link_list_link_field_revision",
 *   translatable = TRUE,
 *   admin_permission = "administer link list link entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "vid",
 *     "uuid" = "uuid",
 *     "langcode" = "langcode",
 *     "published" = "status",
 *   },
 *   revision_metadata_keys = {
 *     "revision_user" = "revision_uid",
 *     "revision_created" = "revision_timestamp",
 *     "revision_log_message" = "revision_log"
 *   },
 *   links = {
 *     "add-form" = "/admin/content/link_list_link/add",
 *     "edit-form" = "/admin/content/link_list_link/{link_list_link}/edit",
 *     "delete-form" = "/admin/content/link_list_link/{link_list_link}/delete",
 *     "collection" = "/admin/content/link_list_link",
 *   },
 *   constraints = {
 *     "LinkListLinkFieldsRequired" = {}
 *   }
 * )
 */
class LinkListLink extends EditorialContentEntityBase implements LinkListLinkInterface {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function label(): TranslatableMarkup {
    if ($this->getUrl()) {
      return $this->t('External link to: @external_url', ['@external_url' => $this->getUrl()]);
    }

    if ($this->getTargetEntity()) {
      return $this->t('Internal link to: @internal_entity', ['@internal_entity' => $this->getTargetEntity()->label()]);
    }

    return $this->t('Internal link');
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime(): int {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime(int $timestamp): LinkListLinkInterface {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getTargetEntity(): ?EntityInterface {
    return $this->get('target')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getTargetId() {
    return $this->get('target')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setTargetId($target_id): LinkListLinkInterface {
    $this->set('target', $target_id);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getTeaser(): ?string {
    return $this->get('teaser')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setTeaser(string $teaser): LinkListLinkInterface {
    $this->set('teaser', $teaser);
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
  public function setTitle(string $title): LinkListLinkInterface {
    $this->set('title', $title);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getUrl(): ?string {
    return $this->get('url')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setUrl(string $url): LinkListLinkInterface {
    $this->set('url', $url);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type): array {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['url'] = BaseFieldDefinition::create('uri')
      ->setLabel('URL')
      ->setDescription(t('An external URL.'))
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE)
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(FALSE);

    $fields['target'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Target'))
      ->setDescription(t('The target node of the internal link.'))
      ->setSetting('target_type', 'node')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'entity_reference_label',
        'settings' => [
          'link' => TRUE,
        ],
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 0,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => 60,
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(FALSE)
      ->setDefaultValue(0);

    $fields['title'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Title'))
      ->setDescription(t('The title of the link.'))
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE)
      ->setSettings([
        'max_length' => 255,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 10,
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(FALSE);

    $fields['teaser'] = BaseFieldDefinition::create('string_long')
      ->setLabel(t('Teaser'))
      ->setDescription(t('The teaser of the link.'))
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE)
      ->setSettings([
        'max_length' => 2000,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textarea',
        'weight' => 11,
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(FALSE);

    $fields['status']->setDescription(t('A boolean indicating whether the Link list link is published.'))
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
      ]);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }

}