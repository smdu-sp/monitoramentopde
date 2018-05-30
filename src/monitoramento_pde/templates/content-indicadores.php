<script type="text/javascript">
jQuery.noConflict();

var app = angular.module('monitoramentoPde', ['ngResource','ngAnimate','ui.bootstrap','angular.filter']);

app.factory('Indicador',function($resource){
	return $resource('/wp-json/monitoramento_pde/v1/indicador/:id');
});

app.factory('Menu',function($resource){
	return $resource('/wp-json/wp-api-menus/v2/menus/:id');
});

app.controller("indicadores", function($scope, $http, $filter, Indicador, Menu) {

	Indicador.query(function(indicadores) {
		$scope.indicadores = indicadores;
		//$scope.cargaIndicadorValores();
	 });

	 Menu.get({id:4},function(menu){
		 $scope.menuForma = menu;
	 });
	 
	 $scope.estrategias = [
			'socializar os ganhos da produção da cidade',
			'assegurar o direito à moradia digna para quem precisa',
			'melhorar a mobilidade urbana',
			'qualificar a vida urbana dos bairros',
			'orientar o crescimento da cidade nas proximidades do transporte público',
			'reorganizar as dinâmicas metropolitanas',
			'promover o desenvolvimento econômico da cidade',
			'incorporar a agenda ambiental ao desenvolvimento da cidade',
			'preservar o patrimônio e valorizar as iniciativas culturais',
			'fortalecer a participação popular nas decisões dos rumos da cidade'
	 ];
	 
	 $scope.instrumentos = [
			'fundurb',
			'eixos de estruturação da transformação urbana',
			'zonas produtivas [zpi e zde]',
			'perímetros de incentivo ao desenvolvimento econômico',
			'parcelamento, edificação e utilização compulsórios',
			'iptu progressivo no tempo',
			'cota de solidariedade',
			'zeis',
			'regularização fundiária',
			'tac',
			'tca',
			'eia-rima',
			'zonas de proteção ambiental [zepam e zep]',
			'tdc',
			'oodc',
			'ouc',
			'zepec',
			'tombamento'
	 ];
	 
});

</script>

<style type="text/css">
  .panel-heading{
		background-color:#FFF !important;
		color:#000 !important;
		border-top-color:#211D1F !important;
		border-top-width:2px !important;
		border-top-style:dotted !important;
		border-left-width:0;
		border-right:0;
		border-bottom:0;
		font-size:85% !important;
		border-bottom-left-radius:0px !important;
		border-bottom-right-radius:0px !important;
		border-top-left-radius:0px !important;
		border-top-right-radius:0px !important;
	}
	.panel-heading:last-child{
		border-bottom-color:#211D1F !important;
		border-bottom-width:2px !important;
		border-bottom-style:dotted !important;
	}
</style>

<div class="content-page container text-justify" data-ng-app="monitoramentoPde" data-ng-controller="indicadores">

<?php the_content(); ?>

		<p>
		Se desejar, filtre por estratégia
		<br>
		<select style="min-width:250px;max-width:400px;" data-ng-model="optEstrategia" data-ng-options="estrategia for estrategia in estrategias" >
		<option value="">Todas</option>
		</select>
		</p>
		<p>
		Ou por instrumento urbanístico e de gestão ambiental
		<br>
		<select style="min-width:250px;max-width:400px;" data-ng-model="optInstrumento" data-ng-options="instrumento for instrumento in instrumentos">
		<option value="">Todos</option>
		</select>
		</p>

		<uib-accordion close-others="true">

			<div uib-accordion-group is-open="indicador.aberto" class="panel-default" close-others="true" ng-repeat="indicador in indicadores">
				<uib-accordion-heading>
					<span  class="cabecalho-ficha-indicador"> {{indicador.nome}} </span>
						<i class="pull-right glyphicon" ng-class="{'glyphicon-chevron-up': indicador.aberto, 'glyphicon-chevron-down': !indicador.aberto}"></i>
					</span>
				</uib-accordion-heading>
					<div class="col-sm-6"> 
						<p>
						Este indicador visa apresentar como se dá a distribuição nas diferentes partes do Município do Potencial Construtivo adicional (PCA). Lorem ipsum Lorem ipsum Lorem ipsum Lorem ipsum Lorem ipsum Lorem ipsum Lorem ipsum Lorem ipsum Lorem ipsum Lorem ipsum Lorem ipsum Lorem ipsum Lorem ipsum Lorem ipsum Lorem ipsum Lorem ipsum Lorem ipsum 
						</p>
						<h5><strong>Fórmula de cálculo </strong> </h5>
						<p>(área grafada como ZEPAM na Macrozona de Estruturação e Qualificação Urbana)/(área grafada como ZEPAM no Município) * 100
						</p>
						<h5><strong> Fontes </strong></h5>
						<p>Lei Municipal n° 16.402/16 - Lei de Parcelamento, Uso e Ocupação do Solo
						IBGE - Mapeamento do desenvolvimento econômico
						</p>
						<h5> <strong> Nota técnica </strong></h5>
						<p> Este indicador possui a limitação de não representar a totalidade lorem ipsum lorem ipsum 
						</p>
						</div>
						<div class="col-sm-6"> 
						<h5> <strong> Instrumento urbanístico relacionado </strong></h5>
						<p> Outorga Onerosa do Direito de Construir (OODC) </p>
						<h5> <strong> Categoria </strong></h5>
						<p> Arrecadação de recursos </p>
					<h5> <strong> Unidades de medida</strong></h5>
						<p> 1° fator de cálculo: metros quadrados (m²) </p>
						<p> 2° fator de cálculo: metros quadrados (m²) </p>
						<h5> <strong> Periodicidade de atualização</strong> </h5>
						<p> anual </p>
						<h5> <strong> Unidades de análise territorial (desagregação espacial)</strong> </h5>
						<p> Município
								<br>Macroárea
								<br>Região
								<br>Subprefeitura
								<br>Distrito</p>
						</div>
			<!--<div uib-accordion-group class="panel-default"  heading=" {{indicador.nome}} &nbsp; | &nbsp; Instrumento: {{indicador.nome_fonte_dados}}"  ng-repeat="indicador in indicadores">-->
			
			</div>
		</uib-accordion>

		<!--
		<div class="row" style="margin:0;">
		
				<ul class="list-group">
					<li class="list-group-item row list-pontilhada" data-ng-repeat="indicador in indicadores | orderBy: 'nome'">
						<div class="row">
						<div class="col-sm-12"> {{indicador.nome}} </div>
						</div>
						<div class="col-sm-6"> 
						<p>
						Este indicador visa apresentar como se dá a distribuição nas diferentes partes do Município do Potencial Construtivo adicional (PCA). Lorem ipsum Lorem ipsum Lorem ipsum Lorem ipsum Lorem ipsum Lorem ipsum Lorem ipsum Lorem ipsum Lorem ipsum Lorem ipsum Lorem ipsum Lorem ipsum Lorem ipsum Lorem ipsum Lorem ipsum Lorem ipsum Lorem ipsum 
						</p>
						<h5><strong>Fórmula de cálculo </strong> </h5>
						<p>(área grafada como ZEPAM na Macrozona de Estruturação e Qualificação Urbana)/(área grafada como ZEPAM no Município) * 100
						</p>
						<h5><strong> Fontes </strong></h5>
						<p>Lei Municipal n° 16.402/16 - Lei de Parcelamento, Uso e Ocupação do Solo
						IBGE - Mapeamento do desenvolvimento econômico
						</p>
						<h5> <strong> Nota técnica </strong></h5>
						<p> Este indicador possui a limitação de não representar a totalidade lorem ipsum lorem ipsum 
						</p>
						</div>
						<div class="col-sm-6"> 
						<h5> <strong> Instrumento urbanístico relacionado </strong></h5>
						<p> Outorga Onerosa do Direito de Construir (OODC) </p>
						<h5> <strong> Categoria </strong></h5>
						<p> Arrecadação de recursos </p>
					<h5> <strong> Unidades de medida</strong></h5>
						<p> 1° fator de cálculo: metros quadrados (m²) </p>
						<p> 2° fator de cálculo: metros quadrados (m²) </p>
						<h5> <strong> Periodicidade de atualização</strong> </h5>
						<p> anual </p>
						<h5> <strong> Unidades de análise territorial (desagregação espacial)</strong> </h5>
						<p> Município
								<br>Macroárea
								<br>Região
								<br>Subprefeitura
								<br>Distrito</p>
						</div>
					</li>
				</ul>
			</div>-->
			
		</div>
		
</div>
<?php wp_link_pages(['before' => '<nav class="page-nav"><p>' . __('Pages:', 'sage'), 'after' => '</p></nav>']); ?>
