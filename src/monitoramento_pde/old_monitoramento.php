<?php
/**
 * Template Name: Monitoramento
 */
?>

<script type="text/javascript">
function hexToRgb(hex) {
    var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    return result ? {
        r: parseInt(result[1], 16),
        g: parseInt(result[2], 16),
        b: parseInt(result[3], 16)
    } : null;
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
        // If we want 1 decimal place, we want to mult/div by 10
        // If we want 2 decimal places, we want to mult/div by 100, etc
        // So use the following to create that factor
        var factor = "1" + Array(+(places > 0 && places + 1)).join("0");
        return Math.round(input * factor) / factor;
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

app.controller("dashboard", function($scope, $rootScope, $http, $filter, $uibModal, Indicador, IndicadorValores, Noticia, Menu, AcaoPrioritaria, IndicadorHistorico, GrupoIndicador, FichaTecnicaInstrumento, IndicadorMemoria, VariavelHistorico) {
	
	$scope.tabAtivaForma = 1;
	GrupoIndicador.query({tipo:'instrumento',tipo_retorno:'object',formato_retorno:'array'},function(instrumentos){
		$scope.instrumentos = instrumentos;
	});
	GrupoIndicador.query({tipo:'objetivo',tipo_retorno:'object',formato_retorno:'array'},function(objetivos){
		$scope.objetivos = objetivos;
	});	

	
	$scope.idPoligonoAnterior = 0;
	/*$scope.estiloRealce = function(resolution){ 
		var hexColor = this.get('color');
		
		var corRealce = ol.color.asArray(hexColor);
		corRealce = corRealce.slice();
		corRealce[3] = 0.4;
		
	 return new ol.style.Style(
		{
			fill : new ol.style.Fill(
			{
				color : corRealce
			})
		});
	};*/
	
	$scope.inicializarSelecao = function(){
		$scope.selecao.idTerrSel = 4;
		$scope.idIndicadorAnterior = 0;
	}
	
	$scope.formatarData = function(data){
		dataArray = data.split('-');
		return new Date(dataArray[0],dataArray[1]-1,dataArray[2]);
	};
	
	$scope.atualizarAccordion = function(indicador){
		$scope.selecao.idIndicSel = indicador.id_indicador;
		$scope.inicializarSelecao();
		
		$scope.cargaIndicadorValores(true,true);
	};
	
		$scope.fixarMapa = function(idRegiao){
				
				$scope.clickMapa = true;
				$scope.hoverMapa = true;
				$scope.realcarMapa(idRegiao);
			
		};
		
		$scope.estiloMapa = function(feature,resolution,transparencia, corContorno, contador){
			
			//prefixoCor = '00';
			//sufixoCor = '99';
			
			//corContorno = [255,255,255];
			//corForte = [166,21,0];
			//corFraca = [104,122,127];
			cores = [];
			//cores[0] = [218,235,239];
			cores[4] = [168,28,25];
			cores[3] = [193,42,40];
			cores[2] = [197,72,75];
			cores[1] = [208,121,128];
			cores[0] = [228,184,189];
						
			
			$scope.qtdClasses = 5;
			//qtdCores = 15;
			//qtdCores = 5;
			//incrementoCor = qtdCores / qtdClasses;
			
			regiao = $scope.dadosMapa.filter((regiao) => regiao.codigo == feature.get('ID_REGIAO'))[0];
			
			regiaoMaiorValor = $scope.dadosMapa.filter((regiaoMaior) => regiaoMaior.posicao == 0)[0];
			
			//intervalo = Math.floor($scope.dadosMapa.filter((regiao) => regiao.valor > 0).length / ($scope.qtdClasses - 1));
			intervalo = regiaoMaiorValor.valor / $scope.qtdClasses;
		
			/*if(intervalo == 0){
				intervalo = 1;
			}*/
			//console.log(regiao);
			//console.log(intervalo);
			//indiceClasseRegiao = (regiao.posicao + 1) / intervalo;
			indiceClasseRegiao = regiao.valor / intervalo;
			/*
			if(indiceClasseRegiao > $scope.qtdClasses - 1 && regiao.valor > 0){
				indiceClasseRegiao = $scope.qtdClasses - 1;
			}else{*/
				indiceClasseRegiao = Math.ceil(indiceClasseRegiao);
			/*}*/
			
			if(indiceClasseRegiao > $scope.qtdClasses){
				indiceClasseRegiao = $scope.qtdClasses;
			}
			
			if(indiceClasseRegiao == 0){
				indiceClasseRegiao = 1;
			}
			
			
			//classeRegiao = Math.round(qtdCores - (incrementoCor * indiceClasseRegiao ) );
			//corClasse = indiceCorHexadecimal(classeRegiao);
			
			/*corRegiao = [];
			angular.forEach(corFraca, function(value,key){
				corRegiao[key] = Math.round(corFraca[key] - (((corFraca[key] - corForte[key]) / $scope.qtdClasses) * indiceClasseRegiao ));
			});*/
			
			
			//corRegiao = "#" + prefixoCor + corClasse + corClasse + sufixoCor;
			//corBorda = "#" + '11' + corClasse + corClasse + '88';
			

			
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
					//corRegiao[3] = transparencia;
					
					$scope.legenda.push({
						indice: i,
						//maximo: regiao.valor, 
						//minimo: regiao.valor,
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
				corRegiao[3] = transparencia;
			}else{
				//coloco a cor mais fraca
				corRegiao = cores[0];
				corRegiao[3] = transparencia;
			}
			

			
			/*
			else{
				legenda.maximo = regiao.valor > legenda.maximo ? regiao.valor : legenda.maximo;
				legenda.minimo = regiao.valor < legenda.minimo ? regiao.valor : legenda.minimo;
				legenda.regioes.push(regiao.codigo);
			}*/
			
			//$scope.ajustarLimitesLegenda(indiceClasseRegiao);
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
			
			
			
		}
		
		$scope.estiloRealce = function(resolution){
			corContorno = [255,255,255];
			return $scope.estiloMapa(this,resolution,0.3,corContorno);
		};
		
		$scope.estiloVetor = function(feature, resolution){
			
			corContorno = [255,255,255];
			return $scope.estiloMapa(feature,resolution,1,corContorno);
		};
	
	$scope.realcarMapa = function(idPoligonoAtual){


				
				if(idPoligonoAtual==null){
					
					$scope.cargaIndicadorValores(false,true);
					
					return;
					
				}else{
					
					if(idPoligonoAtual != $scope.idPoligonoAnterior || !$scope.idPoligonoAnterior || $scope.hoverMapa == false){
							
							$scope.idPoligonoAnterior = idPoligonoAtual;
							
							if($scope.selecao.idTerrSel != 4){
								//console.log($scope.dadosMapa);
								$scope.regiaoRealcada = angular.copy($scope.dadosMapa.filter((regiao) => regiao.codigo == idPoligonoAtual)[0], $scope.regiaoRealcada) ;
								
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
								
							}else{
								
								$scope.regiaoRealcada = {
									codigo: 1
									,nome: 'Município'
								};
								
								$scope.carregarGraficoHistorico(1);
								//return;
							};
							
					};
				};
				$scope.hoverMapa = true;
			
		};
		
		$scope.carregarGraficoHistorico = function(idRegiao){
			
			dataHistorica = [];
			dataHistorica['original'] = [];
			dataHistorica['formatada'] = [];
			
			angular.forEach($scope.indicador.datas.slice().reverse(), function(valor,chave){
			if(valor >= $scope.selecao.dataMin && valor <= $scope.selecao.dataMax){
					this['original'].push(valor);
					this['formatada'].push($filter('date')(valor, ($scope.indicador.periodicidade == 'mensal' || $scope.indicador.periodicidade == 'trimestral') ? 'MMM yyyy' : 'yyyy'));
				}
			},dataHistorica);
			
			if(angular.isUndefined($scope.selecao.dataMin)){
				$scope.selecao.dataMin = $scope.indicador.datas[$scope.indicador.datas.length - 1];
			}
			
			if(angular.isUndefined($scope.selecao.dataMax)){
				$scope.selecao.dataMax = $scope.indicador.datas[0] == null ? $scope.indicador.datas[1] : $scope.indicador.datas[0];
			}
			
			IndicadorHistorico.get({id:$scope.selecao.idIndicSel,territorio:$scope.selecao.idTerrSel,regiao:idRegiao,dataMinima:$scope.selecao.dataMin,dataMaxima:$scope.selecao.dataMax},function(indicadorHistorico){
				
				$scope.carregarGraficoLinhas = indicadorHistorico.series.length > 0;
					
				if(!$scope.carregarGraficoLinhas){
					$scope.carregandoHistorico = 'Não há dados históricos disponíveis para essa seleção!';
				}else{
					$scope.carregandoHistorico = null;
				}
				
				if(indicadorHistorico.series.length == 1){
					indicadorHistorico.series[0].showInLegend = false;
				}
				
				larguraGraficoLinha = document.getElementById("divGraficoLinha").clientWidth;
				
				subtitulo = $scope.labelTerrSel + ' - ' +  $scope.regiaoRealcada.nome;
				
				$scope.graficoLinhas = Highcharts.chart('graficoLinhas', {
					chart: {
						type: 'line',
						marginTop: 25,
						width:larguraGraficoLinha
					},
					xAxis: {
						type: "category",
						crosshair: true,
						categories: dataHistorica['formatada']
					},
					series: indicadorHistorico.series,
					tooltip: {
						formatter: function(){
							if($scope.selecao.idTerrSel == 4)
								nomeRegiao = 'Município';
							else
								nomeRegiao = $scope.regiaoRealcada.nome;
							
							textoTooltip = (this.series.chart.series.length > 1 ? '<b>' + nomeRegiao + '</b> <br>' : '');
							
							textoTooltip = textoTooltip + '<b>' + this.series.name + ' :</b> ' + Highcharts.numberFormat(this.y, this.y % 1 == 0 ? 0 : this.y < 100 ? 2 : this.y < 1000 ? 1 : 0,',','.') + ' ' + $scope.indicador.simbolo_valor + '<br>';
							
							if(this.series.chart.series.length > 1){
								varFiltro =	$scope.variavelHistorico.filter((variavel) => (variavel.data == $scope.indicador.datas.slice().reverse()[this.point.x] || variavel.data == null) && (variavel.id_regiao == ($scope.selecao.idTerrSel != 4? $scope.regiaoRealcada.codigo : 1)|| variavel.distribuicao == true) && (variavel.dimensao === this.series.name || (variavel.distribuicao == true && variavel.dimensao == null)));
							}else{
								varFiltro =	$scope.variavelHistorico.filter((variavel) => (variavel.data == $scope.indicador.datas.slice().reverse()[this.point.x] || variavel.data == null) && (variavel.id_regiao == ($scope.selecao.idTerrSel != 4? $scope.regiaoRealcada.codigo : 1)|| variavel.distribuicao == true));
							}
							varFiltroSemDataSemDimensao = $scope.variavelHistorico.filter((variavel) => variavel.data == null && (variavel.id_regiao == $scope.regiaoRealcada.codigo || variavel.distribuicao == true) && variavel.dimensao == null);
							varFiltro = varFiltro.concat(varFiltroSemDataSemDimensao);
							
							if(varFiltro.length > 1){
								angular.forEach(varFiltro, function(val,chave){
									textoTooltip = textoTooltip + ' ' + val.nome + ': ' + Highcharts.numberFormat(val.valor, val.valor % 1 == 0 ? 0 : this.y < 100 ? 2 : val.valor < 1000 ? 1 : 0,',','.') + ' ' + (val.tipo_valor ? val.tipo_valor : '') + '<br>'; 
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
							title:{
								text:$scope.indicador.nome
							},
							credits:{
								enabled: true
								,text: "<br> Atualizado em: "  + $filter('date')($scope.indicador.data_atualizacao, 'MMMM yyyy') + "<br>Fonte:" + $scope.indicador.origem
								,style: {
									fontSize: '7px'
								}
								,position: {
									align: 'left',
									y: -15
								}
							},
							subtitle:{
								text:subtitulo
							},
							legend: {
								itemStyle: {
									//color: '#000000',
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
								}]
							}
						}
					},
					yAxis: {
						labels:{
							formatter: function()
							{
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
							y:-15,
							//margin:-80
						}
					},
					plotOptions: {
						series: {
								point: {
									events: {
										click: function(e){
											if($scope.selecao.idTerrSel != 4){
												$scope.selecao.dataSel = dataHistorica['original'][this.index];
												sairMapa();
												$scope.clickMapa = false;
												$scope.idPoligonoAnterior = 0;
												
												$scope.cargaIndicadorValores(false,true);
											}
										}
									}
								},
								events: {
									mouseOut: function () {
										//sairMapa();
									}
								}
						}
					},
					legend: {
						align: 'left',
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
							
						}else{
							$scope.textoTitulo = $scope.indicador.tipo_valor + ' (' + $scope.indicador.simbolo_valor + ')';
						}
				
				margemTitulo = $scope.textoTitulo.length * -6;
				$scope.graficoLinhas.yAxis[0].setTitle({text: $scope.textoTitulo, margin: margemTitulo});
			});
		};
		
		

	$scope.cargaIndicadorValores = function(inserirMapa, inserirTerritorioMapa){
		
		if(inserirTerritorioMapa)
			$scope.carregandoMapa = 'Aguarde... carregando mapa';
		else
			$scope.carregandoMapa = null;
		
		$scope.indicador = $scope.indicadores.filter((indicador) => indicador.id_indicador == $scope.selecao.idIndicSel)[0];
		
		if($scope.idIndicadorAnterior != $scope.selecao.idIndicSel){
			$scope.selecao.dataSel = $scope.indicador.datas[0];
			$scope.idIndicadorAnterior = $scope.selecao.idIndicSel;
			
			padrao_encontrado = false;
			angular.forEach($scope.indicador.territorios, function(territorio) {
			  if($scope.indicador.id_territorio_padrao == territorio.id_territorio){
				  padrao_encontrado = true;
				  $scope.selecao.idTerrSel = $scope.indicador.id_territorio_padrao;
			  }
			});
			if(padrao_encontrado == false){
				$scope.selecao.idTerrSel = $scope.indicador.territorios[0].id_territorio;
			}
			
			//console.log($scope.selecao.idTerrSel);
			//console.log($scope.indicador.id_territorio_padrao);
			
			$scope.selecao.idTerrSel = $scope.indicador.id_territorio_padrao;

		}
		
		$scope.labelTerrSel = $scope.indicador.territorios.filter((territorio) => territorio.id_territorio == $scope.selecao.idTerrSel)[0].nome;
		
		$scope.hoverMapa = false;
		$scope.clickMapa = false;
		$scope.carregarGraficoLinhas = false;
		
		
		
		IndicadorValores.get({id:$scope.selecao.idIndicSel,data:$scope.selecao.dataSel,territorio:$scope.selecao.idTerrSel},function(indicadorValores){
			
			$scope.indicadorValores = indicadorValores;
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
				colors: ['#FFC300', '#FF5733', '#C70039', '#1B8DD8', '#581845'],
			});
			
			VariavelHistorico.query({id:$scope.selecao.idIndicSel,territorio:$scope.selecao.idTerrSel},function(variavelHistorico){
				$scope.variavelHistorico = variavelHistorico;
			});
			
			if($scope.indicadorValores.series.length == 1){
				$scope.indicadorValores.series[0].showInLegend = false;
			}
			
			habilitarExportacao = $scope.labelTerrSel != 'Distrito';
			
			$scope.graficoBarras = new Highcharts.chart('graficoBarras',{
					chart: {
						type: 'column',
						marginTop: 25
					},
					title: {
						text: null
					},
					xAxis: {
						type: "category",
						crosshair: true,
						categories: $scope.indicadorValores.categorias,
						labels: {
							style: {
								fontSize:9
							}
						}
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
							y:-15,
							//margin:-80
						}
					},
					exporting: {
						enabled:habilitarExportacao
						,chartOptions:{
							title:{
								text:$scope.indicador.nome
							},
							credits:{
								enabled: true,
								text: "<br> Atualizado em: "  + $filter('date')($scope.indicador.data_atualizacao, 'MMMM yyyy') + "<br>Fonte:" + $scope.indicador.origem,
								style:{
									fontSize: '7px'
								},
								position:{
									y:-15
								}
							},
							subtitle:{
								text: $scope.labelTerrSel + "s - Período: " + $filter('date')($scope.selecao.dataSel, ($scope.indicador.periodicidade == 'mensal' || $scope.indicador.periodicidade == 'trimestral') ? 'MMM yyyy' : 'yyyy')
							},
							legend: {
								itemStyle: {
									//color: '#000000',
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
										$scope.exportarGrafico(this,'application/pdf')
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
								}]
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
									
								varFiltro =	$scope.variavelHistorico.filter((variavel) => (variavel.data == $scope.selecao.dataSel || variavel.data == null) && (variavel.id_regiao == $scope.indicadorValores.codigos[this.point.x] || variavel.distribuicao == true) && (variavel.dimensao == this.series.name || (variavel.distribuicao == true && variavel.dimensao == null)));
							}
							//varFiltroSemDataSemDimensao = $scope.variavelHistorico.filter((variavel) => variavel.data == null && (variavel.id_regiao == $scope.indicadorValores.codigos[this.point.x] || variavel.distribuicao == true) && variavel.dimensao == null);
							//varFiltro = varFiltro.concat(varFiltroSemDataSemDimensao);
							
							if(varFiltro.length > 1){
								angular.forEach(varFiltro, function(val,chave){
									textoTooltip = textoTooltip + ' ' + val.nome + ': ' + Highcharts.numberFormat(val.valor, val.valor % 1 == 0 ? 0 : val.valor < 100 ? 2 : val.valor < 1000 ? 1 : 0,',','.') + ' ' + (val.tipo_valor ? val.tipo_valor : '') + '<br>'; 
								});
							}
							return textoTooltip;

						}
						//pointFormat: '<b>{series.name}</b>: {point.y} ' + $scope.indicador.simbolo_valor
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
									},
									events: {
											mouseOut: function () {
													//sairMapa();
											}
									}
						}
					},
					legend: {
						align: 'left',
						layout: 'horizontal',
						itemStyle:{
							fontWeight:'normal'
						}
						//verticalAlign: 'bottom',
						
					},
					style:{
						fontFamily: 'museo_slab500'
					},
					credits:false,
					series: $scope.indicadorValores.series
				});
				
				
				
				if ($scope.graficoBarras.yAxis[0].max >= 1000000000){
					$scope.textoTitulo = $scope.indicador.tipo_valor + ' (Em bilhões de ' + $scope.indicador.simbolo_valor + ')';
					
				}else 
					if ($scope.graficoBarras.yAxis[0].max >= 1000000){
						$scope.textoTitulo = $scope.indicador.tipo_valor + ' (Em milhões de ' + $scope.indicador.simbolo_valor + ')';
						
					}else 
						if ($scope.graficoBarras.yAxis[0].max >= 1000) {
							$scope.textoTitulo = $scope.indicador.tipo_valor + ' (Em milhares de ' + $scope.indicador.simbolo_valor + ')';
							
						}else{
							$scope.textoTitulo = $scope.indicador.tipo_valor + ' (' + $scope.indicador.simbolo_valor + ')';
							
						} 
				
				margemTitulo = $scope.textoTitulo.length * -6;
				$scope.graficoBarras.yAxis[0].setTitle({text: $scope.textoTitulo, margin: margemTitulo});
				
				
				if(inserirTerritorioMapa){
					$scope.layerVetor = new ol.layer.Vector({
						source: new ol.source.Vector({
							loader: function (extent) {
								
								
								$http.jsonp('http://monitoramentopde.smul.pmsp/geoserver/Monitoramento_PDE/ows', {
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
										
									}).catch( function () {
										$scope.carregandoMapa = 'Erro no carregamento do mapa';
									})
							},
							strategy: ol.loadingstrategy.bbox
						})
					});	
					$scope.mapa.addLayer($scope.layerContorno);
					$scope.mapa.addLayer($scope.layerVetor);
				}
	
		});
		
		if(inserirMapa == true){
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
						$scope.clickMapa = false;
					}else{
						
						$scope.fixarMapa(idPoligonoClick);
					};
				};
			});
			/*
			$scope.popup = new ol.Overlay({
				element: document.getElementById('infomapa')
			});
			$scope.mapa.addOverlay($scope.popup);
			*/
			$scope.mapa.on('pointermove', function(evt) {
				if (evt.dragging) {
					return;
				}
				var pixel = $scope.mapa.getEventPixel(evt.originalEvent);
				
				if($scope.selecao.idTerrSel != 4){
					$scope.realcarPorMapa(pixel);
				};
				//$scope.popup.setPosition(evt.coordinate);
			});
			
		};
		
		
		function sairMapa(){

				if($scope.hoverMapa){
					$scope.hoverMapa = false;
					
					$scope.regiaoRealcada.codigo = null;
					$scope.layerVetor.getSource().forEachFeature(function(poligono){
						poligono.setStyle(null);
					});
					
					$scope.layerVetor.setStyle($scope.estiloVetor);
					$scope.layerVetor.getSource().changed();
					
					//$scope.cargaIndicadorValores(false,false);
				};
			
		};
		
		$scope.realcarPorMapa = function(pixel) {

				if($scope.mapa.hasFeatureAtPixel(pixel)){
					
					
					
					poligonoRealcado = $scope.mapa.forEachFeatureAtPixel(pixel, function(poligonoRealcado, layer) {
						return poligonoRealcado;
					});
					
					idPoligonoAtual = poligonoRealcado.get('ID_REGIAO');
					
					if(!$scope.clickMapa){
						$scope.realcarMapa(idPoligonoAtual);
					};
				}else{
					
					if(!$scope.clickMapa){
						sairMapa();
					};
				
			};
		};
		
		
		
		angular.forEach($scope.mapa.getLayers(), function(layer,key){
			if(layer.get('name') != 'OSM'){
				$scope.mapa.removeLayer(layer);
			}
		});
		
		$scope.layerContorno = new ol.layer.Tile({
			source: new ol.source.TileWMS({
				url: 'http://monitoramentopde.smul.pmsp/geoserver/Monitoramento_PDE/wms/reflect', 
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
										}else 
											if (valorMaximoLegenda >= 1000) {
												if(valorMaximoLegenda / 1000.0 <= 8)
													valor = Math.round((valor / 1000.0) * 10.0)/10.0;
												else
													valor = Math.round(valor / 1000.0);
											}
											
			return valor;
		}
		/*
		$scope.ajustarLimitesLegenda = function(){
			
			regiaoMaiorValor = $scope.dadosMapa.filter((regiao) => regiao.posicao == 0)[0];
			valorMaximoLegenda = regiaoMaiorValor.valor;
			
			angular.forEach($scope.legenda, function(legenda, key){
				
				legendaAnterior = $scope.legenda.filter((legendaObj) => legendaObj.indice == legenda.indice-1)[0];
				legendaPosterior = $scope.legenda.filter((legendaObj) => legendaObj.indice == legenda.indice+1)[0];
				if(!angular.isUndefined(legendaAnterior)){
					legenda.maximo = $scope.ajustarEscalaValor(valorMaximoLegenda, legendaAnterior.minimo) - 0.1;
					if(legendaAnterior.minimo == 0)
						legenda.maximo = 0;
				}
				if(!angular.isUndefined(legendaPosterior)){
					legenda.minimo = $scope.ajustarEscalaValor(valorMaximoLegenda, legendaPosterior.maximo) + 0.1;
					if(legendaPosterior.maximo == 0)
						legenda.minimo = 0;
				}
			});
		};
		*/
		$scope.exportarGrafico = function(grafico,formatoArquivo){
			console.log($scope.dadosMapa);
			if($scope.hoverMapa)
			{
				grafico.exportChart({
					type: formatoArquivo
					,filename: $scope.indicador.nome + '_' +$scope.selecao.dataMin + '_a_' + $scope.selecao.dataMax
					,sourceWidth:850
				},{
					chart:{
						backgroundColor: '#FFFFFF'
						,marginTop: 60
					}
				});
			}else
			{
				grafico.exportChart({
					type: formatoArquivo
					,filename: $scope.indicador.nome + '_' +$scope.selecao.dataSel
					,sourceWidth:850
				},{
					chart:{
						backgroundColor: '#FFFFFF'
						,marginTop: 60
					}
				});
			}
		};
		
		$scope.carregarVetor = function(resposta){
			
			format = new ol.format.GeoJSON(),
			$scope.layerVetor.getSource().addFeatures(format.readFeatures(resposta.data));
			
			angular.forEach($scope.indicadorValores.series, function(serie, indiceSerie){
				angular.forEach(serie.data, function(dado, indiceDado){
					//Na primeira série, crio o objeto de dados
					if(indiceSerie == 0){
						$scope.dadosMapa.push({codigo: $scope.indicadorValores.codigos[indiceDado], valor: dado, nome: $scope.indicadorValores.categorias[indiceDado]});
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
			//$scope.ajustarLimitesLegenda();
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
				}
			};
			
			if($scope.selecao.idTerrSel == 4){
				
				$scope.fixarMapa(1);
				return;
			}
			
	};
	
	$scope.cargaEstrategia = function(id){
		GrupoIndicador.get({id:id,tipo:'estrategia',tipo_retorno:'object'},function(estrategia){
			$scope.estrategia = estrategia.propriedades;
			$scope.estrategia.nome = estrategia.nome;
			$scope.estrategia.id_grupo_indicador = estrategia.id_grupo_indicador;
		});
		
		$scope.cargaCadastroIndicadores(id);
	};
	
	$scope.abrirModal = function(tipo){
		if(tipo=='instrumento')
		{
			$rootScope.modalFichaInstrumento = $uibModal.open({
				animation: true,
				ariaLabelledBy: 'modal-titulo-ficha-instrumento',
				ariaDescribedBy: 'modal-corpo-ficha-instrumento',
				templateUrl: 'ModalFichaInstrumento.html',
				controller: function($scope){},
				scope:$scope,
				//controllerAs: '$ctrl',
				size: 'lg',
				//appendTo: parentElem,
				//resolve: {
				//	items: function () {
				//		return $ctrl.items;
				//	}
				//}
			});
			GrupoIndicador.get({id:$scope.indicador.id_instrumento,tipo:'instrumento',tipo_retorno:'object'},function(fichaInstrumento){
				
			 $scope.fichaInstrumento = fichaInstrumento;
			 
			});
		}
		else
		{
			if(tipo=='indicador')
			{
				$rootScope.modalFichaIndicador = $uibModal.open({
					animation: true,
					ariaLabelledBy: 'modal-titulo-ficha-indicador',
					ariaDescribedBy: 'modal-corpo-ficha-indicador',
					templateUrl: 'ModalFichaIndicador.html',
					controller: function($scope){},
					scope:$scope,
					//controllerAs: '$ctrl',
					size: 'lg',
					//appendTo: parentElem,
					//resolve: {
					//	items: function () {
					//		return $ctrl.items;
					//	}
					//}
				});
			}else
			{
				if(tipo=='estrategia')
				{
					$rootScope.modalFichaEstrategia = $uibModal.open({
						animation: true,
						ariaLabelledBy: 'modal-titulo-ficha-estrategia',
						ariaDescribedBy: 'modal-corpo-ficha-estrategia',
						templateUrl: 'ModalFichaEstrategia.html',
						controller: function($scope){},
						scope:$scope,
						//controllerAs: '$ctrl',
						size: 'lg',
						//appendTo: parentElem,
						//resolve: {
						//	items: function () {
						//		return $ctrl.items;
						//	}
						//}
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
		var wsNomeMemoria = "Memoria";
		var wsNomeMetadado = "Metadado";	
		
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
			var wb = new Workbook();
			var wsMemoria = sheet_from_array_of_arrays($scope.memoriaIndicador.dados,$scope.memoriaIndicador.qtd_variavel);
			
			var wsMetadado = {};
			coluna = 0, linha = 0;
			wsMetadado[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,$scope.indicador.nome);
			linha = linha + 2;
			wsMetadado[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,$scope.indicador.apresentacao);
			linha = linha + 2;
			wsMetadado[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,'Instrumento de política urbana e ambiental');
			linha++;
			wsMetadado[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,$scope.indicador.instrumento);
			linha = linha + 2;
			wsMetadado[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,'Estratégias');
			linha++;
			angular.forEach($scope.indicador.estrategias,function(estrategia,chave){
				wsMetadado[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,estrategia.nome);
				linha++;
			});
			linha++;
			wsMetadado[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,'Fórmula de cálculo');
			linha++;
			wsMetadado[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,$scope.indicador.formula_calculo);
			linha = linha + 2;
			wsMetadado[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,'Unidade de medida');
			linha++;
			wsMetadado[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,$scope.indicador.tipo_valor);
			linha = linha + 2;
			wsMetadado[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,'Fonte');
			linha++;
			wsMetadado[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,$scope.indicador.fonte);
			linha = linha + 2;
			wsMetadado[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,'Periodicidade de atualização');
			linha++;
			wsMetadado[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,$scope.indicador.periodicidade);
			linha = linha + 2;
			wsMetadado[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,'Unidades de análise territorial');
			linha++;
			angular.forEach($scope.indicador.territorios,function(valor,chave){
				wsMetadado[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,valor.nome);
				linha++;
			});
			
			var range = {s: {c:0, r:0}, e: {c:1, r:30 }};
			wsMetadado['!ref'] = XLSX.utils.encode_range(range);
			
			/* add worksheet to workbook */
			wb.SheetNames.push(wsNomeMetadado);
			wb.SheetNames.push(wsNomeMemoria);
			wb.Sheets[wsNomeMetadado] = wsMetadado;
			wb.Sheets[wsNomeMemoria] = wsMemoria;
			var wbout = XLSX.write(wb, {bookType:'xlsx', bookSST:false, type: 'binary'});
			
			saveAs(new Blob([s2ab(wbout)],{type:"application/octet-stream"}), $scope.indicador.nome + ".xlsx");
			
		});
	}
	
	$scope.cargaCadastroIndicadores = function(id){
		
	  Indicador.query({grupo_indicador:id,somente_ativos:true},function(indicadores) {
		  $scope.indicadores = indicadores;
	 });
	}
	
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
	 }
	 
});	

</script>

<div id="conteudo" data-ng-app="monitoramentoPde" data-ng-controller="dashboard">

<script type="text/ng-template" id="ModalFichaInstrumento.html">

<div class="modal-instrumento">
	<div class="modal-header">
    <h3 class="modal-title" id="modal-titulo-ficha-instrumento">{{indicador.instrumento}} <button class="btn btn-danger pull-right" type="button" ng-click="fecharModal('instrumento')">X</button></h3> 
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
					<a class="link-saiba-mais link-saiba-mais-indicador" ng-click="exportarMemoria()">Memória de cálculo do indicador </a>
				</div>
				<div class="col-sm-6">
					<div class="row" style="width:100%;max-height:30px;">
						<!--<a href="#/compartilhar" class="pull-right"><img class="img-responsive icones-indicador" src="/app/themes/monitoramento_pde/images/icones/compartilhar.png"></a>-->
						<!--<a href="" ng-click="exportarGrafico('application/pdf')" class="pull-right"><img class="img-responsive icones-indicador" src="/app/themes/monitoramento_pde/images/icones/pdf.png"></a>-->
						<!--<a href="" ng-click="exportarGrafico('image/jpeg')" class="pull-right"><img class="img-responsive icones-indicador" src="/app/themes/monitoramento_pde/images/icones/jpg.png"></a>-->
						<!--<a href="" ng-click="exportarMemoria()" class="pull-right"><img class="img-responsive icones-indicador" src="/app/themes/monitoramento_pde/images/icones/odt.png"></a>-->
					</div>
					<div class="row">
						<div class="col-sm-5 col-sm-offset-1 caixa-data">
						
							<p ng-if="hoverMapa && (indicador.datas.length > 0 || indicador.datas[0]) ">
							
								<label for="data"> Data inicial:</label>
								<br>
								<select style="max-width:100%;" data-ng-model="selecao.dataMin" data-ng-options="data as (formatarData(data) | date: indicador.periodicidade == 'anual' ? 'yyyy' : 'MMMM yyyy') for data in indicador.datas | filter:'' " data-ng-change="ajustarDataFinal();carregarGraficoHistorico(regiaoRealcada.codigo);" name="dataInicial"></select>
							</p>
							<p ng-if="hoverMapa && (indicador.datas.length > 0 || indicador.datas[0]) ">
								<label for="data"> Data final: </label>
								<br>
								<select style="max-width:100%;" data-ng-model="selecao.dataMax" data-ng-options="data as (formatarData(data) | date: indicador.periodicidade == 'anual' ? 'yyyy' : 'MMMM yyyy') for data in indicador.datas | filter:'' | dataFinal: selecao.dataMin " data-ng-change="carregarGraficoHistorico(regiaoRealcada.codigo)" name="dataFinal"></select>
							</p>
							
							<p ng-if="!hoverMapa && (indicador.datas.length > 0 && indicador.datas[0]) ">
								<label for="data"> Data</label>
								<br>
								<select style="max-width:100%;" data-ng-model="selecao.dataSel" data-ng-options="data as (formatarData(data) | date: indicador.periodicidade == 'anual' ? 'yyyy' : 'MMMM yyyy') for data in indicador.datas | filter:'' " data-ng-change="cargaIndicadorValores(false,true)" name="periodo"></select>
							</p>
						</div>
						<div class="col-sm-6">
							<p>
								<label for="territorio"> Unidade territorial de análise</label>
								<br>
								<select style="max-width:100%;width:100%;" data-ng-model="selecao.idTerrSel" data-ng-options="territorio.id_territorio as territorio.nome for territorio in indicador.territorios | orderBy: 'ordem'" data-ng-change="cargaIndicadorValores(false,true);atribuirRegiaoSemSelecao()" name="territorio"></select>
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
					<div class="row" id="divGraficoLinha">
					
						<div id="graficoBarras" ng-show="!hoverMapa" style="min-height:465px;">
						</div>
						<div ng-if="hoverMapa && !carregarGraficoLinhas" style="display:flex;align-items:center;min-height:465px;margin-left:120px;">
							<h4 style="font-size:12px;" class="alert alert-danger">{{carregandoHistorico}}</h4>
						</div>
						<div  id="graficoLinhas" ng-show="hoverMapa && carregarGraficoLinhas" style="min-height:465px;">
						</div>
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
							<small ng-if="indicador.data_atualizacao != null">Atualizado em: <span style="text-transform:capitalize">{{indicador.data_atualizacao | date: 'MMMM yyyy'}}</span> </small>
						</div>
					</div>
				</div>
				<div class="col-sm-3" ng-show="selecao.idTerrSel != 4">
					<div class="row">
						<div id="map" style="min-height:400px;"> <span ng-if="carregandoMapa">{{carregandoMapa}}</span></div>
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
	
	<div class="container">
	
	<p> 
			<div class="well" style="background-color:rgb(91,192,222);color:white;font-weight:bold;text-align:center;"> A plataforma de Monitoramento e Avaliação da Implementação do Plano Diretor Estratégico está em processo de desenvolvimento e pode apresentar instabilidades de navegação durante este período
				</div>
		 
	</p>
		
		<hr>
		
		<p> Escolha a forma como deseja visualizar os indicadores </p>
		<hr>
				
		<uib-tabset active="tabAtivaForma" type="pills">
			<uib-tab index="$index + 1" ng-repeat="item in menuForma.items" heading="{{item.title}}" classes="{{item.classes}}">
				<hr>
				
				<p ng-show="tabAtivaForma==1">
					<ul class="list-group row">
						<li class="list-group-item col-sm-3 text-left list-visualizacao" ng-mouseover="this.hover=true" ng-mouseleave="this.hover=false" style="width:20%;" ng-repeat="itemFilho in item.children"><a href=""  ng-click="cargaEstrategia(itemFilho.url.slice(1,itemFilho.url.length))"> <img class="img-responsive icones-visualizacao col-sm-3"  ng-src="/app/themes/monitoramento_pde/images/icones/{{itemFilho.description + ((itemFilho.url.slice(1,itemFilho.url.length) == estrategia.id_grupo_indicador || this.hover)? '_cor' : '_pb')}}.png"><span class="col-sm-12" style="padding:0"><br><strong>{{itemFilho.title}}</strong></span></a> </li>
							
					</ul>
				</p>
				
				<p ng-show="tabAtivaForma==2">	
					Os Instrumentos de Política Urbana e Gestão Ambiental são meios para viabilizar a efetivação dos princípios e objetivos do Plano Diretor. <br><br> Veja abaixo a lista dos instrumentos:<br><br>
					<select style="min-width:250px;max-width:400px;" data-ng-model="optInstrumento" data-ng-options="instrumento.id_grupo_indicador as instrumento.nome for instrumento in instrumentos | orderBy: '-nome' : true" ng-change="cargaCadastroIndicadores(optInstrumento)"><option value="">Todos</option></select>
				</p>
				
				<p ng-show="tabAtivaForma==3">	
					Os objetivos mostram os objetivos do Plano Diretor. <br><br> Veja abaixo a lista dos objetivos:<br><br>
					<select style="min-width:250px;max-width:400px;" data-ng-model="optObjetivo" data-ng-options="objetivo.id_grupo_indicador as objetivo.nome for objetivo in objetivos | orderBy: '-nome' : true" ng-change="cargaCadastroIndicadores(optObjetivo)"><option value="">Todos</option></select>
				</p>
			</uib-tab>
		</uib-tabset>
		
		<span ng-show="tabAtivaForma==1">
			<hr>

				<h4 class="titulo-forma-visu">{{estrategia.nome}}</h3>

			<div class="row" >
				<div class="col-sm-6 col-xs-12" >  
				<p >{{estrategia.descricao}} </p>
				<h4 id="saiba-mais"><a href="" class="link-saiba-mais" ng-click="abrirModal('estrategia')"> Saiba mais sobre essa estratégia </a></h4>
				</div><!--
				--><div class="col-sm-6 col-xs-12" > 
					
					<div><a href="" ng-click="abrirModal('estrategia')"><img class="img-responsive" src="{{estrategia.link_imagem}}" alt="{{estrategia.nome}}"></a></div>
					<!--<div class="row">
						<a class="col-md-3 link-saiba-mais text-center" href="{{estrategia.link_texto_lei}}" target="_blank"><img class="img-responsive icones-saiba-mais" src="/app/themes/monitoramento_pde/images/icones/texto-lei.png"> Texto da lei ilustrado </a>
						<a class="col-md-3 link-saiba-mais text-center" href="{{estrategia.link_perguntas_respostas}}" target="_blank"><img class="img-responsive icones-saiba-mais" src="/app/themes/monitoramento_pde/images/icones/perguntas-respostas.png"> Perguntas e Respostas </a>
						<a class="col-md-3 link-saiba-mais text-center" href="{{estrategia.link_infografico}}" target="_blank"><img class="img-responsive icones-saiba-mais" src="/app/themes/monitoramento_pde/images/icones/infografico.png"> Infográficos </a>
						<a class="col-md-2 link-saiba-mais text-center" href="{{estrategia.link_video}}" target="_blank"><img class="img-responsive icones-saiba-mais" src="/app/themes/monitoramento_pde/images/icones/video.png"> Vídeo </a>
					</div>-->
				</div>
			</div>
		</span>
		
		<hr>
		<h4> <strong>Indicadores </strong></h4>
		
		<uib-accordion close-others="true">

			<div uib-accordion-group is-open="indicador.aberto" class="panel-default" close-others="true" ng-repeat="indicador in indicadores">
				<uib-accordion-heading>
					<span ng-class="indicador.homologacao? 'header-painel-indicadores-homolog' : 'header-painel-indicadores'" > {{indicador.nome}} <br> <small>Instrumento: {{indicador.instrumento}} </small>
						<i class="pull-right glyphicon" ng-class="{'glyphicon-chevron-up': indicador.aberto, 'glyphicon-chevron-down': !indicador.aberto}"></i>
					
					</span>
				</uib-accordion-heading>
					<div ng-include onload="atualizarAccordion(indicador);atribuirRegiaoSemSelecao()" src="indicador.aberto ? 'indicador.html' : ''"></div>
			<!--<div uib-accordion-group class="panel-default"  heading=" {{indicador.nome}} &nbsp; | &nbsp; Instrumento: {{indicador.nome_fonte_dados}}"  ng-repeat="indicador in indicadores">-->
			
			</div>
		</uib-accordion>
		
		
		
		<!--<div class="row" style="margin:0;">
			
			<hr>
			<div class="row">
				<div class="row">
					<div class="col-sm-11" style="display:inline-block;float:none;">
						<h4 style="padding-left:15px;"> <strong> Ações Prioritárias </strong> </h4>
					</div>--><!--
					--><!--<div class="col-sm-1 text-center" style="display:inline-block;float:none;vertical-align:middle;padding-right:30px;"> 
						<strong> Situação </strong>
					</div>
				</div>
				<ul class="list-group">
					<li class="list-group-item row list-pontilhada" data-ng-repeat="acao in acoesPrioritarias | orderBy: 'artigo' | limitTo:5 ">
						<div class="col-sm-10 acao-prioritaria"> {{acao.nome}} </div>
						<div class="col-sm-1 acao-prioritaria text-center"> {{acao.artigo}} </div>
						<div class="col-sm-1 acao-prioritaria text-center"> {{acao.andamento}}</div>
					</li>
				</ul>
			</div>
			<a class="link-saiba-mais link-saiba-mais-indicador" href="/acoes-prioritarias"><span class="glyphicon glyphicon-plus-sign"></span> Clique aqui para ver todas as ações prioritárias </a>
			
		</div>-->
		
	</div>

</div>
