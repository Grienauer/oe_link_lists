<?php

declare(strict_types = 1);

namespace Drupal\oe_link_lists_manual_source\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;

/**
 * Provides an interface for Link list link entities.
 *
 * @ingroup oe_link_lists
 */
interface LinkListLinkInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityPublishedInterface {

  /**
   * Gets the external Link list link url.
   *
   * @return string|null
   *   External url of the Link list link.
   */
  public function getUrl(): ?string;

  /**
   * Sets the Link list link external url.
   *
   * @param string $url
   *   The Link list link external url.
   *
   * @return \Drupal\oe_link_lists\Entity\LinkListLinkInterface
   *   The called Link list link entity.
   */
  public function setUrl(string $url): LinkListLinkInterface;

  /**
   * Gets the internal Link list link reference.
   *
   * @return \Drupal\Core\Entity\EntityInterface|null
   *   Internal referenced entity of the Link list link.
   */
  public function getTargetEntity(): ?EntityInterface;

  /**
   * Gets the internal Link list link reference ID.
   *
   * @return string|int|null
   *   Internal referenced Id of the Link list link.
   */
  public function getTargetId();

  /**
   * Sets the Link list link internal target id.
   *
   * @param string|int $target_id
   *   The Link list link target id.
   *
   * @return \Drupal\oe_link_lists\Entity\LinkListLinkInterface
   *   The called Link list link entity.
   */
  public function setTargetId($target_id): LinkListLinkInterface;

  /**
   * Gets the Link list link teaser.
   *
   * @return string|null
   *   Teaser of the Link list link.
   */
  public function getTeaser(): ?string;

  /**
   * Sets the Link list link teaser.
   *
   * @param string $teaser
   *   The Link list link teaser.
   *
   * @return \Drupal\oe_link_lists\Entity\LinkListLinkInterface
   *   The called Link list link entity.
   */
  public function setTeaser(string $teaser): LinkListLinkInterface;

  /**
   * Gets the Link list link title.
   *
   * @return string|null
   *   Title of the Link list link.
   */
  public function getTitle(): ?string;

  /**
   * Sets the Link list link title.
   *
   * @param string $title
   *   The Link list link title.
   *
   * @return \Drupal\oe_link_lists\Entity\LinkListLinkInterface
   *   The called Link list link entity.
   */
  public function setTitle(string $title): LinkListLinkInterface;

  /**
   * Gets the Link list link creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Link list link.
   */
  public function getCreatedTime(): int;

  /**
   * Sets the Link list link creation timestamp.
   *
   * @param int $timestamp
   *   The Link list link creation timestamp.
   *
   * @return \Drupal\oe_link_lists\Entity\LinkListLinkInterface
   *   The called Link list link entity.
   */
  public function setCreatedTime(int $timestamp): LinkListLinkInterface;

}