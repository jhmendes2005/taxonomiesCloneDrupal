<?php
namespace Drupal\pwa_product_selector\Functions;

use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\DependencyInjection\ContainerInterface;

class LanguagesGet {

  protected $languageManager;

  public function __construct(LanguageManagerInterface $language_manager) {
    $this->languageManager = $language_manager;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('language_manager')
    );
  }

  public function getLanguagesList(): array {
    // Obtém as linguagens do site.
    $languages = $this->languageManager->getLanguages();

    // Constrói a lista de idiomas.
    $language_list = [];
    foreach ($languages as $language) {
      $language_list[] = [
        'id' => $language->getId(),
        'name' => $language->getName(),
      ];
    }

    return $language_list;
  }
}
