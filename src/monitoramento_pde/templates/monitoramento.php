<?php
/**
 * Template Name: Monitoramento
 */
?>

<!-- <script src="./wp/wp-includes/js/html2canvas.min.js"></script> -->
<script src="app/themes/monitoramento_pde/js/html2canvas.min.js"></script>
<script type="text/javascript">

function hexToRgb(hex) {
    var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    return result ? {
        r: parseInt(result[1], 16),
        g: parseInt(result[2], 16),
        b: parseInt(result[3], 16)
    } : null;
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

function componentToHex(c) {
    var hex = c.toString(16);
    return hex.length == 1 ? "0" + hex : hex;
}

function rgbToHex(r, g, b) {
    return "#" + componentToHex(r) + componentToHex(g) + componentToHex(b);
}
jQuery.noConflict();

var app = angular.module('monitoramentoPde', ['ngResource','ngAnimate','ui.bootstrap','ngRoute','ngSanitize']);

// CONTORNO DO MAPA 
const contornoSP = {
	path: "/app/uploads/instrumentos/msp_contorno.kml?d=200616",
	style: {
		stroke_color: 'rgba(0, 0, 0, 0.5)',
		stroke_width: 4,
		fill_color: 'rgba(0, 0, 10, 0.02)',
		style_from_kml: false
	}
};

var computedUrl = window.location.href;
var selecionado = null;
var counter = 0;
var ultimoSelecionado = false;
var featureInfo = null;
// Persistência das opções selecionadas após mudança de aba
var optEstrategiaSup = null;
var optInstrumentoSup = null;
var optObjetivoSup = null;
var instruMap = null;
let labelProps = {};
let exportMargingBot = 0;
let exportChartOpts = {
	height:700,
	width: 1800,
	marginBottom: exportMargingBot,
	spacingLeft: 30,
	spacingRight: 30,
	spacingBottom: 10,
	spacingTop: 30,
	marginLeft: 60
};

var mapWatcher = function(highlightStyle) {
	instruMap.on('pointermove', function (e) {
		if (selecionado !== null) {
	    selecionado.setStyle(undefined);
	    selecionado = null;
	  }
	  var isLit = false;

	  async function percorreFeatures(pixel){
		instruMap.forEachFeatureAtPixel(pixel, function (f, layer) {
		  	if(layer.ocultar_info){
		  		featureInfo.style.opacity = "0";
		  		selecionado = null;
		  		return true;
		  	}
		  	if (f.get('limite_id') !== "27"){	  	  		
		  		// Camada obtida. 
		  		selecionado = f;
			    f.setStyle(highlightStyle);
			    isLit = true;
		  		return true;
			  }
		  });
	  }
	  percorreFeatures(e.pixel).then((retorno)=>{
	  	// Verifica se feature está 'apagada' (sem highlight)
	  	if(!isLit) {
	  		featureInfo.style.opacity = '0.75';
	  	}
	  	if(selecionado)
	  	{
	  		var fProps = {};
	  		let descricao = "";
	  		for (var prop in selecionado.getProperties())
	  		{
	  			if(typeof(selecionado.getProperties()[prop]) === 'string' && prop !== 'styleUrl')
	  			{
	  				let valor = selecionado.getProperties()[prop];
	  				fProps[prop] = valor;
	  				descricao += '<p><strong>' + prop + ':</strong> ' + valor + '</p>';
	  			}
	  		}
	  		featureInfo.innerHTML = descricao;

	  	  featureInfo.style.opacity = '1';
	  	}
	  });	  
	});
}

// DEBUG
var dmap = {};

// CORES PADRÃO DOS GRÁFICOS
app.defaultColors = ['#edc70a', '#4b1241', '#a90537', '#009045', '#5f87c1', '#cb6037', '#6e5128', '#ba007c', '#a3bd31', '#062e45'];

app.factory('Indicador',function($resource){
	return $resource('/wp-json/monitoramento_pde/v1/indicador/:id',{id:'@id_indicador'},{
		get:{
			headers:{
				'X-WP-Nonce': '<?php  echo(wp_create_nonce('wp_rest')); ?>'
					}
		},query:{
			isArray:true,
			cancellable: true,
			headers:{
				'X-WP-Nonce': '<?php  echo(wp_create_nonce('wp_rest')); ?>'
			}	
		}
	}
	);
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


app.factory('IndicadorMemoria',function($resource){
	return $resource('/wp-json/monitoramento_pde/v1/indicador/memoria/:id');
});

app.factory('GrupoIndicador',function($resource){
	return $resource('/wp-json/monitoramento_pde/v1/grupo_indicador/:id');
});

app.factory('Menu',function($resource){
	return $resource('/wp-json/wp-api-menus/v2/menus/:id');
});

app.factory('FichaTecnicaInstrumento',function($resource){
	return $resource('/wp-json/monitoramento_pde/v1/ficha_tecnica_instrumento/:id');
});

app.factory('IndicadorValores',function($resource){
	return $resource('/wp-json/monitoramento_pde/v1/indicador/valores/:id?data=:data&territorio=:territorio');
});

app.factory('IndicadorHistorico',function($resource){
	return $resource('/wp-json/monitoramento_pde/v1/indicador/historico/:id?territorio=:territorio&regiao=:regiao');
});

app.factory('VariavelHistorico',function($resource){
	return $resource('/wp-json/monitoramento_pde/v1/variavel/historico/:id?territorio=:territorio&regiao=:regiao');
});

app.factory('Noticia', function($resource){
	return $resource('/wp-json/wp/v2/posts?per_page=5&filter[category_name]=Noticia');
});

app.factory('AcaoPrioritaria', function($resource){
	return $resource('/wp-json/monitoramento_pde/v1/acoes_prioritarias/');
});

app.filter('trustedHtml',
   function($sce) {
     return function(ss) {
       return $sce.trustAsHtml(ss)
   };
});

app.filter('dataFinal', function() {
    return function(input,dataInicial) {
        var out = [];
        for (var i = 0; i < input.length; i++) {
            if(input[i] >= dataInicial){
                out.push(input[i]);
            }
        }
        return out;
    }
});

app.filter('setDecimal', function ($filter) {
    return function (input, places) {
        if (isNaN(input)) return input;
        var factor = "1" + Array(+(places > 0 && places + 1)).join("0");
        return Math.round(input * factor) / factor;
    };
});

app.directive('checkEnter', function () {
  return function (scope, element, attrs) {
    element.bind("keydown keypress", function (event) {
      if(event.which === 13) {
        scope.$apply(function (){
            scope.$eval(attrs.checkEnter);
        });

        event.preventDefault();
      }
    });
  };
});

app.config(function($routeProvider) {
  $routeProvider
  .when("/estrategias/:idEstrategia", {
		controller:"dashboard",
	 resolve: {
				init: function() {
					return function() {
						$scope.cargaCadastroIndicadores($route.current.params['idEstrategia']);
					}
				}
			}
  })
});

app.controller("dashboard", function($scope, 
									$rootScope, 
									$http, 
									$filter, 
									$uibModal,
									Indicador, 
									IndicadorValores, 
									Noticia, 
									Menu, 
									AcaoPrioritaria,
									IndicadorHistorico, 
									GrupoIndicador, 
									ObterMapa,
									Camadas,
									FichaTecnicaInstrumento, 
									IndicadorMemoria, 
									VariavelHistorico) {
	$scope.geraLink = function(tipo) {
		let idTipo = {
			indicador: $scope.indicador.id_indicador,
			instrumento: $scope.indicador.id_instrumento,
		};
		let link = "<?php echo get_home_url(); ?>" + "/#/mostra_" + tipo + "/" + idTipo[tipo];
		
		// Copia para o clipboard		
		const el = document.createElement('textarea');
		el.value = link;
		el.setAttribute('readonly', '');
		el.style.position = 'absolute';
		el.style.left = '-9999px';
		document.body.appendChild(el);
		el.select();
		document.execCommand('copy');
		document.body.removeChild(el);
		window.alert("O link para o indicador foi copiado para a área de transferência.\n"+link);
	}

	$scope.indicadorValores = {
		categorias: null
	};

	// $scope.tiposGrafico = ['area','barras','colunas','linhas','pizza'];
	// Oculta tipos adicionais até solucionar bugs de exibição dos novos gráficos
	$scope.tiposGrafico = ['area','barras','colunas','linhas'];
	$scope.tipoGraficoSelecionado = 'linhas';

	$scope.atualizaListaIndicadores = function(){
		$scope.abortReqs();
		switch ($scope.tabAtivaForma) {
			case 1:
				$scope.cargaCadastroIndicadores(optEstrategiaSup);
				break;
			case 2: 
				$scope.atualizarStatusMapa(optInstrumentoSup);
				$scope.cargaCadastroIndicadores(optInstrumentoSup);
				$scope.atualizaFicha(optInstrumentoSup);
				break;
			case 3:
				$scope.atualizarStatusMapa(optObjetivoSup);
				$scope.cargaCadastroIndicadores(optObjetivoSup);
				$scope.atualizaFicha(optObjetivoSup);
				break;
			case 4:
				// Aba de pesquisa de indicadores por texto
				$scope.termoBuscado = "";
				$scope.indicadores = [];
		}
	};

	// Verifica se obteve URL de um indicador específico e carrega o indicador na tela
	$scope.urlIndicador = function(computedUrl){
		if(computedUrl.includes("mostra_indicador")){
			let indicadorFromUrl = parseInt(computedUrl.split("mostra_indicador/").pop());
			Indicador.query({indicador:indicadorFromUrl},function(indicador) {
				let existeIndicador = indicador.length;
				if(existeIndicador) {
					window.setTimeout(function(){
						$scope.indicadores = indicador;
						$scope.indicador = indicador[0];
						console.log("Indicador:");
						console.log($scope.indicador);
						$scope.indicador.aberto = true;
						$scope.atualizarAccordion(indicador[0]);
						document.getElementsByClassName("panel-group")[0].scrollIntoView();
					}, 100);
				}
			});
		}
	};

	// Verifica se obteve URL de um instrumento e carrega na tela 
	$scope.urlInstrumento = function(computedUrl){
		let instrumentoFromUrl = parseInt(computedUrl.split("mostra_instrumento/").pop());
		GrupoIndicador.query({id:instrumentoFromUrl,tipo:'instrumento',tipo_retorno:'object',formato_retorno:'array'}, function(instrumento) {
			let existeInstrumento = instrumento.length;
			if (existeInstrumento) {
				$scope.optInstrumento = instrumentoFromUrl;
				$scope.abortReqs();
				$scope.cargaCadastroIndicadores(instrumentoFromUrl);
				window.setTimeout(function() {
					$scope.atualizaFicha(instrumentoFromUrl);
					$scope.loadMap();
					$scope.atualizarStatusMapa(instrumentoFromUrl);
				}, 1000);
			}
		});
	};

	// ABORTAR TODAS AS REQUISIÇÕES EM ANDAMENTO
	$scope.abortReqs = function() {
		for (var i = $scope.xhrReqs.length - 1; i >= 0; i--) {
			if(!$scope.xhrReqs[i].$resolved){
				$scope.xhrReqs[i].$cancelRequest();
				$scope.carregandoIndicador = false;
			}
		}
	}

	$scope.textoSelectObjetivo = function(idObjetivo) {
		switch (idObjetivo) {
			case 2:
				return "Escolha uma Macroárea...";
				break;
			case 3:
				return "Escolha uma Zona Especial...";
			default:
				return "Escolha..."
				break;
		}
	}

	angular.element(document).ready(function(){
		$scope.optInstrumento = "";
		$scope.termoBuscado = "";
		if(computedUrl.includes("mostra_")) $scope.tabAtivaForma = 2;
		if(computedUrl.includes("mostra_instrumento")) $scope.urlInstrumento(computedUrl);
	});

	// DECLARAÇÃO DE VARIÁVEIS
	$scope.mostraTabela = false;
	$scope.carregandoIndicador = false;
	$scope.xhrReqs = [];
	
	$scope.tabAtivaForma = 1;
	GrupoIndicador.query({tipo:'instrumento',tipo_retorno:'object',formato_retorno:'array'},function(instrumentos){
		$scope.instrumentos = instrumentos;
	});
	GrupoIndicador.query({tipo:'objetivo',tipo_retorno:'object',formato_retorno:'array'},function(objetivos){
		$scope.rawObjetivos = objetivos;
		// $scope.objetivos = objetivos;
	});	

	$scope.idPoligonoAnterior = 0;

	$scope.inicializarSelecao = function(){
		$scope.selecao.idTerrSel = 4;
		$scope.idIndicadorAnterior = 0;
	}
	
	$scope.formatarData = function(data){
		dataArray = data.split('-');
		return new Date(dataArray[0],dataArray[1]-1,dataArray[2]);
	};
	
	$scope.formatarTrimestre = function(data){
		dataAjustada = $scope.formatarData(data)
		quarter = Math.floor((dataAjustada.getMonth() + 3) / 3);
		
		return 'Q' + quarter.toString() + ' / ' + dataAjustada.getYear().toString()
	};

	$scope.razaoOODC = function(id){
		if(id == 107){
			window.setTimeout(function(){
				$scope.selecao.dataMin = "2002-01-01";
				$scope.ajustarDataFinal();
				$scope.carregarGraficoHistorico($scope.regiaoRealcada.codigo, true);				
			}, 2000);
			return "2002-01-01";
		}
		return;
	}
	
	$scope.pontoParaVirgula = function(v){
		if(v !== null && typeof(parseFloat(v)) != NaN)
			v = v.replace('.',',');
		return v;
	};
	
	// BUSCA INFORMACOES DO INDICADOR SELECIONADO PARA CARREGAR DADOS
	$scope.atualizarAccordion = function(indicador){
		$scope.selecao.idIndicSel = indicador.id_indicador;
		$scope.inicializarSelecao();
		// VERIFICA SE JA EXISTE UMA VIEWPORT PARA O MAPA E A REMOVE PARA EVITAR PROBLEMAS DE EXIBICAO
		// if(document.getElementsByClassName('ol-viewport')[0])
		// 	document.getElementsByClassName('ol-viewport')[0].remove();
		
		$scope.cargaIndicadorValores(true,true);
		// semDados precisa ser inicializada neste ponto para evitar que ela seja reescrita antes de ser checada no timeout
		$scope.semDados = false;
		window.setTimeout(function(){			
			if($scope.semDados && $scope.selecao.categorias && $scope.selecao.categorias.length == 1){
				$scope.filtraCategoria();
			}
		}, 1800);
	};
	
	$scope.fixarMapa = function(idRegiao){
		$scope.clickMapa = true;
		$scope.hoverMapa = true;
		
		if($scope.semMunicipio() && !idRegiao && $scope.selecao.categorias && $scope.selecao.categorias.length == 1){
			$scope.selecao.categoria = $scope.selecao.categorias[0];
			$scope.filtraCategoria();
			return;
		};
		$scope.realcarMapa(idRegiao);
	};
		
	$scope.estiloMapa = function(feature,resolution, estiloFundo, corContorno, contador){
		cores = [];
		cores[4] = [79,21,27];
		cores[3] = [155,23,49];
		cores[2] = [186,33,36];
		cores[1] = [220,171,174];
		cores[0] = [240,219,226];
		$scope.qtdClasses = 5;
		regiao = $scope.dadosMapa.filter((regiao) => regiao.codigo == feature.get('ID_REGIAO'))[0];
		regiaoMaiorValor = $scope.dadosMapa.filter((regiaoMaior) => regiaoMaior.posicao == 0)[0];
		if(!angular.isUndefined(regiaoMaiorValor) && !angular.isUndefined(regiao)){
			intervalo = regiaoMaiorValor.valor / $scope.qtdClasses;
			indiceClasseRegiao = regiao.valor / intervalo;
			indiceClasseRegiao = Math.ceil(indiceClasseRegiao);
		}

		if(!angular.isUndefined(indiceClasseRegiao) && indiceClasseRegiao > $scope.qtdClasses){
			indiceClasseRegiao = $scope.qtdClasses;
		}
		
		if(indiceClasseRegiao == 0){
			indiceClasseRegiao = 1;
		}
		
		for(i=1;i<= $scope.qtdClasses;i++){
			legenda = $scope.legenda.filter((legenda) => legenda.indice == i)[0];
			
			if(angular.isUndefined(legenda)){
				maximoLegenda = i * intervalo;
				minimoLegenda = (i - 1) * intervalo;
				maximoLegenda = $scope.ajustarEscalaValor(regiaoMaiorValor.valor, maximoLegenda);
				minimoLegenda = $scope.ajustarEscalaValor(regiaoMaiorValor.valor, minimoLegenda);
				
				if(minimoLegenda != 0){
					minimoLegenda += 0.1;
				}
				corRegiao = cores[i-1];
				
				$scope.legenda.push({
					indice: i,
					maximo: maximoLegenda,
					minimo: minimoLegenda,
					regioes: [],
					cor: rgbToHex(corRegiao[0],corRegiao[1],corRegiao[2])
				});
			}
		}
		
		legenda = $scope.legenda.filter((legenda) => legenda.indice == indiceClasseRegiao)[0];
		
		if(!angular.isUndefined(legenda)){
			regiaoLegenda = legenda.regioes.filter((regiaoLeg) => regiaoLeg == regiao.codigo)[0];
			
			if(angular.isUndefined(regiaoLegenda)){
				legenda.regioes.push(regiao.codigo);
			}
			
			corRegiao = cores[legenda.indice-1];
			corRegiao[3] = 1;
		}else{
			// COLOCA A COR MAIS FRACA
			corRegiao = cores[0];
			corRegiao[3] = 1;
		}
		
		if(estiloFundo){
			corRegiao = [224,224,225];
		}
		
		$scope.$apply();
		return new ol.style.Style({
			fill: new ol.style.Fill({
				color: corRegiao
			}),
			stroke: new ol.style.Stroke({
				color: corContorno,
				width: 0.5
			})
		});
	};
		
	$scope.estiloRealce = function(resolution){
		corContorno = [255,255,255];
		return $scope.estiloMapa(this,resolution,true,corContorno);
	};
	
	$scope.estiloVetor = function(feature, resolution){
		corContorno = [255,255,255];
		return $scope.estiloMapa(feature,resolution,false,corContorno);
	};
	
	$scope.realcarMapa = function(idPoligonoAtual){
		if(idPoligonoAtual==null){
			$scope.cargaIndicadorValores(false,true);
			return;					
		}
		else {
			if(idPoligonoAtual != $scope.idPoligonoAnterior 
				|| !$scope.idPoligonoAnterior 
				|| !$scope.hoverMapa){
				$scope.idPoligonoAnterior = idPoligonoAtual;
				if($scope.selecao.idTerrSel != 4){
					$scope.regiaoRealcada = angular.copy($scope.dadosMapa.filter((regiao) => regiao.codigo == idPoligonoAtual)[0], $scope.regiaoRealcada);								
					$scope.layerVetor.setStyle($scope.estiloVetor);
					$scope.layerVetor.getSource().changed();
					
					$scope.layerVetor.getSource().forEachFeature(function(poligono){
						if (poligono.get('ID_REGIAO') != idPoligonoAtual){
							poligono.setStyle($scope.estiloRealce);
							poligono.changed();
						}else{
							poligono.setStyle(null);
						};
					});
					
					$scope.carregarGraficoHistorico(idPoligonoAtual);
				}
				else {
					$scope.regiaoRealcada = {
						codigo: 1
						,nome: 'Município'
					};
					
					$scope.carregarGraficoHistorico(1);
				}
			}
		}
		$scope.hoverMapa = true;	
	};

	$scope.carregarGraficoHistoricoTotal = function(){
		let isMunicipio = true; // Substituir por parametro da funcao ao modularizar
		dataHistorica = [];
		dataHistorica['original'] = [];
		dataHistorica['formatada'] = [];
		angular.forEach($scope.indicador.datas.slice().reverse(), function(valor,chave){
			if(valor >= $scope.selecao.dataMin && valor <= $scope.selecao.dataMax){
				this['original'].push(valor);
				trimestre = Math.floor((new Date(valor).getMonth() + 3) / 3);
				trimestre = new Date(valor).getMonth()
				dataHistorica['formatada'].push($filter('date')(valor, ($scope.indicador.periodicidade == 'mensal') ? 'MMM yyyy' : (($scope.indicador.periodicidade == 'trimestral') ? 'MM/yyyy' : 'yyyy')));
			}
		},dataHistorica);
		
		if(angular.isUndefined($scope.selecao.dataMin)){
			$scope.selecao.dataMin = $scope.indicador.datas[$scope.indicador.datas.length - 1];
		}
		
		if(angular.isUndefined($scope.selecao.dataMax)){
			$scope.selecao.dataMax = $scope.indicador.datas[0] == null ? $scope.indicador.datas[1] : $scope.indicador.datas[0];
		}
		// Criado FOR para percorrer regioes ativas e armazenar os dados para gerar grafico
		let todasRegioes = [];
		let respostasPendentes = $scope.dadosMapa.length;
		if(respostasPendentes == 0){
			// Não obteve dados do mapa. Busca dados
			$scope.semDados = true;
		}

		angular.forEach($scope.dadosMapa, function(valor, chave){
			IndicadorHistorico.get({
				id: $scope.selecao.idIndicSel,
				territorio: $scope.selecao.idTerrSel,
				regiao: valor.codigo,
				dataMinima: $scope.selecao.dataMin,
				dataMaxima: $scope.selecao.dataMax
			}, function(indicadorHistorico){
				populaTodasRegioes: for (var i = 0; i < indicadorHistorico.series.length; i++) {
					if($scope.selecao.categorias.length > 1){
						indicadorHistorico.series[i].categoria = indicadorHistorico.series[i].name;
					}

					indicadorHistorico.series[i].name = valor.nome;
					// Corrige bug de lentidao na resposta, para inserir somente a quantidade de itens relacionados à serie
					if($scope.selecao.categorias.length > 1 || todasRegioes.length < $scope.dadosMapa.length)
						todasRegioes.push(indicadorHistorico.series[i]);
				}
				respostasPendentes--;
				if(respostasPendentes == 0){
					// RECEBIDAS TODAS AS RESPOSTAS DO SERVIDOR
					
					// TODO: MODULARIZAR FUNCAO SEGUINTE
					let serieHistorica = $scope.selecao.categorias.length > 1 ? $filter('filter')(todasRegioes, $scope.selecao.categoria.name) : todasRegioes;
					serieHistorica = $filter('orderBy')(serieHistorica, 'name');
					
					// APLICA MESMO PADRÃO DE MARCADOR A TODOS OS ITENS DO GRÁFICO DE LINHA
					for (item in serieHistorica) {
						serieHistorica[item].marker = { symbol: "circle" }
					}
					
					$scope.carregarGraficoLinhas = indicadorHistorico.series ? indicadorHistorico.series.length > 0 : false;

					if(!$scope.carregarGraficoLinhas){
						// $scope.carregandoHistorico = 'Não há dados históricos disponíveis para essa seleção!';
						$scope.carregandoHistorico = 'Procurando dados...';
						$scope.carregarGraficoHistoricoTotal();
						return;
					} else {
						$scope.carregandoHistorico = null;
					}
					//

					larguraGraficoLinha = document.getElementById("divGraficoLinha").clientWidth;
					let ultimoItemVarFiltro = "";
					
					subtitulo = "Unidade territorial de análise: " +  "Município" + " <br> Período: " + $filter('date')($scope.indicador.datas[$scope.indicador.datas.length-1], $scope.indicador.periodicidade == 'anual' ? 'yyyy' : 'MMMM yyyy') + " a " + $filter('date')($scope.indicador.datas[0], $scope.indicador.periodicidade == 'anual' ? 'yyyy' : 'MMMM yyyy');
					
					// Issue 10
					/** ATUALIZAR GRÁFICOS RELACIONADOS AO DE LINHA 
					Para os gráficos de linha, as opções alternativas seriam:
						Gráfico de área; Gráfico de colunas; e Gráfico de pizza (ano a ano)
					*/
					$scope.tipoGraficoSelecionado = $scope.mostrarGrafico('linhas') ? 'linhas' : 'colunas';
					window.setTimeout(function(){$scope.$apply()}, 100);
					$scope.graficoArea = new Highcharts.chart('graficoArea',$scope.objGrafico($scope.grafBarSub, $scope.grafBarLeg, 'area', $scope.indicador, $scope.indicadorValores));
					$scope.graficoColunas = new Highcharts.chart('graficoColunas',$scope.objGrafico($scope.grafBarSub, $scope.grafBarLeg, 'column', $scope.indicador, $scope.indicadorValores));
					$scope.graficoLinhas = new Highcharts.chart('graficoLinhas',$scope.objGrafico($scope.grafBarSub, $scope.grafBarLeg, 'line', $scope.indicador, $scope.indicadorValores));
					$scope.graficoPizza = new Highcharts.chart('graficoPizza',$scope.objGrafico($scope.grafBarSub, $scope.grafBarLeg, 'pie', $scope.indicador, $scope.indicadorValores));
					
					if ($scope.graficoLinhas.yAxis[0].max >= 1000000000){
						$scope.textoTitulo = $scope.indicador.tipo_valor + ' (Em bilhões de ' + $scope.indicador.simbolo_valor + ')';
						
					}else 
						if ($scope.graficoLinhas.yAxis[0].max >= 1000000){
							$scope.textoTitulo = $scope.indicador.tipo_valor + ' (Em milhões de ' + $scope.indicador.simbolo_valor + ')';
						}else 
							if ($scope.graficoLinhas.yAxis[0].max >= 1000) {
								$scope.textoTitulo = $scope.indicador.tipo_valor + ' (Em milhares de ' + $scope.indicador.simbolo_valor + ')';
							}
							else {
								$scope.textoTitulo = $scope.indicador.tipo_valor + ' (' + $scope.indicador.simbolo_valor + ')';
							}
					margemTitulo = $scope.textoTitulo.length * -6;
					$scope.graficoLinhas.yAxis[0].setTitle({text: $scope.textoTitulo, margin: margemTitulo});
				}
			});
		});
	};

	$scope.carregarGraficoHistorico = function(idRegiao, dataAlterada){
		if(angular.isUndefined($scope.regiaoRealcada) || $scope.regiaoRealcada.codigo == null) {
			$scope.carregarGraficoHistoricoTotal();
			console.warn("ERRO");
			return;
		}
		$scope.verMemoria(true); // OCULTA TABELA DE VALORES
		dataHistorica = [];
		dataHistorica['original'] = [];
		dataHistorica['formatada'] = [];
		angular.forEach($scope.indicador.datas.slice().reverse(), function(valor,chave){
			if(valor >= $scope.selecao.dataMin && valor <= $scope.selecao.dataMax){
				this['original'].push(valor);
				trimestre = new Date(valor).getMonth();
				// this['formatada'].push($filter('date')(valor, ($scope.indicador.periodicidade == 'mensal') ? 'MMM yyyy' : (($scope.indicador.periodicidade == 'trimestral') ? 'MM/yyyy' : 'yyyy')));
				dataHistorica['formatada'].push($filter('date')(valor, ($scope.indicador.periodicidade == 'mensal') ? 'MMM yyyy' : (($scope.indicador.periodicidade == 'trimestral') ? 'MM/yyyy' : 'yyyy')));
			}
			else if(!dataAlterada) {
				this['original'].push(valor);
				trimestre = Math.floor((new Date(valor).getMonth() + 3) / 3);
				trimestre = new Date(valor).getMonth()
				
				dataHistorica['formatada'].push($filter('date')(valor, ($scope.indicador.periodicidade == 'mensal') ? 'MMM yyyy' : (($scope.indicador.periodicidade == 'trimestral') ? 'MM/yyyy' : 'yyyy')));
			}
		},dataHistorica);
		
		if(angular.isUndefined($scope.selecao.dataMin)){
			$scope.selecao.dataMin = $scope.indicador.datas[$scope.indicador.datas.length - 1];
		}
		
		if(angular.isUndefined($scope.selecao.dataMax)){
			$scope.selecao.dataMax = $scope.indicador.datas[0] == null ? $scope.indicador.datas[1] : $scope.indicador.datas[0];
		}

		IndicadorHistorico.get({
			id:$scope.selecao.idIndicSel,
			territorio:$scope.selecao.idTerrSel,
			regiao:idRegiao,
			dataMinima:$scope.selecao.dataMin,
			dataMaxima:$scope.selecao.dataMax
		},function(indicadorHistorico){
			indicadorHistorico.series = $filter('orderBy')(indicadorHistorico.series, 'name');
			
			$scope.carregarGraficoLinhas = indicadorHistorico.series ? indicadorHistorico.series.length > 0 : false;

			if(!$scope.carregarGraficoLinhas){
				$scope.carregandoHistorico = 'Não há dados históricos disponíveis para essa seleção!';
			} else {
				$scope.carregandoHistorico = null;
			}
			// APLICA MESMO PADRÃO DE MARCADOR A TODOS OS ITENS DO GRÁFICO DE LINHA
			for (item in indicadorHistorico.series) {
				indicadorHistorico.series[item].marker = { symbol: "circle" }
			}
			
			larguraGraficoLinha = document.getElementById("divGraficoLinha").clientWidth;
			let ultimoItemVarFiltro = "";
			
			subtitulo = "Unidade territorial de análise: " +  $scope.regiaoRealcada.nome + " <br> Período: " + $filter('date')($scope.indicador.datas[$scope.indicador.datas.length-1], $scope.indicador.periodicidade == 'anual' ? 'yyyy' : 'MMMM yyyy') + " a " + $filter('date')($scope.indicador.datas[0], $scope.indicador.periodicidade == 'anual' ? 'yyyy' : 'MMMM yyyy');
			$scope.graficoLinhas = Highcharts.chart('graficoLinhas', {
				chart: {
					type: 'line',
					marginTop: 25,
					width:larguraGraficoLinha
			        },
			    colors: app.defaultColors,
		        xAxis: {
					type: "category",
					crosshair: true,
					categories: dataHistorica['formatada']
				}, 
				series: indicadorHistorico.series,
				tooltip: {
					formatter: function(){
						nomeRegiao = $scope.regiaoRealcada.nome;
						let listaDeDatas = $scope.indicador.datas.slice().reverse()
						let dataInicial = $scope.selecao.dataMin
						let indiceDataInicial = listaDeDatas.indexOf(dataInicial)
						
						textoTooltip = (this.series.chart.series.length > 1 ? '<b>' + nomeRegiao + '</b> <br>' : '');
						
						textoTooltip = textoTooltip + '<b>' + this.series.name + ':</b> ' + Highcharts.numberFormat(this.y, this.y % 1 == 0 ? 0 : this.y < 100 ? 2 : this.y < 1000 ? 1 : 0,',','.') + ' ' + $scope.indicador.simbolo_valor + '<br>';
						
						if(this.series.chart.series.length > 1){
							varFiltro =	$scope.variavelHistorico.filter(
								(variavel) => (variavel.data == listaDeDatas[this.point.x + indiceDataInicial] || variavel.data == null) &&
								(variavel.id_regiao == ($scope.selecao.idTerrSel != 4? $scope.regiaoRealcada.codigo : 1)|| variavel.distribuicao == true)&& 
								(variavel.dimensao === this.series.name || (variavel.distribuicao == true && variavel.dimensao == null))
								);
						}else if (!angular.isUndefined($scope.variavelHistorico)){
							varFiltro =	$scope.variavelHistorico.filter((variavel) => (variavel.data == listaDeDatas[this.point.x + indiceDataInicial] || variavel.data == null) && (variavel.id_regiao == ($scope.selecao.idTerrSel != 4? $scope.regiaoRealcada.codigo : 1)|| variavel.distribuicao == true));
						}
						varFiltroSemDataSemDimensao = $scope.variavelHistorico.filter(
							(variavel) => variavel.data == null && 
							(variavel.id_regiao == $scope.regiaoRealcada.codigo || variavel.distribuicao == true) && 
							variavel.dimensao == null
							);
						varFiltro = varFiltro.concat(varFiltroSemDataSemDimensao);
						// Reduzido numero de itens no array varFiltro para que o indicador mostre o numerador
						if(varFiltro.length >= 0){
							let ultimoValor = ''; // REGISTRA O VALOR PARA COMPARAR E EVITAR DUPLICATAS
							angular.forEach(varFiltro, function(val,chave){
								if(ultimoValor !== val.valor) {
									textoTooltip = textoTooltip + ' ' + val.nome + ': ' + Highcharts.numberFormat(val.valor, val.valor % 1 == 0 ? 0 : this.y < 100 ? 2 : val.valor < 1000 ? 1 : 0,',','.') + ' ' + (val.tipo_valor ? val.tipo_valor : '') + '<br>'; 
									ultimoValor = val.valor;
								}
							});
						}
						return textoTooltip;
					}
				},
				title: null,
				credits:false,
				
				exporting: {
					enabled:true
					,chartOptions:{
						chart: {
							marginBottom: 160,
							marginTop: 130,
							height: 700,
							spacingLeft: 30,
							spacingRight: 30,
							spacingBottom: 10,
							spacingTop: 30
						},
						title:{
							text:$scope.indicador.nome
						},
						credits:{
							enabled: true,
							
							text: "Fórmula de cálculo: <br> " + $scope.indicador.formula_calculo + "  <br> _____________________________________________________________________ <br> Atualizado até: "  + $filter('date')($scope.indicador.data_atualizacao, 'MMMM yyyy') + "<br>Fonte:" + $scope.indicador.origem,
							style:{
								fontSize: '8px'
								,fontWeight: 'normal'
								,color: '#000000'
							},
							position:{
								y:-55
								,x: 20
								,align: 'left'
							}
						},
						xAxis: {
							labels:{
								padding:10
							}
						},
						subtitle:{
							text:subtitulo
							,align:'left'
							,x: -5
						},
						legend: {
							layout: 'vertical',
							align: 'left',
							floating: true,
							x: 0,
							verticalAlign: 'bottom',
							y:-55,
							itemStyle: {
								fontWeight: 'normal',
								fontSize: '8px'
							}
						}
						,style:{
							fontFamily: 'museo_slab500'
						}
					}
					,buttons: {
						contextButton: {
							menuItems: [{
								text: 'Exportar para PDF',
								onclick: function(){
									$scope.exportarGrafico(this,'application/pdf');
								}
							}, {
								text: 'Exportar para PNG',
								onclick: function(){
									$scope.exportarGrafico(this,'image/png')
								},
								separator: false
							}, {
								text: 'Exportar para JPEG',
								onclick: function(){
									$scope.exportarGrafico(this,'image/jpeg')
								},
								separator: false
							},{
								//#24 Exportar para SVG
								text: 'Exportar para SVG',
								onclick: function(){
									$scope.exportarGrafico(this,'image/svg+xml')
								},
								separator: false
							}]
						}
					}
				},
				yAxis: {
					labels:{
						formatter: function(){
							result = this.value;
							if (this.chart.yAxis[0].max >= 1000000000) {
								if(this.chart.yAxis[0].max/ 1000000000.0 <= 8)
									result = Math.round((this.value / 1000000000.0) * 10.0)/10.0;
								else
									result = Math.round(this.value / 1000000000.0);
							}
							else 
									if (this.chart.yAxis[0].max >= 1000000) { 
										if(this.chart.yAxis[0].max / 1000000.0 <= 8)
											result = Math.round((this.value / 1000000.0) * 10.0)/10.0;
										else
											result = Math.round(this.value / 1000000.0);
									}else 
										if (this.chart.yAxis[0].max >= 1000) {
											if(this.chart.yAxis[0].max / 1000.0 <= 8)
												result = Math.round((this.value / 1000.0) * 10.0)/10.0;
											else
												result = Math.round(this.value / 1000.0);
										}
							return result;
						}
					},
					title: {
						align:'high',
						rotation:0,
						y:-15
					}
				},
				plotOptions: {
					series: {
						point: {
							events: {
								click: function(e){
									if($scope.selecao.idTerrSel != 4){
										$scope.selecao.dataSel = dataHistorica['original'][this.index];
										$scope.clickMapa = false;
										$scope.idPoligonoAnterior = 0;
										$scope.cargaIndicadorValores(false,true);
									}
								}
							}
						}
						
					}
				},
				legend: {
					align: 'left',
					enabled: serieHistorica.length > 1,
					layout: 'horizontal',
					itemStyle:{
						fontWeight:'normal'
					}
				}
			});

			if ($scope.graficoLinhas.yAxis[0].max >= 1000000000){
				$scope.textoTitulo = $scope.indicador.tipo_valor + ' (Em bilhões de ' + $scope.indicador.simbolo_valor + ')';
				
			}else 
				if ($scope.graficoLinhas.yAxis[0].max >= 1000000){
					$scope.textoTitulo = $scope.indicador.tipo_valor + ' (Em milhões de ' + $scope.indicador.simbolo_valor + ')';
				}else 
					if ($scope.graficoLinhas.yAxis[0].max >= 1000) {
						$scope.textoTitulo = $scope.indicador.tipo_valor + ' (Em milhares de ' + $scope.indicador.simbolo_valor + ')';
					}
					else {
						$scope.textoTitulo = $scope.indicador.tipo_valor + ' (' + $scope.indicador.simbolo_valor + ')';
					}
			margemTitulo = $scope.textoTitulo.length * -6;
			$scope.graficoLinhas.yAxis[0].setTitle({text: $scope.textoTitulo, margin: margemTitulo});
		});
	};

	// Issue 10
	$scope.mostrarGrafico = function(tipoGrafico){
		// Verifica tipo de gráfico e retorna 'true' se tiver que ser exibido
		let priorizaBarras = !$scope.hoverMapa;
		$scope.tipoGraficoSelecionado

		switch (tipoGrafico) {
			case 'area':
				if(!priorizaBarras){
					return true
				}
				else { return false }
			case 'barras':
				if(priorizaBarras){
					$scope.tipoGraficoSelecionado = 'colunas';
					return true
				}
				else { return false }
			case 'colunas':
				if(true){
					return true
				}
				else { return false }
			case 'linhas':
				// if($scope.hoverMapa && $scope.carregarGraficoLinhas){
				if(!priorizaBarras){
					$scope.tipoGraficoSelecionado = 'linhas';
					return true
				}
				else { return false }
			case 'pizza':
				if(!priorizaBarras){
					return true
				}
				else { return false }
			default:
				return false;
		}
	}
	/**
		GRÁFICOS HIGHCHARTS
	*/
	$scope.objGrafico = function(grafBarSub, grafBarLeg, tipoChart = 'line', indicador = $scope.indicador, indicadorValores = $scope.indicadorValores) {
		let highchartObj = {
			chart: {
				type: tipoChart,
				marginTop: 35,
				width: document.getElementById("divGraficoLinha").clientWidth
			},
			colors: app.defaultColors,
			title: {
				text: null
			},
			xAxis: {
				type: "category",
				crosshair: true,
				categories: indicadorValores.categorias,
				labels: labelProps
			},
			yAxis: {
				labels:{
					formatter: function(){
						result = this.value;
						if (this.chart.yAxis[0].max >= 1000000000) {
							if(this.chart.yAxis[0].max / 1000000000.0 <= 8)
								result = Math.round((this.value / 1000000000.0) * 10.0)/10.0;
							else
								result = Math.round(this.value / 1000000000.0);
						}
						else 
								if (this.chart.yAxis[0].max >= 1000000) { 
									if(this.chart.yAxis[0].max / 1000000.0 <= 8)
										result = Math.round((this.value / 1000000.0) * 10.0)/10.0;
									else
										result = Math.round(this.value / 1000000.0);
								}else 
									if (this.chart.yAxis[0].max >= 1000) {
										if(this.chart.yAxis[0].max / 1000.0 <= 8)
											result = Math.round((this.value / 1000.0) * 10.0)/10.0;
										else
											result = Math.round(this.value / 1000.0);
									}
						return result;
					}
				},
				title: {
					align:'high',
					rotation:0,
					y:-15
				}
			},
			exporting: {
				chartOptions:{
					chart: exportChartOpts,
					colors: app.defaultColors,
					title:{ text: $scope.indicador.nome },
					xAxis: {
						labels: labelProps
					},
					credits:{
						enabled: true,
						text: "Fórmula de cálculo: <br> " + indicador.formula_calculo + " <br> " +  "  " + ' <br> _____________________________________________________________________ <br>Atualizado até: '  + $filter('date')(indicador.data_atualizacao, 'MMMM yyyy') + "<br>Fonte:" + indicador.origem,
						style:{
							fontSize: '8px'
							,fontWeight: 'normal'
							,color: '#000000'
						},
						position:{
							x: 20
							,y:-55
							,align: 'left'
						}
					},
					subtitle: grafBarSub,
					legend: grafBarLeg,
					style:{
						fontFamily: 'museo_slab500'
					}
				}
				,buttons:{
					contextButton: {
						menuItems: [
						{text: 'Exportar para PDF', onclick: function(){$scope.exportarGrafico(this,'application/pdf');}},
						{text: 'Exportar para PNG', onclick: function(){$scope.exportarGrafico(this,'image/png');},separator: false},
						{text: 'Exportar para JPEG', onclick: function(){$scope.exportarGrafico(this,'image/jpeg');},separator: false},
						{text: 'Exportar para SVG', onclick: function(){$scope.exportarGrafico(this,'image/svg+xml');},separator: false}
						]
					}
				}
			},
			tooltip: {
				formatter: function(){
					textoTooltip = '';
					if(tipoChart === 'pie'){
						textoTooltip = '<b>' + this.key + '</b><br>';
					}
					else {
						textoTooltip = (this.series.name != 'Não categorizado' ? '<b>' + this.x  + '</b> <br>' : '');
					}
					textoTooltip = textoTooltip + '<b>' + (this.series.name == 'Não categorizado' ? this.x : this.series.name) + ': </b> ' + Highcharts.numberFormat(this.y, this.y % 1 == 0 ? 0 : this.y < 100 ? 2 : this.y < 1000 ? 1 : 0,',','.') + ' ' + indicador.simbolo_valor + '<br>';
					if(this.series.name == 'Não categorizado'){
						varFiltro =	$scope.variavelHistorico.filter((variavel) => (variavel.data == $scope.selecao.dataSel || variavel.data == null) && (variavel.id_regiao == indicadorValores.codigos[this.point.x] || variavel.distribuicao == true));
					}else{
						varFiltro =	$scope.variavelHistorico.filter((variavel) => (variavel.data == $scope.selecao.dataSel || variavel.data == null) && (variavel.id_regiao == indicadorValores.codigos[this.point.x] || variavel.distribuicao == true) && (variavel.dimensao == this.series.name || variavel.dimensao == null));
					}																
					if(varFiltro.length > 1){
						angular.forEach(varFiltro, function(val,chave){
							textoTooltip = textoTooltip + ' ' + val.nome + ': ' + Highcharts.numberFormat(val.valor, val.valor % 1 == 0 ? 0 : val.valor < 100 ? 2 : val.valor < 1000 ? 1 : 0,',','.') + ' ' + (val.tipo_valor ? val.tipo_valor : '') + '<br>'; 
						});
					}
					return textoTooltip;
				}
			},
			plotOptions: {
				column: {
					stacking: 'normal',
					borderWidth: 0
				},
				series: {
					point: {
						events: {
							click: function(e){
								if(tipoChart !== "pie")
									$scope.fixarMapa($scope.dadosMapa[this.x].codigo);
							}
						}
					}
				}
			},
			legend: {
				align: 'left',							
				enabled: !(indicadorValores.series.length === 1 && indicadorValores.series[0].name === "Não categorizado"),
				layout: 'horizontal',
				itemStyle:{
					fontWeight:'normal'
				}
				//verticalAlign: 'bottom',							
			},
			style:{ fontFamily: 'museo_slab500' },
			credits:false,
			series: indicadorValores.series
		}
		if(tipoChart === 'pie') {
			// Gráfico tipo pizza
			highchartObj.accessibility = {
				point: {
					valueSuffix: ''
				}
			}
			highchartObj.plotOptions.pie = {
				allowPointSelect: true
			}
			let pData = [];
			// Popula dados conforme series do indicadorValores
			for (var i = indicadorValores.series.length - 1; i >= 0; i--) {
				if(indicadorValores.series[i].data[0] > 0) {
					pData.push({
						name: indicadorValores.series[i].name,
						y: indicadorValores.series[i].data[0]
					})
				}
			}
			let pSeries = [{
				name: indicadorValores.nome,
				colorByPoint: true,
				data: pData
			}];
			highchartObj.series = pSeries;
		}
		return highchartObj;
	}
	
	// VERIFICA SE HA MUNICIPIO DENTRE OS TERRITORIOS DO INDICADOR
	$scope.semMunicipio = function(){
		for (var i = 0; i < $scope.indicador.territorios.length; i++) {
			if($scope.indicador.territorios[i].nome == "Município")
				return false;
		}
		return true;
	}
	
	$scope.cargaIndicadorValores = function(inserirMapa, inserirTerritorioMapa){
		// VERIFICA SE MAPA ESTA SENDO CARREGADO
		$scope.carregandoMapa = inserirTerritorioMapa ? 'Aguarde... carregando mapa' : null;
		// ATUALIZA INDICADOR ATUAL COM OS DADOS DO INDICADOR SOLICITADO
		$scope.indicador = $scope.indicadores.filter((indicador) => indicador.id_indicador == $scope.selecao.idIndicSel)[0];
		
		if($scope.idIndicadorAnterior != $scope.selecao.idIndicSel){
			$scope.selecao.dataSel = $scope.indicador.datas[0];
			$scope.idIndicadorAnterior = $scope.selecao.idIndicSel;
			// Verificar aqui para duplicados
			padrao_encontrado = false;
			angular.forEach($scope.indicador.territorios, function(territorio) {
				if(territorio && $scope.indicador.id_territorio_padrao == territorio.id_territorio){
				  padrao_encontrado = true;
				  $scope.selecao.idTerrSel = $scope.indicador.id_territorio_padrao;
			  }
			});
			if(!padrao_encontrado && $scope.indicador.territorios[0]){
				$scope.selecao.idTerrSel = $scope.indicador.territorios[0].id_territorio;
			}			
			$scope.selecao.idTerrSel = $scope.indicador.id_territorio_padrao;
		}
		if($scope.indicador.territorios[0])
			$scope.labelTerrSel = $scope.indicador.territorios.filter((territorio) => territorio.id_territorio == $scope.selecao.idTerrSel)[0].nome;
		
		$scope.hoverMapa = false;
		$scope.verMemoria(true); // OCULTA TABELA DE VALORES
		//$scope.clickMapa = false;		
		$scope.carregarGraficoLinhas = false;
		IndicadorValores.get({id:$scope.selecao.idIndicSel,data:$scope.selecao.dataSel,territorio:$scope.selecao.idTerrSel},function(indicadorValores){

			$scope.indicadorValores = indicadorValores;
			
			// ATUALIZA LISTA DE FILTRO POR CATEGORIAS
			$scope.selecao.categorias = [];
			angular.forEach(indicadorValores.series, function(valor, chave){
				$scope.selecao.categorias.push(valor);
			});
			
			// FILTRA CATEGORIAS
			/*
			if(($scope.selecao.categorias.length > 1) && (!$scope.regiaoRealcada || $scope.regiaoRealcada.codigo == null)){
				$scope.filtraCategoria();
				return;
			}
			*/

			$scope.selecao.dataMin = $scope.indicador.datas[$scope.indicador.datas.length - 1];
			$scope.selecao.dataMax = $scope.indicador.datas[0] == null ? $scope.indicador.datas[1] : $scope.indicador.datas[0];
			$scope.legenda = [];
			$scope.dadosMapa = [];
			$scope.idPoligonoAnterior = 0;
			
			Highcharts.setOptions({
				chart: {
					style: {
						fontFamily: 'museo_slab500'
					}
				},
				colors: app.defaultColors
			});

			VariavelHistorico.query({id:$scope.selecao.idIndicSel,territorio:$scope.selecao.idTerrSel},function(variavelHistorico){
				$scope.variavelHistorico = variavelHistorico;
				
				if(!angular.isUndefined($scope.indicadorValores.series) && $scope.indicadorValores.series.length == 1){
					$scope.indicadorValores.series[0].showInLegend = true;
				}
			
				mostrarDistritos = $scope.labelTerrSel == 'Distrito';
				trimestre = Math.floor((new Date($scope.selecao.dataSel).getMonth() + 3) / 3);
				$scope.indicadorValores.series = $filter('orderBy')($scope.indicadorValores.series, 'name');
				
				exportMargingBot = 140+($scope.indicadorValores.series.length*8);
				if($scope.labelTerrSel == "Subprefeitura" || mostrarDistritos)
					exportMargingBot += mostrarDistritos ? 40 : 60;

				$scope.grafBarSub = {
					margin: 50,
					text: 'Unidade territorial de análise: ' + $scope.labelTerrSel + " <br> Período: " + $filter('date')($scope.selecao.dataSel, ($scope.indicador.periodicidade == 'mensal') ? 'MMM yyyy' : (($scope.indicador.periodicidade == 'trimestral') ? 'MM/yyyy' : 'yyyy')),
					align: 'left',
					x: -5,
					style: {
						// paddingBottom: 120
					}
				};
				$scope.grafBarLeg = {
					layout: 'vertical',
					align: 'left',
					x: 0,
					verticalAlign: 'bottom',
					y: -55,
					itemStyle: {
						fontWeight: 'normal',
						fontSize: '8px'
					}
				};
				labelProps = {};
				// CONFIGURAÇÕES ESPECÍFICAS PARA QUE CAIBAM TODOS OS DISTRITOS
				if(mostrarDistritos){
					labelProps = {
						padding: 5,
						rotation: -90,
						step: 1,
						style: { fontSize: '8px' }
					};
				}
				else {
					labelProps = {
						padding: 8,
						style: { fontSize: '10px' }
					};
				}
				
				if(!$scope.semMunicipio() || $scope.selecao.categorias && $scope.selecao.categorias.length != 1){
					/*
					$scope.graficoBarras = new Highcharts.chart('graficoBarras',{
						chart: {
							type: 'column'
							,marginTop: 35
						},
						colors: app.defaultColors,
						title: {
							text: null
						},
						xAxis: {
							type: "category",
							crosshair: true,
							categories: $scope.indicadorValores.categorias,
							labels: labelProps
						},
						yAxis: {
							labels:{
								formatter: function(){
									result = this.value;
									if (this.chart.yAxis[0].max >= 1000000000) {
										if(this.chart.yAxis[0].max / 1000000000.0 <= 8)
											result = Math.round((this.value / 1000000000.0) * 10.0)/10.0;
										else
											result = Math.round(this.value / 1000000000.0);
									}
									else 
											if (this.chart.yAxis[0].max >= 1000000) { 
												if(this.chart.yAxis[0].max / 1000000.0 <= 8)
													result = Math.round((this.value / 1000000.0) * 10.0)/10.0;
												else
													result = Math.round(this.value / 1000000.0);
											}else 
												if (this.chart.yAxis[0].max >= 1000) {
													if(this.chart.yAxis[0].max / 1000.0 <= 8)
														result = Math.round((this.value / 1000.0) * 10.0)/10.0;
													else
														result = Math.round(this.value / 1000.0);
												}
									return result;
								}
							},
							title: {
								align:'high',
								rotation:0,
								y:-15
							}
						},
						exporting: {
							chartOptions:{
								chart: exportChartOpts,
								colors: app.defaultColors,
								title:{ text:$scope.indicador.nome },
								xAxis: {
									labels: labelProps
								},
								credits:{
									enabled: true,
									text: "Fórmula de cálculo: <br> " + $scope.indicador.formula_calculo + " <br> " +  "  " + ' <br> _____________________________________________________________________ <br>Atualizado até: '  + $filter('date')($scope.indicador.data_atualizacao, 'MMMM yyyy') + "<br>Fonte:" + $scope.indicador.origem,
									style:{
										fontSize: '8px'
										,fontWeight: 'normal'
										,color: '#000000'
									},
									position:{
										x: 20
										,y:-55
										,align: 'left'
									}
								},
								subtitle: $scope.grafBarSub,
								legend: $scope.grafBarLeg,
								style:{
									fontFamily: 'museo_slab500'
								}
							}
							,buttons:{
								contextButton: {
									menuItems: [
									{text: 'Exportar para PDF', onclick: function(){$scope.exportarGrafico(this,'application/pdf');}},
									{text: 'Exportar para PNG', onclick: function(){$scope.exportarGrafico(this,'image/png');},separator: false},
									{text: 'Exportar para JPEG', onclick: function(){$scope.exportarGrafico(this,'image/jpeg');},separator: false},
									{text: 'Exportar para SVG', onclick: function(){$scope.exportarGrafico(this,'image/svg+xml');},separator: false}
									]
								}
							}
						},
						tooltip: {
							formatter: function(){
								textoTooltip = (this.series.name != 'Não categorizado' ? '<b>' + this.x  + '</b> <br>' : '');
								textoTooltip = textoTooltip + '<b>' + (this.series.name == 'Não categorizado' ? this.x : this.series.name) + ': </b> ' + Highcharts.numberFormat(this.y, this.y % 1 == 0 ? 0 : this.y < 100 ? 2 : this.y < 1000 ? 1 : 0,',','.') + ' ' + $scope.indicador.simbolo_valor + '<br>';
								if(this.series.name == 'Não categorizado'){
									varFiltro =	$scope.variavelHistorico.filter((variavel) => (variavel.data == $scope.selecao.dataSel || variavel.data == null) && (variavel.id_regiao == $scope.indicadorValores.codigos[this.point.x] || variavel.distribuicao == true));
								}else{
									varFiltro =	$scope.variavelHistorico.filter((variavel) => (variavel.data == $scope.selecao.dataSel || variavel.data == null) && (variavel.id_regiao == $scope.indicadorValores.codigos[this.point.x] || variavel.distribuicao == true) && (variavel.dimensao == this.series.name || variavel.dimensao == null));
								}																
								if(varFiltro.length > 1){
									angular.forEach(varFiltro, function(val,chave){
										textoTooltip = textoTooltip + ' ' + val.nome + ': ' + Highcharts.numberFormat(val.valor, val.valor % 1 == 0 ? 0 : val.valor < 100 ? 2 : val.valor < 1000 ? 1 : 0,',','.') + ' ' + (val.tipo_valor ? val.tipo_valor : '') + '<br>'; 
									});
								}
								return textoTooltip;
							}
						},
						plotOptions: {
							column: {
								stacking: 'normal',
								borderWidth: 0
							},
							series: {
								point: {
									events: {
										click: function(e){
											$scope.fixarMapa($scope.dadosMapa[this.x].codigo);
										}
									}
								}
							}
						},
						legend: {
							align: 'left',							
							enabled: !($scope.indicadorValores.series.length === 1 && $scope.indicadorValores.series[0].name === "Não categorizado"),
							layout: 'horizontal',
							itemStyle:{
								fontWeight:'normal'
							}
							//verticalAlign: 'bottom',							
						},
						style:{ fontFamily: 'museo_slab500' },
						credits:false,
						series: $scope.indicadorValores.series
					});
					*/
					/**
						Atualiza gráficos relacionados ao gráfico de barras
						Para os gráficos de barras, as opções alternativas seriam:
							Gráfico de colunas; Gráfico de barra (ano a ano)
					*/
					$scope.graficoBarras = new Highcharts.chart('graficoBarras',$scope.objGrafico($scope.grafBarSub, $scope.grafBarLeg, 'bar', $scope.indicador, $scope.indicadorValores));
					$scope.graficoColunas = new Highcharts.chart('graficoColunas',$scope.objGrafico($scope.grafBarSub, $scope.grafBarLeg, 'column', $scope.indicador, $scope.indicadorValores));
					$scope.tipoGraficoSelecionado = $scope.mostrarGrafico('linhas') ? 'linhas' : 'colunas';
					window.setTimeout(function(){$scope.$apply()}, 100);
					
					if ($scope.graficoBarras.yAxis[0].max >= 1000000000){
						$scope.textoTitulo = $scope.indicador.tipo_valor + ' (Em bilhões de ' + $scope.indicador.simbolo_valor + ')';
					}
					else {
						if ($scope.graficoBarras.yAxis[0].max >= 1000000){
							$scope.textoTitulo = $scope.indicador.tipo_valor + ' (Em milhões de ' + $scope.indicador.simbolo_valor + ')';
							
						}
						else 
							if ($scope.graficoBarras.yAxis[0].max >= 1000) {
								$scope.textoTitulo = $scope.indicador.tipo_valor + ' (Em milhares de ' + $scope.indicador.simbolo_valor + ')';							
							}
							else{
								$scope.textoTitulo = $scope.indicador.tipo_valor + ' (' + $scope.indicador.simbolo_valor + ')';
							}
						margemTitulo = $scope.textoTitulo.length * -6;
						$scope.graficoBarras.yAxis[0].setTitle({text: $scope.textoTitulo, margin: margemTitulo});
					}
				}
								
				// CARREGA MAPA DO INDICADOR
				if(inserirTerritorioMapa){
					$scope.layerVetor = new ol.layer.Vector({
						source: new ol.source.Vector({
							loader: function (extent) {
								$http.jsonp('<?php echo bloginfo("url"); ?>/geoserver/Monitoramento_PDE/ows', {
									params: {
										service: 'WFS',
										version: '1.1.0',
										request: 'GetFeature',
										typename: 'Monitoramento_PDE:' + $scope.labelTerrSel,
										outputFormat: 'text/javascript',
										format_options: 'callback: JSON_CALLBACK',
										bbox: extent.join(',') + ',EPSG:3857'
									}
									}).then(function(response){
										$scope.carregarVetor(response);
										
									}).catch( function (response) {
										$scope.carregandoMapa = 'Erro no carregamento do mapa' + response;
									})
							},
							strategy: ol.loadingstrategy.bbox
						})
					});	
					$scope.mapa.addLayer($scope.layerContorno);
					$scope.mapa.addLayer($scope.layerVetor);
				}
				// FIM
				if($scope.selecao.categorias.length == 1 && $scope.semMunicipio()) {
					$scope.selecao.categoria = $scope.selecao.categorias[0];
					$scope.filtraCategoria();
				}
				
			});

		});

		
		if(inserirMapa){
			// CORRIGE FALHA DE EXIBICAO 
			let mapCanvasDivs = document.getElementsByClassName('fMap');
			// let canvasDivs = document.getElementsByClassName('ol-viewport');
			if(mapCanvasDivs.length > 1){
				// IDENTIFICA SE O ELEMENTO ESTÁ NA DIV DE ACCORDION QUE ESTÁ SENDO FECHADA (COLLAPSING)
				if(mapCanvasDivs[0].parentElement.parentElement.parentElement.parentElement.parentElement.parentElement.classList.contains("in"))
					mapCanvasDivs[0].remove();
				else
					mapCanvasDivs[1].remove();				
			}
			$scope.mapa = new ol.Map({
				target: 'map',
				view: new ol.View({
					center: ol.proj.transform([-46.6, -23.68], 'EPSG:4326','EPSG:3857'),
					zoom: 9.5
				}),
				interactions: ol.interaction.defaults({
					mouseWheelZoom:false,
					doubleClickZoom:false,
					altShiftDragRotate:false,
					keyboard:false,
					shiftDragZoom:false,
					dragPan:false,
					pinchRotate:false,
					pinchZoom:false
				}),
				controls: ol.control.defaults({
							zoom: false,
							attribution: false,
							rotate: false
				}).extend([
					//new ol.control.ScaleLine()
				]),
				layers: [
					/*new ol.layer.Tile({
						name: 'OSM',
						source: new ol.source.OSM()
					})*/
				]
			});
			
			$scope.mapa.on("click", function(evt) {
				if (evt.dragging) {
					return;
				}
				if($scope.mapa.hasFeatureAtPixel(evt.pixel)){
				
					poligonoRealcado = $scope.mapa.forEachFeatureAtPixel(evt.pixel, function(poligonoRealcado, layer) {
						return poligonoRealcado;
					});
					idPoligonoClick = poligonoRealcado.get('ID_REGIAO');
				
					if($scope.regiaoRealcada.codigo == idPoligonoClick && $scope.clickMapa == true){
						// $scope.clickMapa = false;
					}else{
						
						$scope.fixarMapa(idPoligonoClick);
					};
				};
			});
			$scope.mapa.on('pointermove', function(evt) {
				if (evt.dragging) {
					return;
				}
				var pixel = $scope.mapa.getEventPixel(evt.originalEvent);
				
				if($scope.selecao.idTerrSel != 4){
					$scope.realcarPorMapa(pixel);
				};
			});
		};
		
		function sairMapa(){
			if($scope.hoverMapa){
				$scope.hoverMapa = false;
				if($scope.regiaoRealcada)
					$scope.regiaoRealcada.codigo = null;
				if($scope.selecao.categoria)
					$scope.selecao.categoria = null;
				$scope.layerVetor.getSource().forEachFeature(function(poligono){
					poligono.setStyle(null);
				});
				
				$scope.layerVetor.setStyle($scope.estiloVetor);
				$scope.layerVetor.getSource().changed();
			};
			
		};
		
		$scope.realcarPorMapa = function(pixel) {
			if($scope.mapa.hasFeatureAtPixel(pixel)){
				poligonoRealcado = $scope.mapa.forEachFeatureAtPixel(pixel, function(poligonoRealcado, layer) {
					return poligonoRealcado;
				});

				idPoligonoAtual = poligonoRealcado.get('ID_REGIAO');
				
				if(!$scope.clickMapa){ $scope.realcarMapa(idPoligonoAtual); };
			}else{
				if(!($scope.selecao.categorias && $scope.selecao.categorias.length == 1) && !$scope.clickMapa){ sairMapa(); };				
			};
		};

		$scope.mostrarCategoria = function() {
			if($scope.selecao.categorias && $scope.selecao.categorias.length == 1)
				return false;
			for (var i = 0; i < $scope.indicador.territorios.length; i++) {
				if($scope.indicador.territorios[i].nome == "Município")
					return false;
			}
			return true;
		}

		// Filtra categoria e gera gráfico em série histórica
		$scope.filtraCategoria = function(){
			// $scope.regiaoRealcada = null;
			if($scope.selecao.categoria == null) {
				sairMapa();
				return;
			}
			$scope.hoverMapa = true;
			$scope.carregarGraficoHistoricoTotal();
		}
		
		/* FOR suprimido por nao apresentar alteracoes no funcionamento e apresentar erro
		if($scope.mapa) {
			$scope.mapa.getLayers().forEach(function(layer, key){
				if(layer.get('name') != 'OSM'){
					$scope.mapa.removeLayer(layer);
				}
			});
		}
		*/
		
		$scope.layerContorno = new ol.layer.Tile({
			source: new ol.source.TileWMS({
				url: '<?php echo bloginfo("url"); ?>/geoserver/Monitoramento_PDE/wms/reflect', 
				params: {layers: 'Monitoramento_PDE:Município', tiled: true},
				serverType: 'geoserver'
			})
		});
		
		$scope.ajustarEscalaValor = function(valorMaximoLegenda,valor){			
			if (valorMaximoLegenda >= 1000000000) {
				if(valorMaximoLegenda/ 1000000000.0 <= 8)
					valor = Math.round((valor / 1000000000.0) * 10.0)/10.0;
				else
					valor = Math.round(valor / 1000000000.0);
			}
			else 
				if (valorMaximoLegenda >= 1000000) { 
					if(valorMaximoLegenda / 1000000.0 <= 8)
						valor = Math.round((valor / 1000000.0) * 10.0)/10.0;
					else
						valor = Math.round(valor / 1000000.0);
				}
				else 
					if (valorMaximoLegenda >= 1000) {
						if(valorMaximoLegenda / 1000.0 <= 8)
							valor = Math.round((valor / 1000.0) * 10.0)/10.0;
						else
							valor = Math.round(valor / 1000.0);
					}
			return valor;
		}

		$scope.exportarGrafico = function(grafico,formatoArquivo){
			if($scope.hoverMapa)
			{
				grafico.exportChart({
					type: formatoArquivo
					,filename: $scope.indicador.nome + '_' +$scope.selecao.dataMin + '_a_' + $scope.selecao.dataMax
					//850
					,sourceWidth:1200
				},{
					chart:{
						backgroundColor: '#FFFFFF'
						,marginTop: 145
					}
				});
			}else
			{
				grafico.exportChart({
					type: formatoArquivo
					,filename: $scope.indicador.nome + '_' +$scope.selecao.dataSel
					,sourceWidth:1200
				},{
					chart:{
						backgroundColor: '#FFFFFF'
						,marginTop: 145
					}
				});
			}
		};
		
		$scope.carregarVetor = function(resposta){
			format = new ol.format.GeoJSON(),
			$scope.layerVetor.getSource().addFeatures(format.readFeatures(resposta.data));
			angular.forEach($scope.indicadorValores.series, function(serie, indiceSerie){
				// Acrescenta valores dos dados do indicador aos dados do mapa, para que as densidades sejam corretamente apresentadas
				angular.forEach(serie.data, function(dado, indiceDado){
					//Na primeira série, cria o objeto de dados					
					if(indiceSerie == 0){
						let novoDado = {codigo: $scope.indicadorValores.codigos[indiceDado], valor: dado, nome: $scope.indicadorValores.categorias[indiceDado]};
						// Verificar por insercoes duplicadas antes de inserir novo valor
						if($scope.dadosMapa.length > 1){
							let elementoDuplicado = false;
							$scope.dadosMapa.forEach(function(elemento){
								if(!elementoDuplicado && elemento.codigo == $scope.indicadorValores.codigos[indiceDado]){
									elementoDuplicado = true;
								}
							});
							if(!elementoDuplicado)
								$scope.dadosMapa.push(novoDado);
						}
						else
							$scope.dadosMapa.push(novoDado);
					}else{
						$scope.dadosMapa[indiceDado].valor = $scope.dadosMapa[indiceDado].valor + dado;
					}
				});
			});
			
			$scope.dadosMapa = $filter('orderBy')($scope.dadosMapa, 'valor', true);
			angular.forEach($scope.dadosMapa, function(value, key){
				value.posicao = key;
			});
			if($scope.selecao.idTerrSel == 3){
				$scope.dadosMapa = $filter('orderBy')($scope.dadosMapa, 'codigo', false);
			}else{
				$scope.dadosMapa = $filter('orderBy')($scope.dadosMapa, 'nome', false);
			}
			$scope.layerVetor.setStyle($scope.estiloVetor);
			$scope.carregandoMapa = null;
			
		};
		
		$scope.realcarClasse = function(classe){
			if(!$scope.clickMapa){
				$scope.layerVetor.setStyle($scope.estiloVetor);
				$scope.layerVetor.getSource().changed();
				
				$scope.layerVetor.getSource().forEachFeature(function(poligono){
					apagarFeature = true;
					angular.forEach(classe.regioes, function(regiao,chave){
						if(poligono.get('ID_REGIAO') == regiao)
							apagarFeature = false;
					});
					if(apagarFeature){
						poligono.setStyle($scope.estiloRealce);
						poligono.changed();
					}else{
						poligono.setStyle(null);
					};
					
				});
			}
		};
			
		$scope.deixarClasse = function(){
			if(!$scope.clickMapa){
				$scope.layerVetor.getSource().forEachFeature(function(poligono){
					poligono.setStyle(null);
				});
				$scope.layerVetor.setStyle($scope.estiloVetor);
				$scope.layerVetor.getSource().changed();
				$scope.filtraCategoria();
			}
		};
		
		if($scope.selecao.idTerrSel == 4){
			$scope.fixarMapa(1);
			return;
		}
	};
	
	$scope.cargaEstrategia = function(id){
		optEstrategiaSup = id;
		GrupoIndicador.get({id:id,tipo:'estrategia',tipo_retorno:'object'},function(estrategia){
			$scope.estrategia = estrategia.propriedades;
			$scope.estrategia.nome = estrategia.nome;
			$scope.estrategia.id_grupo_indicador = estrategia.id_grupo_indicador;
		});
		
		$scope.cargaCadastroIndicadores(id);
	};
	
	$scope.atualizaFicha = function(idGrupo, isObjetivo){
		if(idGrupo == null)
			return;
		// if(isObjetivo) {
			
		// }
		tipoGrupo = isObjetivo ? 'objetivo' : 'instrumento';
		// else {
			GrupoIndicador.get({id:idGrupo,tipo:tipoGrupo,tipo_retorno:'object'},function(fichaInstrumento){
				$scope.fichaInstrumento = fichaInstrumento;
				$scope.indicador = {id_instrumento: idGrupo, instrumento: fichaInstrumento.nome};
				if(fichaInstrumento.propriedades["Definição"]){
					$scope.descricaoGrupoIndicador = fichaInstrumento.propriedades["Definição"].substr(0,300);
					$scope.obterCamadas(fichaInstrumento.id_grupo_indicador)
					$scope.verMais = true;
				}
				else{
					$scope.descricaoGrupoIndicador = 'Não há definição para este '+tipoGrupo+'.';
					$scope.verMais = false;
				}
			});
		// }
	};

	$scope.abrirModal = function(tipo){
		if(tipo=='instrumento')
		{
			$rootScope.modalFichaInstrumento = $uibModal.open({
				ariaLabelledBy: 'modal-titulo-ficha-instrumento',
				ariaDescribedBy: 'modal-corpo-ficha-instrumento',
				templateUrl: 'ModalFichaInstrumento.html',
				controller: function($scope){},
				scope:$scope,
				size: 'lg'
			});
			GrupoIndicador.get({id:$scope.indicador.id_instrumento,tipo:'instrumento',tipo_retorno:'object'},function(fichaInstrumento){
				
			 $scope.fichaInstrumento = fichaInstrumento;
			 
			});
		}
		else if(tipo=='objetivo')
		{
			$rootScope.modalFichaInstrumento = $uibModal.open({
				ariaLabelledBy: 'modal-titulo-ficha-instrumento',
				ariaDescribedBy: 'modal-corpo-ficha-instrumento',
				templateUrl: 'ModalFichaInstrumento.html',
				controller: function($scope){},
				scope:$scope,
				size: 'lg'
			});
			GrupoIndicador.get({id:$scope.indicador.id_instrumento,tipo:'objetivo',tipo_retorno:'object'},function(fichaInstrumento){
				$scope.fichaInstrumento = fichaInstrumento;
			});
		}
		else
		{
			if(tipo=='indicador')
			{
				$rootScope.modalFichaIndicador = $uibModal.open({
					// animation: false,
					ariaLabelledBy: 'modal-titulo-ficha-indicador',
					ariaDescribedBy: 'modal-corpo-ficha-indicador',
					templateUrl: 'ModalFichaIndicador.html',
					controller: function($scope){},
					scope:$scope,
					size: 'lg'
				});
			}else
			{
				if(tipo=='estrategia')
				{
					$rootScope.modalFichaEstrategia = $uibModal.open({
						// animation: false,
						ariaLabelledBy: 'modal-titulo-ficha-estrategia',
						ariaDescribedBy: 'modal-corpo-ficha-estrategia',
						templateUrl: 'ModalFichaEstrategia.html',
						controller: function($scope){},
						scope:$scope,
						size: 'lg'
					});
				}
			};
		};
	};
	$scope.fecharModal = function(tipo){
		if(tipo=='instrumento'){
			$rootScope.modalFichaInstrumento.close();
		}else{
			if(tipo=='indicador'){
				$rootScope.modalFichaIndicador.close();
			}else{
				if(tipo=='estrategia'){
					$rootScope.modalFichaEstrategia.close();
				}
			}
		}
	};

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
						radius: $scope.raio ? $scope.raio : 4,
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
			kmlLayer.ocultar_info = index.style.ocultar_info;
			$scope.mapLayers.push(kmlLayer);			
		}
	};

	$scope.rgbaToHex = function(rgbaString) {
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

	$scope.obterCamadas = function(idInstrumento){
		Camadas.query({id_grupo_indicador:idInstrumento}, function(retorno){
			if(retorno.length > 0) {
				$scope.camadasInstrumento = [];
				// Adiciona itens obtidos ao array camadasInstrumento
				for (var i = retorno.length - 1; i >= 0; i--) {
					$scope.camadasInstrumento.push(retorno[i]);
				}
				
				for (var i = $scope.camadasInstrumento.length - 1; i >= 0; i--) {
					$scope.camadasInstrumento[i].parametros_estilo = JSON.parse($scope.camadasInstrumento[i].parametros_estilo);
				}
				$scope.renderizarMapa();
			}
			else {
				console.error("Nenhuma camada obtida", retorno);
				$scope.camadasInstrumento = [];
				$scope.renderizarMapa();
			}
		},
		function(erro){
			console.error(erro)
		});
	};

	$scope.renderizarMapa = function() {
		$scope.map = $scope.mapaInstrumento;
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
				console.log("SEM PARAMETROS ESTILO", camada);
				camada.parametros_estilo = {
					style_from_kml: true,
					fill_color: "rgba(255,255,255, 0.5)",
					stroke_color: "rgba(0, 0, 0, 0.9)"
				}
			}

			camada.style = camada.style ? camada.style : camada.parametros_estilo;
			customLayers.push(camada);
		}
		// Ordena camadas conforme propriedade 'ordem'
		customLayers.sort(function(a,b){return a.ordem-b.ordem});
		// FIM ITERAÇÃO DE CAMADAS
		if($scope.map.getLayers().getLength() === 1)
			$scope.addLayers([contornoSP]);
		
		$scope.addLayers(customLayers);
		

		var cLayers = $scope.map.getLayers();
		for(layer in cLayers) {
			$scope.map.removeLayer(layer);
		}

		for(layer in $scope.mapLayers){
			$scope.map.addLayer($scope.mapLayers[layer]);
		}
		var mapLayersSource = $scope.mapLayers[1].getSource();
		/*
		window.setTimeout(function(){
			var extent = ol.extent.createEmpty();
			$scope.map.getLayers().forEach(function(layer) {
				// Ajusta zoom e centraliza mapa
				if(layer.getSource().getExtent !== undefined){
		  		ol.extent.extend(extent, layer.getSource().getExtent());
		  	}
			});
			$scope.map.getView().fit(extent, $scope.map.getSize());
		}, 2000);
		*/
	};

	$scope.ocultarMapaIndicador = true;
	$scope.mostrarMapa = false;

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

	$scope.atualizarStatusMapa = function(opcao) {
		$scope.mostrarMapa = typeof opcao === "number" ? opcao >= 0 : false;
		switch ($scope.tabAtivaForma) {
			case 2:
				optInstrumentoSup = opcao;
				break;
			case 3:
				optObjetivoSup = opcao;
		}
	};

	$scope.loadMap = function() {
		console.log("function loadMap");

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
				url: mbLight,
				crossOrigin: 'anonymous',
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
		
		$rootScope.mapaInstrumento = new ol.Map({
			target: 'map-instrumento',
			layers: $rootScope.mapLayers,
			view: new ol.View({
				// center: [-5191207.638373509,-2698731.105121977],
				center: [-5190000,-2715000],
				zoom: 9.75,
				maxZoom: 20
			})
		});
		instruMap = $rootScope.mapaInstrumento;
		console.log("Mapa carregado");
		console.log(instruMap);
		
		const white = [255, 255, 255, 0.7];
		const blue = [80, 80, 80, 1];
		const width = 4;

		var highlightStyle = new ol.style.Style({
		  fill: new ol.style.Fill({
		    // color: 'rgba(255,255,255,0.7)'
		    color: white
		  }),
		  stroke: new ol.style.Stroke({
		    color: blue,
		    width: 3
		  }),
		  image: new ol.style.Circle({
       radius: width * 2,
       fill: new ol.style.Fill({
         color: white
       }),
       stroke: new ol.style.Stroke({
         color: blue,
         width: width / 2
       })
     })
		});

		mapWatcher(highlightStyle);

		// var selecionado = null;
		// variavel foi elevada para solucionar problema de highlight (highlight não apagava após desselecionar feature)
		var ultimoEstilo = null;
		featureInfo = document.getElementById('feature-info');
		/*
		$rootScope.mapaInstrumento.on('pointermove', function (e) {
			if (selecionado !== null) {
		    selecionado.setStyle(undefined);
		    selecionado = null;
		  }
		  var isLit = false;

		  async function percorreFeatures(pixel){
	  	  $rootScope.mapaInstrumento.forEachFeatureAtPixel(pixel, function (f) {
	  	  	if (f.get('limite_id') !== "27"){	  	  		
	  	  		counter++;
	  	  		console.log(counter);
	  		    selecionado = f;
	  		    f.setStyle(highlightStyle);
	  		    isLit = true;
	  	  		// console.log(f.getProperties());	  	  		
	  		    return true;
	  		  }
	  	  });
		  }
		  percorreFeatures(e.pixel).then((retorno)=>{
		  	// Verifica se feature está 'apagada' (sem highlight)
		  	if(!isLit) {
		  		featureInfo.style.opacity = '0.75';
		  	}
		  	if(selecionado)
		  	{
		  		var fProps = {};
		  		let descricao = "";

		  		for (var prop in selecionado.getProperties())
		  		{
		  			if(typeof(selecionado.getProperties()[prop]) === 'string' && prop !== 'styleUrl')
		  			{
		  				let valor = selecionado.getProperties()[prop];
		  				fProps[prop] = valor;
		  				descricao += '<p><strong>' + prop + ':</strong> ' + valor + '</p>';
		  			}
		  		}
		  		featureInfo.innerHTML = descricao;

		  	  featureInfo.style.opacity = '1';
		  	}
		  	console.log("Percorrido: ",retorno);
		  });
		  
		  if (selecionado) {
		    // featureInfo.innerHTML = '&nbsp;Hovering: ' + selecionado.get('name');
		    console.log(selecionado);
		  } else {
		  	// Limpa caixa de informações da Feature (featureInfo)
		    // featureInfo.innerHTML = '&nbsp;';
		  }
		});
		*/
	}
	
	$scope.parseJson = function(json) {
		var parsed = {};
		try {
			parsed = JSON.parse(json)
		} catch (e) {
			console.error(e);
		}
		return parsed;
	}
	
	$scope.verMemoria = function(ocultarTabela = false){ // VISUALIZAR TABELA DE VALORES DO INDICADOR
		$scope.mostraTabela = ocultarTabela ? false : !$scope.mostraTabela; // ALTERNA EXIBIÇÃO DA TABELA
		if (!$scope.mostraTabela)
			return;
		$scope.memoriaIndicador = {}; // LIMPA OBJETO PARA EVITAR EXIBIÇÃO INCORRETA DE VALORES NÃO ATUALIZADOS
		$scope.carregandoMemoriaIndicador = true;
		if($scope.hoverMapa){
			dataMemoria = null;
			dataInicioMemoria = $scope.selecao.dataMin;
			dataFimMemoria = $scope.selecao.dataMax;
		}
		else{
			dataInicioMemoria = null;
			dataFimMemoria = null;
			dataMemoria = $scope.selecao.dataSel;
		}

		IndicadorMemoria.get({id:$scope.indicador.id_indicador,
			data_inicio:dataInicioMemoria,
			data_fim:dataFimMemoria,
			data:dataMemoria,
			id_territorio:$scope.selecao.idTerrSel,
			id_regiao:($scope.regiaoRealcada != null)?$scope.regiaoRealcada.codigo:null},function(memoriaIndicador){
			$scope.memoriaIndicador = memoriaIndicador;

			// ATRIBUI NOME DAS VARIAVEIS
			let variavel1 = $scope.variavelHistorico[0].nome;
			let variavel2 = $scope.variavelHistorico[$scope.variavelHistorico.length-1].nome;
			let tipoValor = $scope.variavelHistorico[0].tipo_valor.charAt(0).toUpperCase() + $scope.variavelHistorico[0].tipo_valor.slice(1);
			
			$scope.memoriaIndicador.dados.forEach(function(dado){
				// FORMATA ORDEM E NOMENCLATURA DOS CABECALHOS NA TABELA EXPORTADA
				let nCategoria = dado.Categoria;
				delete dado.Categoria;
				dado["Categoria"] = nCategoria

				dado["Valor do indicador"] = dado.Valor;
				delete dado.Valor;
				dado["Unidade de medida do indicador"] = dado["Unidade de medida"]+" ("+dado["Símbolo de medida"]+")";
				delete dado["Unidade de medida"];
				delete dado["Símbolo de medida"];
				dado["Variável 1: "+variavel1] = dado.Variavel1;
				dado["Variável 1: Unidade de medida"] = tipoValor;
				delete dado.Variavel1;
				dado["Variável 2: "+variavel2] = dado.Variavel2;
				dado["Variável 2: Unidade de medida"] = tipoValor;
				delete dado.Variavel2;
				let nData = dado.Data.substring(0,4);
				delete dado.Data;
				dado["Data"] = nData;
				
				// REMOVE VARIAVEIS SOBRESSALENTES
				delete dado.Variavel3;
				delete dado.Variavel4;
				delete dado.Variavel5;
				delete dado.Variavel6;
				delete dado.Variavel7;
				delete dado.Variavel8;
				delete dado.Nome;
				delete dado['Unidade Territorial de Análise'];

				// CONVERTE PONTO EM VIRGULA (ISSUE P2.3)
				for(valor in dado){
					dado[valor] = $scope.pontoParaVirgula(dado[valor]);
				}
				$scope.carregandoMemoriaIndicador = false;
			});
		});
	}

	// Colunas da Tabela de Valores do Indicador passíveis de mesclagem (para ocultar célular e viabilizar rowspan)
	$scope.valoresMesclaveis = [
		// 'Categoria',
		// 'Região',
		// 'Unidade de medida do indicador'
	];

	$scope.deveMostrarCelula = function(arrayDados, key, coluna, valor) {
		if(!$scope.valoresMesclaveis.includes(coluna) || key === 0)
			return true;

		let first = arrayDados.find(dado => dado[coluna] === valor)
		return first == arrayDados[key];
	}

	$scope.calculaRowspan = function(arrayDados, coluna, valor) {
		if(!$scope.valoresMesclaveis.includes(coluna))
			return 1;
		let rowspan = 0;
		for (var i = 0; i < arrayDados.length; i++) {
			if (arrayDados[i][coluna] === valor){
				rowspan++;
				continue;
			}
		}
		console.log(rowspan);
		return rowspan;
	}

	$scope.insereCategoriaMemoria = function() {
		// Em "Unidade Territorial de Análise" definida como "Município", relação sequencial simples
		for (var i = 0; i < $scope.memoriaIndicador.dados.length; i++){
			let categoria = $scope.indicadorValores.series[Math.floor(i / ($scope.memoriaIndicador.dados.length / $scope.indicadorValores.series.length))].name;
			$scope.memoriaIndicador.dados[i]['Categoria'] = categoria;
			// $scope.memoriaIndicador.dados[i].splice(2, $scope.memoriaIndicador.dados[i].pop());
		}
	}

	$scope.exportarMemoria = function(){
			function datenum(v, date1904) {
			if(date1904) v+=1462;
			var epoch = Date.parse(v);
			return (epoch - new Date(Date.UTC(1899, 11, 30))) / (24 * 60 * 60 * 1000);
		}
		 
		function sheet_from_array_of_arrays(data, offset) {
			var ws = {};
			var range = {s: {c:10000000, r:10000000}, e: {c:0, r:0 }};
			
			for(var R = 0; R <= data.length + 1; ++R) {
				C = 0;
				
				for(var prop in data[R]) {

					if(prop.substring(prop.length-1,prop.length) <= offset || prop.substring(0,prop.length-1)!= 'Variavel'){
						if(range.s.r > R) range.s.r = R;
						if(range.s.c > C) range.s.c = C;
						if(range.e.r < R) range.e.r = R;
						if(range.e.c < C) range.e.c = C;
						
						if(typeof data[R][prop] === 'function') continue;
						
						if(R == 0)
							var cell = {v: prop };
						else
							var cell = {v: data[R-1][prop] };
						
						if(cell.v == null) cell.v = '';
						var cell_ref = XLSX.utils.encode_cell({c:C,r:R});
						
						if(typeof cell.v === 'number') cell.t = 'n';
						else if(typeof cell.v === 'boolean') cell.t = 'b';
						else if(cell.v instanceof Date) {
							cell.t = 'n'; cell.z = XLSX.SSF._table[14];
							cell.v = datenum(cell.v);
						}
						else cell.t = 's';
						
						ws[cell_ref] = cell;
						C++;
					}
				}
			}
			
			if(range.s.c < 10000000) ws['!ref'] = XLSX.utils.encode_range(range);
			return ws;
		}
		 
		/* original data */
		var wsNomeMemoria = "Tabela_Valores";
		var wsNomeMetadado = "Ficha_Tecnica";
		
		function Workbook() {
			if(!(this instanceof Workbook)) return new Workbook();
			this.SheetNames = [];
			this.Sheets = {};
		}
		
		function s2ab(s) {
			var buf = new ArrayBuffer(s.length);
			var view = new Uint8Array(buf);
			for (var i=0; i!=s.length; ++i) view[i] = s.charCodeAt(i) & 0xFF;
			return buf;
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
		
		if($scope.hoverMapa){
			dataMemoria = null;
			dataInicioMemoria = $scope.selecao.dataMin;
			dataFimMemoria = $scope.selecao.dataMax;
		}else{
			dataInicioMemoria = null;
			dataFimMemoria = null;
			dataMemoria = $scope.selecao.dataSel;
		}
		
		IndicadorMemoria.get({id:$scope.indicador.id_indicador,
			data_inicio:dataInicioMemoria,
			data_fim:dataFimMemoria,
			data:dataMemoria,
			id_territorio:$scope.selecao.idTerrSel,
			id_regiao:($scope.regiaoRealcada != null)?$scope.regiaoRealcada.codigo:null},function(memoriaIndicador){
			$scope.memoriaIndicador = memoriaIndicador;

			// ATRIBUI NOME DAS VARIAVEIS
			let variavel1 = $scope.variavelHistorico[0].nome;
			let variavel2 = $scope.variavelHistorico[$scope.variavelHistorico.length-1].nome;
			let tipoValor = $scope.variavelHistorico[0].tipo_valor.charAt(0).toUpperCase() + $scope.variavelHistorico[0].tipo_valor.slice(1);
			// GRAVA UNIDADE DE MEDIDA DO INDICADOR PARA EXIBIR NA FICHA TECNICA
			let unidadeMedida = $scope.memoriaIndicador.dados[0]["Unidade de medida"]+" ("+$scope.memoriaIndicador.dados[0]["Símbolo de medida"]+")";
			
			$scope.memoriaIndicador.dados.forEach(function(dado){
				// FORMATA ORDEM E NOMENCLATURA DOS CABECALHOS NA TABELA EXPORTADA
				dado["Data (ano-mês-dia)"] = dado.Data;
				delete dado.Data;
				dado["Valor do indicador"] = dado.Valor;
				delete dado.Valor;
				dado["Unidade de medida do indicador"] = dado["Unidade de medida"]+" ("+dado["Símbolo de medida"]+")";
				delete dado["Unidade de medida"];
				delete dado["Símbolo de medida"];
				dado["Variável 1: "+variavel1] = dado.Variavel1;
				delete dado.Variavel1;
				dado["Variável 1: Unidade de medida"] = tipoValor;
				dado["Variável 2: "+variavel2] = dado.Variavel2;
				delete dado.Variavel2;
				dado["Variável 2: Unidade de medida"] = tipoValor;

				// CONVERTE PONTO EM VIRGULA (ISSUE P2.3)
				for(valor in dado){
					dado[valor] = $scope.pontoParaVirgula(dado[valor]);
				}
			});
			
			var wb = new Workbook();
			var wsMemoria = sheet_from_array_of_arrays($scope.memoriaIndicador.dados,$scope.memoriaIndicador.qtd_variavel);
			wsMemoria['!cols'] = [
			    {wpx:280}, // LARGURA COLUNA 1 (PIXELS)
			    {wpx:200}, // COLUNA 2
			    {wpx:100}, // COLUNA 3...
			    {wpx:100},
			    {wpx:130},
			    {wpx:100},
			    {wpx:100},
			    {wpx:100},
			    {wpx:100},
			    {wpx:100}
			];
			
			var wsMetadado = {};
			coluna = 0, linha = 0;
			wsMetadado[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,'Ficha Técnica');
			linha+=2;
			wsMetadado[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,'Nome do indicador');
			coluna++;
			wsMetadado[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,$scope.indicador.nome);

			linha++;coluna--;
			wsMetadado[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,'Descrição completa');
			coluna++;
			wsMetadado[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,$scope.indicador.apresentacao);
			linha++;coluna--;

			wsMetadado[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,'Instrumento de Política Urbana e Gestão Ambiental');
			coluna++;

			wsMetadado[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,$scope.indicador.instrumento);
			linha++;coluna--;

			wsMetadado[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,'Estratégias');
			coluna++;

			angular.forEach($scope.indicador.estrategias,function(estrategia,chave){
				wsMetadado[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,estrategia.nome);
				linha++;
			});
			coluna--;			

			wsMetadado[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,'Fórmula de cálculo');
			coluna++;

			wsMetadado[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,$scope.indicador.formula_calculo);
			linha++;coluna--;

			wsMetadado[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,'Unidade de medida');
			coluna++;

			wsMetadado[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,unidadeMedida);
			linha++;coluna--;

			wsMetadado[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,'Fonte');
			coluna++;

			wsMetadado[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,$scope.indicador.fonte);
			linha++;coluna--;

			wsMetadado[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,'Periodicidade de atualização');
			coluna++;

			wsMetadado[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,$scope.indicador.periodicidade.charAt(0).toUpperCase()+$scope.indicador.periodicidade.slice(1));
			linha++;coluna--;

			wsMetadado[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,'Unidades Territoriais de Análise');
			coluna++;

			angular.forEach($scope.indicador.territorios,function(valor,chave){
				wsMetadado[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,valor.nome);
				linha++;
			});
			linha+=2;coluna--;
			wsMetadado[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,"Variáveis");
			linha+=2;
			wsMetadado[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,"Variável 1");
			coluna++;
			wsMetadado[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,variavel1);
			linha++;coluna--;
			wsMetadado[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,"Variável 1: Unidade de medida");
			coluna++;
			wsMetadado[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,tipoValor);
			linha++;coluna--;
			wsMetadado[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,"Variável 2");
			coluna++;
			wsMetadado[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,variavel2);
			linha++;coluna--;
			wsMetadado[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,"Variável 2: Unidade de medida");
			coluna++;
			wsMetadado[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,tipoValor);
			
			var range = {s: {c:0, r:0}, e: {c:1, r:30 }};
			wsMetadado['!ref'] = XLSX.utils.encode_range(range);
			wsMetadado['!cols'] = [
				{wpx:300},	// LARGURA COLUNA 1
				{wpx:900}	// LARGURA COLUNA 2
			];
			
			/* add worksheet to workbook */ 

			wb.SheetNames.push(wsNomeMemoria);
			wb.SheetNames.push(wsNomeMetadado);

			wb.Sheets[wsNomeMemoria] = wsMemoria;
			wb.Sheets[wsNomeMetadado] = wsMetadado;
			var wbout = XLSX.write(wb, {bookType:'xlsx', bookSST:false, type: 'binary'});
			
			saveAs(new Blob([s2ab(wbout)],{type:"application/octet-stream"}), $scope.indicador.nome + ".xlsx");
			
		});
	}
	
	$scope.cargaCadastroIndicadores = function(id){
		$scope.carregandoIndicador = true;
		// ARMAZENA OBJETO DA REQUISIÇÃO EM UM ARRAY PARA ABOTAR REQUISIÇÕES CONFORME NECESSÁRIO
	  $scope.xhrReqs.push(Indicador.query(
	  	{
	  		grupo_indicador:id,
	  		somente_ativos:true
	  	},
	  	function(indicadores) {
			  $scope.indicadores = indicadores;
			  $scope.carregandoIndicador = false;
			  // Após carregar indicadores, verifica se pode carregar indicador do link
			  $scope.urlIndicador(computedUrl);
			}
		));
	}

	/**** 
	** TODO: REESCREVER FUNCAO DE FILTRO DE OBJETIVOS QUANDO A NOVA ESTRUTURA FOR DEFINIDA
	** NO MOMENTO O FILTRO ESTA PRE-DEFINIDO NO CODIGO
	****/
	
	// Filtros para a visualização de objetivos
	$scope.filtraObjetivos = function(filtro){
		// console.log($scope.filtrosObjetivos.zonas_especiais.objetivos)
		return;
	};

	// $scope.filtraObjetivos = function(filtro){
	// 	$scope.mostraPDE = false;
	// 	// Não havendo objetivos, escapa função para otimizar performance
	// 	if($scope.rawObjetivos.length === 0)
	// 		return;
	// 	console.log("Objetivos:");
	// 	console.log($scope.rawObjetivos);
	// 	if (filtro === null){ // Se não selecionar filtro, mostra todos os indicadores
	// 		$scope.objetivos = $scope.rawObjetivos;
	// 		$scope.cargaCadastroIndicadores(null);
	// 		return;
	// 	}
	// 	else if (filtro.id === 1) { // Se filtro selecionado for 'Plano Diretor Estratégico (ID 1), carrega os indicadores relativos'
	// 		$scope.objetivos = [];
	// 		console.log("idPDE...");
	// 		console.log($scope.rawObjetivos);
	// 		var idPDE = $scope.rawObjetivos.filter(function(obj){
	// 			console.log("filtraObjetivos obj");
	// 			console.log(obj);
	// 			return obj.nome === "Plano Diretor Estratégico"
	// 		})[0].id_grupo_indicador;
	// 		$scope.cargaCadastroIndicadores(idPDE);
	// 		$scope.atualizaFicha(idPDE, true)
	// 		$scope.mostraPDE = true;
	// 		return;
	// 	}
	// 	let checkObjetivos = function(ob){			
	// 		for (i in filtro.objetivos) {
	// 			if (ob.nome === filtro.objetivos[i])
	// 				return true;
	// 		}
	// 		return false;
	// 	}
	// 	let objetivosFiltrados = $scope.rawObjetivos.filter(checkObjetivos);
	// 	$scope.objetivos = objetivosFiltrados;
	// };

	$scope.buscaIndicador = function(termo){
		if(termo.length < 2 || $scope.carregandoIndicador)
			return;
		$scope.carregandoIndicador = true;
		Indicador.query({somente_ativos:true,termo_buscado:termo},function(indicadores){
			$scope.indicadores = indicadores;
			$scope.carregandoIndicador = false;
		});
	}

	$scope.filtrosObjetivos = {
		// plano_diretor: {
		// 	id: 1,
		// 	nome: "Plano Diretor Estratégico",
		// 	objetivos: []
		// },
		// macroareas: {
		// 	id: 2,
		// 	nome: "Macroáreas",
		// 	objetivos: [
		// 		"Macroárea de Estruturação Urbana",
		// 		"Macroárea de Urbanização Consolidada",
		// 		"Macroárea de Qualificação da Urbanização",
		// 		"Macroárea de Redução da Vulnerabilidade Urbana",
		// 		"Macroárea de Redução da Vulnerabilidade Urbana e Recuperação Ambiental",
		// 		"Macroárea de Controle e Qualificação Urbana e Ambiental",
		// 		"Macroárea de Contenção Urbana e Uso Sustentável",
		// 		"Macroárea de Preservação dos Ecossistemas Naturais"
		// 	]
		// },
		zonas_especiais: {
			id: 3,
			nome: "Zonas Especiais",
			objetivos: [{
					id: 22,
					nome: "Zonas Especiais de Interesse Social (ZEIS)"
				},
				{
				// "Zonas Especiais de Preservação (ZEP)",
					id: 21,
					nome: "Zonas Especiais de Preservação Cultural (ZEPEC)"
				},
				{
					id: 19,
					nome: "Zonas Especiais de Proteção Ambiental (ZEPAM)"
				}
			]
		}
		// "Macroáreas": ["A","B"],
		// "Zonas Especiais": ["C","D"]
	};
	
	$scope.ajustarDataFinal = function(){
		if($scope.selecao.dataMin >= $scope.selecao.dataMax){
			$scope.selecao.dataMax = $scope.indicador.datas[0] == null ? $scope.indicador.datas[1] : $scope.indicador.datas[0];
		}
	};

	
	$scope.selecao = {};
	$scope.inicializarSelecao();
	
	$scope.cargaEstrategia(1);
	
	 Noticia.query(function(noticias){
		 $scope.noticias = noticias;
	 });
	 
	 AcaoPrioritaria.query(function(acoesPrioritarias){
		$scope.acoesPrioritarias = acoesPrioritarias;
	 });
	 
	 Menu.get({id:4},function(menu){
		 $scope.menuForma = menu;
	 });
	 
	 // TROCAR PREFEITURA REGIONAL POR SUBPREFEITURA
	 $scope.subRegional = function(elemento){
	 	var opts = elemento.target;
	 	for(var i=0; i < opts.length; i++){
	 		if(opts[i].label == "Prefeitura Regional")
	 			opts[i].label = "Subprefeitura";
	 	}
	 };


	 $scope.atribuirRegiaoSemSelecao = function(){
		 nomeTerritorio = $scope.labelTerrSel.split(" ");
		 nomeTerritorio = nomeTerritorio[0];
		 ultimaLetraTerritorio = nomeTerritorio.substring(nomeTerritorio.length-1,nomeTerritorio.length);
		 if(ultimaLetraTerritorio == 'a'){
			 $scope.regiaoSemSelecao = 'Todas';
		 }else{
			$scope.regiaoSemSelecao = 'Todos';
		 }
	 };
	 
	 $scope.exportarMapa = function(){
		 $scope.mapa.once('postcompose', function(event) {
          var canvas = event.context.canvas;
          if (navigator.msSaveBlob) {
            navigator.msSaveBlob(canvas.msToBlob(), 'map.png');
          } else {
            canvas.toBlob(function(blob) {
              saveAs(blob, 'map.png');
            });
          }
        });
	 };

	 $scope.salvarComo = function(uri, nome) {
	    var link = document.createElement('a');
	    if (typeof link.download === 'string') {
			link.href = uri;
			link.download = nome;
			// INSERE LINK NO CORPO PARA FIREFOX SUPORTAR
			document.body.appendChild(link);
			// SIMULA O CLICK E REMOVE O ELEMENTO
			link.click();
			document.body.removeChild(link);
    	} else {
      		window.open(uri);
    	}
	}

	$scope.printScreen = function(tipo) {
		tipoParams = {
			instrumento: {
				nome: $scope.indicador.instrumento,
				classe: 'abas-container'
			},
			indicador: {
				nome: $scope.indicador.nome,
				classe: 'panel-open'
			}
		}

		html2canvas(document.getElementsByClassName(tipoParams[tipo].classe)[0], {allowTaint: true, scale: 2}).then(function(canvas) {
			$scope.salvarComo(canvas.toDataURL('image/png'), tipoParams[tipo].nome + '.png');
		});
	};

	$scope.debugLog = function(el) {
		console.warn("DEBUG LOG:");
		console.log(el);
	}
});	

</script>

<div id="conteudo" data-ng-app="monitoramentoPde" data-ng-controller="dashboard">

<script type="text/ng-template" id="ModalFichaInstrumento.html">
	<div class="modal-instrumento">
		<div class="modal-header">
	    <h3 class="modal-title" id="modal-titulo-ficha-instrumento">{{indicador.instrumento}} <button class="btn btn-danger pull-right" type="button" ng-click="fecharModal('instrumento')" style="font-family: sans-serif, cursive; font-weight: bold;">X</button></h3> 
		</div>
		<div class="modal-body" id="modal-corpo-ficha-instrumento" style="text-align:justify">			
			<p ng-repeat="(chave, valor) in fichaInstrumento.propriedades" >
				<span ng-click="this.aberto==true? this.aberto=false : this.aberto=true"><strong> {{chave}} </strong> <i class="glyphicon" ng-if="chave!='Definição'" ng-class="{'glyphicon-chevron-up': this.aberto, 'glyphicon-chevron-down': !this.aberto}"></i>
				<br>
				<span ng-bind-html="valor" ng-if="this.aberto==true || chave=='Definição'" class="quebra-linha"></span>
			</span>		
			</p>
		</div>
		<div class="modal-footer">	
			<button class="btn btn-danger" type="button" ng-click="fecharModal('instrumento')">	Fechar</button>
		</div>
	</div>
</script>	

<script type="text/ng-template" id="ModalFichaIndicador.html">
	<div class="modal-instrumento">
		<div class="modal-header">
	    <h3 class="modal-title" id="modal-titulo-ficha-instrumento">{{indicador.nome}} <button class="btn btn-danger pull-right" type="button" ng-click="fecharModal('indicador')">X</button> </h3> 
		</div>
		<div class="modal-body" id="modal-corpo-ficha-instrumento" style="text-align:justify">
				<p class="quebra-linha" ng-bind-html="indicador.nota_tecnica_resumida"> </p>
				<p class="quebra-linha" ng-show="indicador.nota_tecnica != null"><strong> Nota técnica  </strong> <br> <span ng-bind-html="indicador.nota_tecnica"></span>  </p>
				<p><strong> Instrumento de Política Urbana e Gestão Ambiental  </strong> <br> <a class="link-ficha-instrumento btn btn-default btn-sm" style="margin-left:-7px;padding-left:7px;padding-right:7px;background-color:#ccc" ng-click="abrirModal('instrumento')"> {{indicador.instrumento}} </a> <br>  </p>
				<p><strong> Estratégias </strong> <br>
				<span ng-repeat="estrategia in indicador.estrategias">
				 <a class="link-ficha-instrumento btn btn-default btn-sm" style="margin-left:-7px;padding-left:7px;padding-right:7px;background-color:#ccc" ng-click="abrirModal('estrategia')"> {{estrategia.nome}} </a> <br> 
				</span>
				</p>
				<p style="white-space:pre-wrap;"><strong> Fórmula de cálculo </strong> <br> <span ng-bind-html="indicador.formula_calculo"></span> </p>
				<p><strong> Unidade de medida </strong> <br> <span ng-bind-html="indicador.tipo_valor"></span> </p>
				<p><strong> Série histórica </strong> <br> De {{indicador.datas[indicador.datas.length-1] | date: indicador.periodicidade == 'anual' ? 'yyyy' : 'MMMM yyyy'}} a {{indicador.datas[0] | date: indicador.periodicidade == 'anual' ? 'yyyy' : 'MMMM yyyy' }}</p>
				<p><strong> Fontes </strong> <br> <span ng-bind-html="indicador.fonte"></span> </p>
				<p><strong> Periodicidade de atualização </strong> <br> <span class="primeira-maiuscula">{{indicador.periodicidade}}</span> </p>
				<p><strong> Unidade territorial de análise </strong> <br>
				<span ng-repeat="territorio in indicador.territorios | orderBy: 'ordem'">
					{{territorio.nome}} <br>
				</span>
				</p>
			</div>
		<div class="modal-footer">	
			<button class="btn btn-danger" type="button" ng-click="fecharModal('indicador')">	Fechar</button>
		</div>
	</div>
</script>	


<script type="text/ng-template" id="ModalFichaEstrategia.html">
	<div class="modal-estrategia">
		<div class="modal-header">
	    <h3 class="modal-title" id="modal-titulo-ficha-estrategia">{{estrategia.nome}} <button class="btn btn-danger pull-right" type="button" ng-click="fecharModal('estrategia')">X</button> </h3> 
		</div>
		<div class="modal-body" id="modal-corpo-ficha-estrategia" style="text-align:justify">
				<p class="quebra-linha"> <a href="{{estrategia.link_infografico}}" target="_blank"><img class="img-responsive" src="{{estrategia.link_imagem}}" alt="{{estrategia.nome}}"></a> </p>
				<p class="quebra-linha" ng-bind-html="estrategia.descricao"> </p>
				<p class="quebra-linha" ng-if="estrategia.texto_complementar!=null" ng-bind-html="estrategia.texto_complementar">  </p>
				<p class="quebra-linha text-center" ng-if="estrategia.link_video_embed!=null">
				<object style="width:100%;height:100%;width: 820px; height: 461.25px; float: none; clear: both; margin: 2px auto;" data="{{estrategia.link_video_embed}}">
				</object>
				</p>
				
				<p class="quebra-linha" ng-if="estrategia.perguntas_respostas!=null"> <strong>Perguntas e respostas </strong> <br> <span ng-bind-html="estrategia.perguntas_respostas"></span></p>
			</div>
		<div class="modal-footer">	
			<button class="btn btn-danger" type="button" ng-click="fecharModal('estrategia')">	Fechar</button>
		</div>
	</div>
</script>	

<script type="text/ng-template" id="indicador.html">
	<div class="row">
				<div class="col-sm-6"> 
					<p class="quebra-linha" ng-bind-html="indicador.apresentacao"></p>
					
					<a class="link-saiba-mais link-saiba-mais-indicador" ng-click="abrirModal('indicador')"> Ficha técnica do indicador </a>
					<a class="link-saiba-mais link-saiba-mais-indicador" ng-click="abrirModal('instrumento')"> Ficha técnica do instrumento </a>
					<a class="link-saiba-mais link-saiba-mais-indicador" ng-click="verMemoria()">{{ mostraTabela ? 'Ocultar' : 'Visualizar' }} Tabela de valores do indicador </a>
					<a class="link-saiba-mais link-saiba-mais-indicador" ng-click="exportarMemoria()">Baixar Tabela de valores do indicador</a>
					<br>
					<div>
						<label for="tipoGrafico">Tipo de visualização: </label>
						<select id="tipoGrafico" data-ng-model="tipoGraficoSelecionado">
							<option ng-if="mostrarGrafico(tipo)" data-ng-repeat="tipo in tiposGrafico">{{ tipo }}</option>
						</select>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="row">

						<div class="col-sm-5 col-sm-offset-1 caixa-data">
						
							<p ng-if="hoverMapa && (indicador.datas.length > 0 || indicador.datas[0]) ">
								<label for="data">Data inicial:</label>
								<br>
								<select 
									style="max-width:100%;" 
									data-ng-model="selecao.dataMin"
									data-ng-init="selecao.dataMin = razaoOODC(indicador.id_indicador)"
									data-ng-options="data as (formatarData(data) | date: indicador.periodicidade == 'anual' ? 'yyyy' : 'MMMM yyyy') for data in indicador.datas | filter:'' " 
									data-ng-change="ajustarDataFinal();carregarGraficoHistorico(regiaoRealcada.codigo, true);"
									name="dataInicial">
								</select>
							</p>
							<p ng-if="hoverMapa && (indicador.datas.length > 0 || indicador.datas[0]) ">
								<label for="data">Data final:</label>
								<br>
								<select 
									style="max-width:100%;" 
									data-ng-model="selecao.dataMax" 
									data-ng-options="data as (formatarData(data) | date: indicador.periodicidade == 'anual' ? 'yyyy' : 'MMMM yyyy') for data in indicador.datas | filter:'' | dataFinal: selecao.dataMin " 
									data-ng-change="carregarGraficoHistorico(regiaoRealcada.codigo, true)" 
									name="dataFinal">
								</select>
							</p>
							
							<p ng-if="!hoverMapa && (indicador.datas.length > 0 && indicador.datas[0]) ">
								<label for="data"> Data</label>
								<br>
								<select ng-if="indicador.periodicidade == 'anual' || indicador.periodicidade == 'mensal' || !indicador.periodicidade" style="max-width:100%;" data-ng-model="selecao.dataSel" data-ng-options="data as (formatarData(data) | date: indicador.periodicidade == 'anual' ? 'yyyy' : 'MMMM yyyy') for data in indicador.datas | filter:'' " data-ng-change="cargaIndicadorValores(false,true)" name="periodo"></select>
								<select ng-if="indicador.periodicidade == 'trimestral'" style="max-width:100%;" data-ng-model="selecao.dataSel" data-ng-options="data as formatarTrimestre(data) for data in indicador.datas | filter:'' " data-ng-change="cargaIndicadorValores(false,true)" name="periodo"></select>
							</p>
						</div>
						<div class="col-sm-3 caixa-categoria" style="position: absolute; left: 10em">
							<p ng-if="(regiaoRealcada.codigo == null) && mostrarCategoria()">
								<label>Categoria:</label>
								<br>
								<select style="max-width:100%;" data-ng-model="selecao.categoria" data-ng-options="categoria as categoria.name for categoria in selecao.categorias" data-ng-change="filtraCategoria()" name="categoria">
									<option value="">Escolha uma categoria</option>
								</select>
							</p>
						</div>
						<div class="col-sm-6">
							<p>
								<label for="territorio">Unidade territorial de análise</label>
								<!-- UNIDADE TERRITORIAL DE ANÁLISE DO INDICADOR -->
								<br>
								<select style="max-width:100%;width:100%;" id="seletor-territorio" data-ng-model="selecao.idTerrSel" data-ng-click="subRegional($event);" data-ng-options="territorio.id_territorio as territorio.nome for territorio in indicador.territorios | orderBy: 'ordem'" data-ng-change="cargaIndicadorValores(false,true);atribuirRegiaoSemSelecao();" name="territorio"></select>
							</p>
							<p ng-show="selecao.idTerrSel != 4">
								
								
								<select style="max-width:100%;width:100%;" data-ng-model="regiaoRealcada.codigo" data-ng-options="regiao.codigo as regiao.nome for regiao in dadosMapa" data-ng-change="fixarMapa(regiaoRealcada.codigo);" name="regiao">
								<option value="">{{regiaoSemSelecao}}</option></select>

							</p>
							
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div ng-class="(selecao.idTerrSel != 4) ? 'col-sm-9' : 'col-sm-12'">
					<!-- Tabela de Valores -->
					<div class="row" ng-if="mostraTabela">
						<div class="tabela-valores-container">
							<table class="tabela-valores">
								<tr>
									<th ng-repeat="(key, valor) in memoriaIndicador.dados[0]">{{key}}</th>
								</tr>
								<tr ng-repeat="(key, dado) in memoriaIndicador.dados">
									<td ng-repeat="(coluna, valor) in dado" 
											ng-if="deveMostrarCelula(memoriaIndicador.dados, key, coluna, valor)"
											rowspan="{{ calculaRowspan(memoriaIndicador.dados, coluna, valor) }}">
										{{ valor }}
									</td>
								</tr>
							</table>
						</div>
					</div>
					<div class="row" id="divGraficoLinha" ng-show="!mostraTabela">
						<div ng-if="hoverMapa && !carregarGraficoLinhas" style="display:flex;align-items:center;margin-left:120px;">
							<h4 style="font-size:12px;" class="alert alert-danger">{{carregandoHistorico}}</h4>
						</div>
					
						<!-- <div id="graficoBarras" ng-show="!hoverMapa"></div> -->
						
						<!-- <div  id="graficoLinhas" ng-show="hoverMapa && carregarGraficoLinhas"></div> -->
						<!-- EXIBE OS 5 TIPOS DE GRÁFICO: area, barras, colunas, linha, pizza -->
						<div data-ng-repeat="tipo in tiposGrafico" id="{{ 'grafico' + tipo[0].toUpperCase()+tipo.slice(1) }}" ng-show="tipo === tipoGraficoSelecionado"></div>
						<!-- ng-show="mostrarGrafico(tipo)" -->
					</div>
					<div class="row">
						<div class="col-md-12">
							<small>Fonte: {{indicador.origem}} </small>
						</div>
					</div>
					<div class="row" ng-if="indicador.observacao">
						<div class="col-md-12">
							<small>Observação	: {{indicador.observacao}} </small>
						</div>
					</div>
					<div class="row" ng-if="indicador.data_atualizacao">
						<div class="col-md-12">
							<small ng-if="indicador.data_atualizacao != null">Atualizado até: <span style="text-transform:capitalize">{{indicador.data_atualizacao | date: 'MMMM yyyy'}}</span> </small>
						</div>
					</div>
				</div>
				<div class="col-sm-3" ng-show="selecao.idTerrSel != 4">
					<div class="row">
						<div id="map" class="fMap" style="min-height:400px;"> <span ng-if="carregandoMapa">{{carregandoMapa}}</span></div>
						<!--<div id="infomapa" ng-show="hoverMapa">{{regiaoRealcada.nome}} {{regiaoRealcada.valor}}</div>-->
					</div>
					<div class="row">
						<div style="width:18%;float:left;padding-left:1px;padding-right:1px;" data-ng-repeat="classe in legenda | orderBy: 'indice'">
							<div ng-mouseover="realcarClasse(classe)" ng-mouseleave="deixarClasse()" style="height:5px;width:100%;display:block;background-color:{{classe.cor}}" class=""></div>
							<small>
							<!--<div ng-if="classe.maximo==classe.minimo && classe.maximo==0" style="text-align:right;font-size:75%;">{{classe.minimo | setDecimal: 1}}</div>-->
							<div ng-mouseover="realcarClasse(classe)" ng-mouseleave="deixarClasse()" style="text-align:left;font-size:75%;">{{classe.minimo | setDecimal: 1}}-{{classe.maximo | setDecimal: 1}}</div>
							</small>
						</div>
						<div style="width:100%;clear:both;padding-left:5px">
						<small>{{textoTitulo}}</small>
						</div>
					</div>
					<!--<div class="row">
						<div class="col-md-12" style="padding-left:5px;padding-right:5px;">
							<small>Nota técnica: {{indicador.nota_tecnica_resumida}}</small>
						</div>
					</div>-->
					<div class="row">
						<div class="col-md-12" style="padding-left:5px;padding-right:5px;">

							<small><small>Base cartográfica: Mapa Digital de São Paulo, 2004. <div style="margin-top:-0.5em">Projeção Datum UTM/23S. Datum horizontal SAD69.</span></small></small>
						</div>
						<!--<button class="btn btn-default" type="button" ng-click="exportarMapa()">	<small>Exportar Mapa</small></button>-->
					</div>
				</div>
			</div>
</script>
	
	<div uib-carousel active="0" interval="0" no-wrap="false">
		<div uib-slide data-ng-repeat="noticia in noticias track by $index" index="$index" class="slide-carrosel container-fluid">
			<div class="carousel-caption">
				<h4 class="text-uppercase" ng-bind-html="noticia.title.rendered | trustedHtml"></h4>
				<p ng-bind-html="noticia.content.rendered | trustedHtml"></p>
			</div>
		</div>
	</div>
	
	<div class="container abas-container">
		<!-- <p> 
			<div class="well" style="background-color:rgb(91,192,222);color:white;font-weight:bold;text-align:center;">
			A plataforma de Monitoramento e Avaliação da Implementação do Plano Diretor Estratégico está em processo de desenvolvimento e pode apresentar instabilidades de navegação durante este período
			</div>		 
		</p> -->
		<span data-html2canvas-ignore>
			<hr>
			<p>Escolha a forma como deseja visualizar os indicadores</p>
			<hr>
		</span>
		<uib-tabset active="tabAtivaForma" type="pills">
			<uib-tab index="$index + 1" ng-click="atualizaListaIndicadores()" ng-repeat="item in menuForma.items" heading="{{item.title}}" classes="{{item.classes}}" data-html2canvas-ignore>
				<hr data-html2canvas-ignore>
				
				<div ng-show="tabAtivaForma==1">
					<ul class="list-group row">
						<li class="list-group-item col-sm-3 text-left list-visualizacao" ng-mouseover="this.hover=true" ng-mouseleave="this.hover=false" style="width:20%;" ng-repeat="itemFilho in item.children"><a href=""  ng-click="cargaEstrategia(itemFilho.url.slice(1,itemFilho.url.length))"> <img class="img-responsive icones-visualizacao col-sm-3"  ng-src="/app/themes/monitoramento_pde/images/icones/{{itemFilho.description + ((itemFilho.url.slice(1,itemFilho.url.length) == estrategia.id_grupo_indicador || this.hover)? '_cor' : '_pb')}}.png"><span class="col-sm-12" style="padding:0"><br><strong>{{itemFilho.title}}</strong></span></a> </li>
					</ul>
				</div>
				
				<div ng-show="tabAtivaForma==2">	
					<span data-html2canvas-ignore>Os Instrumentos de Política Urbana e Gestão Ambiental são meios para viabilizar a efetivação dos princípios e objetivos do Plano Diretor. <br><br> Veja abaixo a lista dos instrumentos:<br><br></span>
					<select style="min-width:250px;max-width:400px;" 
						ng-disabled="carregandoIndicador" 
						data-ng-model="optInstrumento" 
						data-ng-options="instrumento.id_grupo_indicador as instrumento.nome for instrumento in instrumentos | orderBy: '-nome' : true" 
						ng-change="cargaCadastroIndicadores(optInstrumento); atualizaFicha(optInstrumento); loadMap(); atualizarStatusMapa(optInstrumento)" data-html2canvas-ignore><option value="">Todos</option>
					</select>
					<div ng-show="optInstrumento">
						<h4><strong>{{ fichaInstrumento.nome }}</strong></h4>
						<p>
							{{ descricaoGrupoIndicador }}... <a href='' ng-click='abrirModal("instrumento")' data-html2canvas-ignore>ver mais</a>
						</p>
						<p ng-show="kmlMapaAtual">Download do mapa georreferenciado: <a ng-href="{{kmlMapaAtual}}" class="download-badge">KML</a></p>
						<!-- DADOS ABERTOS -->
						<div class="download-dados-abertos" data-html2canvas-ignore>
							<p>Download do banco de dados:
								<a class="download-badge" target="_blank" ng-href="<?php echo bloginfo('url'); ?>/dados-abertos">Dados abertos</a>
							<!-- <a href="" ng-click="exportDadoFromInstrumento(optInstrumento,formato)" class="download-badge" data-ng-repeat="formato in grupoInstrumento.tipoArquivo"> <strong> DOWNLOAD {{formato}} </strong></a> -->
							</p>
						</div>
					</div>					
				</div>
				
				<div ng-show="tabAtivaForma==3">	
					Para garantir um desenvolvimento urbano sustentável e equilibrado, o Plano Diretor definiu em sua estratégia de ordenamento territorial um conjunto de objetivos a serem atingidos.<br><br>Veja abaixo os avanços realizados em relação aos objetivos do Plano Diretor Estratégico, das Macroáreas e das Zonas Especiais:<br><br>
					<select style="min-width:250px;max-width:400px;" ng-disabled="carregandoIndicador" data-ng-model="fltrObjetivo" data-ng-options="filtro.nome for filtro in filtrosObjetivos" ng-change="filtraObjetivos(fltrObjetivo)"><option value="">Todos</option></select>
					<!-- <select style="min-width:250px;max-width:400px;" data-ng-model="optObjetivo" data-ng-options="objetivo.id_grupo_indicador as objetivo.nome for objetivo in objetivos | orderBy: '-id_grupo_indicador' : true" ng-change="cargaCadastroIndicadores(optObjetivo); atualizaFicha(optObjetivo, true)" ng-show="objetivos.length > 0 && objetivos.length !== rawObjetivos.length"><option value="">{{ textoSelectObjetivo(fltrObjetivo.id) }}</option></select> -->
					<select style="min-width:250px;max-width:400px;" 
						ng-disabled="carregandoIndicador"
						data-ng-model="optObjetivo" 
						data-ng-options="objetivo.nome for objetivo in fltrObjetivo.objetivos | orderBy: '-nome' : true" 
						ng-change="cargaCadastroIndicadores(optObjetivo.id); atualizaFicha(optObjetivo.id); loadMap(); atualizarStatusMapa(optObjetivo.id)" 
						ng-show="fltrObjetivo.objetivos"><option value="" disabled selected hidden>Selecione...</option></select>
					<br />
					<div ng-show="optObjetivo">
						<h4><strong>{{ fichaInstrumento.nome }}</strong></h4>
						<p>
							{{ descricaoGrupoIndicador }}... <a href='' ng-click='abrirModal("instrumento")'>ver mais</a>
						</p>
						<p ng-show="kmlMapaAtual">Download do mapa georreferenciado: <a ng-href="{{kmlMapaAtual}}" class="download-badge">KML</a></p>
						<!-- DADOS ABERTOS -->
						<div class="download-dados-abertos">
							<p>Download do banco de dados:
								<a class="download-badge" target="_blank" ng-href="<?php echo bloginfo('url'); ?>/dados-abertos">Dados abertos</a>
							<!-- <a href="" ng-click="exportDadoFromInstrumento(optInstrumento,formato)" class="download-badge" data-ng-repeat="formato in grupoInstrumento.tipoArquivo"> <strong> DOWNLOAD {{formato}} </strong></a> -->
							</p>
						</div>
					</div>					
					<!-- <div ng-show="optObjetivo || mostraPDE">
						<h4><strong>{{ fichaInstrumento.nome }}</strong></h4>
						<p>
							{{ descricaoGrupoIndicador }}... <a href='' ng-show="verMais" ng-click='abrirModal("objetivo")'>ver mais</a>
						</p>
					</div> -->
				</div>

				<div ng-show="tabAtivaForma==4">
					<input type="text" name="busca-indicador" ng-disabled="carregandoIndicador" ng-model="termoBuscado" placeholder="Buscar por nome ou palavra" class="campo-busca" check-enter="buscaIndicador(termoBuscado)">
					<button class="campo-busca" ng-disabled="carregandoIndicador" ng-click="buscaIndicador(termoBuscado)">Pesquisar</button>
				</div>
			</uib-tab>
		</uib-tabset>

		<!-- Mapa dos instrumentos -->
			<div id="mapcontainer" ng-show="tabAtivaForma==2 || tabAtivaForma==3" ng-class="{'zeroheight': !mostrarMapa}">
				

				<div id="map-instrumento" class="map"></div>
				
				<div id="legenda-mapa" ng-show="camadasInstrumento.length > 0">
					<span><strong>Legenda</strong></span>
					<div ng-repeat="(key, camada) in camadasInstrumento | orderBy: ['+dimensao_feature', '+ordem_legenda']">
						<div ng-style="estiloLegenda(camada)"></div><span>{{camada.nome_camada}}</span>
					</div>
				</div>
				<div class="info-box" ng-show="mostrarMapa">
					<h5>Informações do item</h5>
					<br>
					<div id="feature-info">&nbsp;</div>
				</div>
				<div id="botoes-mapa" data-html2canvas-ignore>
					<button class="gera-link-botao" ng-click="geraLink('instrumento')" type="button" title="Gerar link para este instrumento" ng-show="mostrarMapa">
					</button>
					<button class="print-screen-botao" ng-click="printScreen('instrumento')" type="button" title="Salvar instrumento como imagem">	
					</button>
				</div>
			</div>			
		</div>		

		<span ng-show="tabAtivaForma==1">
			<hr>
			<h4 class="titulo-forma-visu">{{estrategia.nome}}</h4>
			<div class="row" >
				<div class="col-sm-6 col-xs-12" >  
				<p >{{estrategia.descricao}} </p>
				<h4 id="saiba-mais"><a href="" class="link-saiba-mais" ng-click="abrirModal('estrategia')"> Saiba mais sobre essa estratégia </a></h4>
				</div>
				<div class="col-sm-6 col-xs-12" >
					<div><a href="" ng-click="abrirModal('estrategia')"><img class="img-responsive" ng-src="{{estrategia.link_imagem}}" alt="{{estrategia.nome}}"></a></div>
				</div>
			</div>
		</span>		
		<hr>
		<h4 ng-show="indicadores.length > 0"><strong>Indicadores </strong></h4>
		<uib-accordion close-others="true">
			<!-- Alerta de carregamento -->
			<div id="alerta-carregamento" ng-show="carregandoIndicador">
				Carregando indicadores...
			</div>
			<div uib-accordion-group is-open="indicador.aberto" class="panel-default" close-others="true" ng-repeat="indicador in indicadores">
				<uib-accordion-heading>
					<span ng-class="indicador.homologacao ? 'header-painel-indicadores-homolog' : 'header-painel-indicadores'" > {{indicador.nome}} <br> <small>Instrumento: {{indicador.instrumento}} </small>
						<i class="pull-right glyphicon" ng-class="{'glyphicon-chevron-up': indicador.aberto, 'glyphicon-chevron-down': !indicador.aberto}"></i>
					
					</span>
				</uib-accordion-heading>
				
				<div ng-include onload="atualizarAccordion(indicador);atribuirRegiaoSemSelecao();" src="indicador.aberto ? 'indicador.html' : ''"></div>
				<div style="text-align: right; width: 100%; margin-top: -30px;" data-html2canvas-ignore>
				<button class="gera-link-botao" ng-click="geraLink('indicador')" type="button" title="Gerar link para este indicador">
				</button>
				<button class="print-screen-botao" ng-click="printScreen('indicador')" type="button" title="Salvar indicador como imagem">	
				</button>
				</div>			
			</div>
		</uib-accordion>
	</div>
</div>

<style type="text/css">
	.abas-container {
		padding-bottom: 15px;
	}
	#map-instrumento {
		display: block;
		position: relative;
		min-height: 400px;
		max-height: 500px;
		width: 100%;		
	}
	#mapcontainer {
		/*height: 400px;*/
		/*border: 1px solid grey;*/
		position: relative;
	}
	.zeroheight {
		height: 0;
		z-index: -1;
		opacity: 0;
	}
	#legenda-mapa {
		position: absolute;
    right: 0;
    bottom: 0;
    margin: 10px;
    padding: 10px;
    border-radius: 5px;
    min-width: 200px;
    min-height: 50px;
    background-color: rgba(255,255,255,0.95);
    border: 1px solid #cccccc;
    line-height: 2em;
    overflow: auto;
    z-index: 1;
	}

	#botoes-mapa {
		margin: 10px;
		padding: 10px;
		position: absolute;
		left: 0;
		bottom: 0;
		min-width: 50px;
		min-height: 50px;
		background-color: rgba(255,255,255,0.95);
		border: 1px solid #cccccc;
		border-radius: 5px;
		display: flex;
		justify-content: center;
		align-items: center;
	}

	.gera-link-botao {
		border: none;
		background: url(./app/themes/monitoramento_pde/images/icon-link.jpg);
		background-repeat: no-repeat;
		background-size: contain;
		margin: 0 5px;
		width: 30px;
		height: 30px;
	}

	.print-screen-botao {
		border: none;
		background: url(./app/themes/monitoramento_pde/images/icon-image-save.jpg);
		background-repeat: no-repeat;
		background-size: contain;
		margin: 0 5px;
		width: 30px;
		height: 30px;
	}

	#feature-info {
		/*background-color: rgba(255,255,255,0.8);*/
		opacity: 0;
		z-index: 2;
	}
	.info-box {
		margin: 10px;
    padding: 10px;
    border: 1px solid #cccccc;
    background-color: rgba(255,255,255,0.95);
    position: absolute;
    top: 0;
    right: 0;
    width: 20%;
    min-height: 20%;
    max-height: 50%;
    overflow: auto;
    border-radius: 5px;
	}
	a.download-badge {
    background-color: #cccc;
    color: black;
    padding: 5px 10px;
    border-radius: 1em;
    font-size: .75em;
	}
	#alerta-carregamento {
		display: block;
    position: absolute;
    width: 100%;
    height: 100%;
    background-color: rgba(255,255,255,0.9);
    text-align: center;
    padding: calc(100px - 1em);
    z-index: 9;
    left: 0;
	}
	input.campo-busca {
		height: 2em;
    width: 50%;
    border: 1px solid #EEE;
    border-radius: 15px;
    padding: 15px;
	}
	button.campo-busca {
		padding: 5px 10px;
		border-radius: 20px;
		border: 0;
	}
	.tabela-valores td, .tabela-valores th {
		padding: 6px;
		font-size: 12px;
	}
	.tabela-valores tr:nth-child(even) {
		background-color: #EEEEEE;
	}
	.tabela-valores-container {
		background-color: white;
		z-index: 2;
		padding: 5px;
		margin: 30px 15px;
		max-height: 500px;
		overflow: auto;
	}
	#divGraficoLinha > div {
		min-height: 465px;
	}
</style>
