<?php

declare(strict_types = 1);

namespace Drupal\oe_link_lists\EventSubscriber;

use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\Exception\UndefinedLinkTemplateException;
use Drupal\Core\Url;
use Drupal\oe_link_lists\DefaultEntityLink;
use Drupal\oe_link_lists\Event\EntityValueResolverEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Default subscriber that resolves event values into a link object.
 */
class DefaultEntityValueResolverSubscriber implements EventSubscriberInterface {

  /**
   * The entity repository.
   *
   * @var \Drupal\Core\Entity\EntityRepositoryInterface
   */
  protected $entityRepository;

  /**
   * DefaultEntityValueResolverSubscriber constructor.
   *
   * @param \Drupal\Core\Entity\EntityRepositoryInterface $entityRepository
   *   The entity repository.
   */
  public function __construct(EntityRepositoryInterface $entityRepository) {
    $this->entityRepository = $entityRepository;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [EntityValueResolverEvent::NAME => 'resolveEntityValues'];
  }

  /**
   * Resolves the link object from an entity with simple and default values.
   *
   * @param \Drupal\oe_link_lists\Event\EntityValueResolverEvent $event
   *   The event.
   */
  public function resolveEntityValues(EntityValueResolverEvent $event): void {
    $entity = $this->entityRepository->getTranslationFromContext($event->getEntity());
    $title = $entity->label() ?? '';
    $teaser = [
      '#markup' => '',
    ];
    if ($entity->hasField('body')) {
      $teaser = [
        '#type' => 'processed_text',
        '#text' => text_summary($entity->get('body')->value, $entity->get('body')->format),
        '#format' => $entity->get('body')->format,
      ];
    }

    try {
      $url = $entity->toUrl();
    }
    catch (UndefinedLinkTemplateException $exception) {
      // This should not happen normally as referenceable entity types have a
      // canonical URL. But in case an entity doesn't, we should not crash
      // the entire thing.
      $url = Url::fromRoute('<front>');
    }
    $link = new DefaultEntityLink($url, $title, $teaser);
    $link->setEntity($entity);
    $event->setLink($link);
  }

}
