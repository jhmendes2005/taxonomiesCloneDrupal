<?php

namespace Drupal\pwa_product_selector\Functions;

use Drupal\taxonomy\Entity\Term;
use Drupal\taxonomy\Entity\Vocabulary;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

class TaxonomyTermsInjector {

  use StringTranslationTrait;

  protected $logger;
  protected $entityTypeManager;

  public function __construct(LoggerChannelFactoryInterface $logger_factory, EntityTypeManagerInterface $entity_type_manager) {
    $this->logger = $logger_factory->get('pwa_product_selector');
    $this->entityTypeManager = $entity_type_manager;
  }

  public function injectTerms(array $taxonomies_terms, string $prefix) {
    foreach ($taxonomies_terms as $lang => $taxonomies) {
      foreach ($taxonomies as $vocabulary_machine_name => $terms) {
        // Adiciona o prefixo enviado, ao nome de máquina da taxonomia
        $prod_vocabulary_machine_name = $prefix . $vocabulary_machine_name;

        // Verifica se a taxonomia com o prefixo escolhido já existe, senão cria uma nova
        $vocabulary = Vocabulary::load($prod_vocabulary_machine_name);
        if (!$vocabulary) {
          $vocabulary = Vocabulary::create([
            'vid' => $prod_vocabulary_machine_name,
            'description' => '',
            'name' => $vocabulary_machine_name,
            'language' => $lang,
          ]);
          $vocabulary->save();
        }

        foreach ($terms as $term_data) {
          // Verifica se o termo já existe na taxonomia com o prefixo escolhido
          $existing_terms = $this->entityTypeManager->getStorage('taxonomy_term')->loadByProperties([
            'name' => $term_data['label'],
            'vid' => $prod_vocabulary_machine_name,
          ]);

          if (empty($existing_terms)) {
            // Cria e salva o novo termo
            $term = Term::create([
              'vid' => $prod_vocabulary_machine_name,
              'name' => $term_data['label'],
              'langcode' => $lang,
            ]);
            $term->save();
            $this->logger->info('Termo "@term" adicionado à taxonomia "@vocabulary".', [
              '@term' => $term_data['label'],
              '@vocabulary' => $prod_vocabulary_machine_name,
            ]);
          } else {
            $this->logger->warning('Termo "@term" já existe na taxonomia "@vocabulary".', [
              '@term' => $term_data['label'],
              '@vocabulary' => $prod_vocabulary_machine_name,
            ]);
          }
        }
      }
    }
    //dump($this->deleteTaxonomies($prefix));
  }

  public function deleteTaxonomies(string $prefix) {
    $vocabularies = Vocabulary::loadMultiple();
    $deleted_vocabularies = [];

    foreach ($vocabularies as $vocabulary) {
      if (strpos($vocabulary->id(), $prefix) === 0) {
        $vocabulary->delete();
        $deleted_vocabularies[] = $vocabulary->label();
        $this->logger->info('Deletou a taxonomia: @label', ['@label' => $vocabulary->label()]);
      }
    }

    return $deleted_vocabularies;
  }

}
