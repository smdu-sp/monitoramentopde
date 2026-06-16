<script>
jQuery.noConflict();

var app = angular.module('monitoramentoPde', ['ngResource','ngAnimate','ui.bootstrap','angular.filter']);

app.controller("tabelaDinamica", function($scope, $http, $filter, $q) {
    const urlDados = `/wp-json/monitoramento_pde/v1/tabelas_dinamicas?tabela=${NOME_TABELA}/`;
    const urlColunas = `/wp-json/monitoramento_pde/v1/tabelas_dinamicas_colunas?tabela=${NOME_TABELA}/`;

    $scope.filtros = {};
    $scope.opcoes = {};
    $scope.selectDesabilitado = {};
    $scope.todosOsCampos = [];
    $scope.titulosCampos = [];
    $scope.camposComFiltro = [];
    $scope.temFiltro = false;

    $q.all([
        $http.get(urlDados),
        $http.get(urlColunas)
    ]).then(function(responses) {
        const acoesBrutas = responses[0].data;
        const colunasEndpoint = responses[1].data; 

        $scope.titulosCampos = colunasEndpoint.sort((a, b) => a.ordem - b.ordem);

        const chavesPermitidas = $scope.titulosCampos.map(coluna => coluna.campo);

        const acoesLimpas = acoesBrutas.map(acaoBruta => {
            let acaoLimpa = {};
            chavesPermitidas.forEach(chave => {
                if (acaoBruta[chave] !== undefined) {
                    acaoLimpa[chave] = acaoBruta[chave];
                }
            });
            return acaoLimpa;
        });

        $scope.acoesPrioritarias = acoesLimpas;
        $scope.acoesFiltro = acoesLimpas;
        $scope.todosOsCampos = chavesPermitidas;

        $scope.camposComFiltro = $scope.titulosCampos
            .filter(meta => meta.possui_filtro === true)
            .map(meta => meta.campo);

        $scope.temFiltro = $scope.camposComFiltro.length > 0;

        $scope.camposComFiltro.forEach(campo => {
            $scope.filtros[campo] = null;
            $scope.opcoes[campo] = $filter('unique')(acoesLimpas, campo).map((obj => obj[campo]));
            $scope.selectDesabilitado[campo] = false;
        });
    }).catch(function(error) {
        console.error("Erro ao carregar os dados da tabela dinâmica:", error);
    });

    $scope.atualizarFiltros = (campoAtual) => {
        const valorAtual = $scope.filtros[campoAtual];

        if (!$scope.primeiroFiltro && valorAtual) {
            $scope.primeiroFiltro = campoAtual;
        }

        if (!$scope.filtros[$scope.primeiroFiltro]) {
            $scope.primeiroFiltro = null;

            $scope.camposComFiltro.forEach(campo => {
                $scope.selectDesabilitado[campo] = false;
                $scope.opcoes[campo] = $filter('unique')($scope.acoesPrioritarias, campo).map((obj => obj[campo]));
                $scope.filtros[campo] = null;
            });
        }

        $scope.camposComFiltro.forEach(campo => {
            if (campo !== $scope.primeiroFiltro) {
                let baseFiltrada = $scope.acoesPrioritarias;
                
                for (const filtroCampo of $scope.camposComFiltro) {
                    const valor = $scope.filtros[filtroCampo];
                    if (filtroCampo !== campo && valor) {
                        baseFiltrada = baseFiltrada.filter(acao => acao[filtroCampo] === valor);
                    }
                }

                const filtroOpcoes = $filter('unique')(baseFiltrada, campo).map((obj => obj[campo]));
                $scope.opcoes[campo] = filtroOpcoes;

                if (filtroOpcoes.length === 1) {
                    const unicoValor = filtroOpcoes[0];
                    if ($scope.filtros[campo] !== unicoValor) {
                        $scope.filtros[campo] = unicoValor;
                    }
                    $scope.selectDesabilitado[campo] = ($scope.primeiroFiltro !== campo);
                } else {
                    $scope.selectDesabilitado[campo] = false;
                }
            }
        });

        if ($scope.primeiroFiltro != campoAtual && valorAtual) {
            $scope.selectDesabilitado[$scope.primeiroFiltro] = true;
        } else {
            $scope.selectDesabilitado[$scope.primeiroFiltro] = false;
        }

        let acoesFiltradas = $scope.acoesPrioritarias;
        
        $scope.camposComFiltro.forEach(campo => {
            const valor = $scope.filtros[campo];
            if (valor) {
                acoesFiltradas = acoesFiltradas.filter(acao => acao[campo] === valor);
            }
        });
        
        $scope.acoesFiltro = acoesFiltradas;
    };
});
</script>

<div
	id="conteudo"
	class="content-page container text-justify"
	data-ng-app="monitoramentoPde"
	data-ng-controller="tabelaDinamica">
	
	<?php the_content(); ?>

	<div class="row" style="margin:0;">	
		<hr>
		<div class="table-responsive" style="margin-top: 20px;">
			<table class="table table-striped table-hover">
				<thead>
					<tr>
						<th 
                            data-ng-repeat="coluna in titulosCampos" 
                            style="vertical-align: middle; font-size: 16px;">
							<strong>{{ coluna.titulo }}</strong>
						</th>
					</tr>
					
					<tr data-ng-show="temFiltro">
						<th data-ng-repeat="coluna in titulosCampos">
							<select
								data-ng-if="coluna.possui_filtro"
								style="width: 100%; font-weight: normal;"
								data-ng-model="filtros[coluna.campo]"
								data-ng-options="opcao for opcao in opcoes[coluna.campo]"
								data-ng-change="atualizarFiltros(coluna.campo)"
								data-ng-disabled="selectDesabilitado[coluna.campo]">
								<option value="">Todos</option>
							</select>
						</th>
					</tr>
				</thead>
				
				<tbody>
					<tr data-ng-repeat="acao in acoesFiltro">
						<td data-ng-repeat="coluna in titulosCampos" style="vertical-align: middle;">
							{{ acao[coluna.campo] }}
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>

<?php wp_link_pages(['before' => '<nav class="page-nav"><p>' . __('Pages:', 'sage'), 'after' => '</p></nav>']); ?>
