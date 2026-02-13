# 🚀 Quick Start - Branding Plugin

Guia rápido para colocar o plugin funcionando em 5 minutos!

## ⚡ Instalação Rápida

### 1. Download e Extração (30 segundos)

```bash
cd /var/www/html/glpi/plugins
git clone https://github.com/pluginsGLPI/branding.git
cd branding
chown -R www-data:www-data .
```

### 2. Instalação via Interface (2 minutos)

1. Acesse: **Configurar > Plugins**
2. Encontre "Branding"
3. Clique **Instalar** → **Ativar**

### 3. Configuração Básica (2 minutos)

1. Vá em: **Administração > Entidades > Entidade raiz**
2. Clique na aba **Branding**
3. Configure:
   ```
   ✅ Ativar personalização: Sim
   📁 Logo da página de login: [escolha sua imagem]
   🎨 Cor primária: #2c3e50
   🎨 Cor secundária: #3498db
   ```
4. Clique **Atualizar**

### 4. Teste (30 segundos)

1. Faça **Logout**
2. Veja seu logo na tela de login!
3. Faça **Login**
4. Veja as cores aplicadas!

## 🎨 Exemplos de Configuração

### Tema Corporativo Azul

```yaml
Cores Dia:
  Primária: #0066cc
  Secundária: #004c99
  Fundo: #f5f7fa
  Texto: #2c3e50
  Sidebar BG: #1a2332
  Sidebar Text: #ffffff

Cores Noite:
  Primária: #003d7a
  Secundária: #002952
  Fundo: #0d1117
  Texto: #c9d1d9
  Sidebar BG: #161b22
  Sidebar Text: #8b949e
```

### Tema Moderno Verde

```yaml
Cores Dia:
  Primária: #27ae60
  Secundária: #16a085
  Fundo: #ecf0f1
  Texto: #2c3e50
  Sidebar BG: #2c3e50
  Sidebar Text: #ecf0f1

Cores Noite:
  Primária: #1e8449
  Secundária: #117864
  Fundo: #1c2833
  Texto: #ecf0f1
  Sidebar BG: #17202a
  Sidebar Text: #aab7b8
```

### Tema Dark Mode Completo

```yaml
Cores Dia: (mesmo do default)
  
Cores Noite:
  Primária: #1f6feb
  Secundária: #58a6ff
  Fundo: #0d1117
  Texto: #c9d1d9
  Sidebar BG: #161b22
  Sidebar Text: #8b949e

Horário:
  Tema dia inicia: 07:00
  Tema noite inicia: 19:00
```

## 🖼️ Tamanhos de Imagem Recomendados

| Tipo | Tamanho | Formato | Uso |
|------|---------|---------|-----|
| Logo Login | 300x100px | PNG | Tela de login |
| Logo Sidebar (expandida) | 200x50px | PNG | Menu lateral aberto |
| Logo Sidebar (colapsada) | 40x40px | PNG | Menu lateral fechado |
| Favicon | 32x32px | ICO/PNG | Ícone da aba |
| Background Login | 1920x1080px | JPG | Fundo da tela de login |

## 🔥 Dicas Pro

### 1. CSS Customizado para Cards com Sombra

```css
.card {
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
}
```

### 2. Botões Arredondados

```css
.btn {
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s;
}

.btn:hover {
    transform: scale(1.05);
}
```

### 3. Sidebar com Gradiente

```css
.sidebar {
    background: linear-gradient(180deg, #2c3e50 0%, #34495e 100%) !important;
}
```

### 4. Login com Blur no Background

```css
body.login-page {
    backdrop-filter: blur(10px);
}

body.login-page::before {
    content: '';
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.3);
    z-index: -1;
}
```

## 🐛 Problemas Comuns

### Plugin não aparece?
```bash
# Verifique permissões
chown -R www-data:www-data /var/www/html/glpi/plugins/branding

# Verifique nome do diretório (deve ser "branding", minúsculo)
ls -la /var/www/html/glpi/plugins/
```

### Imagens não carregam?
```bash
# Crie diretório de uploads
mkdir -p /var/www/html/glpi/files/_plugins/branding
chown www-data:www-data /var/www/html/glpi/files/_plugins/branding
chmod 755 /var/www/html/glpi/files/_plugins/branding
```

### CSS não aplica?
```
1. Limpe cache do navegador (Ctrl+Shift+R)
2. Verifique se "Ativar personalização" = Sim
3. Tente o modo de recuperação: ?disable_branding=1
```

## 🔌 API Rápida

```bash
# Ver configuração atual
curl http://localhost/glpi/plugins/branding/api.php?entity_id=0

# Resposta JSON com todas as configurações
{
    "success": true,
    "entity_id": 0,
    "enabled": true,
    "colors": {...},
    "assets": {...}
}
```

## 📚 Próximos Passos

Agora que está funcionando:

1. 📖 Leia o [README.md](README.md) completo
2. 🎨 Experimente diferentes [esquemas de cores](https://coolors.co/)
3. 🏢 Configure para [múltiplas entidades](README.md#-multi-entidade)
4. 🔌 Explore a [API REST](README.md#-api-rest)
5. 💻 Customize o [CSS avançado](README.md#-css-customizado)

## 🆘 Precisa de Ajuda?

- 📖 [Documentação Completa](README.md)
- 🔧 [Guia de Instalação](INSTALL.md)
- 💬 [Forum GLPI](https://forum.glpi-project.org/)
- 🐛 [Report Issues](https://github.com/pluginsGLPI/branding/issues)

---

**Pronto! Seu GLPI agora tem cara de empresa! 🎉**
