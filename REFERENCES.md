Configuração do ambiente do centos 7 - 64 bits para instalação do sistema *MonitoramentoPDE*.
=============================================================================================
# Referências 

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
