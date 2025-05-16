<script>
jQuery.noConflict();

var app = angular.module('monitoramentoPde', ['ngResource','ngAnimate','ui.bootstrap','angular.filter']);

app.factory('AcaoPrioritaria', function($resource){
	return $resource('/wp-json/monitoramento_pde/v1/acoes_prioritarias/');
});

app.controller("acoesPrioritarias", function($scope, $http, $filter, AcaoPrioritaria) {
	$scope.todosOsCampos = ['politica_setorial', 'sistemas_urbanos_e_ambientais'];
	$scope.filtros = {};
	$scope.opcoes = {};
	$scope.selectDesabilitado = {};
	$scope.todosOsCampos.forEach(campo => {
		$scope.filtros[campo] = null;
		$scope.opcoes[campo] = [];
		$scope.selectDesabilitado[campo] = false;
	});
	
	AcaoPrioritaria.query((acoes) => {
		$scope.acoesPrioritarias = acoes;
		$scope.acoesFiltro = acoes;

		// Inicializar com todas as opções disponíveis
		$scope.todosOsCampos.forEach(campo => {
			$scope.opcoes[campo] = $filter('unique')(acoes, campo).map((obj => obj[campo]));
		});
	});

	$scope.atualizarFiltros = (campoAtual) => {
		const valorAtual = $scope.filtros[campoAtual];

		// Guardar o primeiro filtro utilizado
		if (!$scope.primeiroFiltro && valorAtual) {
			$scope.primeiroFiltro = campoAtual;
		}

		// Ao limpar primeiro filtro, limpar os demais filtros
		if (!$scope.filtros[$scope.primeiroFiltro]) {
			$scope.primeiroFiltro = null;

			$scope.todosOsCampos.forEach(campo => {
				$scope.selectDesabilitado[campo] = false;
				$scope.opcoes[campo] = $filter('unique')($scope.acoesPrioritarias, campo).map((obj => obj[campo]));
			});

			for (const filtro of Object.keys($scope.filtros)) {
				$scope.filtros[filtro] = null;
			}
		}


		// Atualiza os selects para mostrar apenas as opções disponíveis com os filtros aplicados
		$scope.todosOsCampos.forEach(campo => {
			// Não atualiza as opções do select se ele for primeiroFiltro
			if (campo !== $scope.primeiroFiltro) {
				let baseFiltrada = $scope.acoesPrioritarias;
				
				for (const [outroCampo, valor] of Object.entries($scope.filtros)) {
					if (outroCampo !== campo && valor) {
						baseFiltrada = baseFiltrada.filter(acao => acao[outroCampo] === valor);
					}
				}

				const filtroOpcoes = $filter('unique')(baseFiltrada, campo).map((obj => obj[campo]));
				$scope.opcoes[campo] = filtroOpcoes;

				// Se restar só uma opção, selecionar automaticamente e desabilitar campo
				if (filtroOpcoes.length === 1) {
					const unicoValor = filtroOpcoes[0];

					if ($scope.filtros[campo] !== unicoValor) {
						$scope.filtros[campo] = unicoValor;
					}

					// Só desabilitar se não for o primeiro filtro
					$scope.selectDesabilitado[campo] = ($scope.primeiroFiltro !== campo);
				} else {
					$scope.selectDesabilitado[campo] = false;
				}
			}
		});

		// Bloquear o campo do primeiroFiltro se mais de um filtro tiver sido aplicado
		if ($scope.primeiroFiltro != campoAtual && valorAtual) {
			$scope.selectDesabilitado[$scope.primeiroFiltro] = true;
		} else {
			$scope.selectDesabilitado[$scope.primeiroFiltro] = false;
		}

		// Aplicar todos os filtros
		let acoesFiltradas = $scope.acoesPrioritarias;
		
		for (const [campo, valor] of Object.entries($scope.filtros)) {
			if (valor) {
				acoesFiltradas = acoesFiltradas.filter(acao => acao[campo] === valor);
			}
		}
		
		$scope.acoesFiltro = acoesFiltradas;
	};
});
</script>

<div
	id="conteudo"
	class="content-page container text-justify"
	data-ng-app="monitoramentoPde"
	data-ng-controller="acoesPrioritarias">
	
	<?php the_content(); ?>

	<div class="row" style="margin:0;">	
		<hr>
		<div class="row">
			<div class="row" style="font-size: 0">
				<div class="col-sm-5" style="display:inline-block;float:none;">
					<div style="padding-left:15px; font-size:18px; margin-top: 10px; margin-bottom: 10px;">
						<strong> Ação Prioritária </strong>
					</div>
				</div>
				<div class="col-sm-1 acao-prioritaria" style="display:inline-block;float:none;vertical-align:middle;"> 
					<strong>Artigo</strong>
				</div>
				<label
					for="politica_setorial"
					class="col-sm-3 acao-prioritaria"
					style="
						display:inline-block;
						float:none;
						vertical-align:middle;
						padding-left:7px">
					<strong>Política Setorial</strong>
				</label>
				<label
					for="sistemas_urbanos_e_ambientais"
					class="col-sm-3 acao-prioritaria"
					style="
						display:inline-block;
						float:none;
						vertical-align:middle;
						padding-left:7px"> 
					<strong>Sistemas Urbanos e Ambientais</strong>
				</label>
			</div>			
			<div class="row" style="font-size: 0">
				<div class="col-sm-5" style="display:inline-block;float:none;">
				</div>
				<div class="col-sm-1" style="display:inline-block;float:none;">
				</div>
				<div
					class="col-sm-3 text-center acao-prioritaria-cbo"
					style="
						display:inline-block;
						float:none;
						vertical-align:middle;
						padding-left:7px">
					<select
						style="width: 100%"
						id="politica_setorial"
						ng-model="filtros.politica_setorial"
						ng-options="politica for politica in opcoes.politica_setorial"
						ng-change="atualizarFiltros('politica_setorial')"
						ng-disabled="selectDesabilitado.politica_setorial">
						<option value="">Todos</option>
					</select>
				</div>
				<div
					class="col-sm-3 text-center acao-prioritaria-cbo"
					style="display:inline-block;float:none;vertical-align:middle;padding-left:0"> 
					<select
						style="width: 100%"
						id="sistemas_urbanos_e_ambientais"
						ng-model="filtros.sistemas_urbanos_e_ambientais"
						ng-options="sistema for sistema in opcoes.sistemas_urbanos_e_ambientais"
						ng-change="atualizarFiltros('sistemas_urbanos_e_ambientais')"
						ng-disabled="selectDesabilitado.sistemas_urbanos_e_ambientais">
						<option value="">Todos</option>
					</select>
				</div>
			</div>			
			<ul class="list-group">
				<li
					class="list-group-item row list-pontilhada"
					data-ng-repeat="acao in acoesFiltro | orderBy: 'artigo'">
					<div class="col-sm-5 acao-prioritaria"> {{acao.acao_prioritaria }} </div>
					<div class="col-sm-1 acao-prioritaria"> <small>{{acao.artigo}}</small> </div>
					<div class="col-sm-3 acao-prioritaria"> {{acao.politica_setorial}} </div>
					<div class="col-sm-3 acao-prioritaria"> {{acao.sistemas_urbanos_e_ambientais}} </div>
				</li>
			</ul>
		</div>	
	</div>
</div>

<?php wp_link_pages(['before' => '<nav class="page-nav"><p>' . __('Pages:', 'sage'), 'after' => '</p></nav>']); ?>
