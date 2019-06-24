<?php
/**
 * Template Name: Cadastro Variável
 */
?>


<script type="text/javascript">
jQuery.noConflict();

var app = angular.module('monitoramentoPde', ['ngResource','ngAnimate','ui.bootstrap','angular.filter']);

app.factory('Variavel',function($resource){
	return $resource('/wp-json/monitoramento_pde/v1/variavel/:id',{id:'@id_variavel'},{
		update:{
			method:'PUT'
		}
	});
});

app.factory('FontesDados',function($resource){
	return $resource('/wp-json/monitoramento_pde/v1/fontes_dados');
});

app.factory('VariavelFiltro',function($resource){
	return $resource('/wp-json/monitoramento_pde/v1/variavel_filtro/:id',{id:'@id_variavel'},{
		update:{
			method:'PUT'
		}
	});
});

app.controller("cadastroVariavel", function($scope, $rootScope, $http, $filter, $uibModal, Variavel, FontesDados, VariavelFiltro) {
	
 	Variavel.query(function(variaveis) {
			$rootScope.listaVariaveis = variaveis;
		  $rootScope.variaveis = variaveis;
	});
	
	FontesDados.query(function(fontesDados){
		$scope.fontesDados = fontesDados;
	});
	
	$scope.estado = "listar";
	
	$scope.carregar = function(){
		$scope.itemAtual = $rootScope.variaveis.filter((variavel) => variavel.id_variavel == $scope.idItemAtual)[0];
		if($scope.itemAtual.tipo_cruzamento == 'full outer')
			$scope.check_tipo_cruzamento = true;
		else
			$scope.check_tipo_cruzamento = false;
		
		if($scope.itemAtual != null){
			$scope.carregarFonteDados();
			VariavelFiltro.query({id:$scope.itemAtual.id_variavel},function(variavelFiltro){
				$scope.variavelFiltro = variavelFiltro;
				$scope.estado = "selecionar";
			});
		}else{
			$scope.fonteDados = null;
			$scope.variavelFiltro = null;
		}

	};

	$scope.delayedRefresh = function() {
		window.setTimeout(function(){
			document.getElementById('delayedRefreshBt').click();
		}, 3000);
	}
	
	$scope.filtrarFonte = function(){
		if($scope.idFonteAtivo != null)
			$rootScope.variaveis = $rootScope.listaVariaveis.filter((variavel) => variavel.id_fonte_dados === $scope.idFonteAtivo);
		else
			$rootScope.variaveis = $rootScope.listaVariaveis;
	}
	
	$scope.adicionarElemento = function(){
		$scope.variavelFiltro.push({});
	};
	
	$scope.carregarFonteDados = function(){
		$scope.fonteDados = $scope.fontesDados.filter((fonteDados) => fonteDados.id_fonte_dados == $scope.itemAtual.id_fonte_dados)[0];
	};
	
	$scope.lancarErro = function(erro){
		alert('Ocorreu um erro ao modificar a variável. \n\n Código: ' + erro.data.code + '\n\n Status: ' + erro.statusText + '\n\n Mensagem: ' + erro.data + '\n\n Mensagem Interna: ' + erro.data.message);
	}
	
	$scope.criarModalConfirmacao = function(acao){
		$rootScope.modalConfirmacao = $uibModal.open({
			animation: true,
			ariaLabelledBy: 'modal-titulo-variavel',
			ariaDescribedBy: 'modal-corpo-variavel',
			templateUrl: 'ModalConfirmacao.html',
			controller: 'cadastroVariavel',
			scope:$scope,
			size: 'md',
		});
		$scope.acao = acao;
	};
	
	$scope.criarModalSucesso = function(){
		$rootScope.modalSucesso = $uibModal.open({
			animation: true,
			ariaLabelledBy: 'modal-titulo-variavel',
			ariaDescribedBy: 'modal-corpo-variavel',
			templateUrl: 'ModalSucesso.html',
			controller: 'cadastroVariavel',
			scope:$scope.parent,
			size: 'md',
		});
	}
	
	
	$scope.limparForm = function(){
		$scope.itemAtual = null;
		$scope.variavelFiltro = [];
		$scope.estado = "inserir";
	};
	
	$scope.atualizar = function(){
		$rootScope.itemAtual = $scope.itemAtual;
		
		VariavelFiltro.update({filtro:$scope.variavelFiltro,id_variavel:$scope.itemAtual.id_variavel}).$promise.then(
			function(mensagem){
				Variavel.update({variavel:$rootScope.itemAtual,usuario:<?php $usrObj = wp_get_current_user(); echo json_encode($usrObj); ?>}).$promise.then(
					function(mensagem){

						Variavel.query(function(variaveis) {
							$rootScope.variaveis = variaveis;
							$scope.delayedRefresh();
													
							$rootScope.modalProcessando.close();		
							$scope.criarModalSucesso();
						});

					},
					function(erro){
						$rootScope.modalProcessando.close();
						$rootScope.modalConfirmacao.close();
						
						$scope.lancarErro(erro);
					}
				);
			},
			function(erro){
				$rootScope.modalProcessando.close();
				$rootScope.modalConfirmacao.close();
				$scope.lancarErro(erro);
			}
		);

	};		
	
	$scope.remover = function(){
		$rootScope.itemAtual = $scope.itemAtual;
		VariavelFiltro.remove({id:$scope.itemAtual.id_variavel}).$promise.then(
			function(mensagem){
				Variavel.remove({id:$rootScope.itemAtual.id_variavel,usuario:<?php $usrObj = wp_get_current_user(); echo json_encode($usrObj); ?>}).$promise.then(
					function(mensagem){

						Variavel.query(function(variaveis) {
							$rootScope.variaveis = variaveis;
							$scope.filtrarFonte();
							
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

		$scope.$parent.idItemAtual = null;	
		$scope.$parent.estado = "listar";
	};	

	$scope.inserir = function(){
		$rootScope.variavelFiltro = $scope.variavelFiltro;
		Variavel.save({variavel:$scope.itemAtual,usuario:<?php $usrObj = wp_get_current_user(); echo json_encode($usrObj); ?>}).$promise.then(
			function(mensagem){
				VariavelFiltro.save({filtro:$rootScope.variavelFiltro,id_variavel:mensagem.id_variavel}).$promise.then(
						function(mensagem){

							Variavel.query(function(variaveis) {
								$rootScope.variaveis = variaveis;
								$scope.filtrarFonte();
								
								$rootScope.modalProcessando.close();		
								$scope.criarModalSucesso();
							});
						},
						function(erro){
							$rootScope.modalProcessando.close();	
							$scope.lancarErro(erro);
						}
				);
				Variavel.query(function(variaveis) {
					$rootScope.variaveis = variaveis;
					$scope.filtrarFonte();
				});

			},
			function(erro){
				$rootScope.modalProcessando.close();
				$scope.lancarErro(erro);
			}
		);
		$scope.$parent.estado = "listar";
		
	};	

	$scope.voltar = function(){
		$scope.estado = "listar";	
	};	

	$scope.fecharModal = function(tipo){
		if(tipo == 'confirmacao')
			$rootScope.modalConfirmacao.close();
		
		if(tipo == 'sucesso')
			$rootScope.modalSucesso.close();
	};

	$scope.ativarCruzamento = function(){
		if($scope.itemAtual.tipo_cruzamento == 'full outer')
			$scope.itemAtual.tipo_cruzamento = 'inner';
		else
			$scope.itemAtual.tipo_cruzamento = 'full outer';
	}
	
	$scope.submeter = function(){	

		$rootScope.modalConfirmacao.close();
		
		$rootScope.modalProcessando = $uibModal.open({
				animation: true,
				ariaLabelledBy: 'modal-titulo-variavel',
				ariaDescribedBy: 'modal-corpo-variavel',
				templateUrl: 'ModalProcessando.html',
				controller: 'cadastroVariavel',
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
	};	
		
	$scope.desabilitarContagem = function(){
		if($scope.itemAtual.operacao_agregacao == 'contagem')
			$scope.itemAtual.coluna_valor = null;
	};
	
	$scope.deletarElemento = function(indice){
		$scope.variavelFiltro.splice(indice,1);
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
	
	$scope.exportarVariaveis = function(){
		
			var wb = new Workbook();
			
			var wsvariavel = {};
			
			//criando cabeçalho
			wsvariavel[XLSX.utils.encode_cell({c:0 ,r:0})] = criarCelula(0 ,0,'ID');
			wsvariavel[XLSX.utils.encode_cell({c:1 ,r:0})] = criarCelula(1 ,0,'Nome');
			wsvariavel[XLSX.utils.encode_cell({c:2 ,r:0})] = criarCelula(2 ,0,'Banco de dados');
			wsvariavel[XLSX.utils.encode_cell({c:3 ,r:0})] = criarCelula(3 ,0,'Coluna de valor');
			wsvariavel[XLSX.utils.encode_cell({c:4 ,r:0})] = criarCelula(4 ,0,'Coluna de data');
			wsvariavel[XLSX.utils.encode_cell({c:5 ,r:0})] = criarCelula(5 ,0,'Coluna de categorias');
			wsvariavel[XLSX.utils.encode_cell({c:6 ,r:0})] = criarCelula(6 ,0,'Periodicidade');
			wsvariavel[XLSX.utils.encode_cell({c:7 ,r:0})] = criarCelula(7 ,0,'Operação de agregação');
			wsvariavel[XLSX.utils.encode_cell({c:8 ,r:0})] = criarCelula(8 ,0,'Variável acumulativa');
			wsvariavel[XLSX.utils.encode_cell({c:9 ,r:0})] = criarCelula(9 ,0,'Variável denominador município');
			wsvariavel[XLSX.utils.encode_cell({c:10,r:0})] = criarCelula(10 ,0,'Variável de crescimento');
			wsvariavel[XLSX.utils.encode_cell({c:11,r:0})] = criarCelula(11,0,'Variável de cruzamento de banco de dados distintos');
			
			                                                   
			linha = 1;
			
			angular.forEach($rootScope.variaveis,function(variavel,chave){
				
				coluna = 0, //linha = 0;
				wsvariavel[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,variavel.id_variavel);
				coluna++;
				wsvariavel[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,variavel.nome);
				coluna++;
				
				var fonteDadosVariavel = $scope.fontesDados.filter((fonteDados) => fonteDados.id_fonte_dados == variavel.id_fonte_dados)[0];
				if(fonteDadosVariavel){
					wsvariavel[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,fonteDadosVariavel.nome);
				}
				coluna++;
				wsvariavel[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,variavel.coluna_valor);
				coluna++;
				wsvariavel[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,variavel.coluna_data);
				coluna++;
				wsvariavel[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,variavel.coluna_dimensao);
				coluna++;
				wsvariavel[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,variavel.periodicidade);
				coluna++;
				wsvariavel[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,variavel.operacao_agregacao);
				coluna++;
				wsvariavel[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,(variavel.acumulativa==true) ? 'X': null);
				coluna++;
				wsvariavel[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,(variavel.distribuicao==true) ? 'X': null);
				coluna++;
				wsvariavel[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,(variavel.crescimento==true) ? 'X': null);
				coluna++;
				wsvariavel[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,(variavel.tipo_cruzamento!='inner') ? 'X': null);
				coluna++;
				
				
				linha++;
				
			});
			
			var range = {s: {c:0, r:0}, e: {c:11, r: linha}};
			wsvariavel['!ref'] = XLSX.utils.encode_range(range);
			
			/* add worksheet to workbook */
			wb.SheetNames.push('Variaveis');
			wb.Sheets['Variaveis'] = wsvariavel;
			
			
			var wbout = XLSX.write(wb, {bookType:'xlsx', bookSST:false, type: 'binary'});
			
			saveAs(new Blob([s2ab(wbout)],{type:"application/octet-stream"}), "variaveis.xlsx");
	}
	
	
});

</script>


<?php get_template_part('templates/page', 'header'); ?>

<div class="content-page container text-justify" data-ng-app="monitoramentoPde" data-ng-controller="cadastroVariavel">

<script type="text/ng-template" id="ModalConfirmacao.html">

<div class="modal-instrumento">
	<div class="modal-header">
    <h3 class="modal-title" id="modal-titulo-variavel"> {{acao}} variável <button class="btn btn-danger pull-right" type="button" ng-click="fecharModal('confirmacao')">X</button></h3> 
	</div>
	<div class="modal-body" id="modal-corpo-variavel">
			Você irá {{acao.toLowerCase()}} a variável <strong>{{itemAtual.nome}}</strong>. <br><br> Confirme sua ação.
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
    <h3 class="modal-title" id="modal-titulo-variavel"> {{acaoExecutando}} variável </h3> 
	</div>
	<div class="modal-body" id="modal-corpo-variavel">
			{{acaoExecutando}} a variável <strong>{{itemAtual.nome}}</strong>, por favor aguarde a conclusão.
			</div>
</div>
</script>

<script type="text/ng-template" id="ModalSucesso.html">

<div class="modal-instrumento">
	<div class="modal-header">
    <h3 class="modal-title" id="modal-titulo-variavel"> Ação concluída <button class="btn btn-danger pull-right" type="button" ng-click="fecharModal('sucesso')">X</button></h3> 
	</div>
	<div class="modal-body" id="modal-corpo-variavel">
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
			<span data-ng-show="estado!='inserir'">
				<div class="elemento-cadastro">
				
				<button class="btn-primary" type="button" ng-click="exportarVariaveis()"> Exportar relação de variáveis </button>
				<button id="delayedRefreshBt" class="btn-primary" data-ng-click="filtrarFonte()">Atualizar filtro</button>
				<br>
				
				<label for="fonte_filtro"> Filtrar por fonte de dados </label>
				<br>
				<div class="descricao-cadastro"><small>Selecione a fonte de dados que a variável pertence</small></div>
				
				<select class="controle-cadastro" style="max-width:100%;" data-ng-options="fonte.id_fonte_dados as fonte.nome for fonte in fontesDados | orderBy: 'nome'" data-ng-model="idFonteAtivo" data-ng-change="filtrarFonte()" id="fonte_filtro">
					<option value=""> Sem filtro </option>
				</select>
			</div>
				<div class="elemento-cadastro" >
				
				
				<label for="variavel"> Variável </label>
				
				<div class="descricao-cadastro"><small>Selecione a variável</small></div>
				
				<select class="controle-cadastro" style="max-width:100%;" data-ng-model="idItemAtual" data-ng-options="variavel.id_variavel as variavel.nome for variavel in variaveis | orderBy: 'nome'" data-ng-change="carregar()" id="variavel">
				<option value=""></option>
				</select>
			</div>
			</span>
			
			<span data-ng-show="estado!='listar'">
			
			<div class="elemento-cadastro">
				<label for="nome"> Nome da variável </label>
				<div class="descricao-cadastro"><small> Defina o nome que a variável terá </small></div>
				<input class="controle-cadastro" type="text" style="max-width:100%;width:100%;" data-ng-model="itemAtual.nome" id="nome"></input>
			</div>
			
			<div class="elemento-cadastro">
				<label for="metrica"> Métrica </label>
				<div class="descricao-cadastro"><small> Defina a métrica da variável. </small></div>
				<input type="text" class="controle-cadastro" style="max-width:100%;width:100%;" data-ng-model="itemAtual.tipo_valor" id="metrica"></input>
			</div>
			
			<div class="elemento-cadastro">
				<label for="fonte_dados"> Banco de dados </label>
				<div class="descricao-cadastro"><small>Selecione o banco de dados cadastrado que contêm as informações correspondentes à variável</small></div>
				<select class="controle-cadastro" style="max-width:100%;" data-ng-options="fonte_dados.id_fonte_dados as fonte_dados.nome for fonte_dados in fontesDados | orderBy: 'nome'" data-ng-model="itemAtual.id_fonte_dados" data-ng-change="carregarFonteDados()" id="fonte_dados"></select>
			</div>
			
			<span data-ng-show="fonteDados!=null">
			
			<div class="elemento-cadastro" ng-if="itemAtual.operacao_agregacao != 'contagem'">
				<div style="margin-top:0;" class="descricao-cadastro"><small>Selecione a coluna do banco de dados que contêm o <u>valor</u> correspondente à variável.<br> Observação: caso a agregação da variável seja ‘contagem’ deixar o campo em branco!</small></div>
				<select class="controle-cadastro" style="max-width:100%;" data-ng-options="coluna as coluna for coluna in fonteDados.colunas" data-ng-model="itemAtual.coluna_valor" id="coluna_valor">
				</select>
			</div>
			
			<div class="elemento-cadastro">
				<div style="margin-top:0;" class="descricao-cadastro"><small>Selecione a coluna do banco de dados que contêm a <u>data</u> correspondente à variável</small></div>
				<select class="controle-cadastro" style="max-width:100%;" data-ng-options="coluna as coluna for coluna in fonteDados.colunas" data-ng-model="itemAtual.coluna_data" id="coluna_data">
						<option value=""></option>
				</select>
			</div>
			
			<div class="elemento-cadastro">
				<div style="margin-top:0;" class="descricao-cadastro"><small>Selecione a coluna do banco de dados que contêm as diferentes <u>categorias</u> no qual a variável está composta. <br> Observação: Caso este campo seja deixado em branco a variável não será dividida em categorias </small></div>
				<select class="controle-cadastro" style="max-width:100%;" data-ng-options="coluna as coluna for coluna in fonteDados.colunas" data-ng-model="itemAtual.coluna_dimensao" id="coluna_dimensao">
				<option value=""></option>
				</select>
			</div>
			
			<div class="elemento-cadastro" ng-if="fonteDados.tipos_territorio != null">
				<div style="margin-top:0;" class="descricao-cadastro"><small>Selecione o tipo de território da fonte de dados </small></div>
				<select class="controle-cadastro" style="max-width:100%;" data-ng-options="tipo_territorio as tipo_territorio for tipo_territorio in fonteDados.tipos_territorio" data-ng-model="itemAtual.tipo_territorio" id="tipo_territorio">
				<option value=""></option>
				</select>
			</div>
			
			</span>
			<div class="elemento-cadastro">
				<label for="periodicidade"> Periodicidade </label>
				<div class="descricao-cadastro"><small> Selecione a periodicidade com que a variável será atualizada </small></div>
				<select class="controle-cadastro" data-ng-model="itemAtual.periodicidade" id="periodicidade">
						<option value="">Sem data</option>
						<option value="anual">Anual</option>
						<option value="semestral">Semestral</option>
						<option value="trimestral">Trimestral</option>
						<option value="mensal">Mensal</option>
				</select>
			</div>
			<div class="elemento-cadastro">
				<label for="operacao_agregacao"> Operação de agregação </label>
				<br>

				<div class="descricao-cadastro"><small>Selecione a forma com que a varável agregará os dados correspondentes à coluna de valor</small></div>
					<select class="controle-cadastro" data-ng-model="itemAtual.operacao_agregacao" data-ng-change="desabilitarContagem()" id="operacao_agregacao">
					<option value="soma">Soma</option>
					<option value="contagem">Contagem</option>
					<option value="media">Média aritmética</option>
					<option value="maximo">Máximo</option>
					<option value="minimo">Mínimo</option>
					</select>
				<div style="margin-top:0.2em;" class="descricao-cadastro">
						<small>
							<ul><li>Soma: calcula a soma da expressão sobre todas as linhas de entrada</li>
							<li>Contagem: calcula o número de linhas de entrada</li>
							<li>Média aritmética: calcula a média aritmética sobre o conjunto de linhas de entrada fornecido</li>
							<li>Máximo: retorna o maior valor entre todos os fornecidos</li>
							<li>Mínimo: retorna o menor valor entre todos os fornecidos</li>
						</small>
					</div>

			</div>
			

			<h5><b> Atributos da variável </b></h5>
			<div class="elemento-cadastro row">
				<input class="col-md-1" style="width:2%;margin-top:-0.5em;margin-left:0.8em;padding-left:2em;" type="checkbox" data-ng-model="itemAtual.acumulativa" id="acumulativa"></input>
				<div class="col-md-11 descricao-cadastro" style="width:95%;"><small>Selecione a opção caso a variável seja acumulativa ao longo do tempo. <br> Exemplo: (valor do numerador na data atual + valor do numerador nas datas anteriores) / (valor do denominador na data atual + valor do denominador nas datas anteriores)</small></div>
			</div>
			
			<div class="elemento-cadastro row">
				<input class="col-md-1" style="width:2%;margin-top:-0.5em;margin-left:0.8em;padding-left:2em;" type="checkbox" data-ng-model="itemAtual.distribuicao" id="distribuicao"></input>
				<div class="col-md-11 descricao-cadastro" style="width:95%"><small>Selecione a opção caso a variável seja um denominador que corresponde ao valor total no Município <br> Exemplo: valor do numerador / valor do denominador para o Município</small></div>
			</div>

			<div class="elemento-cadastro row">
				<input class="col-md-1" style="width:2%;margin-top:-0.5em;margin-left:0.8em;padding-left:2em;" type="checkbox" data-ng-model="itemAtual.crescimento" id="crescimento"></input>
				<div class="col-md-11 descricao-cadastro" style="width:95%"><small>Selecione a opção caso a variável seja um denominador de um indicador de variação temporal. <br> Exemplo: (valor na data atual – valor na data anterior) / valor na data anterior</small></div>
			</div>
			
			<div class="elemento-cadastro row">
				<input class="col-md-1" style="width:2%;margin-top:-0.5em;margin-left:0.8em;padding-left:2em;" ng-model="check_tipo_cruzamento" type="checkbox" ng-click="ativarCruzamento()" id="tipo_cruzamento"></input>
				<div class="col-md-11 descricao-cadastro" style="width:95%"><small>Selecione a opção caso a variável pertença a um indicador que faz o cruzamento entre <u> dois (ou mais) bancos de dados </u> e possua <u> categorias </u> de composição de variável distintas nos dois (ou mais) bancos de dados</small></div>
			</div>
			
			<div class="container elemento-cadastro" style="background-color:#E5E5E5;border-color:#DDDDDD;border-width:1px;border-style:solid;">
			<label> Filtros da variável </label>
			<div><small> Selecione os filtros necessários para composição da variável. Caso deixe o campo em branco a informação não será considerada para a composição da variável </small></div>
			<div data-ng-repeat="filtro in variavelFiltro | orderBy : 'ordem'">
				<div class="row">
					<div class="col-sm-5"> <label ng-attr-for="{{'coluna_valor-'+$index}}"><small>Selecione a coluna do banco de dados que contém o valor a ser filtrado </small> </label> </div>
					<div class="col-sm-2"> <label ng-attr-for="{{'operador_comparador-'+$index}}"><small>Operação de comparação </small></label></div>
					<div class="col-sm-3" ng-if="filtro.operador_comparador!='is' && filtro.operador_comparador!='is not'"> <label ng-attr-for="{{'valor-'+$index}}"><small>Valor </small></label></div>
					<div class="col-sm-1"> <label ng-attr-for="{{'operador_logico-'+$index}}"><small>e/ou </small></label></div>
				</div>
				<div class="row">
				<div class="col-sm-5"> 
				<select class="controle-cadastro" style="max-width:100%;" data-ng-options="coluna as coluna for coluna in fonteDados.colunas" data-ng-model="filtro.coluna" ng-attr-id="{{'coluna_valor-'+$index}}">
					<option value=""></option>
				</select>
				</div>
				<div class="col-sm-2">
				<select class="controle-cadastro" data-ng-model="filtro.operador_comparador" ng-attr-id="{{'operador_comparador-'+$index}}">
					<option value=""></option>
					<option value="=">igual a</option>
					<option value="!=">diferente de</option>
					<option value=">">maior que</option>
					<option value=">=">maior ou igual a</option>
					<option value="<">menor que</option>
					<option value="<=">menor ou igual a</option>
					<option value="like">contém</option>
					<option value="not like">não contém</option>
					<option value="is">valores "vazios"</option>
					<option value="is not">valores "não vazios"</option>
				</select>
				</div>
				<div class="col-sm-3">
					<input class="controle-cadastro" ng-if="filtro.operador_comparador!='is' && filtro.operador_comparador!='is not'" type="text" style="max-width:100%;width:100%;" data-ng-model="filtro.valor" ng-attr-id="{{'valor-'+$index}}" ></input>
				</div>
				<div class="col-sm-1">
				<select class="controle-cadastro" data-ng-model="filtro.operador_logico" ng-attr-id="{{'operador_logico-'+$index}}">
					<option value=""></option>
					<option value="AND">e</option>
					<option value="OR">ou</option>

				</select>
				</div>
				<div class="col-sm-1 pull-right"> 
					<button ng-click="deletarElemento($index)">-</button>
				</div>
				</div>
				
			</div>
			
			<button type="button" style="margin-top:1em;margin-bottom:1em" data-ng-click="adicionarElemento()">Adicionar Filtro</button>
			</div>
			<br>
			<input type="button" data-ng-show="estado!='inserir'" value="Atualizar" data-ng-click="criarModalConfirmacao('Atualizar')">
			<input type="button" data-ng-show="estado!='inserir'" value="Remover" data-ng-click="criarModalConfirmacao('Remover')">
			
			</span>
			
			<input data-ng-show="estado!='inserir'" type="button" value="Nova variável" data-ng-click="limparForm()">
			
			<input data-ng-show="estado=='inserir'" type="button" value="Gravar" data-ng-click="criarModalConfirmacao('Inserir')">
			<input data-ng-show="estado=='inserir'" type="button" value="Voltar" data-ng-click="voltar()">
			
</form>

<?php }else{ ?>
			<h4> Você não possui autorização para visualizar esse conteúdo.</h4>
<?php } ?>

</div>


