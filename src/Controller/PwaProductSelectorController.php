<?php

namespace Drupal\pwa_product_selector\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Response;


class PwaProductSelectorController extends ControllerBase {

  public function simplePage() {
    // Obtem as linguagens.
    $language_getter = \Drupal::service('pwa_product_selector.language_getter');
    $languages_list = $language_getter->getLanguagesList();
    dump($languages_list);

    // Obtem as taxonomias e termos.
    $products_getter = \Drupal::service('pwa_product_selector.taxonomies_terms_get');
    $products_list = $products_getter->extractTaxonomiesAndTerms();
    dump($products_list);

    // Injeta os termos nas taxonomias.
    $products_injector = \Drupal::service('pwa_product_selector.taxonomy_terms_injector');
    $products_injector->injectTerms($products_list);

    $html = '<html>
                <head><title>Exemplo de Página</title></head>
                <body>
                    <h1>Bem-vindo à Página de Exemplo!</h1>
                    <p>Este é um exemplo de controlador básico em Drupal.</p>
                    <a href="/admin/node-export">Download JSON</a>
                </body>
            </html>';
    return new Response($html);
  }
}
