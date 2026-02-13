# Changelog

Todas as mudanças notáveis neste projeto serão documentadas neste arquivo.

O formato é baseado em [Keep a Changelog](https://keepachangelog.com/pt-BR/1.0.0/),
e este projeto adere ao [Semantic Versioning](https://semver.org/lang/pt-BR/).

## [2.0.1] - 2026-02-03

### Adicionado
- 🔌 Registro stateless para `public/api.php` no Firewall
- 🌐 Tradução base `en_GB.php`

### Alterado
- 🔄 Migração SQL idempotente para v1.x → v2.0
- 🔄 Recursividade respeita `is_recursive` (herança controlada)
- 🔄 Lógica de tema ativo unificada (API e CSS)
- 🔄 Sanitização básica do CSS customizado
- 🔄 Validação de uploads (extensão, MIME, tamanho)

### Corrigido
- 🐛 Regex do Firewall para scripts legacy
- 🐛 Aba de Entidades sem `countForItem()`
- 🐛 Template Twig sem filtro `json_decode`
- 🐛 Defaults ausentes no formulário
- 🐛 `GLPI_ROOT` redefinido em endpoints públicos
- 🐛 Favicon não aplicado em páginas autenticadas
- 🐛 Upload de arquivos não funcionava no GLPI 11 (prefixo `_uploader_`)

## [2.0.0] - 2025-01-30

### Adicionado
- ✨ Suporte completo para GLPI 11.0+
- ✨ Arquitetura PSR-4 com namespaces
- ✨ Templates Twig para interface moderna
- ✨ API REST pública em `/plugins/branding/api.php`
- ✨ Temas dia/noite automáticos baseados em horário
- ✨ Suporte a múltiplas entidades com recursividade
- ✨ Editor de CSS customizado integrado
- ✨ Upload de logos (login, sidebar expandida/colapsada, favicon)
- ✨ Background customizado para página de login
- ✨ Esquemas de cores completos (dia e noite)
- ✨ Documentação completa em português
- ✨ Modo de recuperação `?disable_branding=1`

### Alterado
- 🔄 Migrado de SQL direto para QueryBuilder do GLPI
- 🔄 Removido código procedural, agora 100% OOP
- 🔄 Interface de configuração totalmente redesenhada
- 🔄 Estrutura de tabelas melhorada com tipos corretos
- 🔄 Sistema de permissões usando ACLs do GLPI

### Corrigido
- 🐛 SQL injection no install.php
- 🐛 Typo "cache" no uninstall (era DROP TABLE ... cache)
- 🐛 CSS não sendo aplicado em algumas páginas
- 🐛 Conflitos com outros plugins de temas
- 🐛 Problemas de encode em caracteres especiais
- 🐛 Timezone incorreto na alternância dia/noite

### Segurança
- 🔒 Prepared statements em todas as queries
- 🔒 Validação de uploads de arquivo
- 🔒 Sanitização de CSS customizado
- 🔒 CSRF protection habilitado
- 🔒 ACL corretamente implementada

### Removido
- ❌ Código legado do GLPI 9.x
- ❌ jQuery UI dependencies
- ❌ Código procedural
- ❌ SQL queries diretas

## [1.0.4] - 2024-XX-XX (Versão Legado)

### Adicionado
- Suporte básico para GLPI 10.x
- Logo customizado
- CSS básico

### Conhecido Issues
- SQL injection vulnerability
- Sem suporte a entidades
- Sem API
- CSS estático

---

## Legendas

- ✨ Nova funcionalidade
- 🔄 Mudança/Melhoria
- 🐛 Correção de bug
- 🔒 Segurança
- 📚 Documentação
- ❌ Removido
- ⚠️ Depreciado
