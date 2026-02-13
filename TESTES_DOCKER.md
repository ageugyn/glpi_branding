# Testes e Observações no Docker (GLPI Branding Plugin)

Documento para registrar os testes já realizados no container e evitar repetição de tentativas inválidas.

**Contexto**
- GLPI 11.0.4 rodando em container (`glpi`) via podman/docker.
- Caminhos relevantes: `GLPI_ROOT=/var/www/glpi`, `GLPI_VAR_DIR=/var/glpi`.
- Plugin em `/var/glpi/marketplace/branding`.

**Comandos de acesso**
- Entrar no container: `docker exec -it glpi bash`
- Entrar como root no container: `docker exec -it --user root glpi bash`

**Descobertas de caminho**
- `bin/console` não existe em `/var/glpi`, mas existe em `/var/www/glpi/bin/console`.
- `GLPI_ROOT` correto é `/var/www/glpi`.

## Problemas já identificados e correções aplicadas

**1) Instalação do plugin falhando por “Executing direct queries is not allowed!”**
- Causa: uso de `$DB->query()` direto no `hook.php`.
- Correção aplicada: substituir por `$migration->addPostQuery($query)` (install/uninstall).

**2) Erro em `Firewall::addPluginStrategyForLegacyScripts`**
- Causa: método exige 3 argumentos e padrão regex.
- Correção aplicada: `Firewall::addPluginStrategyForLegacyScripts('branding', '#^/public/css/branding\\.css\\.php$#', Firewall::STRATEGY_NO_CHECK);`

**3) Erros de `preg_match` e `PcreException`**
- Causa: regex inválida passada ao firewall (sem delimitador, string errada).
- Correção aplicada: regex com `#^...$#`.

**4) Warnings de `use Plugin; use Session;` sem namespace**
- Causa: `use` com nomes não-qualificados em `setup.php`.
- Correção aplicada: remover esses `use`.

**5) Erro Twig: `json_decode` filter inexistente**
- Causa: Twig não tem filtro `json_decode`.
- Correção aplicada: remover uso do filtro e tratar JSON no PHP (`Config.php`).

**6) Erro Twig: `timeField` macro inexistente**
- Causa: GLPI 11 não possui `timeField` no macro.
- Correção aplicada: trocar por `textField` em `templates/config.html.twig`.

**7) Plugin não aparece no menu / estado “Instalado / não ativado”**
- Causa: plugin ativado via console mas erro interno impedia status.
- Correção aplicada: ativação com `php /var/www/glpi/bin/console glpi:plugin:activate branding -vvv`.

**8) Erro `UndefinedMethodError countForItem`**
- Causa: `Config::getTabNameForItem()` chamava método inexistente.
- Correção aplicada: retorno simples `self::getTypeName(...)`.

## Problemas atuais

**Upload dos logos não persiste**
- Sintoma: ao salvar, logo some, diretório fica vazio.
- Diretório esperado: `/var/glpi/files/_plugins/branding`.
- Logs mostraram `$_FILES` chegando com chaves `_uploader_logo_*`.
- O plugin original procurava `$_FILES['logo_*']` (mismatch).
- Resultado: arquivos não eram processados.

## Observações confirmadas no container

**PHP limits**
- `upload_max_filesize = 5M` (ajustado de 2M).
- `post_max_size = 8M`.
- `file_uploads = On`.

**Log temporário**
- Arquivo: `/var/glpi/logs/branding-upload.log`.
- Log mostra `prepareInput` com:
  - `files_keys` = `_uploader_logo_login`, `_uploader_logo_expanded`, `_uploader_logo_collapsed`, `_uploader_favicon`, `_uploader_background_login`
  - `content_type = multipart/form-data`
- Não houve entradas `file/saved` → bloco de upload não está lendo `_uploader_*`.

## Ações que NÃO resolveram (evitar loop)

- Repetir `glpi:cache:clear` sem corrigir o upload não resolve.
- Repetir upload com o mesmo código não resolve enquanto o plugin não ler `_uploader_*`.
- Ajustar limite de tamanho sem ajustar o código de upload não resolve.

## Solução Implementada (2026-02-05)

**Correção do Upload de Arquivos (`Config.php`)**

O problema era que o GLPI 11 usa um componente JavaScript de upload que prefixa automaticamente os campos com `_uploader_`. O código original procurava `$_FILES['logo_login']`, mas o GLPI 11 envia como `$_FILES['_uploader_logo_login']`.

**Alterações realizadas:**

1. **Mapeamento de campos flexível**:
   - O código agora procura primeiro `_uploader_*` e depois o nome direto
   - Suporta também o padrão `_prefix_*` e `_*` do uploader multi-arquivo do GLPI

2. **Lógica de movimentação corrigida**:
   - Usa `is_uploaded_file()` para verificar se é upload direto
   - Usa `move_uploaded_file()` para uploads via `$_FILES`
   - Usa `copy()` para arquivos temporários do GLPI

3. **Mensagens de erro melhoradas**:
   - Agora incluem o nome do campo que falhou

**Código-chave adicionado em `Config.php`:**
```php
$file_fields = [
    'logo_login'       => ['_uploader_logo_login', 'logo_login'],
    'logo_expanded'    => ['_uploader_logo_expanded', 'logo_expanded'],
    'logo_collapsed'   => ['_uploader_logo_collapsed', 'logo_collapsed'],
    'favicon'          => ['_uploader_favicon', 'favicon'],
    'background_login' => ['_uploader_background_login', 'background_login'],
];
```

## Próximos passos para validação

**1) Copiar arquivo corrigido para o container**
```bash
docker cp branding/src/Config.php glpi:/var/glpi/marketplace/branding/src/Config.php
```

**2) Limpar cache**
```bash
docker exec -it glpi bash
rm -rf /var/glpi/files/_cache/twig
php /var/www/glpi/bin/console glpi:cache:clear
```

**3) Testar upload**
- Acessar Administração > Entidades > Entidade raiz > aba Branding
- Fazer upload de um logo
- Verificar se o arquivo foi salvo

**4) Confirmar escrita**
```bash
ls -la /var/glpi/files/_plugins/branding
```

**5) Confirmar persistência no banco**
```bash
docker exec -it glpi-db mysql -u glpi -p glpi -e "SELECT id, entities_id, logo_login, logo_expanded FROM glpi_plugin_branding_configs;"
```

## Comandos úteis (validados)

- Ativar plugin:
  - `php /var/www/glpi/bin/console glpi:plugin:activate branding -vvv`
- Desativar plugin:
  - `php /var/www/glpi/bin/console glpi:plugin:deactivate branding -vvv`
- Limpar cache:
  - `rm -rf /var/glpi/files/_cache/twig`
  - `php /var/www/glpi/bin/console glpi:cache:clear`
- Ver logs:
  - `tail -n 200 /var/glpi/logs/php-errors.log`
  - `tail -n 200 /var/glpi/logs/branding-upload.log`

## Estado atual resumido

- ✅ Plugin ativa, cores aplicam.
- ✅ Correção implementada para upload com prefixo `_uploader_*`.
- ⏳ Pendente: validação no ambiente Docker após copiar o código corrigido.

