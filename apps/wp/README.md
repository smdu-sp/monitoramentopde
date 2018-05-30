# Configuração do ambiente do centos 7 - 64 bits para instalação do *Wordpress + Bedrock*.

## Pré requisitos
### Ter instalado e *configurado* os seguintes pacotes:
* Pacotes de uso geral:
	1. curl
	1. wget
* Pacotes de uso específicos:
	1. nginx
	1. java + geoserver
	1. mariadb
	1. php 5.6.x

* Ter feito o download do projeto Git **MonitoramentoPDE**:
	* **OBS**: Utilizar usuário **diferente** de _root_. 
	* Criar um subdiretório para efetuar o download do **wordpress**
		```
		mkdir <SUBDIRETORIO>
		cd <SUBDIRETORIO>
		```
	* Download via git no projeto Git **MonitoramentoPDE**
		```
		git clone http://git.cgtic.pmsp/Projetos-SMUL/MonitoramentoPDE.git
		```

### Modo 1: Instalação do Wordpress com Bedrock (via unzip do arquivo *bedrock.zip*):
1. Descompactar arquivo _bedrock.zip_ do subdiretório apps/wp:
```
				cd <SUBDIRETORIO>/Projetos-SMUL/MonitoramentoPDE/apps/wp
				unzip bedrock.zip
```

### Modo 2: Instalação do Wordpress com Bedrock (via download):
1. Download via composer (utilitário do __php__):
```
				wget https://getcomposer.org/composer.phar
				chmod +x composer.phar
				mv composer.phar /usr/local/bin/composer
```

2. Instalação propriamente dita:
>2.1. Configuração das variáveis de ambiente para efetuar o download na _Internet_:
```
						export http_proxy=http://USUARIO:SENHA@IP:PORTA
						export https_proxy=http://USUARIO:SENHA@IP:PORTA
						composer create-project roots/bedrock
```

>2.2. Resultado esperado após comandos acima no console:

```
					Installing roots/bedrock (1.8.8)
					  - Installing roots/bedrock (1.8.8): Downloading (100%)
					Created project in /<SUBDIRETORIO>/bedrock
					> php -r "copy('.env.example', '.env');"
					Loading composer repositories with package information
					Installing dependencies (including require-dev) from lock file
					Warning: The lock file is not up to date with the latest changes in composer.json. You may be getting outdated dependencies. Run update to update them.
					Package operations: 8 installs, 0 updates, 0 removals
					  - Installing johnpbloch/wordpress-core-installer (1.0.0.2): Downloading (100%)
					  - Installing composer/installers (v1.5.0): Downloading (100%)
					  - Installing johnpbloch/wordpress-core (4.9.4): Downloading (100%)
					  - Installing johnpbloch/wordpress (4.9.4): Downloading (100%)
					  - Installing oscarotero/env (v1.1.0): Downloading (100%)
					  - Installing roots/wp-password-bcrypt (1.0.0): Downloading (100%)
					  - Installing vlucas/phpdotenv (v2.4.0): Downloading (100%)
					  - Installing squizlabs/php_codesniffer (3.2.2): Downloading (100%)
					Generating autoload files 
```

### Passo 3: Continuação da instalação do Wordpress com Bedrock (*para ambos os casos*):
3. Substituir o conteúdo do arquivo **.env.example**  para o arquivo **.env**, ambos presentes no diretório **bedrock**.
```
				cd bedrock
				cp .env.example .env
```
4. Atualizar as variáveis de ambiente presentes no arquivo **.env** conforme os dados abaixo:
>4.1. Comando para edição do arquivo:
```
				vi .env
```
>4.2. Conteúdo do arquivo:
```
					DB_NAME=wordpress
					DB_USER=wpuser
					DB_PASSWORD=wppassword

					WP_ENV=development
					WP_HOME=http://monitoramentopde.smul.pmsp
					WP_SITEURL=${WP_HOME}/wp
```
>4.3. A partir do link <https://roots.io/salts.html>, gerar as chaves a seguir 
substituindo as linha abaixo com o que está relacionado no título `#Env Format`
```
				AUTH_KEY='generateme'
				SECURE_AUTH_KEY='generateme'
				LOGGED_IN_KEY='generateme'
				NONCE_KEY='generateme'
				AUTH_SALT='generateme'
				SECURE_AUTH_SALT='generateme'
				LOGGED_IN_SALT='generateme'
				NONCE_SALT='generateme'
```

5. Como usuário **root**, acessar o subdiretório **bedrock** criado ao final do passo 3, copiando **todos** os arquivos para o subdiretório **/var/www/monitoramento_pde**:
```
					mv /SUBDIRETORIO/bedrock/ /var/www/monitoramento_pde
```
6. Adicionar o tema “monitoramento_pde” ao wordpress+bedrock instalado:
```
					cp <SUBDIRETORIO>/Projetos-SMUL/MonitoramentoPDE/apps/wp/bedrock/ /var/www/monitoramento_pde/web/app/themes
```
7. Configuração do __nginx__ para o sistema **MonitoramentoPDE**.
>7.1 Ir para o subdiretório __/etc/nginx/conf.d__ e criar o arquivo de configuração do nginx **monitoramentopde.conf** cujo conteúdo está abaixo:
```
					server {
							listen        80;
							server_name monitoramentopde.smul.pmsp;
							root /var/www/monitoramento_pde/web;
							error_log /var/log/nginx/monitoramentopde.wp.err;
							access_log /var/log/nginx/monitoramentopde.wp.log;
							index index.html index.php;
							include conf.d/global/restrictions.conf;
							include conf.d/global/wordpress.conf;

							fastcgi_buffers 128 128k;
							fastcgi_buffer_size 256k;
							fastcgi_busy_buffers_size 512k;
							fastcgi_temp_file_write_size 512k;

							location /geoserver{
									proxy_pass http://127.0.0.1:8080/geoserver;
							}
					}
```

8. Copiar os subdiretórios [<SUBDIRETORIO>/Projetos-SMUL/MonitoramentoPDE/apps/wp/www](../www) do projeto do Git para o subdiretório __/var/www__.
```
					cp <SUBDIRETORIO>/Projetos-SMUL/MonitoramentoPDE/apps/wp/www /var/www
```

9. Tornar o (usuário,grupo) igual a (_php-fpm.nginx_) para os subdiretórios e arquivos em **monitoramento_pde**, **pentaho**, **.kettle** e **.pentaho**:
```
					chown -R php-fpm.nginx /var/www/monitoramento_pde /var/www/pentaho /var/www/.kettle /var/www/.pentaho
```

10. Atualização das permissões de escrita e leitura para o diretório __/var/www__:
```
					chcon -R -t httpd_sys_rw_content_t /var/www
```

11. Editar o arquivo _www.conf_ atualizando os atributos descritos na sequência:
```
					vi /etc/php-fpm.d/www.conf
```
```
listen.owner = nginx
listen.group = nginx
listen.mode = 0660
listen.acl_users = nginx
```

**OBS**: pesquisar pelos atributos _listen.owner_, _listen.group_, _listen.mode_ e _listen.acl_users_. O atributo _listen.acl_users_ encontra-se separado dos demais.

12. Reiniciar o PHP, o MariaDB e o Nginx:
```
						systemctl restart php-fpm; systemctl restart mariadb; systemctl restart nginx
```

13. Acessar a área de administrador do site. URI: <http://monitoramentopde.smul.pmsp/wp/wp-admin>

## Referências 
>Referência: https://roots.io/bedrock/docs/installing-bedrock/

