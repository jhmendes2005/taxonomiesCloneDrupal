# PWA Product Selector

Este projeto Drupal consiste em duas classes principais para gerenciar termos de taxonomia em um site Drupal, permitindo a injeção de termos e suas traduções em taxonomias existentes ou novas. Além de adicionar os Termos automáticamente, lidando com Termos que possuem Translate.

## Funcionalidades de Funções

1. **TaxonomiesTermsGet**: Responsável por extrair taxonomias e termos de um tipo de conteúdo específico.
2. **TaxonomyTermsInjector**: Responsável por injetar termos em taxonomias com suporte para traduções.


## Requisitos

- Drupal 9.x ou superior

## Instalação

1. Clone o repositório na pasta `modules/custom` do seu projeto Drupal:
    ```sh
    git clone https://github.com/seuusuario/ef5_geocoding.git modules/custom/ef5_geocoding
    ```

2. Ative o módulo:
    ```sh
    drush en ef5_geocoding
    ```

3. Limpe o cache do Drupal:
    ```sh
    drush cr
    ```

## Configuração e Uso

1. Navegue até a página do módulo:
    Vá diretamente para `/admin/config/services/ef5-geocoding`.

2. Preenchendo as informações:
    Selecione o NodeType que precisa, adicione um prefixo para as Taxonomias Clonadas e pronto!

3. Final:
    Vá para o painel de Taxonomias, e pronto! Suas taxonomias estarão clonadas, juntamente com os termos.


## Licença

Este projeto está licenciado sob a licença MIT. Veja o arquivo LICENSE para mais detalhes.

## Problemas Conhecidos

- Certifique-se de que a Taxonomie que deseja clonar, seja do tipo NODE.

## Suporte

Para suporte, abra um issue no repositório do GitHub.

---

Feito por João Henrique Mendes
