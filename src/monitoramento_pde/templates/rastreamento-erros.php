<?php
/**
 * Template Name: Rastreamento de Erros
 */
	
get_template_part('templates/page', 'header');
the_content();

$autorizado = false;
$roleMonitoramento = '';
if (is_user_logged_in()) {
	$usuario = wp_get_current_user();
	if (in_array('mantenedor', $usuario->roles)) $roleMonitoramento = 'mantenedor';
	if (in_array('administrator', $usuario->roles)) $roleMonitoramento = 'administrator';
}
if ($roleMonitoramento) $autorizado = true;
if ($autorizado) {
	// Carregamento dos erros reportados
	$query = <<<SQL
		SELECT *
		FROM mpde_problema_indicador
	SQL;
	$resultado = $wpdb->get_results($query);

	// Remove registros do array caso contenham campo nulo
	foreach ($resultado as $indice => $objeto) {
		foreach ($objeto as $chave => $valor) {
			if ($valor == null) {
				unset($resultado[$indice]);
				break;
			}
		}
	}

	$resultado = array_values($resultado);
	$problemas = json_encode($resultado); ?>

	<script type="text/javascript">
		jQuery.noConflict();

		var app = angular.module('rastreamentoErros', ['ngResource', 'ngAnimate', 'ui.bootstrap', 'ngRoute', 'ngSanitize']);

		app.factory('Indicador', function($resource) {
			return $resource('/wp-json/monitoramento_pde/v1/indicador/:id',{id:'@id_indicador'}, {
				get: {
					headers: {
						'X-WP-Nonce': '<?php  echo(wp_create_nonce('wp_rest')); ?>'
					}
				}, query: {
					isArray: true,
					cancellable: true,
					headers: {
						'X-WP-Nonce': '<?php  echo(wp_create_nonce('wp_rest')); ?>'
					}	
				}
			});
		});

		app.factory('Menu', function($resource) {
			return $resource('/wp-json/wp-api-menus/v2/menus/:id');
		});

		app.controller("dashboard", function($scope, $rootScope, Indicador, Menu) {

			// Inicializa variáveis
			$scope.tabAtivaProblemas = 1;
			$scope.carregandoProblemas = true;
			$scope.problemas = <?=$problemas?>;
			$scope.problemas.carregados = 0;
			// Recupera nomes dos indicadores
			$scope.problemas.forEach(obj => {
				Indicador.query({indicador: obj.id_indicador}, (indicador) => {
					obj.nome_indicador = indicador[0].nome;
					$scope.problemas.carregados++;
					if ($scope.problemas.carregados == $scope.problemas.length) {
						$scope.carregandoProblemas = false;
					}
				});
			});
			
			Menu.get({id: 7}, menu => $scope.menuProblemas = menu);

			// Execução de funções ao abrir e fechar o accordion
			$scope.problemas.forEach((problema, indice) => {
				var aberto = false;
				Object.defineProperty(problema, "aberto", {
					get: function() {
						return aberto;
					},
					set: function(novoValor) {
						aberto = novoValor;
						if (aberto && $scope.problemas[indice].novo == 1) {
							$scope.atualizarEstado($scope.problemas[indice].id);
						}
					}
				});
			});

			$scope.atualizarEstado = id => {
				$scope.problemas.forEach((obj, indice) => {
					if (obj.id == id) {
						$scope.problemas[indice].novo = 0;
					}
				});
			};    
		});
	</script>

	<div id="conteudo" data-ng-app="rastreamentoErros" data-ng-controller="dashboard">

		<script type="text/ng-template" id="problema.html">		
		</script>
		
		<div class="container abas-container" ng-show="!carregandoProblemas">
			<uib-tabset active="tabAtivaProblemas" type="pills">
				<uib-tab index="$index + 1" ng-repeat="item in menuProblemas.items" heading="{{item.title}}" classes="{{item.classes}}">
					<hr>
				</uib-tab>
			</uib-tabset>
			<uib-accordion close-others="true">
				<div uib-accordion-group is-open="problema.aberto" class="panel-default" close-others="true" ng-repeat="problema in problemas | filter: {resolvido: tabAtivaProblemas - 1}">
					<uib-accordion-heading>
						<span>#{{problema.id}} - Indicador "{{problema.nome_indicador}}"</span><br><small class="novo-problema" ng-show="problema.novo == 1">Novo problema</small>
					</uib-accordion-heading>
					<div ng-include src="problema.aberto ? 'problema.html' : ''"></div>
				</div>
			</uib-accordion>
		</div>
	</div>

	<style>
		.novo-problema {
			display: inline-block;
			background-color: #d6342a;
			color: #fff !important;
			padding: 5px;
			border-radius: 20px;
			margin-top: 3px !important;
		}
	</style>
<?php 
} else { ?>
	<div class="content-page container text-justify">
		<h4>Você não possui autorização para visualizar esse conteúdo.</h4>
	</div>
<?php 
} ?>
