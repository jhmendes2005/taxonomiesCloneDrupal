<?php

namespace Drupal\pwa_product_selector\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\NodeType;

class TaxForm extends FormBase {

  public function getFormId() {
    return 'tax_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    // Get all node types.
    $node_types = NodeType::loadMultiple();
    $node_type_options = [];
    foreach ($node_types as $node_type) {
      $node_type_options[$node_type->id()] = $node_type->label();
    }

    // Form element for node types.
    $form['node_types'] = [
      '#type' => 'select',
      '#title' => $this->t('Node Types'),
      '#options' => $node_type_options,
      '#multiple' => FALSE,
    ];

    $form['prefix_node_types'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Digite o Prefixo das Taxonomias Clonadas.')
    ];

    // Submit button.
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $selected_node_types = $form_state->getValue('node_types');
    $prefix_node_types = $form_state->getValue('prefix_node_types');
    dump($selected_node_types);


    if (is_string($selected_node_types)) {
      $products_getter = \Drupal::service('pwa_product_selector.taxonomies_terms_get');
      $products_insert = \Drupal::service('pwa_product_selector.taxonomy_terms_injector');

      $all_products_lists = [];

      $products_list = $products_getter->extractTaxonomiesAndTerms($selected_node_types);
      $products_insert->injectTerms($products_list, $prefix_node_types);
      $all_products_lists[$selected_node_types] = $products_list;

      // Adiciona uma mensagem ao messenger para informar ao usuário
      \Drupal::messenger()->addMessage($this->t('Selected node types: @node_types', ['@node_types' => $selected_node_types]));
    } else {
      \Drupal::messenger()->addMessage($this->t('No node types selected.'));
    }
  }

}
