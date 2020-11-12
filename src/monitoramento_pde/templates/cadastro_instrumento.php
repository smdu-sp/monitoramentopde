<?php
/**
 * Template Name: Cadastro Instrumento
 */
?>
<script type="text/javascript">
jQuery.noConflict();
var customStyle = false;
var app = angular.module('monitoramentoPde', ['ngResource','ngAnimate','ui.bootstrap','angular.filter','as.sortable']);
const contornoSP = {
	path: "/app/uploads/instrumentos/msp_contorno.kml",
	style: {
		stroke_color: 'rgba(0, 0, 0, 0.5)',
		stroke_width: 4,
		fill_color: 'rgba(0, 0, 10, 0.02)',
		style_from_kml: false
	}
};

function hexToRgb(hex) {
	var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
	return result ? {
		r: parseInt(result[1], 16),
		g: parseInt(result[2], 16),
		b: parseInt(result[3], 16)
	} : null;
}

function rgbaToHex(rgbaString) {
	// Verifica se string informada é um hex
	if (rgbaString.length === 7 && rgbaString[0] === '#') {
		return {hex: rgbaString, alfa: '1'}
	}
	rgbaString = rgbaString.replace(/ +/g, '').split('(')[1].split(')')[0].split(',');
	var hexStr = '#';
	for (var i = 0; i < 3; i++) {
		var num = parseInt(rgbaString[i]).toString(16);
		num = num.length === 2 ? num : "0" + num;
		hexStr += num;
	}
	
	var hexAlfa = {	
		hex: hexStr,
		alfa: rgbaString[3]
	}
	return hexAlfa;
}

function abgrHex2rgba(abgrHex){
	// Esperada string aabbggrr hexadecimal
	let r = abgrHex[6]+abgrHex[7];
	let g = abgrHex[4]+abgrHex[5];
	let b = abgrHex[2]+abgrHex[3];
	let a = abgrHex[0]+abgrHex[1];
	let rgb = hexToRgb(r+g+b);
	return 'rgba('+rgb.r+', '+rgb.g+', '+rgb.b+', '+(parseInt(a, 16)/255).toString()+')';
}

function html2olDash(lineDash){
	let olDash = [1,0];
	switch (lineDash) {
		case "dotted":
			olDash = [2,2];
			break;
		case "dashed":
			olDash = [5,5];
			break;
		case "none":
			olDash = [0,1];
			break;
		default:
			// Solid
			olDash = [1,0];
	}
	return olDash;
}

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

app.factory('GravarParametrosMapa',function($resource){
	return $resource('/wp-json/monitoramento_pde/v1/instrumentos/gravar_parametros_mapa/:id_grupo_indicador',{id_grupo_indicador:'@id_grupo_indicador'},{
		update:{
			method:'PUT'
		}
	});
});

const cargaUpdateParams = {
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

// Suporte a multiplas camadas
app.factory('Camadas',function($resource){
	return $resource('/wp-json/monitoramento_pde/v1/instrumentos/camadas/:id_grupo_indicador',{id_grupo_indicador:'@id_instrumento'},{
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
	});
});
app.factory('CarregarCamadaKML',function($resource){
	return $resource('/wp-json/monitoramento_pde/v1/instrumentos/carregar_camada_kml/:id_camada',{id_camada:'@id_camada'},{
		update: cargaUpdateParams
	});
});
app.factory('IncluirCamada',function($resource){
	return $resource('/wp-json/monitoramento_pde/v1/instrumentos/camadas/:id_grupo_indicador',{id_grupo_indicador:'@id_grupo_indicador'},{
		update:{
			method:'POST'
		}
	});
});
app.factory('Camada',function($resource){
	return $resource('/wp-json/monitoramento_pde/v1/instrumentos/camada/:id_camada',{id_camada:'@id_camada'},{
		update:{
			method:'PUT'
		}
	});
});

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

app.controller("cadastroGrupo", function($scope, $rootScope, $http, $filter, $uibModal, GrupoIndicador, Indicador, CarregarMapaTematico, ObterMapa, GravarParametrosMapa, Camadas, CarregarCamadaKML, IncluirCamada, Camada) {
 
	$scope.estado = "listar";
	$scope.estilo = {};
	// $scope.estiloKml = false;
	$scope.raio = 4;

	$scope.mapLegendas = [];

	$scope.carregarMapa = function(){
		$rootScope.carregandoArquivo = true;
		$rootScope.mensagemArquivo = 'Aguarde... Realizando Carga';
		// CARGA DB FONTE DE DADOS
		CarregarMapaTematico.update({id_instrumento:$scope.idItemAtual,arquivos:$scope.arquivos}).$promise.then(
			function(mensagem){
				$rootScope.mensagemArquivo = '';					
				$rootScope.modalProcessando.close();		
				$scope.criarModalSucesso();
				$scope.renderizarMapa();
				// Limpa camadas antes de atualizar mapa
				$rootScope.mapLayers = [$rootScope.mapLayers[0]];
				// Marca opção "Utilizar estilo do KML" de acordo com opção marcada no banco
				if ($scope.mapa.parametros_mapa !== undefined && $scope.mapa.parametros_mapa !== null){
					var params = JSON.parse($scope.mapa.parametros_mapa);						
					// $scope.estiloKml = params.style_from_kml;						
				}

				$rootScope.carregandoArquivo = false;
			},
			function(erro){
				$rootScope.modalConfirmacao.close();						
				$rootScope.carregandoArquivo = false;
				$rootScope.mensagemArquivo = '';
				console.log(erro);					
			}
		).catch(function(err){				
			console.error(err);
		});		
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

			// Para funcionamento em ambiente de homologação ({pasta raiz}/.env)
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
		
		$rootScope.map = new ol.Map({
			target: 'map',
			layers: $rootScope.mapLayers,
			view: new ol.View({
				center: [-5191207.638373509,-2698731.105121977],
				zoom: 10,
				maxZoom: 20
			})
		});
	}
	$scope.loadMap();

	$scope.addLayers = function(layersInstrumento){
		for(i in layersInstrumento) {
			// Verifica se há estilo personalizado
			let index = layersInstrumento[i];

			var olStyle = {};
			if (index.style.stroke_color !== undefined) {				
				olStyle = new ol.style.Style({
					stroke: new ol.style.Stroke({
						color: index.style.stroke_color,
						width: index.style.stroke_width,
						lineDash: html2olDash(index.style.stroke_dash)
					}),
					fill: new ol.style.Fill({
						color: index.style.fill_color
					}),					
					image: new ol.style.Circle({ // Estilo do ponto
						radius: $scope.raio,
						fill: new ol.style.Fill({color: index.style.fill_color}),
						stroke: new ol.style.Stroke({
							color: index.style.stroke_color,
							width: index.style.stroke_width
						})
					})
				})
			}

			let kmlLayer = new ol.layer.Vector({
				style: olStyle,				
				source: new ol.source.Vector({
					url: index.path,
					format: new ol.format.KML({
						extractStyles: index.style.style_from_kml
					})
				})
			});
			kmlLayer.id = index.id_camada;
			kmlLayer.nome = index.nome_camada;
			$scope.mapLayers.push(kmlLayer);			
		}
	};

	$scope.parseJson = function(json) {
		var parsed = {};
		try {
			parsed = JSON.parse(json)
		} catch (e) {
			console.error(e);
		}
		return parsed;
	}

	$scope.renderizarMapa = function(ignoraEstiloDB = false) {
		// limpa layers
		for (var i = $scope.map.getLayers().getArray().length - 1; i > 0; i--) {
			$scope.map.removeLayer($scope.map.getLayers().getArray()[i]);
		}
		$rootScope.mapLayers = [$rootScope.mapLayers[0]];
		
		var customLayers = [];

		for (var indiceCamada = 0; indiceCamada < $scope.camadasInstrumento.length; indiceCamada++) {
			var camada = $scope.camadasInstrumento[indiceCamada];			
			camada.path = "/app/uploads/instrumentos/" + camada.arquivo_kml;

			// Verifica se não há estilo personalizado				
			if (typeof(camada.parametros_estilo) == "string"){
				camada.parametros_estilo = $scope.parseJson(camada.parametros_estilo);					
			}
			if(!camada.parametros_estilo) {
				camada.parametros_estilo = {
					style_from_kml: true,
					fill_color: "rgba(255,255,255, 0.5)",
					stroke_color: "rgba(0, 0, 0, 0.9)"
				}
			}
			/*
			if(!ignoraEstiloDB){
				// se renderização for chamada pela alteração do input estiloKml, ignora valor guardado no banco de dados
				camada.estiloKml = camada.parametros_estilo ? camada.parametros_estilo.style_from_kml : false;
			}
			*/

			// Em features do tipo ponto, prioriza não obter estilo do KML
			if(camada.tipo_feature === "ponto") {
				// camada.parametros_estilo.style_from_kml, camada.estiloKml = false;
				camada.parametros_estilo.style_from_kml = false;
			}

			camada.style = camada.style ? camada.style : camada.parametros_estilo;
			
			camada.hexStyle = {
				stroke_color: camada.style.stroke_color ? rgbaToHex(camada.style.stroke_color).hex : null,
				stroke_color_a: camada.style.stroke_color ? rgbaToHex(camada.style.stroke_color).alfa : null,
				fill_color: camada.style.fill_color ? rgbaToHex(camada.style.fill_color).hex : null,
				fill_color_a: camada.style.fill_color ? rgbaToHex(camada.style.fill_color).alfa : null
			}
			customLayers.push(camada);
		}
		// Ordena camadas conforme propriedade 'ordem'
		customLayers.sort(function(a,b){return a.ordem-b.ordem});
		// FIM ITERAÇÃO DE CAMADAS
		
		$scope.addLayers(customLayers);
		
		if($scope.map.getLayers().getLength() === 1)
			$scope.addLayers([contornoSP]);

		var cLayers = $scope.map.getLayers();
		for(layer in cLayers) {
			$scope.map.removeLayer(layer);
		}

		for(layer in $scope.mapLayers){
			$scope.map.addLayer($scope.mapLayers[layer]);
		}
		var mapLayersSource = $scope.mapLayers[1].getSource();

		// INICIO LEGENDA
		/*
		$scope.map.getLayers().forEach(function(layer){
			if(layer.nome) {
				console.warn("foreach Layer");
				console.log(layer);
			}
		});
		*/
		// FIM LEGENDA
		
		window.setTimeout(function(){
			var extent = ol.extent.createEmpty();
			$scope.map.getLayers().forEach(function(layer) {
				// Ajusta zoom e centraliza mapa
				if(layer.getSource().getExtent !== undefined){
			  		ol.extent.extend(extent, layer.getSource().getExtent());
			  	}
			  // Identifica estilo do KML e grava valores para criação da legenda
			  // ...INSERIR AQUI CASO NÃO CONSIGA REALIZAR IMEDIATAMENTE 
			});
			$scope.map.getView().fit(extent, $scope.map.getSize());
		}, 2000);
	}

	$scope.atualizaEstilo = function(camada) {
		estiloKml = camada.parametros_estilo.style_from_kml;
		/** Verifica checkbox "Usar estilo do KML" e atualiza estilo das features no mapa **/
		if (estiloKml) {
			// Limpa camadas antes de atualizar mapa
			$rootScope.mapLayers = [$rootScope.mapLayers[0]];
			$scope.renderizarMapa(true);
		}
		else {
			$scope.alterarCor(camada);
		}
	}
	
	$scope.forceFileUpdate = function(camada) {
		console.warn("forceFileUpdate");
		console.log(camada);
		if(!$scope.arquivos[0]){
			return;
		}

		for (var i = 0; i < $scope.camadasInstrumento.length; i++) {
			if($scope.camadasInstrumento[i].id_camada === camada.id_camada){
				let data = new Date();
				let prefixo = $scope.itemAtual.id_grupo_indicador + "_" + data.getFullYear().toString()+(data.getMonth()+1)+data.getDate() + "_";
				camada.arquivo_kml = prefixo + $scope.arquivos[0].name;
				break;
			}
		}	
			
		console.log(camada);
	}

	$scope.lerArquivos = function(element) {
		
		$scope.$apply(function($scope) {
		// Converte a lista de arquivos (objeto) em um array
			$scope.arquivos = [];
			for (var i = 0; i < element.files.length; i++) {
				$scope.arquivos.push(element.files[i])
			}
		});
	};

	$scope.alterarCor = function(camada) {
		let hexStroke = camada.hexStyle.stroke_color ? camada.hexStyle.stroke_color : "#000000";
		let opacityStroke = camada.hexStyle.stroke_color_a ? camada.hexStyle.stroke_color_a : "1";
		let hexFill = camada.hexStyle.fill_color ? camada.hexStyle.fill_color : "#000000";
		let opacityFill = camada.hexStyle.fill_color_a ? camada.hexStyle.fill_color_a : "0.5";

		let strokeColor = 'rgba(' + hexToRgb(hexStroke).r + ', ' + hexToRgb(hexStroke).g + ', ' + hexToRgb(hexStroke).b + ', ' + opacityStroke + ')';
		let fillColor = 'rgba(' + hexToRgb(hexFill).r + ', ' + hexToRgb(hexFill).g + ', ' + hexToRgb(hexFill).b + ', ' + opacityFill + ')';

		camada.parametros_estilo.stroke_color = strokeColor;
		camada.parametros_estilo.fill_color = fillColor;

		// Parametros gravados, atualiza mapa
		$scope.renderizarMapa();
	}

	$scope.verificarBorda = function(camada) {
		// Se for selecionada opção "sem borda", muda opacidade do contorno para 0
		if (camada.parametros_estilo.stroke_dash === "none") {
			camada.hexStyle.stroke_color_a = "0";
		}
	}

	$scope.validaLegendas = function() {
		$scope.mapa.parametros_mapa.items_legenda = $scope.mapLegendas;
	}

	$scope.estiloLegenda = function(camada) {
		// ESTILO DO KML
		// Se opção "estilo do KML" estiver marcada, pega estilo da primeira feature para desenhar ícone da legenda
		if (camada.parametros_estilo.style_from_kml && camada.arquivo_kml) {
			var xhttp = new XMLHttpRequest();
			let xmlCamada = '';
			xhttp.onreadystatechange = function() {
		    if (this.readyState == 4 && this.status == 200) {
					parser = new DOMParser();
					xmlCamada = parser.parseFromString(xhttp.responseText,"text/xml");
					let xmlFillColor = xmlCamada.getElementsByTagName("PolyStyle")[0] ? xmlCamada.getElementsByTagName("PolyStyle")[0].childNodes : [];
					for (var i = 0; i < xmlFillColor.length - 1; i++){
						if(xmlFillColor[i].tagName === "color"){
							camada.style.fill_color = abgrHex2rgba(xmlFillColor[i].innerHTML);
							// console.log(xmlFillColor[i].innerHTML);
							break;
						}
					}
					let xmlStrokeColor = xmlCamada.getElementsByTagName("LineStyle")[0] ? xmlCamada.getElementsByTagName("LineStyle")[0].childNodes : [];
					for (var i = 0; i < xmlStrokeColor.length - 1; i++){
						if(xmlStrokeColor[i].tagName === "color"){
							camada.style.stroke_color = abgrHex2rgba(xmlStrokeColor[i].innerHTML);
							// console.log(xmlStrokeColor[i].innerHTML);
							break;
						}
					}
		    }
			};
			xhttp.open("GET", camada.path, true);
			xhttp.send();
		}
		// FIM ESTILO DO KML

		let raio = "0";
		let largura = "20px";
		
		if (camada.tipo_feature == "ponto") {
			raio = "50%";
			largura = "12px";
		}

		let altura = camada.tipo_feature == "linha" ? "0px" : largura;
		let borda = "2px "+camada.style.stroke_dash+" "+camada.style.stroke_color;
		
		if (camada.style.stroke_dash == "none") {
			borda = "none";
		}

		let estilo = {
			"background-color": camada.tipo_feature == "linha" ? "unset" : camada.style.fill_color,
			"width": largura,
			"height": altura,
			"display": "inline-block",
			"margin": camada.tipo_feature == "ponto" ? "0 10px" : "0 5px",
			"border": borda,
			"border-radius": raio,
			"border-top": camada.tipo_feature == "linha" ? "none" : borda,
			"vertical-align": "middle"
		}
		
		return estilo;
	}

	$scope.gravarParametrosMapa = function(){
		var parametrosTratados = typeof($scope.mapa.parametros_mapa) == 'string' ? $scope.mapa.parametros_mapa : JSON.stringify($scope.mapa.parametros_mapa);
		
		GravarParametrosMapa.update({id_grupo_indicador:$scope.idItemAtual,parametros_mapa:parametrosTratados}).$promise.then(
			function(mensagem){				
				$rootScope.modalProcessando.close();
				$scope.criarModalSucesso();
			},
			function(erro){
				$rootScope.modalProcessando.close();
				$scope.lancarErro(erro);
			}
		);		
	};
		
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
		$scope.camadasInstrumento = []; // Limpa legenda / camadas do instrumento

		$scope.itemAtual = $rootScope.grupos.filter((grupo) => grupo.id_grupo_indicador == $scope.idItemAtual)[0];
		
		Indicador.query({grupo_indicador:$scope.idItemAtual,somente_ativos:true},function(indicadores) {
			 $scope.indicadores = indicadores;
			 if($scope.itemAtual != null){
			 	$scope.estado = "selecionar";
			 	$scope.obterCamadas();
			 	 // TODO: verificar se dados do indicador já foram obtidos antes de renderizar mapa
			 }
		 });
	};

	// Suporte multiplas camadas
	$scope.obterCamadas = function(){
		Camadas.query({id_grupo_indicador:$scope.idItemAtual},
			function(retorno){				
				if(retorno.length > 0) {
					$scope.camadasInstrumento = retorno;
					for (var i = $scope.camadasInstrumento.length - 1; i >= 0; i--) {
						$scope.camadasInstrumento[i].parametros_estilo = JSON.parse($scope.camadasInstrumento[i].parametros_estilo);
					}
					$scope.renderizarMapa();
				}
				else {
					console.error("Nenhuma camada obtida", retorno);
				}
			},
			function(erro){
				console.error(erro)
			});
	};

	$scope.incluirCamada = function(){
		IncluirCamada.save({id_grupo_indicador:$scope.idItemAtual},
			function(mensagem){
				console.log(mensagem);
				$scope.obterCamadas();
			},
			function(erro){console.error(erro)});
	};

	$scope.setCamadaAtual = function(idCamada, camada){
		$scope.idCamadaAtual = idCamada;
		$scope.camadaAtual = camada;
		console.log('Camada Atual: '+$scope.idCamadaAtual);
	}
/*
	$scope.aplicarHex = function(camada){
		console.warn("aplicarHex");
		console.log(camada);

		let hexStroke = camada.hexStyle.stroke_color ? camada.hexStyle.stroke_color : "#000000";
		let hexStrokeA = camada.hexStyle.stroke_color_a ? camada.hexStyle.stroke_color_a : "1";
		let hexFill = camada.hexStyle.fill_color ? camada.hexStyle.fill_color : "#000000";
		let hexFillA = camada.hexStyle.fill_color_a ? camada.hexStyle.fill_color_a : "0.5";

		$scope.alterarCor(camada, hexStroke, hexStrokeA, hexFill, hexFillA);

		TODO: REMOVER CASO alterarCor esteja funcionando
	}
*/
	$scope.enviarKML = function(){
		$rootScope.carregandoArquivo = true;
		$rootScope.mensagemArquivo = 'Enviando KML...';
		CarregarCamadaKML.update({id_grupo_indicador:$scope.itemAtual.id_grupo_indicador,id_camada:$scope.idCamadaAtual,arquivos:$scope.arquivos}).$promise.then(
			function(mensagem){
				$rootScope.mensagemArquivo = '';					
				$rootScope.modalProcessando.close();		
				$scope.criarModalSucesso();
				$rootScope.carregandoArquivo = false;
				// Tentar recarregar mapa com novo arquivo
				$scope.carregar();
				// $scope.forceFileUpdate($scope.camadaAtual);
				// $scope.renderizarMapa();

			},
			function(erro){
				$rootScope.modalConfirmacao.close();						
				$rootScope.carregandoArquivo = false;
				$rootScope.mensagemArquivo = '';
				console.log(erro);					
			}
		).catch(function(err){				
			console.error(err);
		});
	};

	$scope.gravarParametrosCamada = function(idCamada, indice){		
		// Pega parâmetros da camada
		let parametrosEstilo = $scope.camadasInstrumento[indice].parametros_estilo;
		let parametrosTratados = typeof(parametrosEstilo) == 'string' ? parametrosEstilo : JSON.stringify(parametrosEstilo);
		let tipoFeature = $scope.camadasInstrumento[indice].tipo_feature;
		let nomeCamada = $scope.camadasInstrumento[indice].nome_camada;
		let ordem = $scope.camadasInstrumento[indice].ordem;

		Camada.update({id_camada:idCamada, parametros_estilo:parametrosTratados, tipo_feature:tipoFeature, nome_camada:nomeCamada, ordem:ordem}).$promise.then(
				function(mensagem){
					console.log(mensagem);
				},
				function(erro){
					console.error(erro);
				}
			);
	};

	$scope.removerCamada = function(idCamada, key){
		Camada.remove({id_camada:idCamada}).$promise.then(
			function(mensagem){
				$scope.camadasInstrumento.splice(key, 1);
			},
			function(erro){
				$scope.lancarErro(erro);
			}
		);
	};
	// END Suporte multiplas camadas
	
	$scope.adicionarElemento = function(){
		$scope.itemAtual.propriedades.push({});
	};
	
	$scope.criarModalConfirmacao = function(acao){
		// Verifica se arquivo foi selecionado antes de carregar o mapa
		if((acao === 'CarregarMapa' || acao === 'EnviarKML') && !$scope.arquivos){
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
		
		switch ($scope.acao) {
			case 'Atualizar':
				$scope.acaoExecutando = 'Atualizando';
				$scope.acaoSucesso = 'Atualizada';
				$scope.atualizar();
				break;
			case 'Remover':
				$scope.acaoExecutando = 'Removendo';
				$scope.acaoSucesso = 'Removida';
				$scope.remover();
				break;
			case 'Inserir':
				$scope.acaoExecutando = 'Inserindo';
				$scope.acaoSucesso = 'Inserida';
				$scope.inserir();
				break;
			case 'CarregarMapa':
				$scope.acaoExecutando = 'Carregando';
				$scope.acaoSucesso = 'Carregado';
				$scope.carregarMapa();
				break;
			case 'EnviarKML':
				$scope.acaoExecutando = 'Carregando';
				$scope.acaoSucesso = 'Carregado';
				$scope.enviarKML();
				break;
			case 'GravarParametrosMapa':
				$scope.acaoExecutando = 'Gravando';
				$scope.acaoSucesso = 'Gravado';
				$scope.gravarParametrosMapa();
				break;
			default:
				console.log($scope.acao);
				// window.alert('Evento inesperado! Contate o desenvolvedor');
		};		
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
				<div class="elemento-cadastro">					
					
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
			<br>
			<hr>
			<br>
			<div data-ng-show="estado!='inserir' && tipo=='instrumento' && idItemAtual">
				<h4>Mapa temático</h4>
				<div id="map" class="map">
					<!-- TODO: Referenciar legendas às camadas -->
					<div id="legenda-mapa">
						<div ng-repeat="(key, camada) in camadasInstrumento">
							<div ng-style="estiloLegenda(camada)"></div><span>{{camada.nome_camada}}</span>
						</div>
					</div>
				</div>
				<div id="controles-mapa">
					<h3>Camadas</h3>
					<div style="margin: 1em 0"><button ng-click="incluirCamada()" class="btn btn-success">+ Adicionar camada</button></div>
					<div class="layer-containers">
						<div ng-repeat="(key, camada) in camadasInstrumento" class="legenda-input">
							<div class="form-inline">
								<div class="form-group">
									<input class="form-control" type="text" ng-model="camadasInstrumento[key].nome_camada" data-ng-model-instant placeholder="Nome">
									<select class="form-control" ng-model="camadasInstrumento[key].tipo_feature">
										<option value="poligono">Polígono</option>
										<option value="linha">Linha</option>
										<option value="ponto">Ponto</option>
									</select>
									<div class="caixa-ordem">
										<label>Ordem</label>
										<input class="form-control" type="number" ng-model="camadasInstrumento[key].ordem" data-ng-model-instant ng-change="gravarParametrosCamada(camada.id_camada, key);alterarCor(camadasInstrumento[key])">
									</div>
								</div>
							</div>
							<div class="form-inline">
								<div class="form-group">
									<span class="caixa-nome-arquivo" ng-attr-title="{{camada.arquivo_kml}}"><strong>Arquivo:</strong> {{camada.arquivo_kml}}</span>
									<div class="caixa-estilo-kml caixa-ordem" ng-if="camada.tipo_feature !== 'ponto'">
										<label>Usar estilo do KML</label>
										<input type="checkbox" ng-change="atualizaEstilo(camadasInstrumento[key])" ng-model="camadasInstrumento[key].parametros_estilo.style_from_kml">
									</div>
								</div>
							</div>
							<div class="form-inline">
								<div class="form-group" ng-class="camada.parametros_estilo.style_from_kml ? 'inativo' : ''">
									<span>Cor do preenchimento</span>
									<input type="color" value="#FFFFFF" ng-model="camadasInstrumento[key].hexStyle.fill_color" ng-change="alterarCor(camadasInstrumento[key])" data-ng-model-instant class="colpick" ng-style="estiloLegenda(camada)" ng-disabled="camada.parametros_estilo.style_from_kml">
									<span>Opacidade</span>
									<input type="range" class="alfa-slider" min="0" max="1" step="0.1" value="1" ng-model="camadasInstrumento[key].hexStyle.fill_color_a" ng-change="alterarCor(camadasInstrumento[key])" ng-disabled="camada.parametros_estilo.style_from_kml">

									<br>									
								 
									<div ng-class="camada.parametros_estilo.stroke_dash === 'none' ? 'inativo' : ''">
										<span>Cor do contorno</span>
										<input 
											type="color"
											value="#FFFFFF"
											data-ng-model-instant
											class="colpick"
											ng-disabled="camada.parametros_estilo.stroke_dash === 'none'"
											ng-model="camadasInstrumento[key].hexStyle.stroke_color"
											ng-change="alterarCor(camadasInstrumento[key])">
										<span>Opacidade</span>
										<input type="range" class="alfa-slider" min="0" max="1" step="0.1" value="1" ng-disabled="camada.parametros_estilo.stroke_dash === 'none'" ng-model="camadasInstrumento[key].hexStyle.stroke_color_a" ng-change="alterarCor(camadasInstrumento[key])">
									</div>
									<select title="Linha de contorno" ng-model="camadasInstrumento[key].parametros_estilo.stroke_dash" ng-change="verificarBorda(camadasInstrumento[key]);alterarCor(camadasInstrumento[key])">
										<option value="solid">Sólida</option>
										<option value="dotted" ng-if="camada.tipo_feature !== 'ponto'">Pontilhada</option>
										<option value="dashed" ng-if="camada.tipo_feature !== 'ponto'">Tracejada</option>
										<option value="none">Sem borda</option>
									</select>									
								</div>
							</div>
							<!-- ENVIAR/SUBSTITUIR ARQUIVO KML -->
							<div class="form-inline">
								<div class="kml-form">
									<label for="arquivo">Selecione o arquivo KML</label>
									<br>
									<!-- <input type="file" style="max-width:100%;width:100%;" data-ng-model-instant name="arquivos" onchange="angular.element(this).scope().lerArquivos(this);angular.element(this).scope().forceFileUpdate(camada)">									 -->
									<input type="file" style="max-width:100%;width:100%;" data-ng-model-instant name="arquivos" onchange="angular.element(this).scope().lerArquivos(this)">									
									<input class="btn btn-info btn-block" type="submit" data-ng-show="estado!='inserir'" value="Carregar KML" data-ng-click="forceFileUpdate(camadasInstrumento[key]);setCamadaAtual(camada.id_camada, camada);criarModalConfirmacao('EnviarKML')">
								</div>
							</div>
							<div>
								<button class="btn btn-primary" ng-click="gravarParametrosCamada(camada.id_camada, key)">Gravar dados</button>
								<button ng-click="removerCamada(camada.id_camada, key)" class="btn btn-danger">Remover camada</button>
							</div>
						</div>
					</div>

				</div>
				
				<!-- <div>
					<label for="arquivo"> Selecione o arquivo </label>
					<br>
					<input type="file" style="max-width:100%;width:100%;" data-ng-model-instant id="arquivos" name="arquivos" onchange="angular.element(this).scope().lerArquivos(this)">
					<input type="submit" data-ng-show="estado!='inserir'" value="Carregar Mapa" data-ng-click="criarModalConfirmacao('CarregarMapa')">
				</div> -->
			</div>
</form>
<style type="text/css">
	#map {
		display: inline-block;
		position: relative;
		height: 650px;
		width: 50%;
	}	
	#controles-mapa {
		display: inline-block;
		position: absolute;
		padding: 0 30px 30px;
		margin: 0 1em;
		max-height: 650px;
		max-width: 580px;
		overflow: auto;
		background-color: #f3f3f3;
		z-index: 1;
	}
	#controles-mapa td {
		padding: 5px;
	}
	#legenda-mapa {
		position: absolute;
		right: 0;
		bottom: 0;
		margin: 10px;
		padding: 10px;
		border-radius: 5px;
		min-width: 200px;
		min-height: 100px;
		background-color: rgba(255,255,255,0.7);
		border: 1px solid #cccccc;
		z-index: 1;
	}
	.legenda-input {
		border-bottom: 1px solid #aaaaaa;
		margin: 0;
		padding: 30px 0;
	}
	.colpick {
		padding: 0;
    margin: 0 !important;
    background: none;
    width: 30px !important;
    height: 30px !important;
    border: none !important;
	}
	.inativo {
		opacity: 0.5;
	}
	.kml-form {
		padding: 5px;
    margin: 5px 0;
    border: 1px solid #dddddd;
    border-radius: 5px;
    text-align: center;
	}
	.alfa-slider {
		display: inline-block !important;
		width: 20% !important;
	}
	/*.ordem-spinner {*/
	.caixa-ordem {
		display: inline-block;
    position: absolute;
    right: 0;
    margin: 0 1em;
	}
	.caixa-ordem input {
		width: 4em !important;
		font-weight: bold;
	}
	.caixa-nome-arquivo {
    display: inline-block;
		margin-right: 2em;
    max-width: 290px;
    max-height: 2em;
    overflow: overlay;
	}
	.caixa-estilo-kml {
		position: relative;
    margin: 0;
	}
</style>
<?php }else{ ?>
			<h4> Você não possui autorização para visualizar esse conteúdo.</h4>
<?php } ?>

</div>


