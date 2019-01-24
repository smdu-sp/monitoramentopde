<?php
/**
 * Template Name: Cadastro Fonte Dados
 */
?>



<script type="text/javascript">
jQuery.noConflict();

var app = angular.module('monitoramentoPde', ['ngResource','ngAnimate','ui.bootstrap','angular.filter']);

app.factory('FonteDados',function($resource){
	return $resource('/wp-json/monitoramento_pde/v1/fontes_dados/:id',{id:'@id_fonte_dados'},{
		update:{
			method:'PUT'
		},
		get:{
			headers:{
				'X-WP-Nonce': '<?php  echo(wp_create_nonce('wp_rest')); ?>'
			}
		},
		query:{
			isArray:true,
			headers:{
				'X-WP-Nonce': '<?php  echo(wp_create_nonce('wp_rest')); ?>'
			}
		}
	});
});

var cargaUpdateParams = {
	method:'POST',
	transformRequest: function(data) {
			if (data === undefined){
				return data;
				}

			var fd = new FormData();
			angular.forEach(data, function(value, key) {
				if (value instanceof FileList) {
					if (value.length == 1) {
						fd.append(key, value[0]);
					} else {
						angular.forEach(value, function(file, index) {
							fd.append(key + '_' + index, file);
						});
					}
				} else {
					if(value instanceof File){
						fd.append('arquivo', value);
					}
					else if(value instanceof Array){
						fd.append('arquivo', value[0]);
					}else
						fd.append(key, value);
				}
			});
			
			return fd;					
		},
	headers:{
		'X-WP-Nonce': '<?php  echo(wp_create_nonce('wp_rest')); ?>'
		,'Content-type': undefined
	}
};
app.factory('FonteDadosCarregar',function($resource){
	return $resource('/wp-json/monitoramento_pde/v1/fontes_dados/carregar/:id',{id:'@id_fonte_dados'},{
		update: cargaUpdateParams
	});
});

app.factory('ArquivoMapasCarregar',function($resource){
	return $resource('/wp-json/monitoramento_pde/v1/fontes_dados/carregar_arquivo_mapas/:id',{id:'@id_fonte_dados'},{
		update: cargaUpdateParams
	});
});

app.factory('ArquivoMetadadosCarregar',function($resource){
	return $resource('/wp-json/monitoramento_pde/v1/fontes_dados/carregar_arquivo_metadados/:id',{id:'@id_fonte_dados'},{
		update: cargaUpdateParams
	});
});

app.factory('Usuarios',function($resource){
	return $resource('/wp-json/wp/v2/users');
});

app.factory('Territorios',function($resource){
	return $resource('/wp-json/monitoramento_pde/v1/territorios');
});

app.factory('FonteDadosColuna',function($resource){
	return $resource('/wp-json/monitoramento_pde/v1/fonte_dados_coluna/:id',{id:'@id_fonte_dados'},{
		update:{
			method:'PUT'
		}
	});
});

app.controller("cadastroFonteDados", function($scope, $rootScope, $http, $filter, $uibModal, FonteDados, Usuarios, FonteDadosCarregar, ArquivoMapasCarregar, ArquivoMetadadosCarregar, Territorios, FonteDadosColuna, uibDateParser) {

	$scope.lerArquivos = function(element) {
		
		$scope.$apply(function($scope) {
		// Turn the FileList object into an Array
			$scope.arquivos = [];
			for (var i = 0; i < element.files.length; i++) {
				$scope.arquivos.push(element.files[i])
			}
		});
	};

	$scope.inicializarDatepicker = {
		datepickerMode: 'month',
		minMode:'month'
	}
	
	$scope.apiNonce = '<?php  echo(wp_create_nonce('wp_rest')); ?>';
	//$scope.idUsuario = '<?php echo(get_current_user_id()); ?>';
	
  Usuarios.query(function(usuarios) {
		  $rootScope.usuarios = usuarios;
	});
 
 Territorios.query(function(territorios){
		$scope.territorios = territorios;
 });
 
 	FonteDados.query({idUsuario: $scope.idUsuario} ,function(fontesDados) {
		  $rootScope.fontesDados = fontesDados;
	});
	
	$scope.estado = "listar";
	$rootScope.carregandoArquivo = false;
	$scope.tipoArquivo = [{
		nome: 'Excel (.xls)',
		id: 1
	},
	{
		nome: 'Excel (.xlsx)',
		id:2
	},
	{
		nome: 'CSV (.csv)',
		id:3	
	}];
	
	$scope.carregar = function(){
		$scope.itemAtual = $rootScope.fontesDados.filter((fonteDados) => fonteDados.id_fonte_dados == $scope.idItemAtual)[0];
		if($scope.itemAtual != null){
			if($scope.itemAtual.data_atualizacao != null){
				$scope.itemAtual.data_atualizacao = uibDateParser.parse($scope.itemAtual.data_atualizacao,'yyyy-MM-dd');
			}
			if($scope.itemAtual.data_inicial != null){
				$scope.itemAtual.data_inicial = uibDateParser.parse($scope.itemAtual.data_inicial,'yyyy-MM-dd');
			}
			if($scope.itemAtual.data_final != null){
				$scope.itemAtual.data_final = uibDateParser.parse($scope.itemAtual.data_final,'yyyy-MM-dd');
			}
			
			if($scope.itemAtual != null){
				if($scope.itemAtual.data_inicial != null)
					$scope.checkDataInicial = true;
				
				if($scope.itemAtual.data_final != null)
					$scope.checkDataFinal = true;
				
				if($scope.itemAtual.data_atualizacao != null)
					$scope.checkDataAtualizacao = true;
			}
			FonteDadosColuna.query({id:$scope.itemAtual.id_fonte_dados},function(colunas){
				$scope.colunas = colunas;
				$scope.estado = "selecionar";
			});
		}else{
			$scope.colunas = null;
		}
	
	};
	
	$scope.criarModalConfirmacao = function(acao){
		$rootScope.modalConfirmacao = $uibModal.open({
			animation: true,
			ariaLabelledBy: 'modal-titulo-fonte-dados',
			ariaDescribedBy: 'modal-corpo-variavel',
			templateUrl: 'ModalConfirmacao.html',
			controller: 'cadastroFonteDados',
			scope:$scope,
			size: 'md',
		});
		$scope.acao = acao;
	};
	
	$scope.criarModalSucesso = function(){
		$rootScope.modalSucesso = $uibModal.open({
			animation: true,
			ariaLabelledBy: 'modal-titulo-fonte-dados',
			ariaDescribedBy: 'modal-corpo-fonte-dados',
			templateUrl: 'ModalSucesso.html',
			controller: 'cadastroFonteDados',
			scope:$scope.parent,
			size: 'md',
		});
	}
	
	$scope.lancarErro = function(erro){
		console.warn(erro);
		alert('Ocorreu um erro ao modificar a fonte de dados. \n\n Código: ' + erro.data.code + '\n\n Status: ' + erro.statusText + '\n\n Mensagem: ' + erro.data + '\n\n Mensagem Interna: ' + erro.data.message);
	}
	
	$scope.inserirForm = function(){
		$scope.idItemAtual = null;		
		$scope.carregar();		
		$scope.estado = "inserir";
	};
	
	$scope.atualizar = function(){
		console.log('scope.atualizar');
		$rootScope.itemAtual = $scope.itemAtual;
		FonteDadosColuna.update({colunas:$scope.colunas,id_fonte_dados:$scope.itemAtual.id_fonte_dados}).$promise.then(
			function(mensagem){
				FonteDados.update({fonte_dados:$rootScope.itemAtual}).$promise.then(
					function(mensagem){
						FonteDados.query(function(fontesDados) {
							$rootScope.fontesDados = fontesDados;
							
							$rootScope.modalProcessando.close();		
							$scope.criarModalSucesso();
						});

					},
					function(erro){
						$rootScope.modalProcessando.close();
						$rootScope.modalConfirmacao.close();
						// $scope.lancarErro(erro);
						console.log("Erro ao atualizar FonteDados");
						console.warn(erro);
					}
				).catch(function(err){
					console.log("Erro excepcional:");
					console.warn(err);
				});
			},
			function(erro){
				$rootScope.modalProcessando.close();
				$rootScope.modalConfirmacao.close();
				// $scope.lancarErro(erro);
				console.log("Erro ao atualizar FonteDadosCOLUNA");
			}
		);
	};		
	
	$scope.remover = function(){
		console.log('scope.remover');
		FonteDadosColuna.remove({id:$scope.itemAtual.id_fonte_dados}).$promise.then(
			function(mensagem){
				FonteDados.remove({id:$scope.itemAtual.id_fonte_dados}).$promise.then(
					function(mensagem){
						FonteDados.query(function(fontesDados) {
							$rootScope.fontesDados = fontesDados;
							
							$rootScope.modalProcessando.close();		
							$scope.criarModalSucesso();
						});

					},
					function(erro){
						$rootScope.modalConfirmacao.close();
						$scope.lancarErro(erro);
					}
				);
			},
			function(erro){
				$rootScope.modalConfirmacao.close();
				$scope.lancarErro(erro);
			}
		);
				
				
		$scope.$parent.idItemAtual = null;	
		$scope.$parent.estado = "listar";
	};	

	$scope.carregarArquivo = function(tipoUpload){
		console.log('carregarArquivo');
		if(!$rootScope.carregandoArquivo){
			$rootScope.carregandoArquivo = true;
			$rootScope.mensagemArquivo = 'Aguarde... Realizando Carga';
			// TO DO confirmar carregamento
			// CARGA DB FONTE DE DADOS
			switch (tipoUpload) {
				case "metadados":
					console.log("METADADOS");
					ArquivoMetadadosCarregar.update({id_fonte_dados:$scope.itemAtual.id_fonte_dados,arquivos:$scope.arquivos}).$promise.then(
						function(mensagem){
							console.log(mensagem);
							$rootScope.carregandoArquivo = false;
							$rootScope.mensagemArquivo = '';					
							$rootScope.modalProcessando.close();		
							$scope.criarModalSucesso();
						},
						function(erro){
							$rootScope.modalConfirmacao.close();						
							$rootScope.carregandoArquivo = false;
							$rootScope.mensagemArquivo = '';
							// $scope.lancarErro(erro);
							console.log("PRE ERROR:");
							console.log(erro);					
						}
					).catch(function(err){
						console.log("HEL");
						console.error(err);
					});
					break;
				case "mapas":
					console.log("MAPAS ArquivoMapasCarregar");
					ArquivoMapasCarregar.update({id_fonte_dados:$scope.itemAtual.id_fonte_dados,arquivos:$scope.arquivos}).$promise.then(
						function(mensagem){
							console.log(mensagem);
							$rootScope.carregandoArquivo = false;
							$rootScope.mensagemArquivo = '';					
							$rootScope.modalProcessando.close();		
							$scope.criarModalSucesso();
						},
						function(erro){
							$rootScope.modalConfirmacao.close();						
							$rootScope.carregandoArquivo = false;
							$rootScope.mensagemArquivo = '';
							// $scope.lancarErro(erro);
							console.log("PRE ERROR:");
							console.log(erro);					
						}
					).catch(function(err){
						console.log("HEL");
						console.error(err);
					});
					break;
				default:
					FonteDadosCarregar.update({id_fonte_dados:$scope.itemAtual.id_fonte_dados,arquivos:$scope.arquivos}).$promise.then(
						function(mensagem){
							$rootScope.carregandoArquivo = false;
							$rootScope.mensagemArquivo = '';					
							$rootScope.modalProcessando.close();		
							$scope.criarModalSucesso();
						},
						function(erro){
							$rootScope.modalConfirmacao.close();						
							$rootScope.carregandoArquivo = false;
							$rootScope.mensagemArquivo = '';
							$scope.lancarErro(erro);					
						}
					).catch(function(err){
						console.log("HEL");
						console.error(err);
					});
			}
		}else{
			alert('Uma carga de fonte de dados já está acontecendo, espere ela acabar para iniciar outra.');
		};
	}
	
	$scope.inserir = function(){
		FonteDados.save({fonte_dados:$scope.itemAtual}).$promise.then(
			function(mensagem){
				FonteDados.query(function(fontesDados) {
					$rootScope.fontesDados = fontesDados;
					
					$rootScope.modalProcessando.close();		
					$scope.criarModalSucesso();
				});

			},
			function(erro){
				$rootScope.modalConfirmacao.close();
				$scope.lancarErro(erro);
			}
		);
		$scope.$parent.estado = "listar";
		$scope.carregar();
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
	
	$scope.changeDataInicial = function(){
		if(!$scope.checkDataInicial)
			$scope.itemAtual.data_inicial = null;
	}
	
	$scope.changeDataAtualizacao = function(){
		if(!$scope.checkDataAtualizacao)
			$scope.itemAtual.data_atualizacao = null;
	}
	
	$scope.changeDataFinal = function(){
		if(!$scope.checkDataFinal)
			$scope.itemAtual.data_final = null;
	}
	
	$scope.submeter = function(){	
	
		$rootScope.modalConfirmacao.close();
		
		$rootScope.modalProcessando = $uibModal.open({
				animation: true,
				ariaLabelledBy: 'modal-titulo-variavel',
				ariaDescribedBy: 'modal-corpo-variavel',
				templateUrl: 'ModalProcessando.html',
				controller: 'cadastroFonteDados',
				scope:$scope,
				size: 'md',
		});

		// REALIZA AÇÃO DE ACORDO COM O PARÂMETRO ATUAL
		switch($scope.acao){
			case 'Atualizar':
				$scope.acaoExecutando = 'Atualizando';
				$scope.acaoSucesso = 'Atualizada';
				$scope.atualizar();
				break;
			case 'Remover':
				$scope.acaoExecutando = 'Removendo';
				$scope.acaoSucesso = 'Removida';
				$scope.remover();
				break;
			case 'Inserir':
				$scope.acaoExecutando = 'Inserindo';
				$scope.acaoSucesso = 'Inserida';
				$scope.inserir();
				break;
			case 'Carregar':
				$scope.acaoExecutando = 'Carregando';
				$scope.acaoSucesso = 'Carregada';
				$scope.carregarArquivo();
				break;
			case 'Carregar Mapas':
				$scope.acaoExecutando = 'Carregando';
				$scope.acaoSucesso = 'Carregado';
				$scope.carregarArquivo('mapas');
				break;
			case 'Carregar Metadados':
				$scope.acaoExecutando = 'Carregando';
				$scope.acaoSucesso = 'Carregado';
				$scope.carregarArquivo('metadados');
				break;
			default:
				console.warn($scope.acao);
		}

	}
	
	$scope.adicionarElementoColuna = function(){
		if($scope.colunas == null){
			$scope.colunas = [];
		}
		
		$scope.colunas.push({});
	};
	
	$scope.adicionarElementoColunaExclusao = function(){
		if($scope.itemAtual.colunas_exclusao == null){
			$scope.itemAtual.colunas_exclusao = [];
		}
		$scope.itemAtual.colunas_exclusao.push(null);
		
	};
	
	$scope.deletarElementoColuna = function(indice){
		$scope.colunas.splice(indice,1);
	};
	
	$scope.deletarElementoColunaExclusao = function(indice){
		$scope.itemAtual.colunas_exclusao.splice(indice,1);
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
	
	$scope.exportarFontesDados = function(){
		
			var wb = new Workbook();
			
			var wsFontesDados = {};
			
			//criando cabeçalho
			wsFontesDados[XLSX.utils.encode_cell({c:0 ,r:0})] = criarCelula(0 ,0,'ID');
			wsFontesDados[XLSX.utils.encode_cell({c:1 ,r:0})] = criarCelula(1 ,0,'Nome');
			wsFontesDados[XLSX.utils.encode_cell({c:2 ,r:0})] = criarCelula(2 ,0,'Usuário Mantenedor');
			wsFontesDados[XLSX.utils.encode_cell({c:3 ,r:0})] = criarCelula(3 ,0,'Nome da tabela');
			wsFontesDados[XLSX.utils.encode_cell({c:4 ,r:0})] = criarCelula(4 ,0,'Linha de cabeçalho');
			wsFontesDados[XLSX.utils.encode_cell({c:5 ,r:0})] = criarCelula(5 ,0,'Disponível para exportação');
			wsFontesDados[XLSX.utils.encode_cell({c:6 ,r:0})] = criarCelula(6 ,0,'Data atualização');
			                                                   
			linha = 1;
			
			angular.forEach($rootScope.fontesDados,function(fonteDados,chave){
				
				console.log(fonteDados);
				
				coluna = 0, //linha = 0;
				wsFontesDados[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,fonteDados.id_fonte_dados);
				coluna++;
				wsFontesDados[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,fonteDados.nome);
				coluna++;
				var usuario = $scope.usuarios.filter((usuario) => usuario.id == fonteDados.id_usuario_mantenedor)[0];
				if(usuario){
					wsFontesDados[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,usuario.name);
				}
				coluna++;
				wsFontesDados[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,fonteDados.nome_tabela);
				coluna++;
				wsFontesDados[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,fonteDados.linha_cabecalho);
				coluna++;
				wsFontesDados[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,(fonteDados.ativa==true)?'X':null );
				coluna++;
				wsFontesDados[XLSX.utils.encode_cell({c:coluna,r:linha})] = criarCelula(coluna,linha,$filter('date')(fonteDados.data_atualizacao , 'MMMM yyyy') );
				coluna++;
				
				linha++;
				
			});
			
			var range = {s: {c:0, r:0}, e: {c:22, r: linha}};
			wsFontesDados['!ref'] = XLSX.utils.encode_range(range);
			
			/* add worksheet to workbook */
			wb.SheetNames.push('Variaveis');
			wb.Sheets['Variaveis'] = wsFontesDados;
			
			
			var wbout = XLSX.write(wb, {bookType:'xlsx', bookSST:false, type: 'binary'});
			
			saveAs(new Blob([s2ab(wbout)],{type:"application/octet-stream"}), "fontes_dados.xlsx");
	}
	
	
});

</script>


<?php get_template_part('templates/page', 'header'); ?>

<div class="content-page container text-justify" data-ng-app="monitoramentoPde" data-ng-controller="cadastroFonteDados">

<script type="text/ng-template" id="ModalConfirmacao.html">

<div class="modal-instrumento">
	<div class="modal-header">
    <h3 class="modal-title" id="modal-titulo-fonte-dados"> {{acao}} Fonte de dados <button class="btn btn-danger pull-right" type="button" ng-click="fecharModal('confirmacao')">X</button></h3> 
	</div>
	<div class="modal-body" id="modal-corpo-fonte-dados">
			Você irá {{acao.toLowerCase()}} a fonte de dados <strong>{{itemAtual.nome}}</strong>. <br><br> Confirme sua ação.
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
    <h3 class="modal-title" id="modal-titulo-fonte-dados"> {{acaoExecutando}} fonte de dados </h3> 
	</div>
	<div class="modal-body" id="modal-corpo-fonte-dados">
			{{acaoExecutando}} a fonte de dados <strong>{{itemAtual.nome}}</strong>, por favor aguarde a conclusão.
			</div>
</div>
</script>

<script type="text/ng-template" id="ModalSucesso.html">

<div class="modal-instrumento">
	<div class="modal-header">
    <h3 class="modal-title" id="modal-titulo-fonte-dados"> Ação concluída <button class="btn btn-danger pull-right" type="button" ng-click="fecharModal('sucesso')">X</button></h3> 
	</div>
	<div class="modal-body" id="modal-corpo-fonte_dados">
			 A ação foi concluída com sucesso!
			</div>
</div>
</script>


<?php the_content(); ?>
<?php 
			$autorizado = false;
			
			if(is_user_logged_in()){
				$usuario = wp_get_current_user();
				$roleMonitoramento = '';
				foreach($usuario->roles as $role) {
					if(strtolower($role) == 'mantenedor' && $roleMonitoramento != 'administrator'){
						$roleMonitoramento = 'mantenedor';
						$autorizado = true;
					}else 
						if(strtolower($role) == 'administrator'){
							$roleMonitoramento = 'administrator';
							$autorizado = true;
						}
				}
			}else
				$autorizado = false;
			
			if($autorizado){
				
 ?>

<form>
		
		<button class="btn-primary" type="button" ng-click="exportarFontesDados()"> Exportar relação de fontes de dados </button>
		
				<p data-ng-show="estado!='inserir'">
				<label for="fonte_dados"> Fonte de dados </label>
				<br>
				<select style="max-width:100%;" data-ng-model="idItemAtual" data-ng-options="fonteDados.id_fonte_dados as fonteDados.nome for fonteDados in fontesDados | orderBy: 'nome'" data-ng-change="carregar()" name="fonte_dados"></select>
			</p>
			
			<span data-ng-show="estado!='listar'">
			
			<?php if($roleMonitoramento == 'administrator'){ ?>
				<p>

				<label for="nome"> Nome </label>
				<br>
				<input type="text" style="max-width:100%;width:100%;" data-ng-model="itemAtual.nome" name="nome"></input>

			</p>
			<p>
				<label for="usuario"> Usuário mantenedor </label>
				<br>
				<select style="max-width:100%;" data-ng-model="itemAtual.id_usuario_mantenedor" data-ng-options="usuario.id as usuario.name for usuario in usuarios | orderBy: 'name' : true" name="usuario"></select>
			</p>
			
				<!--<p>
				<label for="delimitador"> Delimitador </label>
				<br>
				<input type="text" style="max-width:100%;width:100%;" data-ng-model="itemAtual.delimitador" name="delimitador"></input>
			</p>-->
			<!--
			<p>
				<label for="diretorio"> Diretorio </label>
				<br>
				<input type="text" style="max-width:100%;width:100%;" data-ng-model="itemAtual.diretorio" name="diretorio"></input>
			</p>

			<p>
				<label for="diretorio"> Formato de arquivo </label>
				<br>
				<input type="text" style="max-width:100%;width:100%;" data-ng-model="itemAtual.formato_arquivo" name="formato_arquivo"></input>
			</p>
			-->
				<p>
				<label for="nome_tabela"> Nome da tabela </label>
				<br>
				<input type="text" style="max-width:100%;width:100%;" data-ng-model="itemAtual.nome_tabela" name="nome_tabela"></input>
			</p>
			
				<p>
				<label for="linha_cabecalho"> Linha de cabeçalho </label>
				<br>
				<input type="text" style="max-width:100%;width:100%;" data-ng-model="itemAtual.linha_cabecalho" name="linha_cabecalho"></input>
			
			</p>
				<p>
				<input type="checkbox" data-ng-model="itemAtual.ativa" id="ativo">
				<label for="ativo"> Disponível para exportação </label>
				
			</p>
				<div class="row">
				<div class="col-md-6">
				<input type="checkbox" data-ng-model="checkDataAtualizacao" data-ng-change="changeDataAtualizacao()">
				<label for="data_atualizacao"> Data Atualização </label>
				
				<br>
				<div uib-datepicker datepicker-options="inicializarDatepicker" data-ng-model="itemAtual.data_atualizacao" data-ng-show="checkDataAtualizacao" name="data_atualizacao"></div>
				</div>
				
				</div>
				<!--<div class="row">
				
				<div class="col-md-6">
				<label for="data_inicial"> Data Inicial </label>
				<input type="checkbox" data-ng-model="checkDataInicial" data-ng-change="changeDataInicial()">
				<br>
				<div uib-datepicker datepicker-options="inicializarDatepicker" data-ng-model="itemAtual.data_inicial" data-ng-show="checkDataInicial" name="data_inicial"></div>
				</div>
			
				<div class="col-md-6">
				<label for="data_inicial"> Data Final </label>
				<input type="checkbox" data-ng-model="checkDataFinal" data-ng-change="changeDataFinal()">
				<br>
				<div uib-datepicker datepicker-options="inicializarDatepicker" data-ng-model="itemAtual.data_final" data-ng-show="checkDataFinal" name="data_final"></div>
				</div>
				
				</div>-->
				
				<label for="script"> Script SQL </label>
				<textarea rows="5" style="max-width:100%;width:100%;" data-ng-model="itemAtual.script_sql" name="script"></textarea>
	
				<div class="container elemento-cadastro" style="background-color:#E5E5E5;border-color:#DDDDDD;border-width:1px;border-style:solid;">
			<label> Colunas da fonte dados </label>
			<div><small> Cadastre as colunas de território e data da fonte de dados. Caso deixe o campo em branco a informação não será considerada para a composição da variável </small></div>
			<div data-ng-repeat="coluna in colunas | orderBy : 'ordem'">
				<div class="row">
					<div class="col-sm-2"> <label ng-attr-for="{{'tipo-'+$index}}"><small>Selecione o tipo da coluna </small> </label> </div>
					<div class="col-sm-3" ng-if="coluna.tipo"> <label ng-attr-for="{{'nome-'+$index}}"><small> Escolha a coluna </small></label></div>
					<div class="col-sm-4" ng-if="coluna.tipo =='data'"> <label ng-attr-for="{{'formato-'+$index}}"><small> Formato de data </small></label></div>
					<div class="col-sm-3" ng-if="coluna.tipo =='territorio'"> <label ng-attr-for="{{'id_territorio-'+$index}}"><small> Territorio </small></label></div>
					<div class="col-sm-3" ng-if="coluna.tipo =='territorio'"> <label ng-attr-for="{{'tipo_territorio-'+$index}}"><small> Tipo de territorio </small></label></div>
					
				</div>
				<div class="row">
				<div class="col-sm-2">
				<select class="controle-cadastro" data-ng-model="coluna.tipo" ng-attr-id="{{'tipo-'+$index}}">
					<option value=""></option>
					<option value="data">Data</option>
					<option value="territorio">Territorio</option>
					<option value="inteiro">Número</option>
				</select>
				
				</div>
				<div class="col-sm-3" ng-if="coluna.tipo"> 
				<select class="controle-cadastro" style="max-width:100%;" data-ng-options="coluna_base as coluna_base for coluna_base in itemAtual.colunas" data-ng-model="coluna.nome" ng-attr-id="{{'nome-'+$index}}">
					<option value=""></option>
				</select>
				</div>

				<div class="col-sm-4" ng-if="coluna.tipo =='data'">
					<input class="controle-cadastro" type="text" style="max-width:100%;width:100%;" data-ng-model="coluna.formato" ng-attr-id="{{'formato-'+$index}}" ></input>
				</div>
				<div class="col-sm-3" ng-if="coluna.tipo =='territorio'">
				<select class="controle-cadastro" style="max-width:100%;" data-ng-options="territorio.id_territorio as territorio.nome for territorio in territorios" data-ng-model="coluna.id_territorio" ng-attr-id="{{'id_territorio-'+$index}}">
					<option value=""></option>
				</select>
				</div>
				<div class="col-sm-3" ng-if="coluna.tipo =='territorio'">
					<input class="controle-cadastro" type="text" style="max-width:100%;width:100%;" data-ng-model="coluna.tipo_territorio" ng-attr-id="{{'tipo_territorio-'+$index}}" ></input>
				</div>
				<div class="col-sm-1 pull-right"> <button ng-click="deletarElementoColuna($index)">-</button>
				</div>
				
			</div>
			</div>
			<button type="button" style="margin-top:1em;margin-bottom:1em" data-ng-click="adicionarElementoColuna()">Adicionar Coluna</button>
			</div>
			
			<div class="container elemento-cadastro" style="background-color:#E5E5E5;border-color:#DDDDDD;border-width:1px;border-style:solid;">
			<label> Excluir colunas da exportação de dados </label>
			<div><small> Escolha as colunas que não devem ser exibidas na exportação da fonte de dados </small></div>
			<div data-ng-repeat="coluna_exclusao in itemAtual.colunas_exclusao track by $index">
				<div class="row">
					<div class="col-sm-11"> <label ng-attr-for="{{'coluna_exclusao-'+$index}}"><small>Selecione a coluna </small> </label> </div>
				</div>
				<div class="row">

				<div class="col-sm-6"> 
				<select class="controle-cadastro" style="width:100%;" data-ng-options="coluna_base as coluna_base for coluna_base in itemAtual.colunas" data-ng-model="itemAtual.colunas_exclusao[$index]" ng-attr-id="{{'coluna_exclusao-'+$index}}">
					<option value=""></option>
				</select>
				</div>

				<div class="col-sm-1"> <button ng-click="deletarElementoColunaExclusao($index)">-</button>
				</div>
				
			</div>
			</div>
			<button type="button" style="margin-top:1em;margin-bottom:1em" data-ng-click="adicionarElementoColunaExclusao()">Adicionar coluna a ser excluída</button>
			</div>
			
			

			<input type="submit" data-ng-show="estado!='inserir'" value="Atualizar" data-ng-click="criarModalConfirmacao('Atualizar')">
			<input type="submit" data-ng-show="estado!='inserir'" value="Remover" data-ng-click="criarModalConfirmacao('Remover')">
			
			<?php } ?>
			
			<p ng-if="itemAtual.nome_arquivo">
		
				Arquivo Fonte de Dados mais recente carregado: 
				<br>
				<a href="<?php echo bloginfo('url'); ?>/app/uploads/{{itemAtual.nome_tabela}}/{{itemAtual.nome_arquivo}}"> {{itemAtual.nome}} </a>
				
			</p>
			<p ng-if="itemAtual.arquivo_mapas">
		
				Arquivo de mapas mais recente: 
				<br>
				<a href="<?php echo bloginfo('url'); ?>/app/uploads/{{itemAtual.nome_tabela}}/{{itemAtual.arquivo_mapas}}"> {{itemAtual.arquivo_mapas}} </a>
				
			</p>
			<p ng-if="itemAtual.arquivo_metadados">
		
				Arquivo de metadados mais recente: 
				<br>
				<a href="<?php echo bloginfo('url'); ?>/app/uploads/{{itemAtual.nome_tabela}}/{{itemAtual.arquivo_metadados}}"> {{itemAtual.arquivo_metadados}} </a>
				
			</p>

			<p>
				Data da última carga de dados:
				<br>
				{{itemAtual.data_carga}}
			</p>
				<p>
				<label for="arquivo"> Selecione o arquivo </label>
				<br>
				<input type="file" style="max-width:100%;width:100%;" data-ng-model-instant id="arquivos" name="arquivos" onchange="angular.element(this).scope().lerArquivos(this)">
				
			</p>
			
			<input type="submit" data-ng-show="estado!='inserir'" value="Carregar Arquivo Fonte de Dados" data-ng-click="criarModalConfirmacao('Carregar')">
			<!-- Carregar mapas (SHP / Shapefiles / KMZ) -->
			<input type="submit" data-ng-show="estado!='inserir'" value="Carregar Mapas" data-ng-click="criarModalConfirmacao('Carregar Mapas')">
			<!-- Carregar metadados -->
			<input type="submit" data-ng-show="estado!='inserir'" value="Carregar Metadados" data-ng-click="criarModalConfirmacao('Carregar Metadados')">
			</span>
			<?php if($roleMonitoramento == 'administrator'){ ?>
			<input data-ng-show="estado!='inserir'" type="submit" value="Nova Fonte de dados" data-ng-click="inserirForm()">
			
			<br>
			<!--<div class="alert alert-info" ng-show="carregandoArquivo">{{mensagemArquivo}}</div>-->
			
			<input data-ng-show="estado=='inserir'" type="submit" value="Gravar" data-ng-click="criarModalConfirmacao('Inserir')">
			<input data-ng-show="estado=='inserir'" type="submit" value="Voltar" data-ng-click="voltar()">
			<?php }?>
			
</form>

<?php }else{ ?>
			<h4> Você não possui autorização para visualizar esse conteúdo.</h4>
<?php } ?>

</div>


