# 📦 Guia de Instalação do Branding Plugin

Este guia fornece instruções detalhadas para instalar, configurar e solucionar problemas do plugin Branding para GLPI 11.

## 📋 Pré-requisitos

### Software Necessário

| Software | Versão Mínima | Versão Recomendada |
|----------|---------------|-------------------|
| GLPI | 11.0.0 | 11.0.2+ |
| PHP | 8.1 | 8.2+ |
| MySQL/MariaDB | 5.7 / 10.2 | 8.0 / 10.11+ |
| Apache/Nginx | 2.4 / 1.18 | 2.4+ / 1.24+ |

### Extensões PHP Requeridas

```bash
# Extensões básicas do GLPI já incluem:
php-json
php-gd
php-mysqli
php-mbstring
php-curl
php-fileinfo
```

### Verificação do Sistema

Execute no servidor:

```bash
# Verificar versão do PHP
php -v

# Verificar extensões instaladas
php -m | grep -E 'json|gd|mysqli|mbstring'

php -m | grep -E 'curl|gd|intl|mysqli|mbstring|openssl|zlib|bcmath' 

# Verificar versão do GLPI
grep "define('GLPI_VERSION'" /var/www/html/glpi/inc/define.php
```

## 🔧 Instalação

### Método 1: Via Git (Recomendado para Desenvolvimento)

```bash
# 1. Navegue até o diretório de plugins do GLPI
cd /var/www/html/glpi/plugins

# 2. Clone o repositório
git clone https://github.com/pluginsGLPI/branding.git

# 3. Entre no diretório
cd branding

# 4. Instale dependências do Composer (se necessário)
composer install --no-dev --optimize-autoloader

# 5. Ajuste permissões
chown -R www-data:www-data .
chmod -R 755 .

# 6. Crie diretório de upload
mkdir -p /var/www/html/glpi/files/_plugins/branding
chown www-data:www-data /var/www/html/glpi/files/_plugins/branding
chmod 755 /var/www/html/glpi/files/_plugins/branding
```

### Método 2: Via Download de Release

```bash
# 1. Baixe a última release
cd /tmp
wget https://github.com/pluginsGLPI/branding/releases/download/v2.0.1/glpi-branding-2.0.1.tar.gz

# 2. Extraia para o diretório de plugins
tar -xzf glpi-branding-2.0.1.tar.gz -C /var/www/html/glpi/plugins/

# 3. Ajuste permissões
cd /var/www/html/glpi/plugins/branding
chown -R www-data:www-data .

# 4. Crie diretório de upload
mkdir -p /var/www/html/glpi/files/_plugins/branding
chown www-data:www-data /var/www/html/glpi/files/_plugins/branding
```

### Método 3: Via Interface do GLPI (Marketplace)

1. Acesse: **Configurar > Plugins > Marketplace**
2. Busque por "Branding"
3. Clique em **Instalar**
4. Aguarde o download e instalação
5. Clique em **Ativar**

**Nota:** plugins instalados via Marketplace ficam no diretório de dados do GLPI
(ex.: `GLPI_VAR_DIR/marketplace/branding`), não em `plugins/`.

## ⚙️ Configuração Inicial

### 1. Ativar o Plugin

#### Via Interface Web:

1. Acesse: **Configurar > Plugins**
2. Localize "Branding"
3. Clique em **Instalar**
4. Clique em **Ativar**

#### Via Linha de Comando:

```bash
cd /var/www/html/glpi

# Instalar
php /var/www/glpi/bin/console glpi:plugin:install branding

# Ativar
php /var/www/glpi/bin/console glpi:plugin:activate branding

# Verificar status
php bin/console glpi:plugin:list
```

### 2. Configurar Permissões

Certifique-se de que o usuário do webserver tem permissões corretas:

```bash
# Apache/Ubuntu-Debian
chown -R www-data:www-data /var/www/html/glpi/plugins/branding
chown -R www-data:www-data /var/www/html/glpi/files/_plugins/branding

# Apache/RHEL-CentOS
chown -R apache:apache /var/www/html/glpi/plugins/branding
chown -R apache:apache /var/www/html/glpi/files/_plugins/branding

# Nginx
chown -R nginx:nginx /var/www/html/glpi/plugins/branding
chown -R nginx:nginx /var/www/html/glpi/files/_plugins/branding
```

### 3. Verificar Instalação

Acesse o arquivo de teste:

```bash
# Via browser
https://seu-glpi.com/plugins/branding/api.php?entity_id=0

# Via cURL
curl https://seu-glpi.com/plugins/branding/api.php?entity_id=0
```

Resposta esperada (sem configuração):
```json
{
    "error": "Not found",
    "message": "No branding configuration found for entity 0"
}
```

## 🎨 Primeira Configuração

### 1. Acessar Configuração

1. Vá em: **Administração > Entidades**
2. Clique em "Entidade raiz" (ou sua entidade)
3. Clique na aba **Branding**

### 2. Habilitar Branding

1. Marque: **Ativar personalização** = Sim
2. Configure conforme necessário
3. Clique em **Atualizar**

### 3. Upload de Logos

Formatos suportados:
- PNG (recomendado - suporta transparência)
- JPG/JPEG
- GIF
- WebP

Tamanhos recomendados:
- Logo login: 300x100px
- Logo sidebar expandida: 200x50px
- Logo sidebar colapsada: 40x40px
- Favicon: 32x32px ou 16x16px
- Background login: 1920x1080px ou maior

### 4. Configurar Cores

Use cores em formato hexadecimal:
- `#2c3e50` - Azul escuro
- `#3498db` - Azul claro
- `#f8f9fa` - Cinza muito claro
- `#ffffff` - Branco
- `#000000` - Preto

### 5. Testar

1. Faça logout
2. Verifique a tela de login
3. Faça login
4. Verifique logos na sidebar

## 🔍 Verificação Pós-Instalação

Execute este checklist:

```bash
# ✅ Plugin instalado?
ls -la /var/www/html/glpi/plugins/branding/

# ✅ Permissões corretas?
ls -la /var/www/html/glpi/files/_plugins/

# ✅ Tabela criada?
mysql -u root -p glpi -e "SHOW TABLES LIKE 'glpi_plugin_branding%';"

# ✅ API respondendo?
curl http://localhost/glpi/plugins/branding/api.php?entity_id=0

# ✅ CSS sendo gerado?
curl http://localhost/glpi/plugins/branding/css/branding.css.php?entities_id=0
```

## 🐛 Solução de Problemas

### Problema 1: Plugin não aparece na lista

**Sintomas:**
- Plugin não visível em Configurar > Plugins

**Soluções:**
```bash
# 1. Verificar nome do diretório
ls -la /var/www/html/glpi/plugins/ | grep branding

# 2. Verificar permissões
ls -la /var/www/html/glpi/plugins/branding/

# 3. Verificar logs do PHP
tail -f /var/log/apache2/error.log  # Apache
tail -f /var/log/nginx/error.log    # Nginx

# 4. Verificar syntax do PHP
php -l /var/www/html/glpi/plugins/branding/setup.php
```

### Problema 2: Erro na instalação

**Sintomas:**
- Mensagem de erro ao clicar em Instalar

**Soluções:**
```bash
# 1. Verificar permissões do banco
mysql -u root -p -e "SHOW GRANTS FOR 'glpi'@'localhost';"

# 2. Verificar logs do GLPI
tail -f /var/www/html/glpi/files/_log/php-errors.log
tail -f /var/www/html/glpi/files/_log/sql-errors.log

# 3. Instalar via CLI para ver erros
php bin/console glpi:plugin:install branding -vvv
```

### Problema 3: CSS não aplicado

**Sintomas:**
- Logos não aparecem
- Cores não mudam

**Soluções:**
```bash
# 1. Limpar cache do navegador (Ctrl+Shift+R)

# 2. Verificar se CSS está sendo gerado
curl http://localhost/glpi/plugins/branding/css/branding.css.php?entities_id=0

# 3. Verificar configuração
mysql -u root -p glpi -e "SELECT * FROM glpi_plugin_branding_configs WHERE entities_id = 0;"

# 4. Verificar se está habilitado
# enabled = 1 na query acima

# 5. Usar modo de recuperação
https://seu-glpi.com/?disable_branding=1
```

### Problema 4: Imagens não carregam

**Sintomas:**
- Imagens retornam 404

**Soluções:**
```bash
# 1. Verificar se arquivos existem
ls -la /var/www/html/glpi/files/_plugins/branding/

# 2. Verificar permissões
chown www-data:www-data /var/www/html/glpi/files/_plugins/branding/*
chmod 644 /var/www/html/glpi/files/_plugins/branding/*

# 3. Verificar configuração do webserver
# Apache
sudo apache2ctl -S | grep branding

# Nginx
sudo nginx -t
```

### Problema 5: Alternância dia/noite não funciona

**Sintomas:**
- Sempre usa mesmo tema

**Soluções:**
```bash
# 1. Verificar horário do servidor
date

# 2. Verificar timezone do PHP
php -i | grep date.timezone

# 3. Ajustar timezone se necessário
echo "date.timezone = America/Sao_Paulo" >> /etc/php/8.1/apache2/php.ini
systemctl restart apache2

# 4. Verificar se schedule está habilitado
mysql -u root -p glpi -e "SELECT schedule_enabled, day_start, night_start FROM glpi_plugin_branding_configs WHERE entities_id = 0;"
```

## 🔄 Atualização

### De v1.x para v2.0

```bash
# 1. Backup do banco de dados
mysqldump -u root -p glpi > backup_glpi_$(date +%Y%m%d).sql

# 2. Backup dos arquivos
tar -czf backup_branding_$(date +%Y%m%d).tar.gz /var/www/html/glpi/plugins/branding

# 3. Desativar plugin antigo
php bin/console glpi:plugin:deactivate branding

# 4. Remover versão antiga
rm -rf /var/www/html/glpi/plugins/branding

# 5. Instalar nova versão (seguir método 1 ou 2 acima)

# 6. Executar script de migração (se necessário)
mysql -u root -p glpi < /var/www/html/glpi/plugins/branding/sql/update-1.0-2.0.sql

# 7. Instalar e ativar
php bin/console glpi:plugin:install branding
php bin/console glpi:plugin:activate branding
```

## 📊 Logs e Debug

### Ativar Debug do GLPI

1. Vá em: **Configurar > Geral > Sistema**
2. Ative: **Modo de debug**
3. Configure: **Logs in files = SQL + WARNING + ERROR + CRITICAL**

### Ver Logs

```bash
# Logs do GLPI
tail -f /var/www/html/glpi/files/_log/php-errors.log
tail -f /var/www/html/glpi/files/_log/sql-errors.log

# Logs do Apache
tail -f /var/log/apache2/error.log
tail -f /var/log/apache2/access.log

# Logs do Nginx
tail -f /var/log/nginx/error.log
tail -f /var/log/nginx/access.log

# Logs do PHP-FPM
tail -f /var/log/php8.1-fpm.log
```

## 🔐 Segurança

### Checklist de Segurança

```bash
# ✅ Permissões corretas?
find /var/www/html/glpi/plugins/branding -type f -exec chmod 644 {} \;
find /var/www/html/glpi/plugins/branding -type d -exec chmod 755 {} \;

# ✅ Diretórios de upload protegidos?
ls -la /var/www/html/glpi/files/_plugins/branding/.htaccess

# ✅ SQL injection protection?
grep -r "DB->query.*\$" /var/www/html/glpi/plugins/branding/

# ✅ XSS protection?
grep -r "echo.*\$_" /var/www/html/glpi/plugins/branding/
```

## 📚 Recursos Adicionais

- **Documentação Oficial**: https://github.com/pluginsGLPI/branding/wiki
- **Forum GLPI**: https://forum.glpi-project.org/
- **Issues**: https://github.com/pluginsGLPI/branding/issues
- **API Docs**: Ver README.md seção API REST

## 💬 Suporte

Se precisar de ajuda:

1. Consulte este guia primeiro
2. Verifique [Issues existentes](https://github.com/pluginsGLPI/branding/issues)
3. Pergunte no [Forum GLPI](https://forum.glpi-project.org/)
4. Abra um [novo Issue](https://github.com/pluginsGLPI/branding/issues/new) com:
   - Versão do GLPI
   - Versão do Plugin
   - Versão do PHP
   - Logs de erro
   - Passos para reproduzir

---

**Boa instalação! 🚀**
