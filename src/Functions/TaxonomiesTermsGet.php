<?php
namespace Drupal\pwa_product_selector\Functions;

use Drupal\taxonomy\Entity\Vocabulary;
use Drupal\node\Entity\NodeType;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;

class TaxonomiesTermsGet {

  protected $logger;
  protected $entityFieldManager;
  protected $languageManager;
  protected $entityTypeManager;
  protected $languagesGet;

  public function __construct(
    LoggerChannelFactoryInterface $logger_factory,
    EntityFieldManagerInterface $entity_field_manager,
    LanguageManagerInterface $language_manager,
    EntityTypeManagerInterface $entity_type_manager,
    LanguagesGet $languages_get
  ) {
    $this->logger = $logger_factory->get('pwa_product_selector');
    $this->entityFieldManager = $entity_field_manager;
    $this->languageManager = $language_manager;
    $this->entityTypeManager = $entity_type_manager;
    $this->languagesGet = $languages_get;
  }

  public function extractTaxonomiesAndTerms($node_type = null) {
    $tax_terms = [];

    // Verifica se o node_type é nulo e define um valor padrão
    if ($node_type === null) {
      $node_type = 'ons_product_selector';
    }

    $type = NodeType::load($node_type);
    if (!$type) {
      $this->logger->error('O tipo de nó @type não foi encontrado.', ['@type' => $node_type]);
      return [];
    }

    $languages_list = $this->languagesGet->getLanguagesList();
    $default_langcode = $this->languageManager->getDefaultLanguage()->getId();

    foreach ($languages_list as $lang) {
      $lang_id = $lang['id'];
      $tax_terms[$lang_id] = [];
      $fields = $this->entityFieldManager->getFieldDefinitions('node', $node_type);

      foreach ($fields as $field_name => $field_definition) {
        if ($field_definition->getType() == 'entity_reference' && $field_definition->getSetting('target_type') == 'taxonomy_term') {
          $vocabulary_ids = $field_definition->getSetting('handler_settings')['target_bundles'];

          foreach ($vocabulary_ids as $vid => $value) {
            $vocabulary = Vocabulary::load($vid);
            if (!$vocabulary) {
              $this->logger->error('O vocabulário com ID @vid não foi encontrado.', ['@vid' => $vid]);
              continue;
            }

            if (!isset($tax_terms[$lang_id][$vid])) {
              $tax_terms[$lang_id][$vid] = [];
            }

            $terms = $this->entityTypeManager->getStorage('taxonomy_term')->loadByProperties(['vid' => $vid]);

            foreach ($terms as $term) {
              try {
                if ($term->hasTranslation($lang_id)) {
                  $translated_term = $term->getTranslation($lang_id);
                  $tax_terms[$lang_id][$vid][] = [
                    'label' => $translated_term->label(),
                    'langType' => $lang_id == $default_langcode ? 'Main' : 'Translate',
                    'main' => $lang_id == $default_langcode ? 'None' : $default_langcode,  // Define o idioma principal para traduções
                  ];
                }
              } catch (\Exception $e) {
                $this->logger->error('Ocorreu uma exceção ao processar o termo @term: @message', [
                  '@term' => $term->id(),
                  '@message' => $e->getMessage(),
                ]);
              }
            }
          }
        }
      }

      // Adiciona o campo `langType` e `main` no nível do idioma
      $tax_terms[$lang_id]['langType'] = $lang_id == $default_langcode ? 'Main' : 'Translate';
      $tax_terms[$lang_id]['main'] = $lang_id == $default_langcode ? 'None' : $default_langcode;
    }

    return $tax_terms;
  }
}
