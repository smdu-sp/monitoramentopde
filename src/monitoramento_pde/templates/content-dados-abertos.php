<script type="text/javascript">
jQuery.noConflict();

var app = angular.module('monitoramentoPde', ['ngResource','ngAnimate','ui.bootstrap','angular.filter']);

app.factory('FontesDados', function($resource){
	return $resource('/wp-json/monitoramento_pde/v1/fontes_dados/');
});

app.factory('DadoAberto', function($resource){
	return $resource('/wp-json/monitoramento_pde/v1/dado_aberto/:fonte_dados');
});

app.factory('Instrumentos', function($resource){
	return $resource('/wp-json/monitoramento_pde/v1/instrumentos/');
});

app.filter('trustedHtml',
   function($sce) {
     return function(ss) {
       return $sce.trustAsHtml(ss)
   };
});

app.controller("dadosAbertos", function($scope, $http, $filter, FontesDados, DadoAberto) {
 
 	FontesDados.query({ativa:true},function(fontesDados) {
		$scope.fontesDados = fontesDados;
	
	$scope.menuTipoDados = [
		{	
			titulo:'Banco de dados',
			introducao: 'Os indicadores são fruto do cálculo e cruzamento de dados organizados em bancos que alimentam o sistema de monitoramento e avaliação. <br> Veja abaixo a lista de bancos de dados. ',
			tipoArquivo:['XLSX',' | CSV', ' | TXT'],
			seletor:'instrumento',
			dados:$scope.fontesDados
			//['oodc','tdc','tdc_certidoes','zepam','cota-solidariedade'];
		},
		{	
			titulo:'Ficha técnica dos instrumentos', 
			introducao: 'Os Instrumentos Urbanísticos e de Gestão Ambiental são meio para viabilizar a efetivação dos princípios e objetivos do Plano Diretor.<br> Veja abaixo a lista dos instrumentos. <br> Se desejar, filtre por estratégia: <br> ',
			tipoArquivo:['DOC |','PDF'],
			seletor:'estrategia',
			dados:['Transferência do direito de construir','Estudo de Impacto Ambiental','Estudo de viabilidade ambiental', 'Avaliação ambiental estratégica']
		},
		{
			titulo:'Ficha técnica dos indicadores',
			introducao: 'Os indicadores de monitoramento e avaliação contemplam, abordando a eficiência, eficácia e efetividade, das diferentes dimensões de avaliação das políticas públicas presentes no Plano Diretor. <br> Veja abaixo a lista dos indicadores. <br> Se desejar, filtre por estratégia: <br> 		',
			tipoArquivo:['DOC','PDF'],
			seletor:'estrategia',
			dados:['Percentual de áreas grafadas como ZEPAM','Variação da cobertura vegetal em ZEPAM', 'Densidade de ZEPAM por habitante', 'Distribuição de usos nas áreas marcadas como ZEPAM']
		}
	];
	
	//$scope.menuTipoDados[0].dados.push('Ficha Técnica dos Instrumentos');
	
	$scope.item = $scope.menuTipoDados[0];
	
	 $scope.estrategias = [
			{id:1,nome:'Socializar os ganhos da produção na cidade'},
			{id:2,nome:'Assegurar o direito à moradia digna para quem precisa'},
			{id:3,nome:'Melhorar a mobilidade urbana'},
			{id:4,nome:'Qualificar a vida nos bairros'},
			{id:5,nome:'Orientar o crescimento da cidade nas proximidades do transporte público'},
			{id:6,nome:'Reorganizar as dinâmicas metropolitanas'},
			{id:7,nome:'Promover o desenvolvimento econômico da cidade'},
			{id:8,nome:'Incorporar a agenda ambiental ao desenvolvimento da cidade'},
			{id:9,nome:'Preservar o patrimonio e valorizar as iniciativas culturais'},
			{id:10,nome:'Fortalecer a participação popular nas decisoes dos rumos da cidade'}
	 ];
	 
	$scope.instrumentos = [
		{id:13,nome:'FUNDURB'},
		{id:12,nome:'Eixos de Estruturação da Transformação Urbana [EETU]'},
		{id:18,nome:'Zonas Produtivas [ZPI+ZDE]'},
		{id:15,nome:'Perímetros de Incentivo ao Desenvolvimento Econômico'},
		{id:16,nome:'Parcelamento, Edificação e Utilização Compulsórios [PEUC]'},
		{id:14,nome:'IPTU Progressivo no Tempo'},
		{id:22,nome:'ZEIS'},
		{id:23,nome:'Regularização Fundiária'},
		{id:24,nome:'Termo de Compensação Ambiental [TCA]'},
		{id:11,nome:'EIA-RIMA'},
		{id:19,nome:'ZEPAM'},
		{id:17,nome:'Transferência do Direito de Construir [TDC]'},
		{id:26,nome:'Outorga Onerosa do Direito de Construir [OODC]'},
		{id:25,nome:'Operação Urbana Consorciada [OUC]'},
		{id:21,nome:'ZEPEC'},
		{id:20,nome:'Tombamento'}
	 ];	 
	});

 	// ISSUE 53
	$scope.formataData = function(rawDate) {
		let dataFinal = $filter('date')(rawDate, 'MMMM yyyy');
		dataFinal = dataFinal.charAt(0).toUpperCase() + dataFinal.slice(1); // Torna primeira letra maiúscula
		return "Atualizado em: "+dataFinal;
	}
	
	$scope.pontoParaVirgula = function(v){
		if(v !==null && !isNaN(parseFloat(v))){
			let vString = v.toString();
			if(vString.length - vString.indexOf('.') === 3 || vString.length - vString.indexOf('.') === 2){
				if(vString.charAt(vString.length - 1) === '0' && vString.charAt(vString.length - 2) === '.'){
					return vString.split('.')[0];
				}
				v = vString.replace('.',',');
			}
		}
		return v;
	}
	
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
			
			
		function sheet_from_array_of_objects(data, offset) {
			var ws = {};
			data.unshift(data[0]); // Duplica primeiro item do array para evitar supressão dos valores
			var range = {s: {c:10000000, r:10000000}, e: {c:0, r:0 }};
			
			for(var R = 0; R < data.length; R++) {
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
							var cell = {v: data[R][prop] };
						
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
	
	$scope.exportarDadoAberto = function(id_fonte, formato){
		DadoAberto.query({fonte_dados:id_fonte},function(dadoAberto) {
			// ALTERA PONTO PARA VIRGULA
			for(dado in dadoAberto){
				for(valor in dadoAberto[dado]){
		 			dadoAberto[dado][valor] = $scope.pontoParaVirgula(dadoAberto[dado][valor]);
		 		}
		 	}
		 	// FIM ALTERA PONTO PARA VIRGULA
			$scope.dadoAberto = dadoAberto;
		 	fonteDados = $scope.item.dados.filter((fonteDados) => fonteDados.id_fonte_dados == id_fonte)[0];
			var wb = new Workbook();
			
			wsDadoAberto = sheet_from_array_of_objects(dadoAberto,0);
			wb.SheetNames.push('dados');
			wb.Sheets['dados'] = wsDadoAberto;
			
			switch(formato){
				case 'XLSX':
					extensaoArquivo = 'xlsx';
					var wbout = XLSX.write(wb, {bookType:'xlsx', bookSST:false, type: 'binary'});
					break;
				case ' | CSV':
					extensaoArquivo = 'csv';
					var wbout = XLSX.utils.sheet_to_csv(wsDadoAberto,{FS:";"});
					break;
				case ' | TXT':
					extensaoArquivo = 'txt';
					var wbout = XLSX.utils.sheet_to_csv(wsDadoAberto,{FS:"\t"});
					break;
			}
			
			saveAs(new Blob([s2ab(wbout)],{type:"application/octet-stream"}), fonteDados.nome_tabela + '.'  + extensaoArquivo);
			
		});
	}
	
	$scope.debugLog = function (el) {
		console.warn("DebugLOG:");
		console.log(el);
	}
});

</script>


<div class="content-page container text-justify" data-ng-app="monitoramentoPde" data-ng-controller="dadosAbertos">
<?php the_content(); ?>

	<!--<uib-tabset active="active" type="pills">
		<uib-tab index="$index + 1" ng-repeat="item in menuTipoDados" heading="{{item.titulo}}" classes="{{item.classes}}">-->
			
			<p><span ng-bind-html="item.introducao | trustedHtml"></span>

			</p>
		<ul class="list-group">
			
			<li class="list-group-item row list-pontilhada" data-ng-repeat="dado in item.dados | orderBy: 'nome'">
				<div class="col-sm-8">
					<span><b>{{!dado.nome? dado : dado.nome}}</b></span>
					<!-- ISSUE 52 -->
					<br>
					<span>{{dado.data_atualizacao ? formataData(dado.data_atualizacao) : ''}}</span>					
				</div>
				<div class="col-sm-4 text-right"> <a href="" ng-click="exportarDadoAberto(dado.id_fonte_dados,formato)" data-ng-repeat="formato in item.tipoArquivo"> <strong> {{formato}} </strong></a>
					<a ng-if="dado.arquivo_metadados" href="<?php echo bloginfo('url'); ?>/app/uploads/{{dado.nome_tabela}}/{{dado.arquivo_metadados}}"><strong> | Metadados</strong></a> 
					<a ng-if="dado.arquivo_mapas" href="<?php echo bloginfo('url'); ?>/app/uploads/{{dado.nome_tabela}}/{{dado.arquivo_mapas}}"><strong> | SHP</strong></a> 
					<a ng-if="dado.arquivo_tabelas" href="<?php echo bloginfo('url'); ?>/app/uploads/{{dado.nome_tabela}}/{{dado.arquivo_tabelas}}"><strong> | Tabelas</strong></a>
				</div>

			</li>
			
		</ul>
		
			<!--<hr>
			<ul class="list-group row">
				<li class="list-group-item col-sm-3 text-left list-visualizacao" ng-repeat="itemFilho in item.children"><a href="{{itemFilho.url}}" class="row"> <img class="img-responsive icones-visualizacao col-sm-3" src="/app/themes/monitoramento_pde/images/icones/{{itemFilho.description}}.png"><span class="col-sm-9"><strong>{{itemFilho.title}}</strong></span></a> </li>
				
			</ul>-->
		<!--</uib-tab>
	</uib-tabset>-->

</div>
<?php wp_link_pages(['before' => '<nav class="page-nav"><p>' . __('Pages:', 'sage'), 'after' => '</p></nav>']); ?>
