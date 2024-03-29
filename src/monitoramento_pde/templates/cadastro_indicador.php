<?php
/**
 * Template Name: Cadastro Indicadores
 */
?>


<script type="text/javascript">
jQuery.noConflict();

var app = angular.module('monitoramentoPde', ['ngResource','ngAnimate','ui.bootstrap','angular.filter','angularjs-dropdown-multiselect']);

app.factory('Indicador',function($resource){
	return $resource('/wp-json/monitoramento_pde/v1/indicador/:id',{id:'@id_indicador'},{
		update:{
			headers:{
				'X-WP-Nonce': '<?php  echo(wp_create_nonce('wp_rest')); ?>'
					}
			,method:'PUT'
		},get:{
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

app.factory('IndicadorValores',function($resource){
	return $resource('/wp-json/monitoramento_pde/v1/indicador/valores/:id',{id:'@id_indicador'},{
		update:{
			method:'PUT'
		}
	});
});

app.factory('Variavel',function($resource){
	return $resource('/wp-json/monitoramento_pde/v1/variavel/:id');
});

app.factory('IndicadorComposicao',function($resource){
	return $resource('/wp-json/monitoramento_pde/v1/indicador_composicao/:id',{id:'@id_indicador'},{
			update:{
				method:'PUT'
			}
	});
});

app.factory('Instrumentos',function($resource){
	return $resource('/wp-json/monitoramento_pde/v1/instrumentos');
});

app.factory('Estrategias',function($resource){
	return $resource('/wp-json/monitoramento_pde/v1/grupo_indicador');
});

app.factory('Objetivos',function($resource){
	return $resource('/wp-json/monitoramento_pde/v1/grupo_indicador');
});

app.factory('ObjetivoIndicador',function($resource){
	return $resource('/wp-json/monitoramento_pde/v1/objetivo_indicador');
});

app.factory('Territorios',function($resource){
	return $resource('/wp-json/monitoramento_pde/v1/territorios');
});

app.factory('FontesDados',function($resource){
	return $resource('/wp-json/monitoramento_pde/v1/fontes_dados');
});

var escopo = {};
var primeiroRender = true;
let estrategiasDoIndicador = [];
let trocouSegundaEstrategia = false;

app.controller("cadastroIndicador", function($scope, $rootScope, $http, $filter, $uibModal, Indicador, Instrumentos, Territorios, IndicadorComposicao, Variavel, IndicadorValores, Estrategias, Objetivos, ObjetivoIndicador, FontesDados) {
 
 	Indicador.query(function(indicadores) {
		$rootScope.listaIndicadores = indicadores;
		$rootScope.indicadores = indicadores;
		// INDICADORES CARREGADOS. CHAMA FUNÇÃO PARA VERIFICAR SE URL ENCAMINHA PARA UM INDICADOR ESPECÍFICO
		$scope.urlIndicador();
	});
	
	 Variavel.query(function(variaveis) {
		 $scope.variaveis = variaveis;
	});
	
	Estrategias.query({tipo:'estrategia',tipo_retorno:'object',formato_retorno:'array'},function(estrategias) {
		$scope.estrategias = estrategias;
	});
	
	FontesDados.query(function(fontesDados){
		$scope.fontesDados = fontesDados;
	});
	
	Objetivos.query({tipo:'objetivo',tipo_retorno:'object',formato_retorno:'array'},function(objetivos) {
		$scope.objetivos = objetivos;
	});
	
	Instrumentos.query(function(instrumentos){
		$scope.instrumentos = instrumentos;
	});	
	
	$scope.mensagemCalculo = "O indicador será calculado e armazenado na base de dados";
	
	Territorios.query(function(territorios){
		$scope.territorios = territorios;
		$scope.territorios_dropdown = [];
		
		angular.forEach($scope.territorios,function(valor,chave){
			$scope.territorios_dropdown.push( {id: valor.id_territorio, label: valor.nome});
		});
		
	});
	
	$scope.estado = "listar";
	escopo = $scope;

	// Verifica se obteve URL de um indicador específico e carrega o indicador na tela
	$scope.urlIndicador = function(){
		let computedUrl = window.location.href;
		if(primeiroRender && computedUrl.includes("mostra_indicador")){
			let indicadorFromUrl = parseInt(computedUrl.split("mostra_indicador/").pop());
			$scope.idIndicadorAtivo = indicadorFromUrl;
			primeiroRender = false;
			$scope.carregarIndicador();
		}
	}

	$scope.geraLink = function() {
		let link = "<?php echo get_home_url(); ?>"+"/cadastro-de-indicadores/#/mostra_indicador/"+$scope.idIndicadorAtivo;
		
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
		window.alert("O link para o Cadastro do Indicador "+$scope.idIndicadorAtivo+" foi copiado para a área de transferência.\n"+link);
	}
	
	$scope.lancarErro = function(erro){
		alert('Ocorreu um erro ao atualizar o indicador. \n\n Código: ' + erro.data.code + '\n\n Status: ' + erro.statusText + '\n\n Mensagem: ' + erro.data + '\n\n Mensagem Interna: ' + erro.data.message);
	}

	$scope.trocaSegundaEstrategia = function() {
		trocaSegundaEstrategia = true;
	}

	$scope.corrigeOrdemEstrategias = function() {
		if(trocouSegundaEstrategia)
			$scope.indicadorAtivo.estrategias = [$scope.indicadorAtivo.estrategias[1], $scope.indicadorAtivo.estrategias[0]];
	}
	
	$scope.filtrarInstrumento = function(){
		if($scope.idInstrumentoAtivo !== null){
			$rootScope.indicadores = $rootScope.listaIndicadores.filter((indicador) => indicador.id_instrumento === $scope.idInstrumentoAtivo);
		}
		else
			$rootScope.indicadores = $rootScope.listaIndicadores;
	}
	
	$scope.carregarIndicador = function(){
		// CARREGA TODOS OS DADOS DO INDICADOR ANTES DE REALIZAR OPERAÇÕES
		Indicador.query({id:$scope.idIndicadorAtivo}, function(indicadorRetornado){
			$scope.indicadorAtivo = indicadorRetornado[0];

			// estrategiasDoIndicador = $scope.indicadorAtivo.estrategias?.map((valor) => { return valor });
			estrategiasDoIndicador = $scope.indicadorAtivo.estrategias;
			objetivosDoIndicador = $scope.indicadorAtivo.objetivos;
			if (objetivosDoIndicador === null) {
				objetivosDoIndicador = [];
			}
			window.setTimeout(() => { 
				$scope.indicadorAtivo.estrategias = estrategiasDoIndicador;
				$scope.indicadorAtivo.objetivos = objetivosDoIndicador;
				$scope.$apply();
			}, 500);
			
			if($scope.indicadorComposicao)
				$scope.indicadorComposicao.id_fonte_dados = null;
			$scope.indicadorAtivo = $rootScope.indicadores.filter((indicador) => indicador.id_indicador == $scope.idIndicadorAtivo)[0];
			// if($scope.indicadorAtivo){
			// 	$scope.indicadorAtivo.territorio_exclusao = $scope.indicadorAtivo.territorio_exclusao.filter((exc) => exc.id);		
			// 	if(!$scope.indicadorAtivo.territorio_exclusao || $scope.indicadorAtivo.territorio_exclusao.length === 0){
			// 		$scope.indicadorAtivo.territorio_exclusao = [];
			// 	}else{
			// 		if(!$scope.indicadorAtivo.territorio_exclusao[0].id)
			// 			$scope.indicadorAtivo.territorio_exclusao = [];
			// 	}
			// }

			if($scope.indicadorAtivo != null){
				IndicadorComposicao.query({id:$scope.indicadorAtivo.id_indicador},function(indicadorComposicao){
					$scope.indicadorComposicao = indicadorComposicao;
					angular.forEach($scope.indicadorComposicao,function(comp,chave){
						comp.variaveis = $scope.variaveis;
					});
					$scope.estado = "selecionar";
				});
				// Puxa Objetivos referentes ao indicador
				// Indicador.query({grupo_indicador:$scope.indicadorAtivo.id_indicador,somente_ativos:true},function(indicadores) {
				// 	$scope.indicadores = indicadores;
				// });
				ObjetivoIndicador.query({id:$scope.indicadorAtivo.id_indicador}, function(objetivoIndicador){
					if(objetivoIndicador[0])
						$scope.indicadorAtivo.id_objetivo = objetivoIndicador[0].id_grupo_indicador;
				});
			}
			else
				$scope.indicadorComposicao = null;
		});
	};
	
	$scope.adicionarElemento = function(){
		$scope.indicadorComposicao.push({variaveis: $scope.variaveis});
		
	};
	
	$scope.adicionarObjetivo = function(){
		const obj = {
			id_grupo_indicador: null,
			nome: null,
			ordem: null,
		};
		$scope.indicadorAtivo.objetivos.push(obj);		
	};
	
	$scope.criarModalConfirmacao = function(acao){
		$rootScope.modalConfirmacao = $uibModal.open({
			animation: true,
			ariaLabelledBy: 'modal-titulo-indicador',
			ariaDescribedBy: 'modal-corpo-indicador',
			templateUrl: 'ModalConfirmacao.html',
			controller: 'cadastroIndicador',
			scope:$scope,
			size: 'md',
		});
		$scope.acao = acao;
	};
	
	$scope.criarModalSucesso = function(){
		$rootScope.modalSucesso = $uibModal.open({
			animation: true,
			ariaLabelledBy: 'modal-titulo-indicador',
			ariaDescribedBy: 'modal-corpo-indicador',
			templateUrl: 'ModalSucesso.html',
			controller: 'cadastroIndicador',
			scope:$scope.parent,
			size: 'md',
		});
	};
	
	$scope.limparForm = function(){
		$scope.indicadorAtivo = {estrategias: [], objetivos: []};
		$scope.indicadorComposicao = [];
		$scope.estado = "inserir";
	};

// ISSUE #27 - Ao atualizar um indicador, ou variável ou fonte de dados, é necessário dar F5
	// $scope.delayedRefresh = function(){
	// 	window.setTimeout(function(){
	// 		$scope.filtrarInstrumento();
	// 	}, 1500);
	// }
	$scope.delayedRefresh = function() {
		window.setTimeout(function(){
			document.getElementById('delayedRefreshBt').click();
		}, 3000);
	}

	$scope.atualizar = function(){
		IndicadorComposicao.update({composicao:$scope.indicadorComposicao,id_indicador:$scope.indicadorAtivo.id_indicador}).$promise.then(
			function(mensagem){
				Indicador.update({indicador:$scope.indicadorAtivo,usuario:<?php $usrObj = wp_get_current_user(); echo json_encode($usrObj); ?>}).$promise.then(
					function(mensagem){
						Indicador.query(function(indicadores) {
							$rootScope.listaIndicadores = indicadores;
							$scope.delayedRefresh();
							$rootScope.modalProcessando.close();		
							$scope.criarModalSucesso();
						});
					},
					function(erro){
						$rootScope.modalProcessando.close();
						$scope.lancarErro(erro);
					}
				);
			},
			function(erro){
				$rootScope.modalProcessando.close();
				$scope.lancarErro(erro);
			}
		);
	};		
	
	/*$scope.calcular = function(){
		IndicadorValores.update({id_indicador:$scope.indicadorAtivo.id_indicador}).$promise.then(
			function(mensagem){
				alert('O indicador foi calculado com sucesso.');
				IndicadorValores.update(function(indicadores) {
					$rootScope.indicadores = indicadores;
				});

			},
			function(erro){
				alert('Ocorreu um erro ao atualizar o indicador. Mensagem de erro:\n\n' + erro.data);
			}
		);
		
	};*/
	
	$scope.remover = function(){
		IndicadorComposicao.remove({id:$scope.indicadorAtivo.id_indicador}).$promise.then(
			function(mensagem){
				Indicador.remove({id:$scope.indicadorAtivo.id_indicador,usuario:<?php $usrObj = wp_get_current_user(); echo json_encode($usrObj); ?>}).$promise.then(
					function(mensagem){

						Indicador.query(function(indicadores) {
							$rootScope.indicadores = indicadores;
							$rootScope.filtrarInstrumento();
							
							$rootScope.modalProcessando.close();		
							$scope.criarModalSucesso();
						});
						
					},
					function(erro){
						$rootScope.modalProcessando.close();
						$scope.lancarErro(erro);
					}
				);
			},
			function(erro){
				$rootScope.modalProcessando.close();
				$scope.lancarErro(erro);
			}
		);
		
		$scope.$parent.idIndicadorAtivo = null;	
		$scope.$parent.estado = "listar";
	};	

	$scope.inserir = function(){
		$rootScope.indicadorComposicao = $scope.indicadorComposicao;
		Indicador.save({indicador:$scope.indicadorAtivo,usuario:<?php $usrObj = wp_get_current_user(); echo json_encode($usrObj); ?>}).$promise.then(
			function(mensagem){
				IndicadorComposicao.save({composicao:$rootScope.indicadorComposicao,id_indicador:mensagem.id_indicador}).$promise.then(
						function(mensagem){

							Indicador.query(function(indicadores) {
								$rootScope.indicadores = indicadores;
								$scope.filtrarInstrumento();
								
								$rootScope.modalProcessando.close();		
								$scope.criarModalSucesso();
							});
						},
						function(erro){
							$rootScope.modalProcessando.close();
							console.log("indicadorcomp.save");
							$scope.lancarErro(erro);
						}
				);
				Indicador.query(function(indicadores) {
					$rootScope.indicadores = indicadores;
					$scope.filtrarInstrumento();
				});
			},
			function(erro){
				$rootScope.modalProcessando.close();
				$scope.lancarErro(erro);
			}
		);

		$scope.$parent.estado = "listar";
		// $scope.carregarIndicador();
	};	

	$scope.voltar = function(){
		$scope.idIndicadorAtivo = null;
		$scope.estado = "listar";	
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
				ariaLabelledBy: 'modal-titulo-indicador',
				ariaDescribedBy: 'modal-corpo-indicador',
				templateUrl: 'ModalProcessando.html',
				controller: 'cadastroIndicador',
				scope:$scope,
				size: 'md',
		});
				
		if($scope.acao == 'Atualizar'){
			$scope.acaoExecutando = 'Atualizando';
			$scope.acaoSucesso = 'Atualizado';
			$scope.atualizar();
			
		}else{
			if($scope.acao == 'Remover'){	
				$scope.acaoExecutando = 'Removendo';
				$scope.acaoSucesso = 'Removido';
				$scope.remover();
				
			}else{
				if($scope.acao == 'Inserir'){	
					$scope.acaoExecutando = 'Inserindo';
					$scope.acaoSucesso = 'Inserido';
					$scope.inserir();
					
				}else{
					if($scope.acao == 'Calcular'){
						$scope.acaoExecutando = 'Calculando';
						$scope.acaoSucesso = 'Calculado';
						$scope.calcular();
					}
				}
			}
		}
	};	
	
	$scope.desabilitarAtivo = function(){
		if($scope.indicadorAtivo.homologacao)
			$scope.indicadorAtivo.ativo = false;
	};
	
	$scope.desabilitarHomologacao = function(){
		if($scope.indicadorAtivo.ativo)
			$scope.indicadorAtivo.homologacao = false;
	};
	
	
	
	$scope.deletarElemento = function(indice){
		$scope.indicadorComposicao.splice(indice,1);
	};

	$scope.deletarObjetivo = function(indice){
		$scope.indicadorAtivo.objetivos.splice(indice,1);
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
	// P1.5 Ao exportar a relação de indicadores, é preciso criar uma coluna informando se o indicador está ativo / homologação / inativo
	$scope.exportarIndicadores = function(){
			var wb = new Workbook();
			var wsindicador = {};
			
			//criando cabeçalho
			wsindicador[XLSX.utils.encode_cell({c:0 ,r:0})] = criarCelula(0 ,0,'ID');
			wsindicador[XLSX.utils.encode_cell({c:1 ,r:0})] = criarCelula(1 ,0,'Nome');
			wsindicador[XLSX.utils.encode_cell({c:2 ,r:0})] = criarCelula(2 ,0,'Descrição Síntese');
			wsindicador[XLSX.utils.encode_cell({c:3 ,r:0})] = criarCelula(3 ,0,'Descrição Completa');
			wsindicador[XLSX.utils.encode_cell({c:4 ,r:0})] = criarCelula(4 ,0,'Nota técnica');
			wsindicador[XLSX.utils.encode_cell({c:5 ,r:0})] = criarCelula(5 ,0,'Instrumento de política urbana e ambiental');
			wsindicador[XLSX.utils.encode_cell({c:6 ,r:0})] = criarCelula(6 ,0,'Estratégia 1');
			wsindicador[XLSX.utils.encode_cell({c:7 ,r:0})] = criarCelula(7 ,0,'Estratégia 2');
			wsindicador[XLSX.utils.encode_cell({c:8 ,r:0})] = criarCelula(8 ,0,'Fórmula de cálculo');
			wsindicador[XLSX.utils.encode_cell({c:9 ,r:0})] = criarCelula(9 ,0,'Unidade de medida');
			wsindicador[XLSX.utils.encode_cell({c:10,r:0})] = criarCelula(10,0,'Fonte');
			wsindicador[XLSX.utils.encode_cell({c:11,r:0})] = criarCelula(11,0,'Periodicidade de atualização');
			wsindicador[XLSX.utils.encode_cell({c:12,r:0})] = criarCelula(12,0,'Série histórica');
			wsindicador[XLSX.utils.encode_cell({c:13,r:0})] = criarCelula(13,0,'Município');
			wsindicador[XLSX.utils.encode_cell({c:14,r:0})] = criarCelula(14,0,'Macrorregião');
			wsindicador[XLSX.utils.encode_cell({c:15,r:0})] = criarCelula(15,0,'Macroárea');
			wsindicador[XLSX.utils.encode_cell({c:16,r:0})] = criarCelula(16,0,'Prefeitura Regional');
			wsindicador[XLSX.utils.encode_cell({c:17,r:0})] = criarCelula(17,0,'Distrito');
			wsindicador[XLSX.utils.encode_cell({c:18,r:0})] = criarCelula(18,0,'Perímetro de incentivo');
			wsindicador[XLSX.utils.encode_cell({c:19,r:0})] = criarCelula(19,0,'ZDE e ZPI');
			wsindicador[XLSX.utils.encode_cell({c:20,r:0})] = criarCelula(20,0,'Operação Urbana Consorciada');
			wsindicador[XLSX.utils.encode_cell({c:21,r:0})] = criarCelula(21,0,'ZEIS');
			wsindicador[XLSX.utils.encode_cell({c:22,r:0})] = criarCelula(22,0,'ZEPEC');
			wsindicador[XLSX.utils.encode_cell({c:23,r:0})] = criarCelula(23,0,'Status'); // ATIVO / HOMOLOGAÇÃO / INATIVO [ativo (boolean), homologacao (boolean)]
			                                                              
			linha = 1;
			
			angular.forEach($scope.indicadores,function(indicador,chave){
				coluna = 0;
				wsindicador[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,indicador.id_indicador);
				coluna++;
				wsindicador[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,indicador.nome);
				coluna++;
				wsindicador[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,indicador.apresentacao);
				coluna++;
				wsindicador[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,indicador.nota_tecnica_resumida);
				coluna++;
				wsindicador[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,indicador.nota_tecnica);
				coluna++;
				wsindicador[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,indicador.instrumento);
				coluna++;
				colunaEstrategia = coluna + 2;
				angular.forEach(indicador.estrategias,function(estrategia,chave){
					if(estrategia){
						wsindicador[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,estrategia.nome);
					}
					coluna++;
				});
				coluna = colunaEstrategia;
				wsindicador[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,indicador.formula_calculo);
				coluna++;
				wsindicador[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,indicador.tipo_valor);
				coluna++;
				wsindicador[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,indicador.fonte);
				coluna++;
				wsindicador[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,indicador.periodicidade);
				coluna++;
				
				var tipoFormatoData = indicador.periodicidade == 'anual' ? 'yyyy' : 'MMMM yyyy';
				
				if(indicador.datas){
					var serieHistorica = $filter('date')(indicador.datas[indicador.datas.length-1] , tipoFormatoData) + ' a ' + $filter('date')(indicador.datas[0], tipoFormatoData);
					if(indicador.datas[0]){
						wsindicador[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha, serieHistorica);
					}
				}
				coluna++;
				angular.forEach(indicador.territorios,function(territorio,chave){
					if(territorio){
						var colunaTerritorio = coluna;
						switch(territorio.nome){
						case 'Município':
							colunaTerritorio = coluna;
							break;
						case 'Macrorregião':
							colunaTerritorio = coluna + 1;
							break;
						case 'Macroárea':
							colunaTerritorio = coluna + 2;
							break;
						case 'Prefeitura Regional':
							colunaTerritorio = coluna + 3;
							break;
						case 'Distrito':
							colunaTerritorio = coluna + 4;
							break;
						case 'Perímetro de Incentivo':
							colunaTerritorio = coluna + 5;
							break;
						case 'ZDE e ZPI':
							colunaTerritorio = coluna + 6;
							break;
						case 'Operação Urbana Consorciada':
							colunaTerritorio = coluna + 7;
							break;
						case 'ZEIS':
							colunaTerritorio = coluna + 8;
							break;
						case 'ZEPEC':
							colunaTerritorio = coluna + 9;
							break;
						}
						wsindicador[XLSX.utils.encode_cell({c:colunaTerritorio,r:linha})] = criarCelula(colunaTerritorio,linha,'Sim');
					}
				});
				// Status do indicador
				coluna += 10;
				let statusIndicador = indicador.homologacao ? "Homologação" : indicador.ativo ? "Ativo" : "Inativo";
				wsindicador[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha, statusIndicador);
				// Encerra preenchimento do indicador e pula para a próxima linha
				linha++;				
			});
			
			var range = {s: {c:0, r:0}, e: {c:23, r: linha}};
			wsindicador['!ref'] = XLSX.utils.encode_range(range);
			
			/* add worksheet to workbook */
			wb.SheetNames.push('Indicadores');
			wb.Sheets['Indicadores'] = wsindicador;
			
			
			var wbout = XLSX.write(wb, {bookType:'xlsx', bookSST:false, type: 'binary'});
			
			saveAs(new Blob([s2ab(wbout)],{type:"application/octet-stream"}), "indicadores.xlsx");
	}
	
	
	$scope.filtrarFonte = function(composicao){
		// console.log(composicao.id_fonte_dados);
		if(composicao.id_fonte_dados != null)
			composicao.variaveis = $scope.variaveis.filter((variavel) => variavel.id_fonte_dados === composicao.id_fonte_dados);
		else
			composicao.variaveis = $scope.variaveis;
	}
	$scope.atualizaFiltroPorFonte = function(composicao){
		composicao.id_fonte_dados = composicao.variaveis.filter((variavelIndicador) => variavelIndicador.id_variavel === composicao.id_variavel)[0].id_fonte_dados;
	}
	// ISSUE CORRECOES MENORES
	$scope.logcon = function(info) {
		console.log(info);
	}

	$scope.ordenaPtbr = function(v1, v2) {
		// Caso algum dos argumentos não seja string comparar por índice
		if (v1.type !== 'string' || v2.type !== 'string') {
  	    	return (v1.index < v2.index) ? -1 : 1;
		}

		// Ordena de acordo com as regras de ordenação do português brasileiro
		return v1.value.localeCompare(v2.value, 'pt-BR');
	};
});

</script>


<?php get_template_part('templates/page', 'header'); ?>

<div class="content-page container text-justify" data-ng-app="monitoramentoPde" data-ng-controller="cadastroIndicador">

<script type="text/ng-template" id="ModalConfirmacao.html">

<div class="modal-instrumento">
	<div class="modal-header">
    <h3 class="modal-title" id="modal-titulo-indicador"> {{acao}} indicador <button class="btn btn-danger pull-right" type="button" ng-click="fecharModal('confirmacao')">X</button></h3> 
	</div>
	<div class="modal-body" id="modal-corpo-indicador">
			Você irá {{acao.toLowerCase()}} o indicador {{indicadorAtivo.nome}}. <br><br> {{acao!="Remover"?mensagemCalculo:""}} Confirme sua ação.
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
    <h3 class="modal-title" id="modal-titulo-indicador"> {{acaoExecutando}} indicador </h3> 
	</div>
	<div class="modal-body" id="modal-corpo-indicador">
			{{acaoExecutando}} o indicador {{indicadorAtivo.nome}}, por favor aguarde a conclusão.
			</div>
</div>
</script>

<script type="text/ng-template" id="ModalSucesso.html">

<div class="modal-instrumento">
	<div class="modal-header">
    <h3 class="modal-title" id="modal-titulo-indicador"> Ação concluída <button class="btn btn-danger pull-right" type="button" ng-click="fecharModal('sucesso')">X</button></h3> 
	</div>
	<div class="modal-body" id="modal-corpo-indicador">
			 A ação foi concluída com sucesso!
			</div>
</div>
</script>


<?php   the_content(); ?>
<?php 
			$autorizado = false;
			
			//var_dump(wp_get_current_user());
			
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


<form style="margin-bottom:2em;">

			<button class="btn-primary" type="button" ng-click="exportarIndicadores()"> Exportar relação de indicadores </button>
			<button id="delayedRefreshBt" class="btn-primary" data-ng-click="filtrarInstrumento()">Atualizar filtro</button>
			<input type="button" data-ng-show="estado!='inserir' && estado!='listar'" value="Novo indicador" class="btn-primary" style="float:left;margin-right:1em;"	data-ng-click="limparForm()"> 
			
			<span data-ng-show="estado!='inserir'">
			<div class="elemento-cadastro">
				<label for="instrumento_filtro"> Filtrar por instrumento </label>
				<br>
				<div class="descricao-cadastro"><small>Selecione o instrumento que o indicador pertence</small></div>
				
				<select class="controle-cadastro" style="max-width:100%;" data-ng-options="instrumento.id_grupo_indicador as instrumento.nome for instrumento in instrumentos | orderBy: 'nome'" data-ng-model="idInstrumentoAtivo" data-ng-change="filtrarInstrumento()" id="instrumento_filtro">
					<option value=""> Sem filtro </option>
				</select>
			</div>
			
			
			<div class="elemento-cadastro">
				<label for="indicador"> Indicador </label>
				<br>
				<div class="descricao-cadastro"><small> Selecione o indicador </small></div>
				
				<select class="controle-cadastro" style="max-width: calc(100% - 220px);" data-ng-model="idIndicadorAtivo" data-ng-options="indicador.id_indicador as indicador.nome for indicador in indicadores | orderBy: 'nome' : false : ordenaPtbr" data-ng-change="carregarIndicador()" id="indicador">
				<option value=""></option>
				</select>
				<div ng-if="idIndicadorAtivo > 0" style="display: inline-flex; margin: 0 10px; width: 190px; cursor: pointer;" ng-click="geraLink()">
					<span>Link para o indicador</span>
					<button style="border: none;
						background: url(../app/themes/monitoramento_pde/images/icon-link.jpg);
	    			background-repeat: no-repeat;
						background-size: contain;
						margin: 0 5px;
					  width: 30px;
					  height: 30px;" type="button" title="Gerar link para este indicador">
					</button>
				</div>
			</div>
			
			</span>
			
			<span data-ng-show="estado!='listar'">
			<div class="elemento-cadastro">
				<label for="nome"> Nome do indicador </label>
				<br>
				<div class="descricao-cadastro"><small> Defina o nome que o indicador terá </small></div>
				
				<input class="controle-cadastro" type="text" style="max-width:100%;width:100%;" data-ng-model="indicadorAtivo.nome" id="nome"></input>
			</div>

			<div class="elemento-cadastro">
				<label for="instrumento"> Nome do instrumento </label>
				<br>
				<div class="descricao-cadastro"><small> Selecione o instrumento que o indicador pertencerá </small></div>
				
				<select class="controle-cadastro" style="max-width:100%;" data-ng-options="instrumento.id_grupo_indicador as instrumento.nome for instrumento in instrumentos | orderBy: 'nome'" data-ng-model="indicadorAtivo.id_instrumento" id="instrumento">
				<option value=""></option>
				</select>
			</div>
			
			<!--<div class="elemento-cadastro">
				<label for="ordem_instrumento"> Ordem no instrumento </label>
				<br>
				<div class="descricao-cadastro"><small> Defina a ordem que o indicador terá no instrumento</small></div>
				
				<input class="controle-cadastro" type="text" style="max-width:50%;width:50%;" data-ng-model="indicadorAtivo.ordem_instrumento" id="ordem_instrumento"></input>
			</div>-->
			<!-- ISSUE MENOR - NOME DO OBJETIVO -->
			<button ng-click='logcon(indicadorAtivo)' style="display: none">VER INDICADOR</button>
			
						<div class="elemento-cadastro">
				<label for="objetivo"> Nome(s) do(s) objetivo(s) </label>
				<br>
				<div class="descricao-cadastro"><small> Selecione o(s) objetivo(s) do indicador</small></div>
				<div data-ng-repeat="objetivoIndicador in indicadorAtivo.objetivos">
					<div>
						<select class="controle-cadastro" style="max-width:100%;" ng-attr-id="{{'objetivo-' + $index}}" data-ng-options="objetivo.id_grupo_indicador as objetivo.nome for objetivo in objetivos | orderBy: 'id_grupo_indicador'" data-ng-model="objetivoIndicador.id_grupo_indicador">
							<option value="Selecione"></option>
						</select>
						<div style="display: inline-block;"> 
							<button class="btn-danger btn-remover" ng-click="deletarObjetivo($index)">-</button>
						</div>
					</div>
					<span ng-repeat="objetivo in objetivos | filter: {'id_grupo_indicador': objetivoIndicador.id_grupo_indicador}">{{objetivo.propriedades.descricao}}</span>
					<br>
				</div>
				<input type="button" class="btn-primary" data-ng-click="adicionarObjetivo()" value="Adicionar Objetivo" style="margin-top:1em;margin-bottom:1em">
			</div>
			
			<!--<div class="elemento-cadastro">
				<label for="ordem_objetivo"> Ordem no objetivo </label>
				<br>
				<div class="descricao-cadastro"><small> Defina a ordem que o indicador terá no objetivo</small></div>
				
				<input class="controle-cadastro" type="text" style="max-width:50%;width:50%;" data-ng-model="indicadorAtivo.ordem_objetivo" id="ordem_objetivo"></input>
			</div>-->
			
			<div class="elemento-cadastro">
				<label for="estrategia1"> Nome da primeira estratégia </label>
				<br>
				<div class="descricao-cadastro"><small> Selecione a estratégia que o indicador pertencerá </small></div>
				
				<select class="controle-cadastro" style="max-width:100%;" data-ng-options="estrategia.id_grupo_indicador as estrategia.nome for estrategia in estrategias | orderBy: 'nome' : true" data-ng-model="indicadorAtivo.estrategias[0].id_grupo_indicador" id="estrategia1">
				<option value=""></option>
				</select>
			</div>
			
			<!--<div class="elemento-cadastro">
				<label for="ordem_estrategia1"> Ordem na primeira estratégia </label>
				<br>
				<div class="descricao-cadastro"><small> Defina a ordem que o indicador terá na estratégia</small></div>
				
				<input class="controle-cadastro" type="text" style="max-width:50%;width:50%;" data-ng-model="indicadorAtivo.estrategias[0].ordem" id="ordem_estrategia1"></input>
			</div>-->
			
			<div class="elemento-cadastro">
				<label for="estrategia2"> Nome da segunda estratégia </label>
				<br>
				<div class="descricao-cadastro"><small> Selecione a segunda estratégia que o indicador pertencerá </small></div>
				
				<select class="controle-cadastro" style="max-width:100%;" data-ng-options="estrategia.id_grupo_indicador as estrategia.nome for estrategia in estrategias | orderBy: 'nome' : true" data-ng-model="indicadorAtivo.estrategias[1].id_grupo_indicador" data-ng-change="trocaSegundaEstrategia()" id="estrategia2">
				<option value=""></option>
				</select>
			</div>
			
			<!--<div class="elemento-cadastro">
				<label for="ordem_estrategia2"> Ordem na segunda estratégia </label>
				<br>
				<div class="descricao-cadastro"><small> Defina a ordem que o indicador terá na segunda estratégia</small></div>
				
				<input class="controle-cadastro" type="text" style="max-width:50%;width:50%;" data-ng-model="indicadorAtivo.estrategias[1].ordem" id="ordem_estrategia2"></input>
			</div>-->
			
			<div class="elemento-cadastro">
				<label for="apresentacao"> Descrição síntese </label>
				<br>
				<div class="descricao-cadastro"><small> Defina o texto de descrição sintética, que explica o nome do indicador. Sugestão: até 150 caracteres. </small></div>
				
				<textarea rows="5" style="max-width:100%;width:100%;" data-ng-model="indicadorAtivo.apresentacao" id="apresentacao"></textarea>
			</div>
			
			<div class="elemento-cadastro">
				<label for="nota_tecnica_resumida"> Descrição completa </label>
				<br>
				<div class="descricao-cadastro"><small> Defina o texto de descrição completa, que contém as principais informações para o cidadão entender o indicador. Sugestão: até 1000 caracteres. </small></div>
				
				<textarea rows="8" style="max-width:100%;width:100%;" data-ng-model="indicadorAtivo.nota_tecnica_resumida" id="nota_tecnica_resumida"></textarea>
			</div>
			
			<div class="elemento-cadastro">
				<label for="nota_tecnica"> Nota técnica </label>
				<br>
				<div class="descricao-cadastro"><small> Defina o texto de nota técnica, que contêm as informações complementares para o cidadão entender o indicador. Sugestão: até 1.000 caracteres. </small></div>
				
				<textarea rows="5" style="max-width:100%;width:100%;" data-ng-model="indicadorAtivo.nota_tecnica" id="nota_tecnica"></textarea>
			</div>

			<div class="elemento-cadastro">
				<label for="periodicidade"> Periodicidade </label>
				<br>
				<div class="descricao-cadastro"><small> Selecione a periodicidade com que o indicador será atualizado. </small></div>
				
				<select class="controle-cadastro" data-ng-model="indicadorAtivo.periodicidade" id="periodicidade">
						<option value="">Sem data</option>
						<option value="anual">Anual</option>
						<option value="trimestral">Trimestral</option>
						<option value="mensal">Mensal</option>
				</select>
			</div>
			
			<div class="elemento-cadastro">
				<label for="fonte"> Fontes </label>
				<br>
				<div class="descricao-cadastro"><small>Defina as fontes que fornecem os dados utilizados no indicador. </small></div>
				
				<textarea rows="2" style="max-width:100%;width:100%;" data-ng-model="indicadorAtivo.fonte" id="fonte"></textarea>
			</div>
			
			<div class="elemento-cadastro">
				<label for="territorio_padrao"> Unidade territorial de análise inicial </label>
				<br>
				<div class="descricao-cadastro"><small> Selecione a unidade territorial de análise inicial que aparecerá quando o cidadão ver o indicador. </small></div>
				
				<select class="controle-cadastro" style="max-width:100%;" data-ng-options="territorio.id_territorio as territorio.nome for territorio in territorios | orderBy: 'id_territorio' : true" data-ng-model="indicadorAtivo.id_territorio_padrao" id="territorio_padrao">
				</select>
			</div>
			
			<div class="elemento-cadastro">
				<label for="territorio_padrao"> Unidade territorial de análise oculta </label>
				<br>
				<div class="descricao-cadastro"><small> Selecione a unidade territorial de análise que não irá aparecer como opção para o cidadão </small></div>
				
				<div ng-dropdown-multiselect="" options="territorios_dropdown" selected-model="indicadorAtivo.territorio_exclusao">
				
				</div>
				<span ng-repeat="ind in indicadorAtivo.territorio_exclusao">
				{{ind.label}} |
				</span>
				<!--<select class="controle-cadastro" style="max-width:100%;" data-ng-options="territorio.id_territorio as territorio.nome for territorio in territorios | orderBy: 'id_territorio' : true" data-ng-model="indicadorAtivo.id_territorio_exclusao" id="select_territorio_exclusao">
				</select>-->
			</div>
			
			<div class="elemento-cadastro">
				<label for="metrica"> Métrica </label>
				<br>
				<div class="descricao-cadastro"><small> Defina a métrica do indicador. </small></div>
				
				<input type="text" class="controle-cadastro" style="max-width:100%;width:100%;" data-ng-model="indicadorAtivo.tipo_valor" id="metrica"></input>
			</div>
			
			<div class="elemento-cadastro">
			<label for="simbolo_valor"> Símbolo da métrica </label>
				<br>
				<div class="descricao-cadastro"><small> Defina o símbolo da métrica do indicador. </small></div>
				
				<input type="text" class="controle-cadastro" style="max-width:100%;width:100%;" data-ng-model="indicadorAtivo.simbolo_valor" id="simbolo_valor"></input>
			</div>
			

			
			<div class="container elemento-cadastro" style="background-color:#E5E5E5;border-color:#DDDDDD;border-width:1px;border-style:solid;">
			<label> Fórmula de cálculo </label>
			<br>
			<small> Selecione as variáveis necessárias para composição do cálculo do indicador. Caso deixe o campo em branco a informação não será considerada para o cálculo do indicador. </small>
			<br>
			<div data-ng-repeat="composicao in indicadorComposicao | orderBy : 'ordem'" class="indicador-item-composicao">

				<div class="row">
					<div class="col-sm-12">
						<label ng-attr-for="{{'fonte_dados-'+$index}}"><small>Filtrar por fonte de dados</small></label>
					</div>
				</div>
				
				<!-- TODO: [P1.4] No cadastro de indicador não está salvando a informação do filtro de 'fonte de dados', em fórmula de cálculo -->
				<div class="row">
					<div class="col-sm-12">
						<select class="controle-cadastro" ng-attr-id="{{'fonte_dados-' + $index}}" style="max-width:100%;" data-ng-model="composicao.id_fonte_dados" data-ng-options="fonte.id_fonte_dados as fonte.nome for fonte in fontesDados | orderBy: 'nome'" data-ng-change="filtrarFonte(composicao)">
							<option value=""> Sem filtro </option>							
						</select>
					</div>
				</div>
				<!-- Ao carregar página com indicador salvo, realiza primeira filtragem para associar variáveis à fonte de dados -->
				{{ filtrarFonte(composicao) }}
				{{ atualizaFiltroPorFonte(composicao) }}
				<div class="row">
					<div class="col-sm-8">
						<label ng-attr-for="{{'variavel-'+$index}}"><small>Nome da variável</small></label>
					</div>
					<div class="col-sm-3">
						<label ng-attr-for="{{'operador-'+$index}}"><small>Operação aritmética</small></label>
					</div>
				</div>
				<div class="row">
				<div class="col-sm-8"> 
				<select class="controle-cadastro" ng-attr-id="{{'variavel-' + $index}}" style="max-width:100%;" data-ng-model="composicao.id_variavel" data-ng-options="variavel.id_variavel as variavel.nome for variavel in composicao.variaveis | orderBy: 'nome'">
					<option value=""></option>
				</select>
				</div>
				<div class="col-sm-3">
				<select class="controle-cadastro" ng-attr-id="{{'operador-' + $index}}" data-ng-model="composicao.operador">
					<option value=""></option>
					<option value="+">Soma (+)</option>
					<option value="-">Subtração (-)</option>
					<option value="/">Divisão (/)</option>
					<option value="*">Multiplicação (*)</option>
				</select>
				</div>
				<div class="col-sm-1 pull-right"> 
					<button class="btn-danger btn-remover" ng-click="deletarElemento($index)">-</button>
				</div>
				</div>

			</div>
			
			<input type="button" class="btn-primary" data-ng-click="adicionarElemento()" value="Adicionar Variável" style="margin-top:1em;margin-bottom:1em">
			</div>
			<div class="elemento-cadastro">
				<label for="ativo"> Ativação </label>
				<br>
				<input type="checkbox" data-ng-model="indicadorAtivo.ativo" id="ativo" data-ng-click="desabilitarHomologacao()"></input>
				<div style="display:inline-block" class="descricao-cadastro"><small> Selecione se o indicador está ativo na plataforma para visualização. </small></div>
				
			</div>
			
			<div class="elemento-cadastro">
				<label for="homologacao"> Homologação </label>
				<br>
				<input type="checkbox" data-ng-model="indicadorAtivo.homologacao" id="homologacao" data-ng-click="desabilitarAtivo()"></input>
				<div style="display:inline-block" class="descricao-cadastro"><small> Selecione se o indicador está habilitado para visualização somente dos administradores. </small></div>
				
			</div>
			
			<div class="elemento-cadastro">
				<label for="ativo"> Preencher valores vazios com zero ou nulo </label>
				<br>
				  <input type="radio" name="preencher_zero" data-ng-model="indicadorAtivo.preencher_zero" data-ng-value="true" > Preencher com zero
				  <input type="radio" name="preencher_zero" data-ng-model="indicadorAtivo.preencher_zero" data-ng-value="false" > Preencher com nulo
				  
			</div>
			
			<input type="button" class="btn-primary" data-ng-show="estado!='inserir'" value="Atualizar" data-ng-click="corrigeOrdemEstrategias();criarModalConfirmacao('Atualizar')"> 
			<input type="button" class="btn-primary" data-ng-show="estado!='inserir'" value="Remover" data-ng-click="criarModalConfirmacao('Remover')"> 
			<!--<input type="button" class="btn-primary" data-ng-show="estado!='inserir'" value="Calcular" data-ng-click="criarModalConfirmacao('Calcular')"> -->
			
			</span>
			
			<input type="button" class="btn-primary" data-ng-show="estado!='inserir'" value="Novo indicador" data-ng-click="limparForm()"> 
			
			<input type="button" class="btn-primary" data-ng-show="estado=='inserir'" value="Gravar" data-ng-click="criarModalConfirmacao('Inserir')">
			<input type="button" class="btn-primary" data-ng-show="estado=='inserir'" value="Voltar" data-ng-click="voltar()"> 
			
</form>

<?php }else{ ?>
			<h4> Você não possui autorização para visualizar esse conteúdo.</h4>
<?php } ?>

</div>

<style>
	.btn-danger.btn-remover {
		height: 27px;
		width: 27px;
		vertical-align: top;
		line-height: 0.4;
		font-size: 28px;
	}
</style>
