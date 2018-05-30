<?php
/**
 * Template Name: Cadastro Indicador Composto
 */
?>

<script type="text/javascript">
jQuery.noConflict();

var app = angular.module('monitoramentoPde', ['ngResource','ngAnimate','ui.bootstrap','angular.filter']);

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
	});
});

app.factory('IndicadorFusao',function($resource){
	return $resource('/wp-json/monitoramento_pde/v1/indicador/fusao/:id',{id:'@id_indicador'},{
		update:{
			method:'PUT'
		}
	});
});

app.controller("cadastroIndicadorComposicao", function($scope, $rootScope, $http, $filter, $uibModal, Indicador, IndicadorFusao) {
	
	Indicador.query(function(indicadores){
		$scope.indicadores = indicadores;
	});
	
 	IndicadorFusao.query(function(indicadoresCompostos) {
			$rootScope.listaIndicadoresCompostos = indicadoresCompostos;
		  $rootScope.indicadoresCompostos = indicadoresCompostos;
			
	});
	
	$scope.estado = "listar";
	
	$scope.carregar = function(){
		$scope.itemAtual = $rootScope.indicadoresCompostos.filter((indicador) => indicador.id_indicador_pai == $scope.idItemAtual)[0];
		$scope.estado = 'selecionar';
	};
	
	$scope.carregarIndicadorNovo = function(){
		$rootScope.idNovoIndicador = $scope.idNovoIndicador;
	}
	
	$scope.adicionarElemento = function(){
		$scope.itemAtual.composicao.push({});
	};
	

	$scope.criarModalConfirmacao = function(acao){
		$rootScope.modalConfirmacao = $uibModal.open({
			animation: true,
			ariaLabelledBy: 'modal-titulo-indicador-composto',
			ariaDescribedBy: 'modal-corpo-indicador-composto',
			templateUrl: 'ModalConfirmacao.html',
			controller: 'cadastroIndicadorComposicao',
			scope:$scope,
			size: 'md',
		});
		$scope.acao = acao;
	};
	
	$scope.criarModalSucesso = function(){
		$rootScope.modalSucesso = $uibModal.open({
			animation: true,
			ariaLabelledBy: 'modal-titulo-indicador-composto',
			ariaDescribedBy: 'modal-corpo-indicador-composto',
			templateUrl: 'ModalSucesso.html',
			controller: 'cadastroIndicadorComposicao',
			scope:$scope.parent,
			size: 'md',
		});
	};
	
	$scope.limparForm = function(){
		$scope.itemAtual = {};
		$scope.itemAtual.composicao = [];
		$scope.idItemAtual = null;
		$scope.estado = "inserir";
	};
	
	$scope.atualizar = function(){
		$rootScope.itemAtual = $scope.itemAtual;
		IndicadorFusao.update({composicao:$scope.itemAtual.composicao,id_indicador:$scope.itemAtual.id_indicador_pai}).$promise.then(
			function(mensagem){
				IndicadorFusao.query(function(indicadoresCompostos) {
					$rootScope.indicadoresCompostos = indicadoresCompostos;
					
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
		IndicadorFusao.remove({id:$scope.itemAtual.id_indicador_pai}).$promise.then(
			function(mensagem){
				IndicadorFusao.query(function(indicadoresCompostos) {
					$rootScope.indicadoresCompostos = indicadoresCompostos;
					
					$rootScope.modalProcessando.close();		
					$scope.criarModalSucesso();
				});
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
		IndicadorFusao.save({composicao:$scope.itemAtual.composicao,id_indicador:$rootScope.idNovoIndicador}).$promise.then(
			function(mensagem){
				IndicadorFusao.query(function(indicadoresCompostos) {
					$rootScope.indicadoresCompostos = indicadoresCompostos;
					
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
	
	$scope.lancarErro = function(erro){
		alert('Ocorreu um erro ao atualizar o indicador. \n\n Código: ' + erro.data.code + '\n\n Status: ' + erro.statusText + '\n\n Mensagem: ' + erro.data + '\n\n Mensagem Interna: ' + erro.data.message);
	}
	
	$scope.voltar = function(){
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
				ariaLabelledBy: 'modal-titulo-indicador-composto',
				ariaDescribedBy: 'modal-corpo-indicador-composto',
				templateUrl: 'ModalProcessando.html',
				controller: 'cadastroIndicadorComposicao',
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
					$scope.acaoSucesso = 'Inserido';
					$scope.inserir();
					
				}
			}
		}
	};		
	
	$scope.deletarElemento = function(indice){
		$scope.itemAtual.composicao.splice(indice,1);
	};
	
	
});

</script>


<?php get_template_part('templates/page', 'header'); ?>

<div class="content-page container text-justify" data-ng-app="monitoramentoPde" data-ng-controller="cadastroIndicadorComposicao">

<script type="text/ng-template" id="ModalProcessando.html">

<div class="modal-indicador-composto">
	<div class="modal-header">
    <h3 class="modal-title" id="modal-titulo-indicador-composto"> {{acaoExecutando}} indicador </h3> 
	</div>
	<div class="modal-body" id="modal-corpo-indicador-composto">
			{{acaoExecutando}} o indicador {{itemAtual.nome_indicador_pai}}, por favor aguarde a conclusão.
			</div>
</div>
</script>

<script type="text/ng-template" id="ModalConfirmacao.html">

<div class="modal-indicador-composto">
	<div class="modal-header">
    <h3 class="modal-title" id="modal-titulo-indicador-composto"> {{acao}} indicador composto <button class="btn btn-danger pull-right" type="button" ng-click="fecharModal('confirmacao')">X</button></h3> 
	</div>
	<div class="modal-body" id="modal-corpo-indicador-composto">
			Você irá {{acao.toLowerCase()}} o indicador composto {{itemAtual.nome_indicador_pai}}. <br><br> Confirme sua ação.
			</div>
	<div class="modal-footer">	
		<button class="btn btn-danger" type="button" ng-click="fecharModal('confirmacao')">	Abortar</button>
		<button class="btn btn-success" type="button" ng-click="submeter()">	Confirmar</button>
	</div>
</div>
</script>

<script type="text/ng-template" id="ModalSucesso.html">

<div class="modal-indicador-composto">
	<div class="modal-header">
    <h3 class="modal-title" id="modal-titulo-indicador-composto"> Ação concluída <button class="btn btn-danger pull-right" type="button" ng-click="fecharModal('sucesso')">X</button></h3> 
	</div>
	<div class="modal-body" id="modal-corpo-indicador-composto">
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
				<div class="elemento-cadastro" >
				
				
				<label for="indicador_composto"> Indicador pai </label>
				
				<div class="descricao-cadastro"><small>Selecione o indicador pai</small></div>
				
				<select class="controle-cadastro" style="max-width:100%;" data-ng-model="idItemAtual" data-ng-options="indicador_pai.id_indicador_pai as indicador_pai.nome_indicador_pai for indicador_pai in indicadoresCompostos | orderBy: 'nome_indicador_pai'" data-ng-change="carregar()" id="indicador_composto">
				<option value=""></option>
				</select>
			</div>
			</span>
			
			<span data-ng-show="estado=='inserir'">
				<div class="elemento-cadastro" >
				
				
				<label for="indicador_pai"> Indicador pai </label>
				
				<div class="descricao-cadastro"><small>Selecione o indicador pai</small></div>
				
				<select class="controle-cadastro" style="max-width:100%;" data-ng-model="idNovoIndicador" data-ng-options="indicador.id_indicador as indicador.nome for indicador in indicadores | orderBy: 'nome'"  data-ng-change="carregarIndicadorNovo()">
				<option value=""></option>
				</select>
			</div>
			</span>
			
			<span data-ng-show="estado!='listar'">
			
			
			<div class="container elemento-cadastro" style="background-color:#E5E5E5;border-color:#DDDDDD;border-width:1px;border-style:solid;">
			<label> Indicadores Filho </label>
			<div><small> Selecione os indicadores e as categorias que irão compor o indicador pai.  </small></div>
			<div data-ng-repeat="filho in itemAtual.composicao | orderBy : 'ordem'">
				<div class="row">
					<div class="col-sm-7"> <label ng-attr-for="{{'indicador_filho-'+$index}}"><small>Selecione o indicador filho </small> </label> </div>
					<div class="col-sm-4"> <label ng-attr-for="{{'categoria-'+$index}}"><small>Insira a categoria </small></label></div>
					
				</div>
				<div class="row">
				<div class="col-sm-7"> 
				<select class="controle-cadastro" style="max-width:100%;" data-ng-options="indicador.id_indicador as indicador.nome for indicador in indicadores | orderBy: 'nome'" data-ng-model="filho.id_indicador_filho" ng-attr-id="{{'indicador_filho-'+$index}}">
		
				</select>
				</div>

				<div class="col-sm-4">
					<input class="controle-cadastro" type="text" style="max-width:100%;width:100%;" data-ng-model="filho.dimensao" ng-attr-id="{{'categoria-'+$index}}" ></input>
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
			
			<input data-ng-show="estado!='inserir'" type="button" value="Novo Indicador composto" data-ng-click="limparForm()">
			
			<input data-ng-show="estado=='inserir'" type="button" value="Gravar" data-ng-click="criarModalConfirmacao('Inserir')">
			<input data-ng-show="estado=='inserir'" type="button" value="Voltar" data-ng-click="voltar()">
			
</form>

<?php }else{ ?>
			<h4> Você não possui autorização para visualizar esse conteúdo.</h4>
<?php } ?>

</div>


