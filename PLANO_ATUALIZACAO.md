# Plano de Atualizacao do Plugin Branding (GLPI 11.x)

Este documento detalha todas as etapas e pontos de mudanca recomendados para alinhar o plugin Branding com a API e as boas praticas do GLPI 11.x.

## 1. Diagnostico Inicial e Baseline
- Confirmar versao do GLPI (11.0.x) e do PHP. (pendente)
- Confirmar caminho do plugin ativo (ex.: `marketplace/branding`). (pendente)
- Criar branch local para as mudancas (se houver git). (pendente)
- Fazer backup da configuracao atual do plugin e do banco. (pendente)

## 2. Correcoes Criticas de Runtime
### 2.1 Firewall
- [OK] `setup.php`: ajustar `Firewall::addPluginStrategyForLegacyScripts()` para usar regex com delimitadores e terceiro parametro `Firewall::STRATEGY_NO_CHECK`.
- [OK] Exemplo correto: `'#^/public/css/branding\\.css\\.php$#'`.
- [OK] Incluir tambem `public/api.php` como stateless.

### 2.2 Remover `use` invalidos (PHP 8.5)
- [OK] `setup.php`: remover `use Plugin;` e `use Session;` (nomes sem namespace).
- [OK] Remover `use Glpi\\Plugin\\Hooks;` se nao for usado.

### 2.3 Aba em Entidades
- [OK] `src/Config.php`: remover uso de `countForItem()` que nao existe na API atual.
- [OK] Simplificar `getTabNameForItem()` para apenas retornar o nome.

### 2.4 Twig / Template
- [OK] `templates/config.html.twig`: remover filtro `json_decode` (nao existe no Twig do GLPI).
- [OK] Garantir que `colors_day` e `colors_night` cheguem como arrays via PHP.

### 2.5 Defaults obrigatorios
- [OK] `src/Config.php`: preencher defaults completos (is_recursive, colors_day/night, custom_css).
- [OK] Garantir que JSON vazio nao quebre template.

## 3. Camada de Dados (Migracoes)
### 3.1 Instalacao
- [OK] `hook.php`: evitar SQL direto com `$DB->query`.
- [OK] Usar `$migration->addPostQuery()` para CREATE TABLE.

### 3.2 Atualizacao
- [OK] Revisar `sql/update-1.0-2.0.sql` para migracao idempotente.
- [OK] Padronizar indices e tipos com GLPI 11.

### 3.3 Uninstall
- [OK] Validar drop seguro de tabelas sem erro em ambiente restrito.

## 4. Paridade Funcional com Documentacao
### 4.1 Recursividade real
- [OK] `Config::getForEntity()`: respeitar `is_recursive`.
- [OK] Permitir que entidade filha desative herdanca quando `is_recursive = 0`.

### 4.2 Tema ativo
- [OK] API e CSS precisam usar a mesma logica de horario.
- [OK] Ajustar `active_theme` na API para refletir o tema realmente aplicado.

### 4.3 Favicon
- [OK] Implementar insercao real no HTML (head), nao apenas na API.

### 4.4 Assets ausentes
- [OK] Tratar ausencia de imagens sem warnings (fallbacks).

## 5. Seguranca e Robustez
### 5.1 Uploads
- [OK] Validar extensoes e MIME (png, jpg, gif, webp, ico).
- [OK] Limitar tamanho maximo (ex.: 5MB).
- [OK] Rejeitar arquivos invalidos.

### 5.2 CSS Customizado
- [OK] Sanitizar CSS contra `@import` e `url()` externos se necessario.
- [OK] Remover tags ou caracteres invalidos.

### 5.3 Permissoes e ACL
- [OK] Garantir que apenas usuarios autorizados possam alterar configuracoes.
- [OK] Manter `Session::checkRight()` no frontend.

## 6. Documentacao e Metadados
### 6.1 README/INSTALL/ARCHITECTURE
- [OK] Alinhar promessas com implementacao real.
- [OK] Remover referencias a pasta `docs/` se ela nao existir.

### 6.2 branding.xml
- [OK] Ajustar linguas listadas para apenas as existentes.
- [OK] Documentação sincronizada com código fonte (README, CONTRIBUTING, ARCHITECTURE).

## 7. Testes e Validacao
- Testar API e CSS em ambiente GLPI 11. (pendente)
- Testar Entidades com e sem config. (pendente)
- Validar tema dia/noite. (pendente)
- Confirmar uploads e permissao de arquivos. (pendente)

## 8. Release
- [OK] Atualizar CHANGELOG (v2.0.1 documentada, Unreleased removida).
- [OK] Atualizar versao (2.0.1) em `setup.php`, `composer.json` e `branding.xml`.
- [OK] README.md atualizado com changelog v2.0.1.
- Gerar pacote release com `build-release.sh`. (pendente)
- Testar em instalacao limpa antes de publicar. (pendente)
