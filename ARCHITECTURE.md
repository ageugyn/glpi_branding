# 🏗️ Arquitetura do Plugin Branding

Documentação técnica da arquitetura e implementação do plugin.

## 📐 Visão Geral

O plugin Branding segue os padrões modernos do GLPI 11, utilizando:

- **PSR-4 Autoloading** para classes PHP
- **Twig Templates** para renderização de views
- **DB request API + Migration** para queries seguras e DDL controlado
- **REST API** para integrações externas
- **Multi-entity Support** com herança recursiva

## 🗂️ Estrutura de Diretórios

```
branding/
├── src/                          # Classes PHP (PSR-4)
│   └── Config.php                # Classe principal de configuração
│
├── public/                       # Arquivos públicos acessíveis via web
│   ├── css/
│   │   └── branding.css.php      # Gerador de CSS dinâmico
│   └── api.php                    # Endpoint REST API
│
├── templates/                    # Templates Twig
│   └── config.html.twig          # Formulário de configuração
│
├── front/                        # Páginas frontend do GLPI
│   └── config.form.php           # Página do formulário
│
├── ajax/                         # Endpoints AJAX (futuro)
│
├── locales/                      # Traduções i18n
│   ├── en_GB.php                 # Tradução inglês
│   └── pt_BR.php                 # Tradução português
│
├── sql/                          # Scripts SQL
│   └── update-1.0-2.0.sql        # Migração v1 → v2
│
├── examples/                     # Exemplos de uso
│   └── api_test.php              # Teste da API
│
├── tools/                        # Ferramentas auxiliares
│   └── check_system.php          # Verificação do sistema
│
├── setup.php                     # Configuração do plugin
├── hook.php                      # Hooks de instalação/desinstalação
├── composer.json                 # Dependências e autoload
├── branding.xml                  # Metadata para marketplace
│
├── README.md                     # Documentação principal
├── INSTALL.md                    # Guia de instalação
├── QUICKSTART.md                 # Início rápido
├── CONTRIBUTING.md               # Guia de contribuição
├── CHANGELOG.md                  # Histórico de mudanças
└── ARCHITECTURE.md               # Este arquivo
```

## 🔌 Fluxo de Funcionamento

### 1. Inicialização (setup.php)

```
GLPI Boot
    ↓
plugin_init_branding()
    ↓
Registrar Classe Config
    ↓
Adicionar Hooks de CSS
    ↓
Registrar API Endpoints
```

### 2. Renderização de CSS

```
Requisição HTTP
    ↓
public/css/branding.css.php
    ↓
Config::getForEntity($entity_id)
    ↓
Config::getActiveColors($config)
    ↓
Gerar CSS dinâmico
    ↓
Output com header text/css
```

### 3. Configuração (Entity Tab)

```
Entity Form
    ↓
Config::getTabNameForItem()
    ↓
Config::displayTabContentForItem()
    ↓
Config::showForEntity()
    ↓
Render Twig Template
    ↓
User Submit
    ↓
Config::prepareInputForUpdate()
    ↓
Upload files + JSON encode
    ↓
Save to Database
```

### 4. API REST

```
HTTP GET /plugins/branding/api.php?entity_id=0
    ↓
Config::getForEntity($entity_id)
    ↓
Config::getActiveColors($config)
    ↓
Build JSON Response
    ↓
Return with CORS headers
```

## 🗄️ Estrutura do Banco de Dados

### Tabela: glpi_plugin_branding_configs

```sql
CREATE TABLE `glpi_plugin_branding_configs` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `entities_id` int unsigned NOT NULL DEFAULT '0',
    `is_recursive` tinyint NOT NULL DEFAULT '0',
    `enabled` tinyint NOT NULL DEFAULT '0',
    
    -- Imagens
    `logo_login` varchar(255) DEFAULT NULL,
    `logo_expanded` varchar(255) DEFAULT NULL,
    `logo_collapsed` varchar(255) DEFAULT NULL,
    `favicon` varchar(255) DEFAULT NULL,
    `background_login` varchar(255) DEFAULT NULL,
    
    -- Cores
    `colors_day` json DEFAULT NULL,
    `colors_night` json DEFAULT NULL,
    
    -- Agendamento
    `schedule_enabled` tinyint NOT NULL DEFAULT '0',
    `day_start` time DEFAULT '08:00:00',
    `night_start` time DEFAULT '20:00:00',
    
    -- CSS Customizado
    `custom_css` text,
    
    -- Timestamps
    `date_creation` timestamp NULL DEFAULT NULL,
    `date_mod` timestamp NULL DEFAULT NULL,
    
    PRIMARY KEY (`id`),
    KEY `entities_id` (`entities_id`),
    KEY `is_recursive` (`is_recursive`),
    KEY `date_mod` (`date_mod`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
```

### Estrutura JSON das Cores

```json
{
    "primary": "#2c3e50",
    "secondary": "#3498db",
    "background": "#f8f9fa",
    "text": "#212529",
    "sidebar_bg": "#343a40",
    "sidebar_text": "#ffffff"
}
```

## 🔐 Segurança

### Proteções Implementadas

1. **SQL Injection**
   - ✅ Uso exclusivo de QueryBuilder/Prepared Statements
   - ✅ Sem concatenação direta de SQL
   - ✅ Validação de tipos (int, string, etc.)

2. **XSS (Cross-Site Scripting)**
   - ✅ Twig auto-escape habilitado
   - ✅ Html::cleanOutput() em saídas PHP
   - ✅ Sanitização de CSS customizado

3. **CSRF (Cross-Site Request Forgery)**
   - ✅ Token CSRF do GLPI habilitado
   - ✅ Verificação em todos os forms

4. **File Upload**
   - ✅ Validação de extensão
   - ✅ Nome aleatório (uniqid)
   - ✅ Diretório dedicado com permissões corretas
   - ✅ Move_uploaded_file (não copy)

5. **ACL (Access Control)**
   - ✅ Session::checkRight() em todas as páginas
   - ✅ Permissões baseadas em entidade
   - ✅ Verificação de UPDATE/CREATE/DELETE

### Checklist de Código Seguro

```php
// ❌ NUNCA FAÇA
$DB->query("SELECT * FROM table WHERE id = " . $_GET['id']);
echo $_POST['user_input'];

// ✅ SEMPRE FAÇA
$DB->request([
    'FROM' => 'table',
    'WHERE' => ['id' => (int)$_GET['id']]
]);
echo Html::cleanOutput($_POST['user_input']);
```

## ⚡ Performance

### Otimizações Implementadas

1. **Cache de Consultas**
   - Configuração carregada apenas quando necessário
   - Busca recursiva com early return

2. **CSS Dinâmico**
   - Gerado apenas quando solicitado
   - Headers corretos para cache do browser
   - Minificação via compressão gzip

3. **Índices de Banco**
   - `entities_id` para busca por entidade
   - `is_recursive` para filtros
   - `date_mod` para ordenação

4. **Queries Otimizadas**
   ```php
   // Uma query para tudo, não N+1
   $configs = $DB->request([
       'FROM' => self::getTable(),
       'WHERE' => ['entities_id' => $entities_ids],
       'LIMIT' => 1
   ]);
   ```

## 🧩 Componentes Principais

### Config.php (src/Config.php)

**Responsabilidades:**
- Gerenciar configurações de branding
- Interface com banco de dados
- Renderização do formulário Twig
- Upload e gestão de arquivos
- Lógica de temas dia/noite

**Métodos Principais:**
```php
getForEntity($entity_id, $recursive)  // Busca config da entidade
getActiveColors($config)               // Retorna cores ativas
showForEntity($entity)                 // Exibe formulário
prepareInputForAdd/Update($input)     // Processa dados do form
```

### branding.css.php (public/css/branding.css.php)

**Responsabilidades:**
- Gerar CSS dinâmico
- Aplicar logos via background-image
- Aplicar esquema de cores ativo
- Incluir CSS customizado

**Fluxo:**
```
1. Recebe entity_id via GET
2. Carrega Config::getForEntity()
3. Determina tema ativo (dia/noite)
4. Gera CSS com cores + logos
5. Adiciona custom_css
6. Output com header text/css
```

### api.php (public/api.php)

**Responsabilidades:**
- Endpoint REST público
- Retornar config em JSON
- Suporte a CORS
- Error handling

**Formato de Resposta:**
```json
{
    "success": true,
    "entity_id": 0,
    "enabled": true,
    "active_theme": "day",
    "colors": {...},
    "assets": {...},
    "css_url": "..."
}
```

### config.html.twig (templates/config.html.twig)

**Responsabilidades:**
- Renderizar formulário de configuração
- Upload de arquivos
- Color pickers
- Editor de CSS

**Estrutura:**
```twig
{% extends "generic_show_form.html.twig" %}
{% block form_fields %}
    - Seção: Logos e Imagens
    - Seção: Esquema de Cores (Dia/Noite)
    - Seção: CSS Customizado
{% endblock %}
```

## 🔄 Hooks do GLPI

### Hooks Implementados

1. **plugin_init_branding**
    - Registra classes
    - Adiciona CSS
    - Adiciona favicon no header
    - Configura API

2. **plugin_branding_install**
   - Cria tabela
   - Cria diretórios
   - Executa migrações

3. **plugin_branding_uninstall**
   - Remove tabela
   - (Opcionalmente) remove arquivos

4. **plugin_branding_display_login**
   - Adiciona CSS na página de login

## 🧪 Testabilidade

### Testes Manuais

```bash
# 1. Verificação do sistema
php tools/check_system.php

# 2. Teste da API
php examples/api_test.php

# 3. Teste de instalação
php bin/console glpi:plugin:install branding -vvv

# 4. Teste de CSS
curl http://localhost/glpi/plugins/branding/css/branding.css.php?entities_id=0
```

### Testes Automatizados (Futuro)

```php
// PHPUnit test example
class ConfigTest extends TestCase
{
    public function testGetForEntity()
    {
        $config = Config::getForEntity(0);
        $this->assertIsArray($config);
    }
    
    public function testGetActiveColors()
    {
        $config = ['colors_day' => '{"primary":"#000"}'];
        $colors = Config::getActiveColors($config);
        $this->assertArrayHasKey('primary', $colors);
    }
}
```

## 📊 Métricas

### Complexidade

- **Linhas de código**: ~1,500
- **Arquivos PHP**: 7
- **Templates**: 1
- **Endpoints**: 2 (CSS, API)

### Performance

- **Tempo de carregamento CSS**: < 50ms
- **Queries por requisição**: 1-2
- **Tamanho médio do CSS**: 2-5 KB

## 🚀 Roadmap Futuro

### v2.1.0
- [ ] Interface de preview ao vivo
- [ ] Importar/exportar configurações
- [ ] Histórico de mudanças
- [ ] Testes automatizados (PHPUnit)

### v2.2.0
- [ ] Suporte a múltiplos temas salvos
- [ ] Theme marketplace
- [ ] Gerador de paleta de cores
- [ ] Dark mode detector automático

### v3.0.0
- [ ] Page builder visual
- [ ] Widget customizável
- [ ] Animações e transições
- [ ] Suporte a Web Components

## 📚 Referências

- [GLPI Developer Documentation](https://glpi-developer-documentation.readthedocs.io/)
- [PSR-4 Autoloading](https://www.php-fig.org/psr/psr-4/)
- [Twig Documentation](https://twig.symfony.com/doc/3.x/)
- [REST API Best Practices](https://restfulapi.net/)

## 👥 Contribuindo para a Arquitetura

Ao adicionar novas funcionalidades, siga:

1. **Princípios SOLID**
2. **DRY (Don't Repeat Yourself)**
3. **KISS (Keep It Simple, Stupid)**
4. **Padrões do GLPI 11**
5. **Documentação inline**

---

**Arquitetura sólida = Plugin confiável! 💪**
