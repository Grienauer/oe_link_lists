<?php

declare(strict_types = 1);

namespace Drupal\oe_link_lists;

use Drupal\Component\Plugin\ConfigurableInterface;
use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\PluginFormInterface;

/**
 * Base class for link_list_display_options plugins.
 */
abstract class LinkListDisplayOptionsPluginBase extends PluginBase implements PluginFormInterface, ConfigurableInterface {

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->setConfiguration($configuration);
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getConfiguration() {
    return $this->configuration;
  }

  /**
   * {@inheritdoc}
   */
  public function setConfiguration(array $configuration) {
    $this->configuration = $configuration + $this->defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['#tree'] = TRUE;
    $form['#process'][] = [$this, 'processConfigurationForm'];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {
    // Empty in many cases.
  }

  /**
   * Configuration form process callback.
   *
   * This is needed because of the way subforms are embedded.
   *
   * @param array $form
   *   The form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   *
   * @see PluginFormInterface::buildConfigurationForm()
   *
   * @return array
   *   The form.
   */
  abstract public function processConfigurationForm(array &$form, FormStateInterface $form_state): array;

}
