# 🤝 Contribuindo para o Branding Plugin

Obrigado pelo interesse em contribuir! Este documento fornece diretrizes para contribuir com o projeto.

## 📋 Código de Conduta

Este projeto segue o [Código de Conduta do GLPI](https://github.com/glpi-project/glpi/blob/main/CODE_OF_CONDUCT.md). Ao participar, você concorda em manter um ambiente respeitoso e inclusivo.

## 🚀 Como Contribuir

### Reportando Bugs

Antes de reportar um bug:

1. ✅ Verifique se já não foi reportado em [Issues](https://github.com/pluginsGLPI/branding/issues)
2. ✅ Use a versão mais recente do plugin
3. ✅ Teste em ambiente limpo se possível

**Template de Bug Report:**

```markdown
**Versão do GLPI:** 11.0.x
**Versão do Plugin:** 2.0.1
**PHP:** 8.1.x
**Navegador:** Chrome 120

**Descrição:**
[Descreva o problema claramente]

**Passos para Reproduzir:**
1. Vá em...
2. Clique em...
3. Veja o erro...

**Comportamento Esperado:**
[O que deveria acontecer]

**Comportamento Atual:**
[O que está acontecendo]

**Screenshots:**
[Se aplicável]

**Logs:**
```
[Cole logs relevantes aqui]
```
```

### Sugerindo Melhorias

Para sugerir melhorias:

1. Abra uma [Issue](https://github.com/pluginsGLPI/branding/issues/new)
2. Use o label `enhancement`
3. Descreva claramente:
   - Problema que resolve
   - Solução proposta
   - Alternativas consideradas
   - Impacto esperado

### Contribuindo com Código

#### 1. Preparação do Ambiente

```bash
# Clone o repositório
git clone https://github.com/pluginsGLPI/branding.git
cd branding

# Instale dependências
composer install

# Crie uma branch para sua feature
git checkout -b feature/minha-feature
```

#### 2. Padrões de Código

##### PHP

Seguimos os padrões do GLPI:

```php
<?php
/**
 * Docblock completo em todas as classes/métodos
 */
namespace GlpiPlugin\Branding;

use CommonDBTM;

class MinhaClasse extends CommonDBTM
{
    /**
     * Método documentado
     *
     * @param string $param Descrição
     * @return bool
     */
    public function meuMetodo($param)
    {
        // Indentação: 4 espaços
        // Chaves na linha seguinte para classes/métodos
        // Chaves na mesma linha para controle de fluxo
        
        if ($condicao) {
            // código
        }
        
        return true;
    }
}
```

##### CSS

```css
/* Seletores claros e específicos */
.branding-container {
    display: flex;
    padding: 1rem;
}

/* Organize por seções */
/* ===== Header ===== */
.branding-header {
    background: #fff;
}
```

##### JavaScript

```javascript
// Use const/let, nunca var
const config = {
    key: 'value'
};

// Funções arrow quando apropriado
const getData = () => {
    return fetch('/api/data');
};
```

#### 3. Commits

Mensagens de commit devem seguir [Conventional Commits](https://www.conventionalcommits.org/):

```bash
# Features
git commit -m "feat: adiciona suporte a tema escuro"

# Correções
git commit -m "fix: corrige erro no upload de logo"

# Documentação
git commit -m "docs: atualiza README com exemplos de API"

# Refatoração
git commit -m "refactor: melhora estrutura da classe Config"

# Testes
git commit -m "test: adiciona testes para API endpoint"

# Performance
git commit -m "perf: otimiza geração de CSS"

# Chore (manutenção)
git commit -m "chore: atualiza dependências"
```

#### 4. Pull Requests

Antes de abrir um PR:

- ✅ Seu código segue os padrões do projeto
- ✅ Você adicionou/atualizou testes (quando aplicável)
- ✅ Você atualizou a documentação (quando aplicável)
- ✅ Seu código passa em todos os testes
- ✅ Você testou manualmente as mudanças

**Template de Pull Request:**

```markdown
## Tipo de Mudança
- [ ] 🐛 Bug fix
- [ ] ✨ Nova feature
- [ ] 🔒 Segurança
- [ ] 📚 Documentação
- [ ] 🔄 Refatoração
- [ ] ⚡ Performance

## Descrição
[Descreva suas mudanças]

## Issue Relacionada
Closes #123

## Como Testar
1. Instale o plugin
2. Vá em...
3. Verifique que...

## Screenshots
[Se aplicável]

## Checklist
- [ ] Código segue os padrões
- [ ] Documentação atualizada
- [ ] Testes adicionados/atualizados
- [ ] Testado manualmente
- [ ] Sem warnings ou erros
```

## 🧪 Testes

### Testes Manuais

```bash
# 1. Instale em ambiente de desenvolvimento
cd /var/www/html/glpi/plugins
ln -s /caminho/para/seu/fork branding

# 2. Instale e ative
php bin/console glpi:plugin:install branding
php bin/console glpi:plugin:activate branding

# 3. Teste todas as funcionalidades
- Upload de logos
- Alternância de temas
- API endpoint
- Permissões
- Multi-entidade
```

### Testes Automatizados (futuro)

```bash
# PHPUnit
vendor/bin/phpunit

# PHP-CS-Fixer
vendor/bin/php-cs-fixer fix --dry-run --diff

# PHPStan
vendor/bin/phpstan analyse
```

## 📚 Documentação

### Documentando Código

```php
/**
 * Descrição breve do método
 *
 * Descrição detalhada se necessário. Explique:
 * - O que o método faz
 * - Por que é necessário
 * - Como usar
 *
 * @param string $param1 Descrição do parâmetro
 * @param int    $param2 Descrição do parâmetro
 * @return bool          Descrição do retorno
 * @throws Exception     Quando acontece X
 *
 * @example
 * ```php
 * $result = $obj->metodo('valor', 123);
 * ```
 */
public function metodo($param1, $param2)
{
    // Implementação
}
```

### Documentando Funcionalidades

Atualize o README.md com:
- Descrição da nova feature
- Exemplos de uso
- Screenshots
- Configurações necessárias

## 🏗️ Estrutura do Projeto

```
branding/
├── src/               # Classes PHP (PSR-4)
│   └── Config.php
├── public/            # Arquivos públicos
│   ├── css/
│   │   └── branding.css.php
│   └── api.php
├── templates/         # Twig templates
│   └── config.html.twig
├── ajax/              # Endpoints AJAX (reservado)
├── front/             # Páginas frontend
│   └── config.form.php
├── locales/           # Traduções
│   ├── en_GB.php
│   └── pt_BR.php
├── sql/               # Scripts SQL
│   └── update-1.0-2.0.sql
├── examples/          # Exemplos de uso
├── tools/             # Ferramentas auxiliares
├── setup.php          # Configuração do plugin
├── hook.php           # Hooks de instalação
└── *.md               # Documentação
```

## 🌍 Internacionalização

### Adicionando Traduções

```php
// No código
__('Text to translate', 'branding')

// No template Twig
{{ __('Text to translate', 'branding') }}
```

### Criando Arquivo de Tradução

1. Copie `locales/en_GB.php` para `locales/pt_BR.php`
2. Traduza as strings
3. Teste no GLPI

## 📝 Revisão de Código

Pull requests serão revisados considerando:

- ✅ **Funcionalidade**: O código faz o que promete?
- ✅ **Qualidade**: Código limpo, legível e mantível?
- ✅ **Performance**: Não introduz gargalos?
- ✅ **Segurança**: Sem vulnerabilidades?
- ✅ **Compatibilidade**: Funciona em GLPI 11+?
- ✅ **Documentação**: Está documentado?
- ✅ **Testes**: Tem cobertura adequada?

## 💡 Dicas

### Debugging

```php
// Use logs do GLPI
Toolbox::logDebug('Mensagem de debug');
Toolbox::logWarning('Aviso');
Toolbox::logError('Erro');

// Ative logs em Setup > Logs
```

### Performance

```php
// Use cache quando possível
if ($cached = Cache::get('branding_config_' . $entity_id)) {
    return $cached;
}

// Evite queries em loops
$configs = Config::getAll(); // Uma query
foreach ($configs as $config) {
    // Processa
}
```

### Segurança

```php
// NUNCA concatene SQL
$DB->query("SELECT * FROM table WHERE id = " . $id); // ❌ ERRADO

// Use prepared statements ou QueryBuilder
$DB->request([
    'FROM' => 'table',
    'WHERE' => ['id' => $id]
]); // ✅ CORRETO

// Sanitize output
echo Html::cleanOutput($user_input);
```

## 📧 Contato

- **Issues**: https://github.com/pluginsGLPI/branding/issues
- **Forum**: https://forum.glpi-project.org/
- **Email**: branding@glpi-project.org

## 📜 Licença

Ao contribuir, você concorda que suas contribuições serão licenciadas sob a mesma licença do projeto (GPLv2+).

---

**Obrigado por contribuir! 🎉**
