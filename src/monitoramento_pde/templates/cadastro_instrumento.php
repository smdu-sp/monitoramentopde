<?php
/**
 * Template Name: Cadastro Instrumento
 */
?>

<script type="text/javascript">
jQuery.noConflict();

var app = angular.module('monitoramentoPde', ['ngResource','ngAnimate','ui.bootstrap','angular.filter','as.sortable']);

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

app.controller("cadastroGrupo", function($scope, $rootScope, $http, $filter, $uibModal, GrupoIndicador, Indicador) {
 
	$scope.estado = "listar";
	
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
		}
	};
	
	$scope.adicionarElemento = function(){
		$scope.itemAtual.propriedades.push({});
	};
	
	$scope.criarModalConfirmacao = function(acao){
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
		GrupoIndicador.update({grupo:$scope.itemAtual,id_grupo_indicador:$scope.itemAtual.id_grupo_indicador,tipo:$scope.tipo,indicadores:$scope.indicadores}).$promise.then(
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
		GrupoIndicador.remove({id:$scope.itemAtual.id_grupo_indicador}).$promise.then(
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
		GrupoIndicador.save({grupo:$scope.itemAtual,tipo:$scope.tipo}).$promise.then(
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
			
</form>

<?php }else{ ?>
			<h4> Você não possui autorização para visualizar esse conteúdo.</h4>
<?php } ?>

</div>


