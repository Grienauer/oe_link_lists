<?php

declare(strict_types = 1);

namespace Drupal\oe_link_lists_test\Plugin\LinkSource;

use Drupal\Core\Url;
use Drupal\oe_link_lists\DefaultLink;
use Drupal\oe_link_lists\LinkCollection;
use Drupal\oe_link_lists\LinkCollectionInterface;
use Drupal\oe_link_lists\Plugin\ExternalLinkSourcePluginBase;

/**
 * Plugin implementation of the link_source.
 *
 * @LinkSource(
 *   id = "bar",
 *   label = @Translation("Bar"),
 *   description = @Translation("Bar description.")
 * )
 */
class Bar extends ExternalLinkSourcePluginBase {

  /**
   * {@inheritdoc}
   */
  public function getLinks(int $limit = NULL, int $offset = 0): LinkCollectionInterface {
    $collection = new LinkCollection([
      (new DefaultLink(Url::fromUri('http://example.com'), 'Example', ['#markup' => 'Example teaser']))->addCacheTags(['bar_test_tag:1']),
      (new DefaultLink(Url::fromUri('http://ec.europa.eu'), 'European Commission', ['#markup' => 'European teaser']))->addCacheTags(['bar_test_tag:2']),
    ]);

    $collection
      // Cache contexts are validated so we need to use an existing one.
      ->addCacheContexts(['user.is_super_user'])
      ->addCacheTags(['bar_test_tag_list'])
      ->mergeCacheMaxAge(1800);

    return $collection;
  }

}
