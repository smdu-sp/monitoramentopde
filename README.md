Configuração do ambiente do centos 7 - 64 bits para instalação do sistema *MonitoramentoPDE*.
=============================================================================================

## Pré requisitos:
### Instalar os seguintes aplicações Linux para o funcionamento do sistema Monitoramento PDE:
* Pacotes via yum:
	1. curl
	1. wget
	1. nginx
	1. java
	1. mariadb
	1. php 5.6.x
	1. postgresql-9.6

* Pacotes de instalação *manual*:
	1. geoserver
	1. Wordpress bedrooks 

### Arquivos presentes no [projeto Git *MonitoramentoPDE*](http://git.cgtic.pmsp/Projetos-SMUL/MonitoramentoPDE)

### Boas práticas para download dos pacotes e projetos necessários para esse sistema
1. Utilizar usuário **diferente** de _root_ no CentOS
2. Criar um subdiretório para efetuar o download dos pacotes e projetos. Por exemplo:
	```
	mkdir <SUBDIRETORIO>
	cd <SUBDIRETORIO>
	```
3. Para cada download, aconselha-se criar um novo subdiretório e efetuar a operação nesse subdiretório.
	```
	mkdir <SUBDIRETORIO N>
	cd <SUBDIRETORIO N>
	```

# Etapa 01 - Instalação do ambiente Web

## Passo 01 - Instalação do Nginx.

Referência: 
https://www.digitalocean.com/community/tutorials/how-to-install-nginx-on-centos-7

Para instalar e configurar o Nginx, é necessário que o usuário possua permissões de **root**.

1. Para realizar a instalação do Nginx é necessário fazer a instalação do EPEL repository (fornece muitos pacotes de softwares como complemento (instalação via yum)). *O Nginx é um desses pacotes.
```
				yum install epel-release.
```

2. Instalação do Nginx.
```
				yum install nginx.
```
>**OBS**: Nos passos anteriores há algumas perguntas que devem ser respondidas com “Y” para continuar com a instalação.

3. Iniciar o Nginx: Após a instalação é necessário a inicialização do Nginx, pois o mesmo não inicia sozinho.
```
					systemctl start nginx.
```
4. Para configurar o firewall, possibilitando o acesso (**tráfego**) HTTP e HTTPS de outras máquinas para esse servidor, é necessário executar os comandos:
```
				firewall-cmd --permanent --zone=public --add-service=http.
				firewall-cmd --permanent --zone=public --add-service=https.
				firewall-cmd –reload.
```
5. Para testar se a instalação e configuração ocorreu com sucesso, acessar o ip público do servidor em algum navegador. Exemplo: http://10.75.19.221.
```
				http://server_domain_name_or_IP/
```
6. Para configurar o Nginx para que ele inicie quando o sistema for iniciado é necessário executar o comando:
```
				systemctl enable nginx.
```

7. Encontrar endereço de IP público no servidor (comando para encontrar as interfaces de rede do servidor):
```
				ip addr 
```

	No texto que irá aparecer procurar a interface que possui “BROADCAST,MULTICAST,UP,LOWER_UP” e digitar o seguinte comando para encontrar o endereço de IP público do servidor: 

```
				ip addr show INTERFACE | grep inet | awk '{ print $2; }' | sed 's/\/.*$//'
```

## Passo 02 - Java:
* Instalação do Java:
1. Para a instalação do Java é necessário utilizar os seguintes comandos:
```
			wget --no-check-certificate --no-cookies --header "Cookie: oraclelicense=accept-securebackup-cookie" http://download.oracle.com/otn-pub/java/jdk/8u161-b12/2f38c3b165be4555a1fa6e98c45e0808/jdk-8u161-linux-x64.tar.gz
			tar -zxvf jdk-8u*-linux-x64.tar.gz
			mv jdk1.8.*/ /usr/
			install /usr/bin/java java /usr/jdk1.8.*/bin/java 2
			config java
```
2. Para confirmar a versão do Java instalada, utilizar o seguinte comando:
```
			java –version
```

# Etapa 02 - Instalação do ambiente Geoserver
## Passo 03 - Instalação do Geoserver (<http://geoserver.org/>)
Existem duas opções para realizar a instalação do Geoserver:
* *WAR*: opção não utilizada nessa instalação.
* **Binária**: **opção utilizada** nessa instalação.

### Pré-requisitos: 
* Java 8 instalado
* Criação de um usuário geoserver pertencendo aos grupos “geoserver”, “wheel” e “users”.
```
			useradd geoserver –U –p SENHA
			gpasswd -a geoserver wheel
			gpasswd -a geoserver users
```

### Instalação e configuração inicial do geoserver: 
1. Escolher a opção Linux.
2. Escolher em qual subdiretório na máquina local será feita o download. Suponha subdiretório *<SUBDIRETORIO 1>*.
3. Na página de download do Geoserver, é necessário selecionar *Platform Independent Binary*, baixar o arquivo correspondente e descompactar no subdiretório *<SUBDIRETORIO 1>*. A versão do Geoserver deverá ser a **2.11.5**.
```
			wget https://sourceforge.net/projects/geoserver/files/GeoServer/2.11.5/geoserver-2.11.5-bin.zip/download
			unzip download
```
3. criar diretório de instalação do Geoserver:
```
			mkdir /var/www/geoserver
			mv geoserver-2.11.5 /var/www/geoserver/geoserver
```
4. incluir dono para a pasta do Geoserver:
```
			chown -R geoserver.geoserver /var/www/geoserver/
```

5. Criação do servico de inicializacao no boot do geoserver como usuario geoserver:
* Como usuário __root__, criar os 2 arquivos abaixo em _/var/www/geoserver/geoserver_:
	1. geoserver_boot.sh
	2. geoserver.service
	
	onde: 
		**geoserver_boot.sh:**

	```
	#! /bin/bash
	#
	export GEOSERVER_HOME=/var/www/geoserver/geoserver
	# OBS: Mudar isso quando mudar 
	/var/www/geoserver/geoserver/bin/startup.sh 1>> /var/www/geoserver/geoserver/bin/startup.log 2>> startup.err
	```

	onde: 
		**geoserver.service:**

	```
	[Unit]
	Description=Geoserver start up shell script
	After = network.target

	[Service]
	User=<username>
	ExecStart=/var/www/geoserver/geoserver/bin/geoserver_boot.sh

	[Install]
	WantedBy=multi-user.target
	```

* Criar serviço do geoserver no CentOS:

	```
	cd /etc/systemd/system/multi-user.target.wants
	ln -s  geoserver.service /var/www/geoserver/geoserver/geoserver.service
	```

## Passo 04 - Configuração do geoserver no nginx:

1. Criar um subdiretório chamado _conf.d_ em _/etc/nginx_:
	```
	cd /etc/nginx
	mkdir conf.d
	```
2. Alterar o arquivo _nginx.conf_ com o conteúdo abaixo:
	```
	vi nginx.conf
	```

	```
	# For more information on configuration, see:
	#   * Official English Documentation: http://nginx.org/en/docs/
	#   * Official Russian Documentation: http://nginx.org/ru/docs/

	user nginx;
	worker_processes auto;
	error_log /var/log/nginx/error.log;
	pid /run/nginx.pid;

	# Load dynamic modules. See /usr/share/nginx/README.dynamic.
	include /usr/share/nginx/modules/*.conf;

	events {
		worker_connections 1024;
	}

	http {
		log_format  main  '$remote_addr - $remote_user [$time_local] "$request" '
						  '$status $body_bytes_sent "$http_referer" '
						  '"$http_user_agent" "$http_x_forwarded_for"';

		access_log  /var/log/nginx/access.log  main;

		sendfile            on;
		tcp_nopush          on;
		tcp_nodelay         on;
		keepalive_timeout   65;
		types_hash_max_size 2048;

		include             /etc/nginx/mime.types;
		default_type        application/octet-stream;

		# Load modular configuration files from the /etc/nginx/conf.d directory.
		# See http://nginx.org/en/docs/ngx_core_module.html#include
		# for more information.
		include /etc/nginx/conf.d/*.conf;
			server_names_hash_bucket_size 128;

	}
	```

3. No diretório _conf.d_, criar os seguintes arquivos com os conteúdos:
	* **default.conf**
		```
		server {
			   listen       80 default_server;
			   listen       [::]:80 default_server;
			   server_name _;
			   index index.html index.php;
			   root         /usr/share/nginx/html;
			   location / {
			 }
		}
		```
	* **geoserver.conf**
		```
		server {
				listen        80;
				server_name geoserver.smul.pmsp;
				root /var/www/geoserver/geoserver/webapps/geoserver;
				index index.html index.php;
				location /geoserver {
						proxy_pass http://127.0.0.1:8080/geoserver;
				}
		}
		```
4. Alterar o conteúdo do arquivo _/var/www/geoserver/geoserver/start.ini_ para:
	```
	#
	# Jetty configuration, taken originally from jetty-9.2.13.v20150730-distribution.zip
	#
	--exec
	-Xms256m 
	-Xmx1024m

	-Djava.net.preferIPv4Stack=true

	# --------------------------------------- 
	# Module: server
	--module=server

	# minimum number of threads
	threads.min=10
	# maximum number of threads
	threads.max=200
	# thread idle timeout in milliseconds
	threads.timeout=60000
	# buffer size for output
	jetty.output.buffer.size=32768
	# request header buffer size
	jetty.request.header.size=8192
	# response header buffer size
	jetty.response.header.size=8192
	# should jetty send the server version header?
	jetty.send.server.version=true
	# should jetty send the date header?
	jetty.send.date.header=false
	# What host to listen on (leave commented to listen on all interfaces)
	#jetty.host=myhost.com
	#jetty.host=localhost4
	jetty.host=127.0.0.1
	# Dump the state of the Jetty server, components, and webapps after startup
	jetty.dump.start=false
	# Dump the state of the Jetty server, before stop
	jetty.dump.stop=false
	# Enable delayed dispatch optimisation
	jetty.delayDispatchUntilContent=false

	# --------------------------------------- 
	# Module: deploy
	--module=deploy

	# Monitored Directory name (relative to jetty.base)
	# jetty.deploy.monitoredDirName=webapps

	# --------------------------------------- 
	# Module: websocket
	#--module=websocket

	# --------------------------------------- 
	# Module: ext
	#--module=ext

	# --------------------------------------- 
	# Module: resources
	--module=resources

	# --------------------------------------- 
	# Module: http
	--module=http

	# HTTP port to listen on
	jetty.port=8080

	# HTTP idle timeout in milliseconds
	http.timeout=30000

	# HTTP Socket.soLingerTime in seconds. (-1 to disable)
	# http.soLingerTime=-1

	# Parameters to control the number and priority of acceptors and selectors
	# http.selectors=1
	# http.acceptors=1
	# http.selectorPriorityDelta=0
	# http.acceptorPriorityDelta=0

	# --------------------------------------- 
	# Module: webapp
	--module=webapp
	```
	
5. Substituir o conteúdo *default* do subdiretório **data_dir** do *GeoServer* instalado por *data_dir* do projeto *Git*: vide [link](data/README.md)
	```
	rm -rf /var/www/geoserver/geoserver/data_dir
	cp <SUBDIRETORIO>/Projetos-SMUL/MonitoramentoPDE/data/data_dir.zip /var/www/geoserver/geoserver
	unzip data_dir.zip
	```

6. Fazer o reboot da máquina:
	```
	shutdown -r now
	```

# Etapa 03 - Instalação do ambiente WordPress + Bedrock
## Passo 05 - Instalação e configuração do MariaDB:
### Instalação do MariaDB:
1.	Executar o comando:
	```
	yum install mariadb-server mariadb.
	```
### Configuração do MariaDB:
1. Iniciar o MariaDB:
	```
	systemctl start mariadb
	```
2.	Configurar a senha do usuário root do MariaDB:
	```
	mysql_secure_installation
	```
	
**OBS1:** Será solicitada a senha de administrador. Basta apertar o enter, pois ainda não existe uma senha definida.

**OBS2:**  Será questionado se há o desejo de definição de uma nova senha. Apertar “Y” e definir uma nova senha.

3.  Configurar o MariaDB para que este seja iniciado automaticamente no boot:
	```
	systemctl enable mariadb.service
	```

4. Conectar ao MariaDB:
	```
	mysql -u root –p
	```
	
5. No prompt do MariaDB, criação de base de dados:
	```
	CREATE DATABASE wordpress;
	```
6. No prompt do MariaDB, criação de usuário:
	```
	CREATE USER 'wpuser'@'localhost' IDENTIFIED BY 'wppassword';
	```
7. No prompt do MariaDB, atribuir permissões de acesso à base de dados a usuário:
	```
	GRANT ALL PRIVILEGES ON wordpress.* TO 'wpuser'@'localhost';
	```

## Passo 06 - Instalação e configuração do PHP 5.6:
### Instalação do PHP 5.6:
1. Adicionar o IUS repo:
	```
	wget https://dl.fedoraproject.org/pub/epel/epel-release-latest-7.noarch.rpm
	wget https://centos7.iuscommunity.org/ius-release.rpm
	rpm -Uvh ius-release*.rpm
	```

2. Instalar o php 5.6:
	```
	yum install yum-plugin-replace
	yum replace --replace-with php56u php
	yum update
	yum  install  php56u php56u-devel php56u-fpm php56u-mbstring php56u-pdo php56u-opcache php56u-gd php56u-intl php56u-imagick php56u-imap php56u-mcrypt php56u-pspell php56u-recode php56u-tidy php56u-xmlrpc php56u-xml php56u-memcached php-curl php56u-pear php56u-mysqlnd php56u-bcmath php56u-cli php56u-common php56u-pecl-igbinary php56u-pecl-jsonc php56u-pecl-jsonc-devel php56u-process php56u-pgsql
	```
3. Checar versão do php:
	```
	php –v
	```

### Configuração do PHP 5.6:
1. Incluir o usuário “nginx” no grupo “php-fpm” e o usuário “geoserver” nos grupos “wheel” e “users”:
	```
	usermod –G php-fpm nginx
	usermod –G wheel,users geoserver
	```

## [Passo 07 - Instalação e configuração do Wordpress com Bedrock](apps/wp/README.md)


## Passo 08 - Instalação e configuração do Postgres:
### Instalação do Postgres:
1. Instalação:
```
			yum install postgresql-server postgresql-contrib
```
2. Inicialização do Postgres como _serviço_ do _CentOS_:
```
			systemctl start postgresql-9.6
```

3. No arquivo **pg_hba.conf** deverá ser incluído as máquinas que poderão acessar o banco e alterar a coluna _METHOD_ do local e do IPv4 para md5.
Exemplo:

```
					# TYPE  DATABASE        USER            ADDRESS                 METHOD

					# "local" is for Unix domain socket connections only
					local   all             all                                     md5
					# IPv4 local connections:
					host    all             all             10.75.17.1/24           md5
					host    all             all             10.75.19.221/32         md5
					host    all             all             127.0.0.1/32            md5
```

>3.1. Alterar o arquivo **postgresql.conf** atualizando o seu host (listen_addresses = ‘<Server IP address>’) e a porta (port = 5432).

4. Para configurar o firewall é necessário executar os comandos:
	```
		firewall-cmd --permanent --zone=trusted --add-source=<Client IP address>/32
		firewall-cmd --permanent --zone=trusted --add-port=5432/tcp
		firewall-cmd –reload
	```
>**OBS**: para cada máquina que for acessar remotamente o banco de dados, é necessário a verificação se a máquina está parametrizada no arquivo _pg_hba.conf_ (# IPv4 local connections) e executar no servidor os três comandos do 4º passo para o IP do cliente.

5. Fazer o download e instalar o [pgadmin4-2.1-x86.exe](https://www.postgresql.org/ftp/pgadmin/pgadmin4/v2.1/windows/) na máquina Windows.

6. Configurar o servidor no [pgadmin4-2.1-x86.exe](https://www.postgresql.org/ftp/pgadmin/pgadmin4/v2.1/windows/) conforme abaixo:
	1. Aba General:
		1. Name: Alias (qualquer nome)
	2. Aba Connection:
		1. HOST Name/Address: IP do servidor.
		1. Port: 5432.
		1. Maintenance/Database: Postgres.
		1. Username: postgres.
		1. Role: **Não colocar nada**.

7. Acessar utilizando o usuário _postgres_ que foi criado e realizar a criação do usuário **smdu**:
	```
		CREATE USER smdu WITH  LOGIN  SUPERUSER  INHERIT  CREATEDB  NOCREATEROLE  NOREPLICATION;
		CREATE DATABASE "MonitoramentoPDE" WITH OWNER = postgres  ENCODING = 'UTF8'    LC_COLLATE = 'pt_BR.UTF-8'    LC_CTYPE = 'pt_BR.UTF-8'    TABLESPACE = pg_default    CONNECTION LIMIT = -1;
	```

8. Acessando novamente o servidor, realizar a alteração da senha do usuário __postgres__:
	```
		psql -d template1 -c "ALTER USER postgres WITH PASSWORD 'newpassword';"
	```


## Referências 


## Erros ocorridos durante a instalação e solução de contorno adotada 

### Erro 1 - Erro ao executar a URI abaixo:
1. URI executada:
```
http://monitoramentopde.smul.pmsp//wp-json/monitoramento_pde/v1/indicador?grupo_indicador=1&somente_ativos=true
```

2. Resultado foi no arquivo de log do nginx:
```
<br />
<b>Notice</b>:  Undefined index: X-WP-Nonce in <b>/var/www/monitoramento_pde/web/app/themes/monitoramento_pde/lib/api.php</b> on line <b>2726</b><br />
[{"id_indicador":26,"nome":"Evolu\u00e7\u00e3o de im\u00f3veis notificados em rela\u00e7\u00e3o ao total de im\u00f3veis notific\u00e1veis","ativo":true,"homologacao":false,"periodicidade":"anual","tipo_valor":"Percentual","simbolo_valor":"%","nota_tecnica":null,"nota_tecnica_resumida":"O indicador apresenta a evolu\u00e7\u00e3o da 
```

3. Solução: Retirar a chamada da função:
na linha 2726 do arquivo: _/var/www/monitoramento_pde/web/app/themes/monitoramento_pde/lib/api.php__
```
wp_verify_nonce( $_SERVER['X-WP-Nonce'], "wp_rest" );
```
que ocasionava o header a mais de JSON retornado:
```
<br />
<b>Notice</b>:  Undefined index: X-WP-Nonce in <b>/var/www/monitoramento_pde/web/app/themes/monitoramento_pde/lib/api.php</b> on line <b>2726</b><br />
```

### Erro 2 - Erro ao executar a URI abaixo:
1. URI executada:
```
http://monitoramentopde.smul.pmsp/geoserver/Monitoramento_PDE/ows?bbox=-5239848.026701172,-2779423.0285424986,-5134992.693094832,-2649705.0900604283,EPSG:3857&format_options=callback:+angular.callbacks._0&outputFormat=text%2Fjavascript&request=GetFeature&service=WFS&typename=Monitoramento_PDE:Munic%C3%ADpio&version=1.1.0
```
 
2. Solução de contorno (_remoção do cache do geoserver_):
	1. Parar o serviço do geoserver.service
	1. Remover os subdiretórios __/tmp/Geotools/Databases__ via comando `rm -rf /tmp/Geotools/Databases`
	1. sobrescrever o diretorio data_dir do geoserver.
	

### Localização de log - monitoramentoPDE:
1. Como saber log gerado nos programas do monitoramentoPDE (em php):
```
				/var/log/php-fpm/www-error.log
```


## Erros de configuração do GEOSERVER
### Erro 1 - Ocorreu erro em 502 - bad gateway ao acessar /geoserver:
1. Verificar porta 8080 aberta:
```
		nmap -sT -O localhost
```

2. Se porta ok, ver listen
```
		ss -ant
```

3. Ver no log do nginx: _/var/log/nginx/error.log_
	1. Se existe:
	```
	YYYY/MM/DD HH:mm:ss [crit] 5439#0: *1 connect() to 127.0.0.1:8080 failed (13: Permission denied) while connecting to upstream, client: 10.75.17.83, server: 10.75.19.221, request: "GET /geoserver HTTP/1.1", upstream: "http://127.0.0.1:8080/geoserver", host: "10.75.19.221"
	```

	2. Fazer 
	```
	more /etc/selinux/config
	```

	3. Se SELINUX=enforcing
		```
		setsebool -P httpd_can_network_connect 1
		```


	4. Se não funcionar, para CentOS 7:

```
firewall-cmd --permanent --add-port=8080/tcp
firewall-cmd --reload
```

[See the documentation for FirewallD.](https://fedoraproject.org/wiki/FirewallD#Permanent_zone_handling)
