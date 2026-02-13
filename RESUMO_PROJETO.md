# Resumo do Projeto - Plugin Branding para GLPI

## Visao Geral
O projeto e um plugin de branding para o GLPI 11, com foco em personalizacao visual completa por entidade. Permite trocar logos, definir esquemas de cores (dia/noite) com alternancia automatica por horario, aplicar CSS customizado e disponibilizar um endpoint REST publico para consumo externo.

## Metadados Principais
- Nome do plugin: Branding
- Versao: 2.0.1
- Compatibilidade GLPI: 11.0.0 ate 11.0.99
- PHP minimo: 8.1
- Licenca: GPL-2.0-or-later (GPLv2+)
- Autoload: PSR-4 `GlpiPlugin\Branding\` em `src/`
- Tipo Composer: `glpi-plugin`

## Requisitos Tecnicos
- GLPI 11.0.0 ou superior
- PHP 8.1 ou superior
- Extensoes PHP: json, gd, mysqli, mbstring, curl, fileinfo
- Banco: MySQL 5.7+ ou MariaDB 10.2+ (recomendado MySQL 8.0 ou MariaDB 10.11+)
- Servidor web: Apache 2.4+ ou Nginx 1.18+

## Funcionalidades
- Logos personalizados por entidade
- Background customizado na pagina de login
- Favicon customizado
- Temas dia/noite com horarios configuraveis
- Esquema de cores completo para cada tema (6 cores)
- CSS customizado por entidade
- Multi-entidade com heranca recursiva
- API REST publica com resposta JSON
- Modo de recuperacao: `?disable_branding=1`

## Instalacao e Ativacao (Resumo)
- Metodo Git: clonar em `glpi/plugins/branding` e instalar dependencias via Composer
- Metodo release: baixar tar.gz e extrair em `glpi/plugins/`
- Metodo Marketplace: instalar via interface do GLPI
- Ativacao via interface ou CLI `php bin/console glpi:plugin:install branding` e `activate`
- Diretorio de upload: `GLPI_PLUGIN_DOC_DIR/branding` (mapeia para `files/_plugins/branding`)

## Configuracao e Uso
- Configuracao por entidade via aba Branding em Administracao > Entidades
- Campos principais:
- `enabled` e `is_recursive`
- Horarios `day_start` e `night_start`
- Cores dia e noite (primary, secondary, background, text, sidebar_bg, sidebar_text)
- Uploads: `logo_login`, `logo_expanded`, `logo_collapsed`, `favicon`, `background_login`
- `custom_css` aplicado ao final do CSS gerado

## Arquitetura e Fluxos
### Inicializacao do Plugin
- `plugin_init_branding()` em `setup.php` registra classe `Config`, adiciona CSS para usuarios logados, e registra endpoints publicos (API e CSS) como stateless.

### Fluxo de Configuracao
- `front/config.form.php` valida permissao `entity` e delega add/update/delete para `GlpiPlugin\Branding\Config`
- `Config::showForEntity()` carrega dados da entidade e renderiza `templates/config.html.twig`

### Fluxo do CSS
- `public/css/branding.css.php` recebe `entities_id` e `login=1`, busca configuracao com `Config::getForEntity()`, resolve cores ativas via `Config::getActiveColors()` e gera CSS dinamico

### Fluxo da API
- `public/api.php` responde GET com configuracao da entidade
- Retorna JSON com `colors`, `assets` e `css_url`

## Arquivos-Chave
- `setup.php`: hooks, versao, requisitos
- `hook.php`: install/uninstall, cria tabela, cria diretorio de uploads, inclui CSS no login
- `src/Config.php`: modelo, logica de entidade, uploads, schedule, renderizacao Twig
- `front/config.form.php`: handler do formulario
- `templates/config.html.twig`: formulario de configuracao
- `public/api.php`: endpoint REST
- `public/css/branding.css.php`: geracao de CSS
- `sql/update-1.0-2.0.sql`: migracao v1 para v2
- `tools/check_system.php`: script de verificacao do ambiente
- `examples/api_test.php`: exemplo de consumo da API
- `branding.xml`: metadata para marketplace

## Modelo de Dados
Tabela principal: `glpi_plugin_branding_configs`
- `id` (PK)
- `entities_id` (int, default 0)
- `is_recursive` (tinyint, default 0)
- `enabled` (tinyint, default 0)
- `logo_login`, `logo_expanded`, `logo_collapsed`, `favicon`, `background_login` (varchar)
- `colors_day`, `colors_night` (json)
- `schedule_enabled` (tinyint, default 0)
- `day_start`, `night_start` (time)
- `custom_css` (text)
- `date_creation`, `date_mod` (timestamp)

JSON de cores esperado:
```
{
  "primary": "#2c3e50",
  "secondary": "#3498db",
  "background": "#f8f9fa",
  "text": "#212529",
  "sidebar_bg": "#343a40",
  "sidebar_text": "#ffffff"
}
```

## API REST (Resumo)
Endpoint: `GET /plugins/branding/api.php?entity_id=0`
- Retorna 200 com JSON de configuracao, ou 404 se nao houver configuracao habilitada
- CORS liberado para GET e OPTIONS
- `assets` retorna URLs de arquivos em `files/_plugins/branding`
- `css_url` aponta para o CSS dinamico do plugin

## Geracao de CSS
O CSS dinamico aplica:
- Logos na navbar (`.navbar-brand-logo` e `.navbar-brand-logo-mini`)
- Cores globais (primary, secondary, background, text, sidebar)
- Estilos de login quando `login=1`
- CSS customizado ao final

## Ferramentas e Scripts
- `tools/check_system.php`: valida PHP, extensoes, GLPI, banco e arquivos essenciais
- `examples/api_test.php`: testa endpoint e mostra exemplos de integracao
- `build-release.sh`: cria pacote release e checksum

## Internacionalizacao
Existe traducao pt_BR em `locales/pt_BR.php` para strings do formulario.

## Observacoes Importantes (Avaliacao)
- ✅ `Config::getForEntity()` agora respeita `is_recursive` corretamente.
- ✅ A API e o CSS agora usam a mesma lógica de tema via `getActiveTheme()`.
- ✅ O `prepareInput()` agora valida extensão, tipo MIME e tamanho de arquivos.
- ✅ O `hook.php` usa `$migration->addPostQuery()` para criação da tabela.
- ✅ O `branding.xml` lista apenas os idiomas existentes (en_GB, pt_BR).
- ✅ A documentação foi corrigida para remover referências à pasta `docs/` inexistente.
- ✅ O CSS customizado é sanitizado contra `@import`, `expression()` e `javascript:` em URLs.

## Roadmap Documentado
Arquitetura inclui itens futuros:
- Preview ao vivo, import/export, historico de mudancas
- Multiplos temas, marketplace de temas, gerador de paletas
- Page builder visual e componentes web
