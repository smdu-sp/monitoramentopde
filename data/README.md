# Configuração do ambiente do centos 7 - 64 bits para instalação do sistema *MonitoramentoPDE*.

## Pré requisitos:

### para os dados presentes no arquivo *data_dir.zip*
* Ter instalado e configurado todos os subsistemas até o **Geoserver**

### para os dados presentes no subdiretório *uploads*
* Ter instalado e configurado todos os subsistemas do **Monitoramento PDE**
	
## Configuração dos dados presentes no arquivo *data_dir.zip*:
### Sobrescrever os dados *default* do **GeoServer** pelo do projeto **MonitoramentoPDE** 
1. Sobrescrever os dados do arquivo *data_dir.zip* no subdiretório __/var/www/geoserver/geoserver__.
	```
	rm -rf /var/www/geoserver/geoserver/data_dir
	cp <SUBDIRETORIO>/Projetos-SMUL/MonitoramentoPDE/data/data_dir.zip /var/www/geoserver/geoserver
	unzip data_dir.zip
	```
	
## Configuração dos dados presentes no subdiretório *uploads*:
### Configuração dos dados já submetidos no MonitoramentoPDE (até 2018/05/25):
1. Copiar os dados do subdiretório **<SUBDIRETORIO>/Projetos-SMUL/MonitoramentoPDE/data/uploads** do projeto do Git para o subdiretório __/var/www/monitoramento_pde/web/app/uploads__.
	```
	cp <SUBDIRETORIO>/Projetos-SMUL/MonitoramentoPDE/data/uploads /var/www/monitoramento_pde/web/app/uploads
	```
