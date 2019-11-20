<?php

namespace Drupal\oe_link_lists\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines link_display annotation object.
 *
 * @Annotation
 */
class LinkDisplay extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The human-readable name of the plugin.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $title;

  /**
   * The description of the plugin.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $description;

  /**
   * Whether this plugin is meant for internal purposes.
   *
   * Internal plugins are not selectable by the user. By default, plugins are
   * not internal.
   *
   * @var bool
   */
  public $internal = FALSE;

}
