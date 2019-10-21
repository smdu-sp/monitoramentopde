<?php
/**
 * Template Name: Cadastro Instrumento
 */
?>

<script type="text/javascript">
jQuery.noConflict();
var pumba = false;
var customStyle = {};
var app = angular.module('monitoramentoPde', ['ngResource','ngAnimate','ui.bootstrap','angular.filter','as.sortable']);

/** 
	ISSUE 45 - Mapa temático para cada instrumento
**/

app.factory('CarregarMapaTematico',function($resource){
	return $resource('/wp-json/monitoramento_pde/v1/instrumentos/carregar_mapa_tematico/:id',{id:'@id_instrumento'},{
		update: cargaUpdateParams
	});
});

app.factory('ObterMapa',function($resource){
	return $resource('/wp-json/monitoramento_pde/v1/instrumentos/obter_mapa/:id_grupo_indicador',{id_grupo_indicador:'@id_instrumento'},{
		get:{
			headers:{
				'X-WP-Nonce': '<?php  echo(wp_create_nonce('wp_rest')); ?>'
					}
		},query:{
			headers:{
				'X-WP-Nonce': '<?php  echo(wp_create_nonce('wp_rest')); ?>'
			}	
		}
	});
});

var cargaUpdateParams = {
	method:'POST',
	transformRequest: function(data) {
		if (data === undefined){
			return data;
		}

		var fd = new FormData();
		angular.forEach(data, function(value, key) {
			if (value instanceof FileList) {
				if (value.length == 1) {
					fd.append(key, value[0]);
				} else {
					angular.forEach(value, function(file, index) {
						fd.append(key + '_' + index, file);
					});
				}
			} else {
				if(value instanceof File){
					fd.append('arquivo', value);
				}
				else if(value instanceof Array){
					fd.append('arquivo', value[0]);
				}else
					fd.append(key, value);
			}
		});
			
		return fd;					
	},
	headers:{
		'X-WP-Nonce': '<?php  echo(wp_create_nonce('wp_rest')); ?>'
		,'Content-type': undefined
	}
};
// END Issue 45

app.factory('GrupoIndicador',function($resource){
	return $resource('/wp-json/monitoramento_pde/v1/grupo_indicador/:id',{id:'@id_grupo_indicador'},{
		update:{
			method:'PUT'
		}
	});
});

app.factory('Indicador',function($resource){
	return $resource('/wp-json/monitoramento_pde/v1/indicador/:id',{id:'@id_indicador'},{
		get:{
			headers:{
				'X-WP-Nonce': '<?php  echo(wp_create_nonce('wp_rest')); ?>'
					}
		},query:{
			isArray:true,
			headers:{
				'X-WP-Nonce': '<?php  echo(wp_create_nonce('wp_rest')); ?>'
			}	
		}
	}
	);
});

app.controller("cadastroGrupo", function($scope, $rootScope, $http, $filter, $uibModal, GrupoIndicador, Indicador, CarregarMapaTematico, ObterMapa) {
 
	$scope.estado = "listar";

	// Issue 45

	$scope.carregarMapa = function(){
		if(!$rootScope.carregandoArquivo){
			$rootScope.carregandoArquivo = true;
			$rootScope.mensagemArquivo = 'Aguarde... Realizando Carga';
			// TO DO confirmar carregamento
			// CARGA DB FONTE DE DADOS
			CarregarMapaTematico.update({id_instrumento:$scope.idItemAtual,arquivos:$scope.arquivos}).$promise.then(
				function(mensagem){
					// console.log(mensagem);
					$rootScope.carregandoArquivo = false;
					$rootScope.mensagemArquivo = '';					
					$rootScope.modalProcessando.close();		
					$scope.criarModalSucesso();
					$scope.renderizarMapa();
				},
				function(erro){
					$rootScope.modalConfirmacao.close();						
					$rootScope.carregandoArquivo = false;
					$rootScope.mensagemArquivo = '';
					// $scope.lancarErro(erro);
					console.log("PRE ERROR:");
					console.log(erro);					
				}
			).catch(function(err){				
				console.error(err);
			});
		}else{
			alert('O mapa está sendo carregado. Por favor, aguarde.');
		};
	}

	/**
		MAPA OSM
	**/
	$scope.loadMap = function() {
		if($rootScope.mapLoaded)
			return;
		$rootScope.mapLoaded = true;
		$rootScope.osmLayer = new ol.layer.Tile({
			source: new ol.source.OSM()
		});
		// mapa MapBox
		let mbDefault = 'https://api.mapbox.com/styles/v1/mapbox/streets-v11/tiles/256/{z}/{x}/{y}?access_token=pk.eyJ1Ijoicm1nb21lcyIsImEiOiJjazF1eTA2MXcwMWlkM2dwNXJ1ZmZmOXdlIn0.hLv8SFtndRaKPtx2fPrEnQ';
		let mbSatellite = 'https://api.mapbox.com/v4/mapbox.satellite/{z}/{x}/{y}.png?access_token=pk.eyJ1Ijoicm1nb21lcyIsImEiOiJjazF1eTA2MXcwMWlkM2dwNXJ1ZmZmOXdlIn0.hLv8SFtndRaKPtx2fPrEnQ';
		let mbLight = 'https://api.mapbox.com/styles/v1/rmgomes/ck1v59cvs7zw51cow5n7e5itl/tiles/256/{z}/{x}/{y}?access_token=pk.eyJ1Ijoicm1nb21lcyIsImEiOiJjazF1eTA2MXcwMWlkM2dwNXJ1ZmZmOXdlIn0.hLv8SFtndRaKPtx2fPrEnQ';
		let mbMonoblue = 'https://api.mapbox.com/styles/v1/rmgomes/ck1v65hgy0fgf1drt97mjreic/tiles/256/{z}/{x}/{y}?access_token=pk.eyJ1Ijoicm1nb21lcyIsImEiOiJjazF1eTA2MXcwMWlkM2dwNXJ1ZmZmOXdlIn0.hLv8SFtndRaKPtx2fPrEnQ';
		$rootScope.mbLayer = new ol.layer.Tile({
			source: new ol.source.XYZ({
				url: mbLight
			})
		});

		<?php		
		// Verifica se API excedeu a quantidade de tile downloads do MapBox
			$tileUrl = 'https://api.mapbox.com/styles/v1/rmgomes/ck1v59cvs7zw51cow5n7e5itl/tiles/256/13/3034/4647?access_token=pk.eyJ1Ijoicm1nb21lcyIsImEiOiJjazF1eTA2MXcwMWlkM2dwNXJ1ZmZmOXdlIn0.hLv8SFtndRaKPtx2fPrEnQ';

			// Para funcionamento em ambiente de homologação
			if (getenv('PROXY') == true) {
				$auth = base64_encode(getenv('PROXY_AUTH'));
				stream_context_set_default(
					array(
						'http' => array(
							'proxy' => "tcp://".getenv('PROXY'),
							'request_fulluri' => true,
							'header' => "Proxy-Authorization: Basic $auth"
						)
					)
				);
			}

			if (strpos(get_headers($tileUrl)[1], 'image')) {
				// Imagem obtida - utiliza mapa do MapBox
				echo "\$rootScope.mapLayers = [\$scope.mbLayer];";
			}
			else {
				// Erro de autorização (API) - utiliza mapa do OpenStreetMaps
				echo "\$rootScope.mapLayers = [\$scope.osmLayer];";
			}
		?>
		// $rootScope.mapLayers = [$scope.osmLayer];
		// $rootScope.mapLayers = [$scope.mbLayer];

		$rootScope.map = new ol.Map({
			target: 'map',
			layers: $rootScope.mapLayers,
			view: new ol.View({
				// center: ol.proj.fromLonLat([37.41, 8.82]),
				center: [-5191207.638373509,-2698731.105121977],
				zoom: 10,
				maxZoom: 20
			})
		});
	}
	$scope.loadMap();

	$scope.addLayers = function(layersInstrumento){
		for(i in layersInstrumento) {
			let index = layersInstrumento[i];
			// let kmlLayer = new VectorLayer({
			let kmlLayer = new ol.layer.Vector({
				style: new ol.style.Style({
					stroke: new ol.style.Stroke({
						color: index.stroke_color,
						width: index.stroke_width,
						lineDash: index.stroke_dash
					}),
					fill: new ol.style.Fill({
						color: index.fill_color
					})
				}),
				source: new ol.source.Vector({
					url: index.path,
					format: new ol.format.KML({
						extractStyles: index.style_from_kml
					})
				})
			});
			$scope.mapLayers.push(kmlLayer);
			// $scope.map.layers = $scope.mapLayers;			
		}
	};

	$scope.renderizarMapa = function() {
		if(!$scope.renderizandoMapa || true){
			$scope.renderizandoMapa = true;
			console.log('renderizandoMapa');
			ObterMapa.query({id_grupo_indicador:$scope.idItemAtual},function(mapaObtido) {
				$scope.mapa = mapaObtido;
			}).$promise.then(function(){
				// Mapa obtido. Propriedades: 'mapa_tematico'(nome do arquivo), 'parametros_mapa'(json com o estilo do mapa)
				var customLayer = $scope.mapa.parametros_mapa === null ? {} : $scope.mapa.parametros_mapa;
				customLayer.path = "/app/uploads/instrumentos/" + $scope.mapa.mapa_tematico;
				customLayer.style_from_kml = true;
				$scope.addLayers([customLayer]);
				
				$scope.renderizandoMapa = false;
				console.log("Query terminada.");
				
				var contornoSP = {
					path: "/app/uploads/instrumentos/msp_contorno.kml",
					stroke_color: 'rgba(0, 0, 0, 0.5)',
					stroke_width: 4,
					fill_color: 'rgba(0, 0, 10, 0.02)',
					style_from_kml: false
				};

				if($scope.map.getLayers().getLength() === 1)
					$scope.addLayers([contornoSP]);

				var cLayers = $scope.map.getLayers();
				for(layer in cLayers) {
					$scope.map.removeLayer(layer);
				}

				for(layer in $scope.mapLayers){
					$scope.map.addLayer($scope.mapLayers[layer]);
				}
				
				// $scope.map.renderSync();
				// var extent = my_vector_layer.getSource().getExtent();
				// map.getView().fit(extent, map.getSize());
				customStyle = new ol.style.Style({
					stroke: new ol.style.Stroke({
						color: 'rgba(200, 0, 0, 1)',
						width: 2
					}),
					fill: new ol.style.Fill({
						color: 'rgba(0,100,0, 0.5)'
					})
				});
				pumba = $scope.map;
				
				window.setTimeout(function(){
					var extent = ol.extent.createEmpty();
					$scope.map.getLayers().forEach(function(layer) {
						if(layer.getSource().getExtent !== undefined)
					  		ol.extent.extend(extent, layer.getSource().getExtent());
					});
					$scope.map.getView().fit(extent, $scope.map.getSize());
				}, 2000);

				// $scope.map.getView().setZoom(9);
			});
		}
	}

	$scope.lerArquivos = function(element) {
		
		$scope.$apply(function($scope) {
		// Turn the FileList object into an Array
			$scope.arquivos = [];
			for (var i = 0; i < element.files.length; i++) {
				$scope.arquivos.push(element.files[i])
			}
		});
	};

	
		// END Issue 45
	
	$scope.carregarTipo = function(){
		if($scope.tipo=='estrategia')
			$scope.tipoExibicao = 'Estratégia';
		
		if($scope.tipo=='instrumento')
			$scope.tipoExibicao = 'Instrumento';
		
		if($scope.tipo=='objetivo')
			$scope.tipoExibicao = 'Objetivo';
		
		GrupoIndicador.query({tipo:$scope.tipo,tipo_retorno:'array',formato_retorno:'array'},function(grupos) {
			$rootScope.listaGrupos = grupos;
		  $rootScope.grupos = grupos;
		});
		$scope.estado = 'listar';
	};
	
	$scope.carregar = function(){
		$scope.itemAtual = $rootScope.grupos.filter((grupo) => grupo.id_grupo_indicador == $scope.idItemAtual)[0];
		
		Indicador.query({grupo_indicador:$scope.idItemAtual,somente_ativos:true},function(indicadores) {
			 $scope.indicadores = indicadores;
		 });
		if($scope.itemAtual != null){
			$scope.estado = "selecionar";
			$scope.renderizarMapa(); // TODO: verificar se dados do indicador já foram obtidos antes de renderizar mapa
		}
	};
	
	$scope.adicionarElemento = function(){
		$scope.itemAtual.propriedades.push({});
	};
	
	$scope.criarModalConfirmacao = function(acao){
		// Verifica se arquivo foi selecionado antes de carregar o mapa
		if(acao === 'CarregarMapa' && !$scope.arquivos){
			window.alert('Nenhum arquivo enviado. Clique no botão "Escolher arquivo" para enviar o arquivo de mapa primeiro.');
			return;
		}
		$rootScope.modalConfirmacao = $uibModal.open({
			animation: true,
			ariaLabelledBy: 'modal-titulo-instrumento',
			ariaDescribedBy: 'modal-corpo-instrumento',
			templateUrl: 'ModalConfirmacao.html',
			controller: 'cadastroGrupo',
			scope:$scope,
			size: 'md',
		});
		$scope.acao = acao;
	};
	
	$scope.criarModalSucesso = function(){
		$rootScope.modalSucesso = $uibModal.open({
			animation: true,
			ariaLabelledBy: 'modal-titulo-instrumento',
			ariaDescribedBy: 'modal-corpo-instrumento',
			templateUrl: 'ModalSucesso.html',
			controller: 'cadastroGrupo',
			scope:$scope.parent,
			size: 'md',
		});
	}
	
	$scope.limparForm = function(){
		$scope.itemAtual = {};
		$scope.itemAtual.propriedades =[];
		$scope.estado = "inserir";
	};
	
	$scope.lancarErro = function(erro){
		alert('Ocorreu um erro ao atualizar o indicador. \n\n Código: ' + erro.data.code + '\n\n Status: ' + erro.statusText + '\n\n Mensagem: ' + erro.data + '\n\n Mensagem Interna: ' + erro.data.message);
	}
	
	$scope.atualizar = function(){
		GrupoIndicador.update({grupo:$scope.itemAtual,id_grupo_indicador:$scope.itemAtual.id_grupo_indicador,tipo:$scope.tipo,indicadores:$scope.indicadores,usuario:<?php $usrObj = wp_get_current_user(); echo json_encode($usrObj); ?>}).$promise.then(
			function(mensagem){
				
				GrupoIndicador.query({tipo:$scope.tipo,tipo_retorno:'array',formato_retorno:'array'},function(grupos) {
					$rootScope.grupos = grupos;
					
					$rootScope.modalProcessando.close();		
					$scope.criarModalSucesso();
				});

			},
			function(erro){
				$rootScope.modalProcessando.close();
				$scope.lancarErro(erro);
			}
		);
	};		
	
	$scope.remover = function(){
		GrupoIndicador.remove({id:$scope.itemAtual.id_grupo_indicador,grupo:$scope.itemAtual,tipo:$scope.tipo,usuario:<?php $usrObj = wp_get_current_user(); echo json_encode($usrObj); ?>}).$promise.then(
			function(mensagem){
				
				GrupoIndicador.query({tipo:$scope.tipo,tipo_retorno:'array',formato_retorno:'array'},function(grupos) {
					$rootScope.grupos = grupos;
					
					$rootScope.modalProcessando.close();		
					$scope.criarModalSucesso();
				});

			},
			function(erro){
				$rootScope.modalProcessando.close();
				$scope.lancarErro(erro);
			}
		);
		$rootScope.idItemAtual = null;	
		$scope.$parent.estado = "listar";
	};	

	$scope.inserir = function(){
		GrupoIndicador.save({grupo:$scope.itemAtual,tipo:$scope.tipo,usuario:<?php $usrObj = wp_get_current_user(); echo json_encode($usrObj); ?>}).$promise.then(
			function(mensagem){
				GrupoIndicador.query({tipo:$scope.tipo,tipo_retorno:'array',formato_retorno:'array'},function(grupos) {
					$rootScope.grupos = grupos;
					
					$rootScope.modalProcessando.close();		
					$scope.criarModalSucesso();
				});

			},
			function(erro){
				$rootScope.modalProcessando.close();
				$scope.lancarErro(erro);
			}
		);
		$scope.$parent.estado = "listar";
		$scope.carregar();
	};	

	$scope.voltar = function(){
		$scope.estado = "listar";	
		$scope.carregar();
	};	

	$scope.fecharModal = function(tipo){
		if(tipo == 'confirmacao')
			$rootScope.modalConfirmacao.close();
		
		if(tipo == 'sucesso')
			$rootScope.modalSucesso.close();
	};
	
	$scope.submeter = function(){	

		$rootScope.modalConfirmacao.close();
		
		$rootScope.modalProcessando = $uibModal.open({
				animation: true,
				ariaLabelledBy: 'modal-titulo-instrumento',
				ariaDescribedBy: 'modal-corpo-instrumento',
				templateUrl: 'ModalProcessando.html',
				controller: 'cadastroGrupo',
				scope:$scope,
				size: 'md',
		});
		
		
		if($scope.acao == 'Atualizar'){
			$scope.acaoExecutando = 'Atualizando';
			$scope.acaoSucesso = 'Atualizada';
			$scope.atualizar();
			
		}else{
			if($scope.acao == 'Remover'){	
				$scope.acaoExecutando = 'Removendo';
				$scope.acaoSucesso = 'Removida';
				$scope.remover();
				
			}else{
				if($scope.acao == 'Inserir'){	
					$scope.acaoExecutando = 'Inserindo';
					$scope.acaoSucesso = 'Inserida';
					$scope.inserir();
					
				}
			}
		}
		
		// Issue 45
		if($scope.acao == 'CarregarMapa'){
			$scope.acaoExecutando = 'Carregando';
			$scope.acaoSucesso = 'Carregado';
			$scope.carregarMapa();
		}
	};	
	
	$scope.deletarElemento = function(indice){
		$scope.itemAtual.propriedades.splice(indice,1);
	};
	
	function Workbook() {
		if(!(this instanceof Workbook)) return new Workbook();
		this.SheetNames = [];
		this.Sheets = {};
	}
	
	function criarCelula(c, r, val){
		var cell = {v: val };
		
		if(cell.v == null) cell.v = '';
		
		if(typeof cell.v === 'number') cell.t = 'n';
		else if(typeof cell.v === 'boolean') cell.t = 'b';
		else if(cell.v instanceof Date) {
			cell.t = 'n'; cell.z = XLSX.SSF._table[14];
			cell.v = datenum(cell.v);
		}
		else cell.t = 's';
		
		return cell;
	}
	
	function s2ab(s) {
		var buf = new ArrayBuffer(s.length);
		var view = new Uint8Array(buf);
		for (var i=0; i!=s.length; ++i) view[i] = s.charCodeAt(i) & 0xFF;
		return buf;
	}
	
	$scope.exportarGrupos = function(){
		
			var wb = new Workbook();
			
			var wsInstrumentos = {};
			var wsEstrategias = {};
			
			var instrumentosExp = null;
			var estrategiasExp = null;
			
			var linha = 0;
			
			linhaInstrumento = 0;
			linhaEstrategia = 0;
			
			
			wsInstrumentos[XLSX.utils.encode_cell({c:0 ,r:0})] = criarCelula(0 ,0,'ID');
			
			GrupoIndicador.query({tipo:'estrategia',tipo_retorno:'array'},function(grupos) {
				estrategiasExp = grupos;
				GrupoIndicador.query({tipo:'objetivo',tipo_retorno:'array'},function(grupos) {
					objetivosExp = grupos;
					GrupoIndicador.query({tipo:'instrumento',tipo_retorno:'array'},function(grupos) {
						instrumentosExp = grupos;
						exportarInstrumentoEstrategia();
					});
				});
			});                                             
			
			exportarInstrumentoEstrategia = function(){
				wsInstrumentos[XLSX.utils.encode_cell({c:0 ,r:0})] = criarCelula(0 ,0,'ID');
				wsInstrumentos[XLSX.utils.encode_cell({c:1 ,r:0})] = criarCelula(1 ,0,'Nome');
				
				wsEstrategias[XLSX.utils.encode_cell({c:0 ,r:0})] = criarCelula(0 ,0,'ID');
				wsEstrategias[XLSX.utils.encode_cell({c:1 ,r:0})] = criarCelula(1 ,0,'Nome');
				
				wsObjetivos[XLSX.utils.encode_cell({c:0 ,r:0})] = criarCelula(0 ,0,'ID');
				wsObjetivos[XLSX.utils.encode_cell({c:1 ,r:0})] = criarCelula(1 ,0,'Nome');
				
				var indiceInstrumento = 0;
				
				while(!instrumentosExp[indiceInstrumento].propriedades[0].chave && indiceInstrumento < instrumentosExp.length)
					indiceInstrumento++;
				
				coluna = 2;
				
				angular.forEach(instrumentosExp[indiceInstrumento].propriedades,function(propriedade,chave){
					wsInstrumentos[XLSX.utils.encode_cell({c:coluna ,r:0})] = criarCelula(coluna ,0, propriedade.chave);
					coluna++;
				});
				
				var indiceObjetivo = 0;
				
				while(!objetivosExp[indiceObjetivo].propriedades[0].chave && indiceObjetivo < objetivosExp.length)
					indiceObjetivo++;
				
				coluna = 2;
				
				angular.forEach(objetivosExp[indiceObjetivo].propriedades,function(propriedade,chave){
					wsObjetivos[XLSX.utils.encode_cell({c:coluna ,r:0})] = criarCelula(coluna ,0, propriedade.chave);
					coluna++;
				});
				
				var indiceEstrategia = 0;
				
				while(!estrategiasExp[indiceEstrategia].propriedades[0].chave && indiceEstrategia < estrategiasExp.length)
					indiceEstrategia++;
				
				coluna = 2;
				angular.forEach(estrategiasExp[0].propriedades,function(propriedade,chave){
					wsEstrategias[XLSX.utils.encode_cell({c:coluna ,r:0})] = criarCelula(coluna ,0, propriedade.chave);
					coluna++;
				});
				
				linha = 1;
				
				angular.forEach(instrumentosExp,function(instrumento,chave){
					
					wsInstrumentos[XLSX.utils.encode_cell({c:0 ,r:linha})] = criarCelula(0 ,linha,instrumento.id_grupo_indicador);
					wsInstrumentos[XLSX.utils.encode_cell({c:1 ,r:linha})] = criarCelula(1 ,linha,instrumento.nome);
					
					coluna = 2;
					angular.forEach(instrumento.propriedades,function(propriedade,chave){
						
						wsInstrumentos[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,propriedade.valor);
						coluna++;
						
					});
					
					linha++;
					
				});
				
				var range = {s: {c:0, r:0}, e: {c:coluna, r: linha}};
				wsInstrumentos['!ref'] = XLSX.utils.encode_range(range);
				
				linha = 1;
				
				angular.forEach(estrategiasExp,function(estrategia,chave){
					
					wsEstrategias[XLSX.utils.encode_cell({c:0 ,r:linha})] = criarCelula(0 ,linha,estrategia.id_grupo_indicador);
					wsEstrategias[XLSX.utils.encode_cell({c:1 ,r:linha})] = criarCelula(1 ,linha,estrategia.nome);
					
					coluna = 2;
					angular.forEach(estrategia.propriedades,function(propriedade,chave){
						
						wsEstrategias[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,propriedade.valor);
						coluna++;
						
					});
					
					linha++;
					
				});
				
				var range = {s: {c:0, r:0}, e: {c:coluna, r: linha}};
				wsEstrategias['!ref'] = XLSX.utils.encode_range(range);
				
				linha = 1;
				
				angular.forEach(objetivosExp,function(objetivo,chave){
					
					wsObjetivos[XLSX.utils.encode_cell({c:0 ,r:linha})] = criarCelula(0 ,linha,objetivo.id_grupo_indicador);
					wsObjetivos[XLSX.utils.encode_cell({c:1 ,r:linha})] = criarCelula(1 ,linha,objetivo.nome);
					
					coluna = 2;
					angular.forEach(objetivo.propriedades,function(propriedade,chave){
						
						wsObjetivos[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,propriedade.valor);
						coluna++;
						
					});
					
					linha++;
					
				});
				
				var range = {s: {c:0, r:0}, e: {c:coluna, r: linha}};
				wsObjetivos['!ref'] = XLSX.utils.encode_range(range);
				
				/* add worksheet to workbook */
				wb.SheetNames.push('Instrumentos');
				wb.SheetNames.push('Estrategias');
				wb.SheetNames.push('Objetivos');
				
				wb.Sheets['Instrumentos'] = wsInstrumentos;
				wb.Sheets['Estrategias'] = wsEstrategias;
				wb.Sheets['Objetivos'] = wsEstrategias;
				
				var wbout = XLSX.write(wb, {bookType:'xlsx', bookSST:false, type: 'binary'});
				
				saveAs(new Blob([s2ab(wbout)],{type:"application/octet-stream"}), "estrategias_instrumentos_objetivos.xlsx");
			}
	}
	
	
});

</script>


<?php get_template_part('templates/page', 'header'); ?>

<div class="content-page container text-justify" data-ng-app="monitoramentoPde" data-ng-controller="cadastroGrupo">

<script type="text/ng-template" id="ModalConfirmacao.html">

<div class="modal-instrumento">
	<div class="modal-header">
    <h3 class="modal-title" id="modal-titulo-instrumento"> {{acao}} {{tipoExibicao}} <button class="btn btn-danger pull-right" type="button" ng-click="fecharModal('confirmacao')">X</button></h3> 
	</div>
	<div class="modal-body" id="modal-corpo-instrumento">
			Você irá {{acao.toLowerCase()}} a {{tipoExibicao}} <strong>{{itemAtual.nome}}</strong>. <br><br> Confirme sua ação.
			</div>
	<div class="modal-footer">	
		<button class="btn btn-danger" type="button" ng-click="fecharModal()">	Abortar</button>
		<button class="btn btn-success" type="button" ng-click="submeter()">	Confirmar</button>
	</div>
</div>
</script>

<script type="text/ng-template" id="ModalProcessando.html">

<div class="modal-instrumento">
	<div class="modal-header">
    <h3 class="modal-title" id="modal-titulo-instrumento"> {{acaoExecutando}} {{tipoExibicao}} </h3> 
	</div>
	<div class="modal-body" id="modal-corpo-instrumento">
			{{acaoExecutando}} a {{tipoExibicao}}<strong>{{itemAtual.nome}}</strong>, por favor aguarde a conclusão.
			</div>
</div>
</script>

<script type="text/ng-template" id="ModalSucesso.html">

<div class="modal-instrumento">
	<div class="modal-header">
    <h3 class="modal-title" id="modal-titulo-instrumento"> Ação concluída <button class="btn btn-danger pull-right" type="button" ng-click="fecharModal('sucesso')">X</button></h3> 
	</div>
	<div class="modal-body" id="modal-corpo-instrumento">
			 A ação foi concluída com sucesso!
			</div>
</div>
</script>


<?php the_content(); ?>
<?php 
			$autorizado = false;

			if(is_user_logged_in()){
				$usuario = wp_get_current_user();
				foreach($usuario->roles as $role) {
					if(strtolower($role) == 'administrator')
						$autorizado = true;
				}
			}else
				$autorizado = false;
			
			if($autorizado){
 ?>
<form>
			<button class="btn-primary" type="button" ng-click="exportarGrupos()"> Exportar relação de estratégias e instrumentos </button>
			<div class="elemento-cadastro" >
				
				
				<label for="tipo"> Tipo de grupo de indicador </label>
				
				<div class="descricao-cadastro"><small>Selecione o tipo de grupo de indicador</small></div>
				
				<select class="controle-cadastro" ng-model="tipo" ng-change="carregarTipo()" id="tipo">
				<option value="instrumento">Instrumentos</option>
				<option value="estrategia">Estratégias</option>
				<option value="objetivo">Objetivos</option>
				</select>
			</div>

			<span data-ng-show="estado!='inserir' && tipo!=null">
				<div class="elemento-cadastro"  >
					
					
				<label for="grupo" > {{tipoExibicao}} </label>
					
					<div class="descricao-cadastro"><small>Selecione o {{tipoExibicao}}</small></div>
					
					<select class="controle-cadastro" style="max-width:100%;" data-ng-model="idItemAtual" data-ng-options="grupo.id_grupo_indicador as grupo.nome for grupo in grupos | orderBy: 'nome'" data-ng-change="carregar()" id="grupo">
					<option value=""></option>
					</select>
				</div>

			</span>
			
			<span data-ng-show="estado!='listar' && tipo!=null">
			

			
			<div class="elemento-cadastro">
				<label for="nome"> Nome do {{tipoExibicao}} </label>
				<div class="descricao-cadastro"><small> Defina o nome que o {{tipoExibicao}} terá </small></div>
				<input class="controle-cadastro" type="text" style="max-width:100%;width:100%;" data-ng-model="itemAtual.nome" id="nome"></input>
			</div>
			
			<div class="elemento-cadastro">
				<label> Ordenação dos indicadores </label>
				<div class="descricao-cadastro"><small> Defina a ordem que os indicadores aparecem no {{tipoExibicao}} </small></div>
				<ul data-as-sortable="board.dragControlListeners" data-ng-model="indicadores" style="padding-top:5px;padding-bottom:5px;">
				   <li data-ng-repeat="indicador in indicadores" data-as-sortable-item>
					  <div data-as-sortable-item-handle>{{indicador.nome}}</div>
				   </li>
				</ul>
			</div>
			
			<div class="container elemento-cadastro" style="background-color:#E5E5E5;border-color:#DDDDDD;border-width:1px;border-style:solid;">
			<label> Propriedades {{tipo=='estrategia'? 'da':'do'}} {{tipoExibicao}} </label>
			<div><small> Edite as propriedades {{tipo=='estrategia'? 'da':'do'}} {{tipoExibicao}} </small></div>
			<div data-ng-repeat="propriedade in itemAtual.propriedades">
				
					<div class="row">
						<div class="col-sm-3"> <label ng-attr-for="{{'chave-'+$index}}"><small>Item </small> </label> </div>
						<div class="col-sm-7"> <label ng-attr-for="{{'valor-'+$index}}"><small> </small></label></div>
						<div class="col-sm-1"> <label ng-attr-for="{{'ordem-'+$index}}"><small>Ordem</small></label></div>
					</div>
					<div class="row">
						<div class="col-sm-3">
							<input class="controle-cadastro" type="text" style="max-width:100%;width:100%;" data-ng-model="propriedade.chave" ng-attr-id="{{'chave-'+$index}}" ></input>
						</div>
						<div class="col-sm-7">
							<textarea rows="4" type="text" style="max-width:100%;width:100%;" data-ng-model="propriedade.valor" ng-attr-id="{{'valor-'+$index}}" ></textarea>
						</div>
						<div class="col-sm-1">
							<input class="controle-cadastro" type="text" style="max-width:100%;width:100%;" data-ng-model="propriedade.ordem" ng-attr-id="{{'ordem-'+$index}}" ></input>
						</div>
						<div class="col-sm-1 pull-right"> 
							<button ng-click="deletarElemento($index)">-</button>
						</div>
					</div>
				
			</div>
			
			<button type="button" style="margin-top:1em;margin-bottom:1em" data-ng-click="adicionarElemento()">Adicionar item</button>
			</div>
			<br>
			<input type="button" data-ng-show="estado!='inserir'" value="Atualizar" data-ng-click="criarModalConfirmacao('Atualizar')">
			<input type="button" data-ng-show="estado!='inserir'" value="Remover" data-ng-click="criarModalConfirmacao('Remover')">
			
			</span>
			
			<input data-ng-show="estado!='inserir' && tipo!=null" type="button" value="Novo" data-ng-click="limparForm()">
			
			<input data-ng-show="estado=='inserir'" type="button" value="Gravar" data-ng-click="criarModalConfirmacao('Inserir')">
			<input data-ng-show="estado=='inserir'" type="button" value="Voltar" data-ng-click="voltar()">
			<!-- Issue 45 -->
			<br>
			<!-- APAGAR -->
			<input type="submit" data-ng-click="renderizarMapa()" value="TESTAR MAPA">
			<!-- END APAGAR -->
			<hr>
			<br>
			<div data-ng-show="estado!='inserir' && tipo=='instrumento' && idItemAtual">
				<h4>Mapa temático</h4>

				<!-- <link rel="stylesheet" href="https://cdn.rawgit.com/openlayers/openlayers.github.io/master/en/v5.3.0/css/ol.css" type="text/css"> -->
				<style>
					.map {
						height: 550px;
						width: 350px;
					}
				</style>
				<!-- <script src="https://cdn.rawgit.com/openlayers/openlayers.github.io/master/en/v5.3.0/build/ol.js"></script> -->
				<div id="map" class="map"></div>

				<label for="arquivo"> Selecione o arquivo </label>
				<br>
				<input type="file" style="max-width:100%;width:100%;" data-ng-model-instant id="arquivos" name="arquivos" onchange="angular.element(this).scope().lerArquivos(this)">
				<input type="submit" data-ng-show="estado!='inserir'" value="Carregar Mapa" data-ng-click="criarModalConfirmacao('CarregarMapa')">
			</div>
			
</form>

<?php }else{ ?>
			<h4> Você não possui autorização para visualizar esse conteúdo.</h4>
<?php } ?>

</div>


