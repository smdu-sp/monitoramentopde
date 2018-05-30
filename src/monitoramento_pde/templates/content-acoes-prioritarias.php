
<script type="text/javascript">
jQuery.noConflict();

var app = angular.module('monitoramentoPde', ['ngResource','ngAnimate','ui.bootstrap','angular.filter']);

app.factory('AcaoPrioritaria', function($resource){
	return $resource('/wp-json/monitoramento_pde/v1/acoes_prioritarias/');
});

app.controller("acoesPrioritarias", function($scope, $http, $filter, AcaoPrioritaria) {
	
	 AcaoPrioritaria.query(function(acoesPrioritarias){
		$scope.acoesPrioritarias = acoesPrioritarias;
		$scope.acoesFiltro = $scope.acoesPrioritarias;
	 });
	
	$scope.optCategoria = null;
	$scope.optEstrategia = null;
	$scope.optAndamento = null;
	$scope.optArtigo = null;
	$scope.optTema = null;
	$scope.optEstagioImplementacao = null;
	
	$scope.cargaAcoesPrioritarias = function(){
		
		filtroCategoria = !$scope.optCategoria? null : $scope.optCategoria.categoria;
		filtroEstrategia = !$scope.optEstrategia? null : $scope.optEstrategia.objetivo_relacionado;
		filtroAndamento = !$scope.optAndamento? null : $scope.optAndamento.andamento;
		filtroArtigo = !$scope.optArtigo? null : $scope.optArtigo.artigo;
		filtroTema = !$scope.optTema? null : $scope.optTema.tema;
		filtroEstagioImplementacao = !$scope.optEstagioImplementacao? null : $scope.optEstagioImplementacao.estagio_implementacao;
		
		AcaoPrioritaria.query({categoria:filtroCategoria, estrategia:filtroEstrategia, andamento:filtroAndamento, artigo:filtroArtigo, tema:filtroTema, estagio_implementacao: filtroEstagioImplementacao},function(acoesPrioritarias){
		 $scope.acoesFiltro = acoesPrioritarias;
		});
	};
	
});



</script>

<div class="content-page container text-justify" data-ng-app="monitoramentoPde" data-ng-controller="acoesPrioritarias">
<?php the_content(); ?>

<div class="row" style="margin:0;">
	
	<hr>
	<div class="row">
		<div class="row">
			<label for="estrategia" class="col-sm-12 acao-prioritaria" style="display:inline-block;float:none;vertical-align:middle;"> 
				<strong> Estrategia </strong>
			</label>
		</div>
		<div class="row">
			<div class="col-sm-6 text-center" class="acao-prioritaria" style="display:inline-block;float:none;vertical-align:middle;"> 
				<select style="width:100%;" data-ng-model="optEstrategia" class="acao-prioritaria" data-ng-options="acao.objetivo_relacionado for acao in acoesPrioritarias | unique: 'objetivo_relacionado' | filter: {objetivo_relacionado:''}" data-ng-change="cargaAcoesPrioritarias()" name="estrategia" id="estrategia">
				<option value="">Todas</option>
				</select>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-5" style="display:inline-block;float:none;">
			<div class="col-sm-1 text-center" style="display:inline-block;float:none;vertical-align:middle;"> 
				<!--<select style="max-width:100%;" data-ng-model="optEstrategia" data-ng-options="acao.estrategia for acao in acoesPrioritarias | unique: 'estrategia' | filter: {estrategia:''}" data-ng-change="cargaAcoesPrioritarias()" name="estrategia">
				<option value="">Todos</option>
				</select>-->
			</div>
			
				<h4 style="padding-left:15px;"> <strong> Ação </strong> </h4>
			</div><!--
			--><div class="col-sm-1 acao-prioritaria" style="display:inline-block;float:none;vertical-align:middle;"> 
				<strong> Artigo </strong>
			</div><!--
			--><label for="estagio_implementacao" class="col-sm-2 acao-prioritaria" style="display:inline-block;float:none;vertical-align:middle;padding-left:7px"> 
				<strong> Estágio de implementação </strong>
			</label><!--
			--><label for="tema" class="col-sm-2 acao-prioritaria" style="display:inline-block;float:none;vertical-align:middle;padding-left:7px"> 
				<strong> Tema </strong>
			</label><!--
			--><label for="categoria" class="col-sm-2 acao-prioritaria" style="display:inline-block;float:none;vertical-align:middle;padding-left:0"> 
				<strong> Categoria </strong>
			</label>
		</div>
		
		<div class="row">
			<div class="col-sm-5" style="display:inline-block;float:none;">
			</div><!--
			--><div class="col-sm-1" style="display:inline-block;float:none;">
			</div><!--
			--><div class="col-sm-2 text-center" style="display:inline-block;float:none;vertical-align:middle;padding-right:0px;"> 
				<select style="width:100%;" data-ng-model="optEstagio_implementacao" data-ng-options="acao.estagio_implementacao for acao in acoesPrioritarias | unique: 'estagio_implementacao' | filter: {estagio_implementacao:''}" data-ng-change="cargaAcoesPrioritarias()" name="estagio_implementacao">
				<option value="">Todos</option>
				</select>
			</div><!--
			--><div class="col-sm-2 text-center" class="acao-prioritaria-cbo" style="display:inline-block;float:none;vertical-align:middle;padding-left:7px"> 
				<select style="width:100%;" data-ng-model="optTema" class="acao-prioritaria" data-ng-options="acao.tema for acao in acoesPrioritarias | unique: 'tema' | filter: {tema:''}" data-ng-change="cargaAcoesPrioritarias()" name="tema" id="tema">
				<option value="">Todos</option>
				</select>
			</div><!--
			--><div class="col-sm-2 text-center" class="acao-prioritaria-cbo" style="display:inline-block;float:none;vertical-align:middle;padding-left:0"> 
				<select style="width:100%;" data-ng-model="optCategoria" data-ng-options="acao.categoria for acao in acoesPrioritarias | unique: 'categoria' | filter: {categoria:''}" data-ng-change="cargaAcoesPrioritarias()" name="categoria" id="categoria">
				<option value="">Todos</option>
				</select>
			</div>
			
			<!--<div class="col-sm-1 text-center" style="display:inline-block;float:none;vertical-align:middle;padding-right:30px;"> 
				<select style="max-width:100%;" data-ng-model="optAndamento" data-ng-options="acao.andamento for acao in acoesPrioritarias | unique: 'andamento' | filter: {andamento:''}" data-ng-change="cargaAcoesPrioritarias()" name="andamento">
					<option value="">Todos</option>
					</select>
			</div>-->
		</div>
		
		<ul class="list-group">
			<li class="list-group-item row list-pontilhada" data-ng-repeat="acao in acoesFiltro | orderBy: 'artigo'">
				<div class="col-sm-5 acao-prioritaria"> {{acao.acao_prioritaria_estrategica}} </div>
				<div class="col-sm-1 acao-prioritaria"> <small>{{acao.artigo}}</small> </div>
				<div class="col-sm-2 acao-prioritaria"> {{acao.estagio_implementacao}} </div>
				<div class="col-sm-2 acao-prioritaria"> {{acao.tema}} </div>
				<div class="col-sm-2 acao-prioritaria"> {{acao.categoria}} </div>
				<!--<div class="col-sm-1 acao-prioritaria text-center"> {{acao.andamento}}</div>-->
			</li>
		</ul>
	</div>
	
</div>


</div>


<?php wp_link_pages(['before' => '<nav class="page-nav"><p>' . __('Pages:', 'sage'), 'after' => '</p></nav>']); ?>
