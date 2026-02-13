# 🎨 Plugin Branding para GLPI 11

Plugin profissional de branding para GLPI 11 que permite personalização completa da interface, incluindo logos, cores, temas dia/noite automáticos e CSS customizado por entidade.

## ✨ Funcionalidades

### 🎯 Personalização Visual Completa
- **Logos personalizados** por entidade:
  - Logo da página de login
  - Logo do menu lateral (expandido)
  - Logo do menu lateral (colapsado)
  - Favicon customizado
  - Background da página de login

### 🌓 Temas Dia/Noite Automáticos
- Alternância automática de cores baseada em horário
- Configuração de horário de início do tema dia/noite
- Esquemas de cores completamente customizáveis:
  - Cor primária
  - Cor secundária
  - Cor de fundo
  - Cor do texto
  - Cor de fundo da sidebar
  - Cor do texto da sidebar

### 🎨 CSS Customizado
- Editor de CSS integrado
- Aplicação em tempo real
- Suporte a regras CSS complexas

### 🏢 Multi-Entidade
- Configuração por entidade
- Suporte a recursividade
- Herança automática de configurações

### 🔌 API REST
- Endpoint público para consulta de configurações
- Resposta JSON padronizada
- Suporte a CORS
- Documentação incluída

## 📋 Requisitos

- **GLPI**: 11.0.0 ou superior
- **PHP**: 8.1 ou superior
- **Extensões PHP**: json, gd, mbstring, mysqli, curl, fileinfo

## 📥 Instalação

### Método 1: Via Interface do GLPI

1. Baixe o plugin:
   ```bash
   cd plugins
   git clone https://github.com/pluginsGLPI/branding.git
   ```

2. Acesse: **Configurar > Plugins**

3. Clique em **Instalar** no plugin Branding

4. Clique em **Ativar**

### Método 2: Via Linha de Comando

```bash
cd /var/www/html/glpi/plugins
git clone https://github.com/pluginsGLPI/branding.git
cd branding
composer install --no-dev
chown -R www-data:www-data .
```

Depois instale via interface ou CLI:
```bash
php bin/console glpi:plugin:install branding
php bin/console glpi:plugin:activate branding
```

## ⚙️ Configuração

### 1. Acesso à Configuração

- Vá em: **Administração > Entidades**
- Selecione a entidade desejada
- Clique na aba **Branding**

### 2. Configurações Disponíveis

#### 📸 Logos e Imagens

| Campo | Descrição | Tamanho Recomendado |
|-------|-----------|---------------------|
| Logo da página de login | Exibido na tela de login | 300x100 px |
| Logo da sidebar (expandido) | Menu lateral aberto | 200x50 px |
| Logo da sidebar (colapsado) | Menu lateral fechado | 40x40 px |
| Favicon | Ícone da aba do navegador | 32x32 px ou 16x16 px |
| Background de login | Imagem de fundo da tela de login | 1920x1080 px ou maior |

#### 🎨 Esquemas de Cores

Configure dois esquemas completos:

**Tema Dia** (padrão: 08:00 - 20:00)
- Cores claras e vibrantes
- Ideal para uso durante o dia

**Tema Noite** (padrão: 20:00 - 08:00)
- Cores escuras e suaves
- Reduz fadiga visual à noite

#### 🕐 Horários

- **Início do tema dia**: Horário que ativa o tema claro (ex: 08:00)
- **Início do tema noite**: Horário que ativa o tema escuro (ex: 20:00)

#### 💻 CSS Customizado

```css
/* Exemplo de CSS customizado */

/* Customizar cabeçalho */
.navbar {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Customizar botões */
.btn {
    border-radius: 8px;
    font-weight: 600;
}

/* Customizar cards */
.card {
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.05);
}
```

## 🔌 API REST

### Endpoint

```
GET /plugins/branding/api.php?entity_id=0
```

### Parâmetros

| Parâmetro | Tipo | Obrigatório | Descrição |
|-----------|------|-------------|-----------|
| entity_id | integer | Sim | ID da entidade (0 para raiz) |

### Resposta de Sucesso (200)

```json
{
    "success": true,
    "entity_id": 0,
    "enabled": true,
    "is_recursive": true,
    "schedule_enabled": true,
    "day_start": "08:00:00",
    "night_start": "20:00:00",
    "active_theme": "day",
    "colors": {
        "primary": "#2c3e50",
        "secondary": "#3498db",
        "background": "#f8f9fa",
        "text": "#212529",
        "sidebar_bg": "#343a40",
        "sidebar_text": "#ffffff"
    },
    "assets": {
        "logo_login": "/files/_plugins/branding/logo_login.png",
        "logo_expanded": "/files/_plugins/branding/logo_expanded.png",
        "logo_collapsed": "/files/_plugins/branding/logo_collapsed.png",
        "favicon": "/files/_plugins/branding/favicon.ico",
        "background_login": "/files/_plugins/branding/bg_login.jpg"
    },
    "css_url": "/plugins/branding/css/branding.css.php?entities_id=0"
}
```

### Resposta de Erro (404)

```json
{
    "error": "Not found",
    "message": "No branding configuration found for entity 0"
}
```

### Exemplo de Uso

#### cURL
```bash
curl -X GET "https://glpi.exemplo.com/plugins/branding/api.php?entity_id=0"
```

#### JavaScript/Fetch
```javascript
fetch('https://glpi.exemplo.com/plugins/branding/api.php?entity_id=0')
    .then(response => response.json())
    .then(data => {
        console.log('Tema ativo:', data.active_theme);
        console.log('Cores:', data.colors);
    });
```

#### PHP
```php
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://glpi.exemplo.com/plugins/branding/api.php?entity_id=0");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$branding = json_decode($response, true);
print_r($branding['colors']);
```

## 🆘 Recuperação de Emergência

Se algo der errado e a interface ficar inacessível, use o modo de recuperação:

```
https://seu-glpi.com/?disable_branding=1
```

Este parâmetro desativa temporariamente o CSS do plugin, permitindo acesso à configuração.

## 🔧 Estrutura de Arquivos

```
branding/
├── src/
│   └── Config.php              # Classe principal de configuração
├── public/
│   ├── css/
│   │   └── branding.css.php    # Gerador de CSS dinâmico
│   └── api.php                  # Endpoint REST
├── templates/
│   └── config.html.twig        # Template do formulário
├── ajax/                       # Endpoints AJAX (futuro)
├── front/                      # Páginas frontend (futuro)
├── locales/                    # Traduções
├── sql/                        # Scripts SQL (futuro)
├── setup.php                   # Configuração do plugin
├── hook.php                    # Hooks de instalação/desinstalação
├── composer.json               # Dependências e autoload
└── README.md                   # Este arquivo
```

## 🌐 Internacionalização

O plugin suporta múltiplos idiomas. Para adicionar uma tradução:

1. Copie `locales/en_GB.php` para `locales/pt_BR.php` (ou seu idioma)
2. Traduza as strings no array de retorno

## 🐛 Problemas Conhecidos e Soluções

### CSS não está sendo aplicado
- Verifique se o plugin está ativado
- Limpe o cache do navegador (Ctrl+Shift+R)
- Verifique permissões da pasta `/files/_plugins/branding`

### Imagens não aparecem
- Verifique se o diretório `/files/_plugins/branding` existe
- Verifique permissões (deve ser www-data:www-data)
- Confirme que os arquivos foram enviados corretamente

### Tema não alterna automaticamente
- Verifique se "Habilitar alternância dia/noite" está ativado
- Confirme os horários de início configurados
- Verifique o timezone do servidor PHP

## 🔒 Segurança

- ✅ Proteção contra SQL injection (uso de prepared statements)
- ✅ Validação de uploads de arquivo
- ✅ Sanitização de CSS customizado
- ✅ Permissões baseadas em ACL do GLPI
- ✅ CSRF protection habilitado

## 🤝 Contribuindo

Contribuições são bem-vindas! Por favor:

1. Fork o projeto
2. Crie uma branch para sua feature (`git checkout -b feature/MinhaFeature`)
3. Commit suas mudanças (`git commit -m 'Adiciona MinhaFeature'`)
4. Push para a branch (`git push origin feature/MinhaFeature`)
5. Abra um Pull Request

## 📝 Changelog

### v2.0.1 (2026-02-03)
- 🔌 Registro stateless para endpoints públicos (API e CSS)
- 🌐 Tradução base en_GB adicionada
- 🔄 Migração SQL idempotente v1.x → v2.0
- 🔒 Validação de uploads (extensão, MIME, tamanho)
- 🔒 Sanitização de CSS customizado
- 🐛 Correções de bugs diversos

### v2.0.0 (2025-01-30)
- ✨ Reescrita completa para GLPI 11
- ✨ Suporte a namespaces PSR-4
- ✨ Templates Twig
- ✨ API REST pública
- ✨ Temas dia/noite automáticos
- ✨ Multi-entidade com recursividade
- 🔒 Melhorias de segurança
- 📚 Documentação completa

### v1.0.4 (Legado)
- Versão inicial para GLPI 10

## 📄 Licença

Este plugin é software livre e está licenciado sob a **GNU General Public License v2.0 ou posterior**.

Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

## 👥 Autores

- **Branding Plugin Team** - *Desenvolvimento inicial*
- **Comunidade GLPI** - *Contribuições e feedback*

## 🙏 Agradecimentos

- Equipe do GLPI Project
- Comunidade de plugins do GLPI
- Todos os contribuidores

## 📞 Suporte

- **Issues**: https://github.com/pluginsGLPI/branding/issues
- **Forum GLPI**: https://forum.glpi-project.org/
- **Documentação GLPI**: https://glpi-project.org/documentation/

---

**Desenvolvido com ❤️ para a comunidade GLPI**
