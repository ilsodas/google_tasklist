<?php

namespace Drupal\google_tasklist\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

class GoogleTasklist extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {

    $config = $this->getConfiguration();

    $form['username'] = [
      '#type' => 'textfield',
      '#title' => $this->t('API Key'),
      '#default_value' => $config['username'],
      '#required' => TRUE,
    ];
    $form['appearance'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Appearance'),
    ];
    $form['appearance']['theme'] = [
      '#type' => 'select',
      '#title' => $this->t('Theme'),
      '#default_value' => $config['theme'],
      '#options' => [
        '' => $this->t('Default'),
        'dark' => $this->t('Dark'),
      ],
      '#description' => $this->t('Select a theme for the widget.'),
    ];
    $form['appearance']['link_color'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Link color'),
      '#default_value' => $config['link_color'],
      '#maxlength' => 6,
      '#size' => 6,
      '#field_prefix' => '#',
      '#description' => $this->t('Change the link color used by the widget.
        Takes an %format hex format color. Note that some icons in the widget
        will also appear this color.', ['%format' => 'abc123']),
    ];
    $form['appearance']['border_color'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Border color'),
      '#default_value' => $config['border_color'],
      '#maxlength' => 6,
      '#size' => 6,
      '#field_prefix' => '#',
      '#description' => $this->t('Change the border color used by the widget.
        Takes an %format hex format color.', ['%format' => 'abc123']),
    ];
    $form['appearance']['chrome'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Chrome'),
      '#default_value' => $config['chrome'],
      '#options' => [
        'noheader' => $this->t('No header'),
        'nofooter' => $this->t('No footer'),
        'noborders' => $this->t('No borders'),
        'noscrollbar' => $this->t('No scrollbar'),
        'transparent' => $this->t('Transparent'),
      ],
      '#description' => $this->t('Control the widget layout and chrome.'),
    ];

    $form['size']['width'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Width'),
      '#default_value' => $config['width'],
      '#size' => 6,
      '#field_suffix' => 'px',
      '#description' => $this->t('Change the width of the widget.'),
    ];

    $form['size']['height'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Height'),
      '#default_value' => $config['height'],
      '#size' => 6,
      '#field_suffix' => 'px',
      '#description' => $this->t('Change the height of the widget.'),
    ];

    $form['size']['note'] = [
      '#type' => 'markup',
      '#markup' => '<p>' . $this->t('The minimum width of a timeline is 180px
        and the maximum is 520px. The minimum height is 200px.') . '</p>',
    ];

    $form['accessibility']['language'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Language'),
      '#default_value' => $config['language'],
      '#maxlength' => 5,
      '#size' => 5,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->setConfigurationValue('username', $form_state->getValue('username'));
    foreach (['appearance', 'functionality', 'size', 'accessibility'] as $fieldset) {
      $fieldset_values = $form_state->getValue($fieldset);
      foreach ($fieldset_values as $key => $value) {
        $this->setConfigurationValue($key, $value);
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function build() {

    $config = $this->getConfiguration();

    $render['block'] = [
      '#type' => 'link',
      '#title' => $this->t('Title', ['@username' => $config['username']]),
      '#url' => Url::fromUri('https://www.googleapis.com/tasks/v1/lists/' . $config['username'] . 'tasks'),
      '#attributes' => [
        'class' => ['google-tasklist'],
      ],

    ];

    if (!empty($config['theme'])) {
      $render['block']['#attributes']['data-theme'] = $config['theme'];
    }

    if (!empty($config['link_color'])) {
      $render['block']['#attributes']['data-link-color'] = '#' . $config['link_color'];
    }

    if (!empty($config['width'])) {
      $render['block']['#attributes']['data-width'] = $config['width'];
    }

    if (!empty($config['height'])) {
      $render['block']['#attributes']['data-height'] = $config['height'];
    }

    if (!empty($config['chrome'])) {
      $options = array_keys(array_filter($config['chrome']));

      if (count($options)) {
        $render['block']['#attributes']['data-chrome'] = implode(' ', $options);
      }
    }

    if (!empty($config['border_color'])) {
      $render['block']['#attributes']['data-border-color'] = '#' . $config['border_color'];
    }

    if (!empty($config['language'])) {
      $render['block']['#attributes']['lang'] = $config['language'];
    }

    if (!empty($config['tweet_limit'])) {
      $render['block']['#attributes']['data-tweet-limit'] = $config['tweet_limit'];
    }

    if (!empty($config['related'])) {
      $render['block']['#attributes']['data-related'] = $config['related'];
    }

    if (!empty($config['polite'])) {
      $render['block']['#attributes']['aria-polite'] = $config['polite'];
    }

    return $render;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'username' => '',
      'theme' => '',
      'link_color' => '',
      'width' => '',
      'height' => '',
      'chrome' => [],
      'border_color' => '',
      'language' => '',
      'related' => '',
      'polite' => '',
    ];
  }

}
