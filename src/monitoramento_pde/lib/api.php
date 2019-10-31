<?php

//namespace MonitoramentoPde\API;

use Roots\Sage\Setup;

/**
 * Retorna os dados calculados dos indicadores
 *
 * @param array $data Options for the function.
 * @return string|null Post title for the latest,  * or null if none.
 */
 
//TODO: criar tela de admin para essas configurações

add_action( 'rest_api_init', function () {
	global $ApiConfig;
	register_rest_route( $ApiConfig['application'].'/v'.$ApiConfig['version'], '/indicador/valores/(?P<indicador>\d+)', array(
		'methods' => WP_REST_Server::READABLE,
		'callback' => 'indicador_dados'
	) );
} );
 
 add_action( 'rest_api_init', function () {
	global $ApiConfig;
	register_rest_route( $ApiConfig['application'].'/v'.$ApiConfig['version'], '/ficha_tecnica_instrumento/(?P<instrumento>\d+)', array(
		'methods' => WP_REST_Server::READABLE,
		'callback' => 'ficha_tecnica_instrumento'
	) );
} );

 add_action( 'rest_api_init', function () {
	global $ApiConfig;
	register_rest_route( $ApiConfig['application'].'/v'.$ApiConfig['version'], '/instrumentos', array(
		'methods' => WP_REST_Server::READABLE,
		'callback' => 'instrumentos'
	) );
} );

// Issue 45
 add_action( 'rest_api_init', function () {
	 global $ApiConfig;
	register_rest_route( $ApiConfig['application'].'/v'.$ApiConfig['version'], '/instrumentos/carregar_mapa_tematico/(?P<id_grupo_indicador>\d+)', array(
		'methods' => 'POST',
		'callback' => 'carregar_mapa_tematico'
	) );
} );
add_action( 'rest_api_init', function () {
	global $ApiConfig;
	register_rest_route( $ApiConfig['application'].'/v'.$ApiConfig['version'], '/instrumentos/obter_mapa/(?P<id_grupo_indicador>\d+)', array(
		'methods' => WP_REST_Server::READABLE,
		'callback' => 'obter_mapa'
	) );
} );
add_action( 'rest_api_init', function () {
	 global $ApiConfig;
	register_rest_route( $ApiConfig['application'].'/v'.$ApiConfig['version'], '/instrumentos/gravar_parametros_mapa/(?P<id_grupo_indicador>\d+)', array(
		'methods' => 'PUT',
		'callback' => 'gravar_parametros_mapa'
	) );
} );

 add_action( 'rest_api_init', function () {
	global $ApiConfig;
	register_rest_route( $ApiConfig['application'].'/v'.$ApiConfig['version'], '/territorios', array(
		'methods' => WP_REST_Server::READABLE,
		'callback' => 'territorios'
	) );
} );

 add_action( 'rest_api_init', function () {
	global $ApiConfig;
	register_rest_route( $ApiConfig['application'].'/v'.$ApiConfig['version'], '/indicador/memoria/(?P<indicador>\d+)', array(
		'methods' => WP_REST_Server::READABLE,
		'callback' => 'indicador_memoria'
	) );
} );
 
  add_action( 'rest_api_init', function () {
	global $ApiConfig;
	register_rest_route( $ApiConfig['application'].'/v'.$ApiConfig['version'], '/indicador/fusao/(?P<id_indicador>\d+)', array(
		'methods' => WP_REST_Server::READABLE,
		'callback' => 'indicador_fusao'
	) );
} );

  add_action( 'rest_api_init', function () {
	global $ApiConfig;
	register_rest_route( $ApiConfig['application'].'/v'.$ApiConfig['version'], '/dado_aberto/(?P<fonte_dados>\d+)', array(
		'methods' => WP_REST_Server::READABLE,
		'callback' => 'dado_aberto'
	) );
} );

add_action( 'rest_api_init', function () {
	global $ApiConfig;
	register_rest_route( $ApiConfig['application'].'/v'.$ApiConfig['version'], '/indicador/fusao', array(
		'methods' => WP_REST_Server::READABLE,
		'callback' => 'indicador_fusao'
	) );
} );
 
   add_action( 'rest_api_init', function () {
	global $ApiConfig;
	register_rest_route( $ApiConfig['application'].'/v'.$ApiConfig['version'], '/indicador/fusao/(?P<id_indicador>\d+)', array(
		'methods' => 'POST',
		'callback' => 'atualizar_indicador_fusao'
	) );
} );

add_action( 'rest_api_init', function () {
	global $ApiConfig;
	register_rest_route( $ApiConfig['application'].'/v'.$ApiConfig['version'], '/indicador/fusao/(?P<id_indicador>\d+)', array(
		'methods' => 'PUT',
		'callback' => 'atualizar_indicador_fusao'
	) );
} );

add_action( 'rest_api_init', function () {
	global $ApiConfig;
	register_rest_route( $ApiConfig['application'].'/v'.$ApiConfig['version'], '/indicador/fusao/(?P<id>\d+)', array(
		'methods' => WP_REST_Server::DELETABLE,
		'callback' => 'deletar_indicador_fusao'
	) );
} );

 add_action( 'rest_api_init', function () {
	global $ApiConfig;
	register_rest_route( $ApiConfig['application'].'/v'.$ApiConfig['version'], '/indicador/historico/(?P<indicador>\d+)', array(
		'methods' => WP_REST_Server::READABLE,
		'callback' => 'indicador_historico'
	) );
} );
 
  add_action( 'rest_api_init', function () {
	global $ApiConfig;
	register_rest_route( $ApiConfig['application'].'/v'.$ApiConfig['version'], '/variavel/historico/(?P<indicador>\d+)', array(
		'methods' => WP_REST_Server::READABLE,
		'callback' => 'variavel_historico'
	) );
} );
 
 add_action( 'rest_api_init', function () {
	 global $ApiConfig;
	register_rest_route( $ApiConfig['application'].'/v'.$ApiConfig['version'], '/indicador/(?P<indicador>\d+)', array(
		'methods' => WP_REST_Server::READABLE,
		'callback' => 'indicador_cadastro'
	) );
} );

 add_action( 'rest_api_init', function () {
	 global $ApiConfig;
	register_rest_route( $ApiConfig['application'].'/v'.$ApiConfig['version'], '/indicador_composicao/(?P<indicador>\d+)', array(
		'methods' => WP_REST_Server::READABLE,
		'callback' => 'indicador_composicao'
	) );
} );

 add_action( 'rest_api_init', function () {
	 global $ApiConfig;
	register_rest_route( $ApiConfig['application'].'/v'.$ApiConfig['version'], '/variavel/(?P<variavel>\d+)', array(
		'methods' => WP_REST_Server::READABLE,
		'callback' => 'variavel_cadastro'
	) );
} );

 add_action( 'rest_api_init', function () {
	 global $ApiConfig;
	register_rest_route( $ApiConfig['application'].'/v'.$ApiConfig['version'], '/variavel_filtro/(?P<variavel>\d+)', array(
		'methods' => WP_REST_Server::READABLE,
		'callback' => 'variavel_filtro'
	) );
} );

 add_action( 'rest_api_init', function () {
	 global $ApiConfig;
	register_rest_route( $ApiConfig['application'].'/v'.$ApiConfig['version'], '/fonte_dados_coluna/(?P<id_fonte_dados>\d+)', array(
		'methods' => WP_REST_Server::READABLE,
		'callback' => 'fonte_dados_coluna'
	) );
} );

 add_action( 'rest_api_init', function () {
	 global $ApiConfig;
	register_rest_route( $ApiConfig['application'].'/v'.$ApiConfig['version'], '/variavel', array(
		'methods' => WP_REST_Server::READABLE,
		'callback' => 'variavel_cadastro'
	) );
} );

 add_action( 'rest_api_init', function () {
	 global $ApiConfig;
	register_rest_route( $ApiConfig['application'].'/v'.$ApiConfig['version'], '/indicador/(?P<id>\d+)', array(
		'methods' => WP_REST_Server::DELETABLE,
		'callback' => 'deletar_indicador'
	) );
} );

 add_action( 'rest_api_init', function () {
	 global $ApiConfig;
	register_rest_route( $ApiConfig['application'].'/v'.$ApiConfig['version'], '/variavel/(?P<id>\d+)', array(
		'methods' => WP_REST_Server::DELETABLE,
		'callback' => 'deletar_variavel'
	) );
} );

 add_action( 'rest_api_init', function () {
	 global $ApiConfig;
	register_rest_route( $ApiConfig['application'].'/v'.$ApiConfig['version'], '/indicador_composicao/(?P<id_indicador>\d+)', array(
		'methods' => WP_REST_Server::DELETABLE,
		'callback' => 'atualizar_indicador_composicao'
	) );
} );

 add_action( 'rest_api_init', function () {
	 global $ApiConfig;
	register_rest_route( $ApiConfig['application'].'/v'.$ApiConfig['version'], '/indicador', array(
		'methods' => 'POST',
		'callback' => 'inserir_indicador'
	) );
} );

 add_action( 'rest_api_init', function () {
	 global $ApiConfig;
	register_rest_route( $ApiConfig['application'].'/v'.$ApiConfig['version'], '/indicador_composicao/(?P<id_indicador>\d+)', array(
		'methods' => 'POST',
		'callback' => 'atualizar_indicador_composicao'
	) );
} );

 add_action( 'rest_api_init', function () {
	 global $ApiConfig;
	register_rest_route( $ApiConfig['application'].'/v'.$ApiConfig['version'], '/variavel_filtro/(?P<id_variavel>\d+)', array(
		'methods' => 'POST',
		'callback' => 'atualizar_variavel_filtro'
	) );
} );

 add_action( 'rest_api_init', function () {
	 global $ApiConfig;
	register_rest_route( $ApiConfig['application'].'/v'.$ApiConfig['version'], '/variavel_filtro/(?P<id_variavel>\d+)', array(
		'methods' => 'PUT',
		'callback' => 'atualizar_variavel_filtro'
	) );
} );

 add_action( 'rest_api_init', function () {
	 global $ApiConfig;
	register_rest_route( $ApiConfig['application'].'/v'.$ApiConfig['version'], '/fonte_dados_coluna/(?P<id_variavel>\d+)', array(
		'methods' => 'POST',
		'callback' => 'atualizar_coluna_fonte_dados'
	) );
} );

 add_action( 'rest_api_init', function () {
	 global $ApiConfig;
	register_rest_route( $ApiConfig['application'].'/v'.$ApiConfig['version'], '/fonte_dados_coluna/(?P<id_variavel>\d+)', array(
		'methods' => 'PUT',
		'callback' => 'atualizar_coluna_fonte_dados'
	) );
} );

 add_action( 'rest_api_init', function () {
	 global $ApiConfig;
	register_rest_route( $ApiConfig['application'].'/v'.$ApiConfig['version'], '/variavel_filtro/(?P<id>\d+)', array(
		'methods' => WP_REST_Server::DELETABLE,
		'callback' => 'deletar_variavel_filtro'
	) );
} );

 add_action( 'rest_api_init', function () {
	 global $ApiConfig;
	register_rest_route( $ApiConfig['application'].'/v'.$ApiConfig['version'], '/fonte_dados_coluna/(?P<id>\d+)', array(
		'methods' => WP_REST_Server::DELETABLE,
		'callback' => 'deletar_fonte_dados_coluna'
	) );
} );

 add_action( 'rest_api_init', function () {
	 global $ApiConfig;
	register_rest_route( $ApiConfig['application'].'/v'.$ApiConfig['version'], '/fonte_dados_coluna/(?P<id_fonte_dados>\d+)', array(
		'methods' => 'POST',
		'callback' => 'atualizar_fonte_dados_coluna'
	) );
} );

 add_action( 'rest_api_init', function () {
	 global $ApiConfig;
	register_rest_route( $ApiConfig['application'].'/v'.$ApiConfig['version'], '/fonte_dados_coluna/(?P<id_fonte_dados>\d+)', array(
		'methods' => 'PUT',
		'callback' => 'atualizar_fonte_dados_coluna'
	) );
} );

 add_action( 'rest_api_init', function () {
	 global $ApiConfig;
	register_rest_route( $ApiConfig['application'].'/v'.$ApiConfig['version'], '/fonte_dados_coluna/(?P<id>\d+)', array(
		'methods' => WP_REST_Server::DELETABLE,
		'callback' => 'deletar_fonte_dados_coluna'
	) );
} );

 add_action( 'rest_api_init', function () {
	 global $ApiConfig;
	register_rest_route( $ApiConfig['application'].'/v'.$ApiConfig['version'], '/variavel', array(
		'methods' => 'POST',
		'callback' => 'inserir_variavel'
	) );
} );

 add_action( 'rest_api_init', function () {
	 global $ApiConfig;
	register_rest_route( $ApiConfig['application'].'/v'.$ApiConfig['version'], '/indicador', array(
		'methods' => 'PUT',
		'callback' => 'atualizar_indicador'
	) );
} );

 add_action( 'rest_api_init', function () {
	 global $ApiConfig;
	register_rest_route( $ApiConfig['application'].'/v'.$ApiConfig['version'], '/grupo_indicador/(?P<id_grupo_indicador>\d+)', array(
		'methods' => 'PUT',
		'callback' => 'atualizar_grupo_indicador'
	) );
} );

 add_action( 'rest_api_init', function () {
	 global $ApiConfig;
	register_rest_route( $ApiConfig['application'].'/v'.$ApiConfig['version'], '/grupo_indicador', array(
		'methods' => 'POST',
		'callback' => 'inserir_grupo_indicador'
	) );
} );

add_action( 'rest_api_init', function () {
	global $ApiConfig;
	register_rest_route( $ApiConfig['application'].'/v'.$ApiConfig['version'], '/grupo_indicador/(?P<id>\d+)', array(
		'methods' => WP_REST_Server::DELETABLE,
		'callback' => 'deletar_grupo_indicador'
	) );
} );

 add_action( 'rest_api_init', function () {
	 global $ApiConfig;
	register_rest_route( $ApiConfig['application'].'/v'.$ApiConfig['version'], '/indicador_composicao/(?P<id_indicador>\d+)', array(
		'methods' => 'PUT',
		'callback' => 'atualizar_indicador_composicao'
	) );
} );

 add_action( 'rest_api_init', function () {
	 global $ApiConfig;
	register_rest_route( $ApiConfig['application'].'/v'.$ApiConfig['version'], '/variavel', array(
		'methods' => 'PUT',
		'callback' => 'atualizar_variavel'
	) );
} );


 add_action( 'rest_api_init', function () {
	 global $ApiConfig;
	register_rest_route( $ApiConfig['application'].'/v'.$ApiConfig['version'], '/grupo_indicador/(?P<grupo>\d+)', array(
		'methods' => WP_REST_Server::READABLE,
		'callback' => 'grupo_indicador'
	) );
} );

 add_action( 'rest_api_init', function () {
	 global $ApiConfig;
	register_rest_route( $ApiConfig['application'].'/v'.$ApiConfig['version'], '/grupo_indicador', array(
		'methods' => WP_REST_Server::READABLE,
		'callback' => 'grupo_indicador'
	) );
} );

 add_action( 'rest_api_init', function () {
	 global $ApiConfig;
	register_rest_route( $ApiConfig['application'].'/v'.$ApiConfig['version'], '/indicador', array(
		'methods' => WP_REST_Server::READABLE,
		'callback' => 'indicador_cadastro'
	) );
} );
 
  add_action( 'rest_api_init', function () {
		global $ApiConfig;
		register_rest_route( $ApiConfig['application'].'/v'.$ApiConfig['version'], '/acoes_prioritarias', array(
			'methods' => WP_REST_Server::READABLE,
			'callback' => 'acoes_prioritarias'
			) 
		);
	});
 
   add_action( 'rest_api_init', function () {
		global $ApiConfig;
		register_rest_route( $ApiConfig['application'].'/v'.$ApiConfig['version'], '/fontes_dados', array(
			'methods' => WP_REST_Server::READABLE,
			'callback' => 'fontes_dados'
			) 
		);
	});
 
    add_action( 'rest_api_init', function () {
		global $ApiConfig;
		register_rest_route( $ApiConfig['application'].'/v'.$ApiConfig['version'], '/fontes_dados/(?P<fonte_dados>\d+)', array(
			'methods' => WP_REST_Server::READABLE,
			'callback' => 'fontes_dados_coluna'
			) 
		);
	});
 
 add_action( 'rest_api_init', function () {
	 global $ApiConfig;
	register_rest_route( $ApiConfig['application'].'/v'.$ApiConfig['version'], '/fontes_dados/(?P<id>\d+)', array(
		'methods' => WP_REST_Server::DELETABLE,
		'callback' => 'deletar_fonte_dados'
	) );
} );
 
  add_action( 'rest_api_init', function () {
	 global $ApiConfig;
	register_rest_route( $ApiConfig['application'].'/v'.$ApiConfig['version'], '/fontes_dados', array(
		'methods' => 'PUT',
		'callback' => 'atualizar_fonte_dados'
	) );
} );
 
	add_action( 'rest_api_init', function () {
	 global $ApiConfig;
	register_rest_route( $ApiConfig['application'].'/v'.$ApiConfig['version'], '/fontes_dados/carregar/(?P<id>\d+)', array(
		'methods' => 'POST',
		'callback' => 'carregar_fonte_dados'
	) );
} );

// Registra rota carga mapas
add_action( 'rest_api_init', function () {
	global $ApiConfig;
	register_rest_route( $ApiConfig['application'].'/v'.$ApiConfig['version'], '/fontes_dados/carregar_arquivo_mapas/(?P<id>\d+)', array(
		'methods' => 'POST',
		'callback' => 'carregar_arquivo_mapas'
	) );
} );
// Registra rota carga metadados
add_action( 'rest_api_init', function () {
	global $ApiConfig;
	register_rest_route( $ApiConfig['application'].'/v'.$ApiConfig['version'], '/fontes_dados/carregar_arquivo_metadados/(?P<id>\d+)', array(
		'methods' => 'POST',
		'callback' => 'carregar_arquivo_metadados'
	) );
} );
// Registra rota para exibição do(s) objetivo(s) do indicador
add_action( 'rest_api_init', function () {
	global $ApiConfig;
	register_rest_route( $ApiConfig['application'].'/v'.$ApiConfig['version'], '/objetivo_indicador', array(
		'methods' => WP_REST_Server::READABLE,
		'callback' => 'objetivo_indicador'
	) );
} );

  add_action( 'rest_api_init', function () {
	 global $ApiConfig;
	register_rest_route( $ApiConfig['application'].'/v'.$ApiConfig['version'], '/fontes_dados', array(
		'methods' => 'POST',
		'callback' => 'inserir_fonte_dados'
	) );
} );

// ISSUE #42
// function alterLog($acao, $tipoElemento, $idElemento, $usuarioRequest = NULL) {
function alterLog(array $logParams) {
	$usuario = new stdClass();
	if(isset($logParams['usuario'])) {
		if(is_array($logParams['usuario'])) {
			$usuario->data = new stdClass();
			$usuario->data->user_login = $logParams['usuario']['data']['user_login'];
			$usuario->ID = $logParams['usuario']['ID'];
		}
		else {
			$logParams['usuario'] = json_decode($logParams['usuario']);
			foreach ($logParams['usuario'] as $key => $value) {
			    $usuario->$key = $value;
			}
		}
	}
	else {
		$usuario = wp_get_current_user();
	}
	// Oculta parênteses de ID se não houver definição
	$idElemento = isset($logParams['idElemento']) ? " (".$logParams['idElemento'].")" : "";
		
	$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
	if($link === false){
	    die("ERRO: Não foi possível conectar. " . mysqli_connect_error());
	}

	$logmsg = "Usuário ".$usuario->data->user_login." (".$usuario->ID.") ".$logParams['acao']." ".$logParams['tipoElemento']." ".$logParams['nomeElemento'].$idElemento.".";
	$sqlLog = "INSERT INTO wp_simple_history (`date`, `logger`, `level`, `message`) VALUES ('".date("Y-m-d H:i:s")."', 'SimpleLogger', 'info', '".$logmsg."');";
	$retornoLog = $link->query($sqlLog);	
}
 
function deletar_variavel(WP_REST_Request $request){
	$parametros = $request->get_params();

	//  nome do indicador não presente nos parâmetros - buscando manualmente:
	$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https://" : "http://";
    $jsonFile = file_get_contents($protocol.$_SERVER["SERVER_NAME"]."/wp-json/monitoramento_pde/v1/variavel/");
	$jsonStr = json_decode($jsonFile, true);
	
	// API não retorna variável específica. Loop percorre valores para encontrar o correspondente ao ID da variável
	foreach ($jsonStr as $key => $value) {
		if ($value['id_variavel'] == $parametros['id']) {
			$nomeElemento = $value['nome'];
			break;
		}
	}
	alterLog(array(
		'acao'=>'deletou',
		'tipoElemento'=> 'variável', 
		'idElemento'=>$parametros['id'],
		'nomeElemento'=>$nomeElemento,
		'usuario'=>$parametros['usuario']
	));

	global $DbConfig;
	try {
		$pdo = new PDO('pgsql:host='.$DbConfig['host'].';port='.$DbConfig['port'].';user='.$DbConfig['user'].';dbname='.$DbConfig['dbname'].';password='.$DbConfig['password']);
	} catch (PDOException $e) {
		die("Conexão ao banco de dados falhou: " . $e->getMessage());
	}
	
	$comando_string = "delete from sistema.variavel where id_variavel = :id";
	$comando = $pdo->prepare($comando_string);
 
  if(array_key_exists('id',$parametros))
		if($parametros['id'] != '')
			$comando->bindParam(':id',$parametros['id']);
 
 	if(!$comando->execute()){
		$erro = $comando->errorInfo();
		$response = new  WP_REST_Response($erro[2], 500);
	}
	else
	{
		$response = new WP_REST_Response( true );
	}
	
	return $response;
}
 
 
function deletar_variavel_filtro(WP_REST_Request $request){
	$parametros = $request->get_params();
	global $DbConfig;
	try {
		$pdo = new PDO('pgsql:host='.$DbConfig['host'].';port='.$DbConfig['port'].';user='.$DbConfig['user'].';dbname='.$DbConfig['dbname'].';password='.$DbConfig['password']);
	} catch (PDOException $e) {
		die("Conexão ao banco de dados falhou: " . $e->getMessage());
	}
	
	$comando_string = "delete from sistema.variavel_filtro where id_variavel = :id";
	$comando = $pdo->prepare($comando_string);
 
  if(array_key_exists('id',$parametros))
		if($parametros['id'] != '')
			$comando->bindParam(':id',$parametros['id']);
 
 	if(!$comando->execute()){
		$erro = $comando->errorInfo();
		$response = new  WP_REST_Response($erro[2], 500);
	}
	else
	{
		$response = new WP_REST_Response( true );
	}
	
	return $response;
}
 
function deletar_fonte_dados(WP_REST_Request $request){
	$parametros = $request->get_params();
	
	// nome da fonte não presente nos parâmetros - buscando manualmente:
	$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https://" : "http://";
    $jsonFile = file_get_contents($protocol.$_SERVER["SERVER_NAME"]."/wp-json/monitoramento_pde/v1/fontes_dados?fonte_dados=".$parametros['id']);
	$jsonStr = json_decode($jsonFile, true);
	$nomeElemento = $jsonStr[0]['nome'];

	alterLog(array(
		'acao'=>'deletou',
		'tipoElemento'=> 'fonte de dados', 
		'idElemento'=>$parametros['id'],
		'nomeElemento'=>$nomeElemento,
		'usuario'=>$parametros['usuario']
	));

	global $DbConfig;
	try {
		$pdo = new PDO('pgsql:host='.$DbConfig['host'].';port='.$DbConfig['port'].';user='.$DbConfig['user'].';dbname='.$DbConfig['dbname'].';password='.$DbConfig['password']);
	} catch (PDOException $e) {
		die("Conexão ao banco de dados falhou: " . $e->getMessage());
	}
	
	$comando_string = "delete from sistema.fonte_dados where id_fonte_dados = :id";
	$comando = $pdo->prepare($comando_string);
 
  if(array_key_exists('id',$parametros))
		if($parametros['id'] != '')
			$comando->bindParam(':id',$parametros['id']);
 
 	if(!$comando->execute()){
		$erro = $comando->errorInfo();
		$response = new  WP_REST_Response($erro[2], 500);
	}
	else
	{
		$response = new WP_REST_Response( true );
	}
	
	return $response;
} 

function deletar_grupo_indicador(WP_REST_Request $request){
	$parametros = $request->get_params();
	
	if(is_string($parametros['grupo']))
		$parametros['grupo'] = json_decode($parametros['grupo']);
	
	alterLog(array(
		'acao'=>'deletou',
		'tipoElemento'=> $parametros['tipo'],
		'nomeElemento'=>$parametros['grupo']->nome,
		'usuario'=>$parametros['usuario']
	));

	global $DbConfig;
	try {
		$pdo = new PDO('pgsql:host='.$DbConfig['host'].';port='.$DbConfig['port'].';user='.$DbConfig['user'].';dbname='.$DbConfig['dbname'].';password='.$DbConfig['password']);
	} catch (PDOException $e) {
		die("Conexão ao banco de dados falhou: " . $e->getMessage());
	}
	
	$comando_string = "delete from sistema.grupo_indicador where id_grupo_indicador = :id";
	$comando = $pdo->prepare($comando_string);
 
  if(array_key_exists('id',$parametros))
		if($parametros['id'] != '')
			$comando->bindParam(':id',$parametros['id']);
 
 	if(!$comando->execute()){
		$erro = $comando->errorInfo();
		$response = new  WP_REST_Response($erro[2], 500);
	}
	else
	{
		$response = new WP_REST_Response( true );
	}
	
	return $response;
} 

function deletar_fonte_dados_coluna(WP_REST_Request $request){
	$parametros = $request->get_params();
	global $DbConfig;
	try {
		$pdo = new PDO('pgsql:host='.$DbConfig['host'].';port='.$DbConfig['port'].';user='.$DbConfig['user'].';dbname='.$DbConfig['dbname'].';password='.$DbConfig['password']);
	} catch (PDOException $e) {
		die("Conexão ao banco de dados falhou: " . $e->getMessage());
	}
	
	$comando_string = "delete from sistema.coluna where id_fonte_dados = :id";
	$comando = $pdo->prepare($comando_string);
 
  if(array_key_exists('id',$parametros))
		if($parametros['id'] != '')
			$comando->bindParam(':id',$parametros['id']);
 
 	if(!$comando->execute()){
		$erro = $comando->errorInfo();
		$response = new  WP_REST_Response($erro[2], 500);
	}
	else
	{
		$response = new WP_REST_Response( true );
	}
	
	return $response;
} 

function deletar_indicador_fusao(WP_REST_Request $request){
	$parametros = $request->get_params();
	global $DbConfig;
	try {
		$pdo = new PDO('pgsql:host='.$DbConfig['host'].';port='.$DbConfig['port'].';user='.$DbConfig['user'].';dbname='.$DbConfig['dbname'].';password='.$DbConfig['password']);
	} catch (PDOException $e) {
		die("Conexão ao banco de dados falhou: " . $e->getMessage());
	}
	
	$comando_string = "delete from sistema.indicador_composicao where id_indicador_pai = :id";
	$comando = $pdo->prepare($comando_string);
 
  if(array_key_exists('id',$parametros))
		if($parametros['id'] != '')
			$comando->bindParam(':id',$parametros['id']);
 
 	if(!$comando->execute()){
		$erro = $comando->errorInfo();
		$response = new  WP_REST_Response($erro[2], 500);
	}
	else
	{
		$response = new WP_REST_Response( true );
	}
	
	return $response;
} 
 
function deletar_indicador(WP_REST_Request $request){
	$parametros = $request->get_params();
	//  nome do indicador não presente nos parâmetros - buscando manualmente:
	$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https://" : "http://";
    $jsonFile = file_get_contents($protocol.$_SERVER["SERVER_NAME"]."/wp-json/monitoramento_pde/v1/indicador?indicador=".$parametros['id']);
	$jsonStr = json_decode($jsonFile, true);
	$nomeElemento = $jsonStr[0]['nome'];
	
	alterLog(array(
		'acao'=>'deletou',
		'tipoElemento'=> 'indicador', 
		'idElemento'=>$parametros['id'],
		'nomeElemento'=>$nomeElemento,
		'usuario'=>$parametros['usuario']
	));

	global $DbConfig;
	try {
		$pdo = new PDO('pgsql:host='.$DbConfig['host'].';port='.$DbConfig['port'].';user='.$DbConfig['user'].';dbname='.$DbConfig['dbname'].';password='.$DbConfig['password']);
	} catch (PDOException $e) {
		die("Conexão ao banco de dados falhou: " . $e->getMessage());
	}
	
	$comando_string = "delete from sistema.indicador where id_indicador = :id";
	$comando = $pdo->prepare($comando_string);
 
  if(array_key_exists('id',$parametros))
		if($parametros['id'] != '')
			$comando->bindParam(':id',$parametros['id']);
 
 	if(!$comando->execute()){
		$erro = $comando->errorInfo();
		$response = new  WP_REST_Response($erro[2], 500);
	}
	else
	{
		$response = new WP_REST_Response( true );
	}
		
	return $response;
}
 
function atualizar_indicador(WP_REST_Request $request){
	$parametros = $request->get_params();
	alterLog(array(
		'acao'=>'atualizou',
		'tipoElemento'=> 'indicador', 
		'idElemento'=>$parametros['indicador']['id_indicador'],
		'nomeElemento'=>$parametros['indicador']['nome']
	));
	
	global $DbConfig;
	try {
		$pdo = new PDO('pgsql:host='.$DbConfig['host'].';port='.$DbConfig['port'].';user='.$DbConfig['user'].';dbname='.$DbConfig['dbname'].';password='.$DbConfig['password']);
	} catch (PDOException $e) {
		die("Conexão ao banco de dados falhou: " . $e->getMessage());
	}
	
	$indicador = $parametros['indicador'];
	
	$comando_string = "update sistema.indicador set
	nome = :nome,
	periodicidade = :periodicidade,
	tipo_valor = :tipo_valor,
	nota_tecnica = :nota_tecnica,
	nota_tecnica_resumida = :nota_tecnica_resumida,
	apresentacao = :apresentacao,
	simbolo_valor = :simbolo_valor,
	ativo = :ativo,
	homologacao = :homologacao,
	fonte = :fonte,
	id_territorio_padrao = :id_territorio_padrao,
	observacao = :observacao,
	preencher_zero = :preencher_zero
	where id_indicador = :id_indicador";
	$comando = $pdo->prepare($comando_string);
 
  if(array_key_exists('id_indicador',$indicador))
		if($indicador['id_indicador'] != ''){
			$comando->bindParam(':id_indicador',$indicador['id_indicador']);
			$comando->bindParam(':nome',$indicador['nome']);
			$comando->bindParam(':periodicidade',$indicador['periodicidade']);
			$comando->bindParam(':tipo_valor',$indicador['tipo_valor']);
			$comando->bindParam(':nota_tecnica',$indicador['nota_tecnica']);
			$comando->bindParam(':nota_tecnica_resumida',$indicador['nota_tecnica_resumida']);
			$comando->bindParam(':apresentacao',$indicador['apresentacao']);
			$comando->bindParam(':simbolo_valor',$indicador['simbolo_valor']);
			$indicador['ativo'] = ($indicador['ativo'])?'t':'f';
			$indicador['homologacao'] = ($indicador['homologacao'])?'t':'f';
			$indicador['preencher_zero'] = ($indicador['preencher_zero'])?'t':'f';
			$comando->bindParam(':ativo',$indicador['ativo']);
			$comando->bindParam(':homologacao',$indicador['homologacao']);
			$comando->bindParam(':fonte',$indicador['fonte']);
			$comando->bindParam(':id_territorio_padrao',$indicador['id_territorio_padrao']);
			$comando->bindParam(':observacao',$indicador['observacao']);
			$comando->bindParam(':preencher_zero',$indicador['preencher_zero']);
		}
 
 	if(!$comando->execute()){
		$erro = $comando->errorInfo();
		$response = new  WP_REST_Response($erro[2], 500);
		return $response;
	}
	else
	{
		
		$comando_string = "delete from sistema.indicador_territorio_exclusao where id_indicador = :indicador";
		$comando = $pdo->prepare($comando_string);
		
		$comando->bindParam(':indicador',$indicador['id_indicador']);
		
		if(!$comando->execute()){
			$erro = $comando->errorInfo();
			$response = new  WP_REST_Response($erro[2], 500);
			return $response;
		}
		
		if(!empty($indicador['territorio_exclusao'])){

			$comando_string = "insert into sistema.indicador_territorio_exclusao (id_indicador, id_territorio)
								values(:indicador,:territorio);";
			$comando = $pdo->prepare($comando_string);		
			$comando->bindParam(':indicador',$indicador['id_indicador']);
			$comando->bindParam(':territorio',$indicador['id_territorio_exclusao']);
			
			if(!$comando->execute()){
				$erro = $comando->errorInfo();
				$response = new  WP_REST_Response($erro[2], 500);
				return $response;
			}

			foreach($indicador['territorio_exclusao'] as $indice => $valor){
				
				if($valor['id'] != null){
					
					$comando_string = "insert into sistema.indicador_territorio_exclusao (id_indicador, id_territorio)
								values(:indicador,:territorio);";
					$comando = $pdo->prepare($comando_string);
					$comando->bindParam(':indicador',$indicador['id_indicador']);
					$comando->bindParam(':territorio',$valor['id']);
					
					if(!$comando->execute()){
						$erro = $comando->errorInfo();
						$response = new  WP_REST_Response($erro[2], 500);
					}			
				}
			}
			
		};
		
		$comando_string = "delete from sistema.indicador_x_grupo where id_indicador = :id_indicador";
		$comando = $pdo->prepare($comando_string);
		$comando->bindParam(':id_indicador',$indicador['id_indicador']);
		if(!$comando->execute()){
			$erro = $comando->errorInfo();
			$response = new  WP_REST_Response($erro[2], 500);
			return $response;
		}
		
		if(!is_null($indicador['id_instrumento']) ){
			$comando_string = 
			"insert into sistema.indicador_x_grupo( id_grupo_indicador, id_indicador, ordem)
																			values(:id_grupo_indicador,:id_indicador,:ordem)";
		
			$comando = $pdo->prepare($comando_string);
			
			$comando->bindParam(':id_grupo_indicador',$indicador['id_instrumento']);
			$comando->bindParam(':id_indicador',$indicador['id_indicador']);
			$comando->bindParam(':ordem',$indicador['ordem_instrumento']);
			 
			if(!$comando->execute()){
				$erro = $comando->errorInfo();
				$response = new  WP_REST_Response($erro[2], 500);
				return $response;
			}
		}
		
		// if(!is_null($indicador['id_objetivo']) ){
		if(array_key_exists('id_objetivo', $indicador)){ // TODO: VOLTAR AQUI
			$comando_string = 
			"insert into sistema.indicador_x_grupo( id_grupo_indicador, id_indicador, ordem)
																			values(:id_grupo_indicador,:id_indicador,:ordem)";
		
			$comando = $pdo->prepare($comando_string);
			
			$comando->bindParam(':id_grupo_indicador',$indicador['id_objetivo']);
			$comando->bindParam(':id_indicador',$indicador['id_indicador']);
			$comando->bindParam(':ordem',$indicador['ordem_instrumento']);
			 
			if(!$comando->execute()){
				$erro = $comando->errorInfo();
				$response = new  WP_REST_Response($erro[2], 500);
				return $response;
			}
		}
		
		if(!is_null($indicador['estrategias'][0]['id_grupo_indicador']) ){
			$comando_string = 
			"insert into sistema.indicador_x_grupo( id_grupo_indicador, id_indicador, ordem)
																			values(:id_grupo_indicador,:id_indicador,:ordem)";
		
			$comando = $pdo->prepare($comando_string);
			
			$comando->bindParam(':id_grupo_indicador',$indicador['estrategias'][0]['id_grupo_indicador']);
			$comando->bindParam(':id_indicador',$indicador['id_indicador']);
			$comando->bindParam(':ordem',$indicador['estrategias'][0]['ordem']);
			 
			if(!$comando->execute()){
				$erro = $comando->errorInfo();
				$response = new  WP_REST_Response($erro[2], 500);
				return $response;
			}
		}
		
		if(!is_null($indicador['estrategias']) && array_key_exists(1, $indicador['estrategias']) && !is_null($indicador['estrategias'][1]['id_grupo_indicador']) ){
			$comando_string = 
			"insert into sistema.indicador_x_grupo( id_grupo_indicador, id_indicador, ordem)
																			values(:id_grupo_indicador,:id_indicador,:ordem)";
		
			$comando = $pdo->prepare($comando_string);
			
			$comando->bindParam(':id_grupo_indicador',$indicador['estrategias'][1]['id_grupo_indicador']);
			$comando->bindParam(':id_indicador',$indicador['id_indicador']);
			$comando->bindParam(':ordem',$indicador['estrategias'][1]['ordem']);
			 
			if(!$comando->execute()){
				$erro = $comando->errorInfo();
				$response = new  WP_REST_Response($erro[2], 500);
				return $response;
			}
		}
	}
	$response = new WP_REST_Response( true );

	return $response;
} 
 
function atualizar_grupo_indicador(WP_REST_Request $request){
	$parametros = $request->get_params();

	alterLog(array(
		'acao'=>'atualizou',
		'tipoElemento'=> $parametros['tipo'],
		'nomeElemento'=>$parametros['grupo']['nome'],
		'usuario'=>$parametros['usuario']
	));

	global $DbConfig;
	try {
		$pdo = new PDO('pgsql:host='.$DbConfig['host'].';port='.$DbConfig['port'].';user='.$DbConfig['user'].';dbname='.$DbConfig['dbname'].';password='.$DbConfig['password']);
	} catch (PDOException $e) {
		die("Conexão ao banco de dados falhou: " . $e->getMessage());
	}
	
	$grupo = $parametros['grupo'];
	$indicadores = $parametros['indicadores'];
	
	$response = new WP_REST_Response( true );
	
	$comando_string = "delete from sistema.grupo_propriedade where id_grupo_indicador = :id_grupo_indicador";
	$comando = $pdo->prepare($comando_string);
 
  if(array_key_exists('id_grupo_indicador',$parametros))
		if($parametros['id_grupo_indicador'] != '')
			$comando->bindParam(':id_grupo_indicador',$parametros['id_grupo_indicador']);
	
	if(!$comando->execute()){
		$erro = $comando->errorInfo();
		$response = new  WP_REST_Response($erro[2], 500);
	}else{
		$comando_string = "update sistema.grupo_indicador set nome = :nome, tipo = :tipo where id_grupo_indicador = :id_grupo_indicador";
		$comando = $pdo->prepare($comando_string);
	 
		$comando->bindParam(':id_grupo_indicador',$parametros['id_grupo_indicador']);
		$comando->bindParam(':tipo',$parametros['tipo']);
		$comando->bindParam(':nome',$grupo['nome']);
 
		if(!$comando->execute()){
			$erro = $comando->errorInfo();
			$response = new  WP_REST_Response($erro[2], 500);
		}
		else
		{
			foreach($grupo['propriedades'] as $indice => $propriedade){
				
				if($propriedade['chave'] != null){
					
					$comando_string = "insert into sistema.grupo_propriedade (id_grupo_indicador,chave, valor, ordem)
													values (:id_grupo_indicador,:chave,:valor,:ordem)";
					$comando = $pdo->prepare($comando_string);
					//$ordem = $indice + 1;
					$comando->bindParam(':id_grupo_indicador',$parametros['id_grupo_indicador']);
					$comando->bindParam(':chave',$propriedade['chave']);
					$comando->bindParam(':valor',$propriedade['valor']);
					$comando->bindParam(':ordem',$propriedade['ordem']);
					
					if(!$comando->execute()){
						$erro = $comando->errorInfo();
						$response = new  WP_REST_Response($erro[2], 500);
					}			
				}
			}
			
			foreach($indicadores as $indice_indic => $indicador){
				$comando_string = "update sistema.indicador_x_grupo
					set ordem = :ordem
					where id_grupo_indicador = :id_grupo_indicador
					and id_indicador = :id_indicador;";
					
				$comando = $pdo->prepare($comando_string);
				//$ordem = $indice + 1;
				$comando->bindParam(':ordem',$indice_indic);
				$comando->bindParam(':id_grupo_indicador',$parametros['id_grupo_indicador']);
				$comando->bindParam(':id_indicador',$indicador['id_indicador']);
			
				if(!$comando->execute()){
					$erro = $comando->errorInfo();
					$response = new  WP_REST_Response($erro[2], 500);
				}
			
			}
			
		}
	}
	
	return $response;
	
}


function inserir_grupo_indicador(WP_REST_Request $request){
	$parametros = $request->get_params();
	
	alterLog(array(
		'acao'=>'inseriu',
		'tipoElemento'=> $parametros['tipo'],
		'nomeElemento'=>$parametros['grupo']['nome'],
		'usuario'=>$parametros['usuario']
	));

	global $DbConfig;
	try {
		$pdo = new PDO('pgsql:host='.$DbConfig['host'].';port='.$DbConfig['port'].';user='.$DbConfig['user'].';dbname='.$DbConfig['dbname'].';password='.$DbConfig['password']);
	} catch (PDOException $e) {
		die("Conexão ao banco de dados falhou: " . $e->getMessage());
	}
	
	$grupo = $parametros['grupo'];
	
	$response = new WP_REST_Response( true );

	$comando_string = "insert into sistema.grupo_indicador (nome, tipo)
						values(:nome,:tipo)
						returning id_grupo_indicador";
	$comando = $pdo->prepare($comando_string);
 
	$comando->bindParam(':tipo',$parametros['tipo']);
	$comando->bindParam(':nome',$grupo['nome']);

	if(!$comando->execute()){
		$erro = $comando->errorInfo();
		$response = new  WP_REST_Response($erro[2], 500);
	}
	else
	{
		
		$dados = $comando->fetchAll(PDO::FETCH_ASSOC);
		
		foreach($grupo['propriedades'] as $indice => $propriedade){
			
			if($propriedade['chave'] != null){
				
				$comando_string = "insert into sistema.grupo_propriedade (id_grupo_indicador,chave, valor, ordem)
												values (:id_grupo_indicador,:chave,:valor,:ordem)";
				$comando = $pdo->prepare($comando_string);
				//$ordem = $indice + 1;
				$comando->bindParam(':id_grupo_indicador',$dados[0]['id_grupo_indicador']);
				$comando->bindParam(':chave',$propriedade['chave']);
				$comando->bindParam(':valor',$propriedade['valor']);
				$comando->bindParam(':ordem',$propriedade['ordem']);
				
				if(!$comando->execute()){
					$erro = $comando->errorInfo();
					$response = new  WP_REST_Response($erro[2], 500);
				}
			}
		}
	}
	
	
	return $response;
	
}
 
function atualizar_indicador_composicao(WP_REST_Request $request){
	$parametros = $request->get_params();
	global $DbConfig;
	try {
		$pdo = new PDO('pgsql:host='.$DbConfig['host'].';port='.$DbConfig['port'].';user='.$DbConfig['user'].';dbname='.$DbConfig['dbname'].';password='.$DbConfig['password']);
	} catch (PDOException $e) {
		die("Conexão ao banco de dados falhou: " . $e->getMessage());
	}
	
	// Corrige erro 'undefined index: composicao'
	$composicao = [];
	if (isset($parametros['composicao']))
		$composicao = $parametros['composicao'];
	
	$response = new WP_REST_Response( true );
	
	$comando_string = "delete from sistema.indicador_x_variavel where id_indicador = :id_indicador";
	$comando = $pdo->prepare($comando_string);
 
	if(array_key_exists('id_indicador',$parametros))
		if($parametros['id_indicador'] != '')
			$comando->bindParam(':id_indicador',$parametros['id_indicador']);
	
 	if(!$comando->execute()){
		$erro = $comando->errorInfo();
		$response = new  WP_REST_Response($erro[2], 500);
	}
	else
	{
		foreach($composicao as $chave => $valor){
			
			if($valor['id_variavel'] != null){
				
				$comando_string = "insert into sistema.indicador_x_variavel (id_indicador,id_variavel, operador, ordem, aninhamento, principal)
												values (:id_indicador,:id_variavel,:operador,:ordem,:aninhamento,:principal)";
				$comando = $pdo->prepare($comando_string);
				$ordem = $chave + 1;
				$comando->bindParam(':id_indicador',$parametros['id_indicador']);
				$comando->bindParam(':id_variavel',$valor['id_variavel']);
				$comando->bindParam(':operador',$valor['operador']);
				$comando->bindParam(':ordem',$ordem);
				$comando->bindParam(':aninhamento',$valor['aninhamento']);
				$comando->bindParam(':principal',$valor['principal']);
				
				if(!$comando->execute()){
					$erro = $comando->errorInfo();
					$response = new  WP_REST_Response($erro[2], 500);
				}
			}
		}
	}
	
	$comando_string = "select sistema.calcular_indicador(:id_indicador)";
	$comando = $pdo->prepare($comando_string);
 
  if(array_key_exists('id_indicador',$parametros))
		if($parametros['id_indicador'] != ''){
			$comando->bindParam(':id_indicador',$parametros['id_indicador']);
		}
 
 	if(!$comando->execute()){
		$erro = $comando->errorInfo();
		$response = new  WP_REST_Response($erro[2], 500);
	}
	
	return $response;
} 
 
 
function atualizar_indicador_fusao(WP_REST_Request $request){
	$parametros = $request->get_params();
	global $DbConfig;
	try {
		$pdo = new PDO('pgsql:host='.$DbConfig['host'].';port='.$DbConfig['port'].';user='.$DbConfig['user'].';dbname='.$DbConfig['dbname'].';password='.$DbConfig['password']);
	} catch (PDOException $e) {
		die("Conexão ao banco de dados falhou: " . $e->getMessage());
	}
	
	$composicao = $parametros['composicao'];
	
	$response = new WP_REST_Response( true );
	
	$comando_string = "delete from sistema.indicador_composicao where id_indicador_pai = :id_indicador";
	$comando = $pdo->prepare($comando_string);
 
	if(array_key_exists('id_indicador',$parametros))
		if($parametros['id_indicador'] != '')
			$comando->bindParam(':id_indicador',$parametros['id_indicador']);
	
 	if(!$comando->execute()){
		$erro = $comando->errorInfo();
		$response = new  WP_REST_Response($erro[2], 500);
	}
	else
	{
		foreach($composicao as $chave => $valor){
			
			if($valor['id_indicador_filho'] != null){
				
				$comando_string = "insert into sistema.indicador_composicao (id_indicador_pai, id_indicador_filho, dimensao)
																														values (:id_indicador_pai,:id_indicador_filho,:dimensao)";
				$comando = $pdo->prepare($comando_string);
		
				$comando->bindParam(':id_indicador_pai',$parametros['id_indicador']);
				$comando->bindParam(':id_indicador_filho',$valor['id_indicador_filho']);
				$comando->bindParam(':dimensao',$valor['dimensao']);
				
				if(!$comando->execute()){
					$erro = $comando->errorInfo();
					$response = new  WP_REST_Response($erro[2], 500);
				}
			}
		}
	}
	
	$comando_string = "select sistema.calcular_indicador(:id_indicador)";
	$comando = $pdo->prepare($comando_string);
 
  if(array_key_exists('id_indicador',$parametros))
		if($parametros['id_indicador'] != ''){
			$comando->bindParam(':id_indicador',$parametros['id_indicador']);
		}
 
 	if(!$comando->execute()){
		$erro = $comando->errorInfo();
		$response = new  WP_REST_Response($erro[2], 500);
	}
	
	return $response;
} 
 
 
 function atualizar_variavel_filtro(WP_REST_Request $request){
	$parametros = $request->get_params();
	global $DbConfig;
	try {
		$pdo = new PDO('pgsql:host='.$DbConfig['host'].';port='.$DbConfig['port'].';user='.$DbConfig['user'].';dbname='.$DbConfig['dbname'].';password='.$DbConfig['password']);
	} catch (PDOException $e) {
		die("Conexão ao banco de dados falhou: " . $e->getMessage());
	}
	
	$variavelFiltro = $parametros['filtro'];
	
	$response = new WP_REST_Response( true );
	
	$comando_string = "delete from sistema.variavel_filtro where id_variavel = :id_variavel";
	$comando = $pdo->prepare($comando_string);
 
	if(array_key_exists('id_variavel',$parametros))
		if($parametros['id_variavel'] != '')
			$comando->bindParam(':id_variavel',$parametros['id_variavel']);
	
 	if(!$comando->execute()){
		$erro = $comando->errorInfo();
		$response = new  WP_REST_Response($erro[2], 500);
	}
	else
	{
		foreach($variavelFiltro as $chave => $valor){
			
			if($valor['coluna'] != null){
				
				$comando_string = "insert into sistema.variavel_filtro (id_variavel, coluna, valor, operador_comparador, ordem, aninhamento, operador_logico, excluir_regiao_raiz)
																											 values (:id_variavel,:coluna,:valor,:operador_comparador,:ordem,:aninhamento,:operador_logico,:excluir_regiao_raiz)";
				$comando = $pdo->prepare($comando_string);
				$ordem = $chave + 1;
				$comando->bindParam(':id_variavel',$parametros['id_variavel']);
				$comando->bindParam(':coluna',$valor['coluna']);
				$comando->bindParam(':valor',$valor['valor']);
				$comando->bindParam(':operador_comparador',$valor['operador_comparador']);
				$comando->bindParam(':ordem',$ordem);
				$comando->bindParam(':aninhamento',$valor['aninhamento']);
				$comando->bindParam(':operador_logico',$valor['operador_logico']);
				
				$valor['excluir_regiao_raiz'] = ($valor['excluir_regiao_raiz'])?'t':'f';
				$comando->bindParam(':excluir_regiao_raiz',$valor['excluir_regiao_raiz']);
				
				if(!$comando->execute()){
					$erro = $comando->errorInfo();
					$response = new  WP_REST_Response($erro[2], 500);
				}
			}
		}
	}
	
	return $response;
} 
 
function atualizar_fonte_dados_coluna(WP_REST_Request $request){
	$parametros = $request->get_params();
	global $DbConfig;
	try {
		$pdo = new PDO('pgsql:host='.$DbConfig['host'].';port='.$DbConfig['port'].';user='.$DbConfig['user'].';dbname='.$DbConfig['dbname'].';password='.$DbConfig['password']);
	} catch (PDOException $e) {
		die("Conexão ao banco de dados falhou: " . $e->getMessage());
	}
	
	$colunas = $parametros['colunas'];
	
	$response = new WP_REST_Response( true );
	
	$comando_string = "delete from sistema.coluna where id_fonte_dados = :id_fonte_dados";
	$comando = $pdo->prepare($comando_string);
 
	if(array_key_exists('id_fonte_dados',$parametros))
		if($parametros['id_fonte_dados'] != '')
			$comando->bindParam(':id_fonte_dados',$parametros['id_fonte_dados']);
	
 	if(!$comando->execute()){
		$erro = $comando->errorInfo();
		$response = new  WP_REST_Response($erro[2], 500);
	}
	else
	{
		foreach($colunas as $chave => $valor){
			
			if($valor['tipo'] != null){
				
				$comando_string = "insert into sistema.coluna (id_fonte_dados, tipo, nome, formato, id_territorio, tipo_territorio)
																						  values (:id_fonte_dados,:tipo,:nome,:formato,:id_territorio,:tipo_territorio)";
				$comando = $pdo->prepare($comando_string);
				$ordem = $chave + 1;
				$comando->bindParam(':id_fonte_dados',$parametros['id_fonte_dados']);
				$comando->bindParam(':tipo',$valor['tipo']);
				$comando->bindParam(':nome',$valor['nome']);
				$comando->bindParam(':formato',$valor['formato']);
				$comando->bindParam(':id_territorio',$valor['id_territorio']);
				$comando->bindParam(':tipo_territorio',$valor['tipo_territorio']);
				
				if(!$comando->execute()){
					$erro = $comando->errorInfo();
					$response = new  WP_REST_Response($erro[2], 500);
				}
			}
		}
	}
	
	return $response;
}
 
 
function atualizar_variavel(WP_REST_Request $request){
	$parametros = $request->get_params();

	alterLog(array(
		'acao'=>'atualizou',
		'tipoElemento'=> 'variável', 
		'idElemento'=>$parametros['variavel']['id_variavel'],
		'nomeElemento'=>$parametros['variavel']['nome'],
		'usuario'=>$parametros['usuario']
	));

	global $DbConfig;
	try {
		$pdo = new PDO('pgsql:host='.$DbConfig['host'].';port='.$DbConfig['port'].';user='.$DbConfig['user'].';dbname='.$DbConfig['dbname'].';password='.$DbConfig['password']);
	} catch (PDOException $e) {
		die("Conexão ao banco de dados falhou: " . $e->getMessage());
	}
	
	$variavel = $parametros['variavel'];
	
	$comando_string = "update sistema.variavel set
	nome = :nome,
	coluna_valor = :coluna_valor,
	operacao_agregacao = :operacao_agregacao,
	coluna_data = :coluna_data,
	coluna_dimensao = :coluna_dimensao,
	id_fonte_dados = :id_fonte_dados,
	distribuicao = :distribuicao,
	periodicidade = :periodicidade,
	desabilitar_dim_raiz = :desabilitar_dim_raiz,
	acumulativa = :acumulativa,
	crescimento = :crescimento,
	tipo_cruzamento = :tipo_cruzamento,
	tipo_valor = :tipo_valor,
	tipo_territorio = :tipo_territorio
	where id_variavel = :id_variavel";
	$comando = $pdo->prepare($comando_string);
 
 
  if(array_key_exists('id_variavel',$variavel))
		if($variavel['id_variavel'] != ''){
				 $variavel['desabilitar_dim_raiz'] = ($variavel['desabilitar_dim_raiz'])?'t':'f';
				 $variavel['distribuicao'] = ($variavel['distribuicao'])?'t':'f';
				 $variavel['acumulativa'] = ($variavel['acumulativa'])?'t':'f';
				 $variavel['crescimento'] = ($variavel['crescimento'])?'t':'f';
				if(is_null($variavel['tipo_cruzamento']))
					$variavel['tipo_cruzamento'] = 'inner';
				
				$comando->bindParam(':nome',$variavel['nome']);
				$comando->bindParam(':coluna_valor',$variavel['coluna_valor']);
				$comando->bindParam(':operacao_agregacao',$variavel['operacao_agregacao']);
				$comando->bindParam(':coluna_data',$variavel['coluna_data']);
				$comando->bindParam(':coluna_dimensao',$variavel['coluna_dimensao']);
				$comando->bindParam(':id_fonte_dados',$variavel['id_fonte_dados']);
				$comando->bindParam(':distribuicao',$variavel['distribuicao']);
				$comando->bindParam(':periodicidade',$variavel['periodicidade']);
				$comando->bindParam(':desabilitar_dim_raiz',$variavel['desabilitar_dim_raiz']);
				$comando->bindParam(':acumulativa',$variavel['acumulativa']);
				$comando->bindParam(':crescimento',$variavel['crescimento']);
				$comando->bindParam(':tipo_cruzamento',$variavel['tipo_cruzamento']);
				$comando->bindParam(':tipo_valor',$variavel['tipo_valor']);
				$comando->bindParam(':tipo_territorio',$variavel['tipo_territorio']);
				$comando->bindParam(':id_variavel',$variavel['id_variavel']);
		}
 
 	if(!$comando->execute()){
		$erro = $comando->errorInfo();
		$response = new  WP_REST_Response($erro[2],500);
	}
	else
	{
		$resp_indicador = "Erro: não foi possível obter dados do indicador.";
		$comando_string = "select distinct id_indicador From sistema.indicador_x_variavel where id_variavel = :id_variavel";
		$comando = $pdo->prepare($comando_string);
		
		$comando->bindParam(':id_variavel',$variavel['id_variavel']);
		
		if(!$comando->execute()){
			$erro = $comando->errorInfo();
			$response = new  WP_REST_Response($erro[2],500);
		}
		else
		{
			$dados = $comando->fetchAll(PDO::FETCH_ASSOC);
			
			foreach($dados as $indicador){
				$resp_indicador = $indicador;
				$id_indicador = $indicador['id_indicador'];
				$comando_string = "select sistema.calcular_indicador(:id_indicador)";
				
				$comando = $pdo->prepare($comando_string);
				
				$comando->bindParam(':id_indicador',$indicador['id_indicador']);
				
				if(!$comando->execute()){
					$erro = $comando->errorInfo();
					//$debug = var_export($indicador,true);
					$response = new  WP_REST_Response($erro[2], 500);//new  WP_REST_Response($erro[2],500);
					return $response;
				}
			}
			
		}
		$response = new WP_REST_Response( $resp_indicador );
	}
	
	return $response;
} 

function atualizar_view_dado_aberto($pdo, $id_fonte_dados){
	
	$comando_string = 'select sistema.criar_view_dado_aberto(:id_fonte_dados)';
	
	$comando = $pdo->prepare($comando_string);
	
	$comando->bindParam(':id_fonte_dados',$id_fonte_dados);
	
	if(!$comando->execute()){
		$erro = $comando->errorInfo();
		$response = new  WP_REST_Response($erro[2], 500);
	}
	else
	{
		$response = new WP_REST_Response( true );
	}
	return $response;
}


function atualizar_fonte_dados(WP_REST_Request $request){
	$parametros = $request->get_params();
	alterLog(array(
		'acao'=>'atualizou',
		'tipoElemento'=> 'fonte de dados', 
		'idElemento'=>$parametros['fonte_dados']['id_fonte_dados'],
		'nomeElemento'=>$parametros['fonte_dados']['nome'],
		'usuario'=>$parametros['usuario']
	));

	global $DbConfig;
	try {
		$pdo = new PDO('pgsql:host='.$DbConfig['host'].';port='.$DbConfig['port'].';user='.$DbConfig['user'].';dbname='.$DbConfig['dbname'].';password='.$DbConfig['password']);
	} catch (PDOException $e) {
		die("Conexão ao banco de dados falhou: " . $e->getMessage());
	}
	
	$fonte_dados = $parametros['fonte_dados'];
	
	atualizar_view_dado_aberto($pdo, $fonte_dados['id_fonte_dados']);
	
	$comando_string = "update sistema.fonte_dados set
	nome = :nome,
	delimitador = :delimitador,
	diretorio = :diretorio,
	data_atualizacao = :data_atualizacao,
	formato_arquivo = :formato_arquivo,
	nome_tabela = :nome_tabela,
	linha_cabecalho = :linha_cabecalho,
	data_inicial = :data_inicial,
	data_final = :data_final,
	tipo = :tipo,
	periodicidade = :periodicidade,
	origem = :origem,
	link = :link,
	id_usuario_mantenedor = :id_usuario_mantenedor,
	script_sql = :script_sql,
	ativa = :ativa
	where id_fonte_dados = :id_fonte_dados";
	$comando = $pdo->prepare($comando_string);
	
  if(array_key_exists('id_fonte_dados',$fonte_dados))
		if($fonte_dados['id_fonte_dados'] != ''){
				
				$comando->bindParam(':id_fonte_dados',$fonte_dados['id_fonte_dados']);
				$comando->bindParam(':nome',$fonte_dados['nome']);
				$comando->bindParam(':delimitador',$fonte_dados['delimitador']);
				$comando->bindParam(':diretorio',$fonte_dados['diretorio']);
				$comando->bindParam(':data_atualizacao',$fonte_dados['data_atualizacao']);
				$comando->bindParam(':formato_arquivo',$fonte_dados['formato_arquivo']);
				$comando->bindParam(':nome_tabela',$fonte_dados['nome_tabela']);
				$comando->bindParam(':linha_cabecalho',$fonte_dados['linha_cabecalho']);
				$comando->bindParam(':data_inicial',$fonte_dados['data_inicial']);
				$comando->bindParam(':data_final',$fonte_dados['data_final']);
				$comando->bindParam(':tipo',$fonte_dados['tipo']);
				$comando->bindParam(':periodicidade',$fonte_dados['periodicidade']);
				$comando->bindParam(':origem',$fonte_dados['origem']);
				$comando->bindParam(':link',$fonte_dados['link']);
				$comando->bindParam(':id_usuario_mantenedor',$fonte_dados['id_usuario_mantenedor']);
				$fonte_dados['ativa'] = ($fonte_dados['ativa'])?'t':'f';
				$comando->bindParam(':ativa',$fonte_dados['ativa']);
				$comando->bindParam(':script_sql',$fonte_dados['script_sql']);
		}
 
 	if(!$comando->execute()){
		$erro = $comando->errorInfo();
		$response = new  WP_REST_Response($erro[2], 500);
	}
	else
	{
		
		$colunas_exclusao = $fonte_dados['colunas_exclusao'];
		
		$response = new WP_REST_Response( true );
		
		$comando_string = "delete from sistema.fonte_dados_exclusao_coluna where id_fonte_dados = :id_fonte_dados";
		$comando = $pdo->prepare($comando_string);
	 
		$comando->bindParam(':id_fonte_dados',$fonte_dados['id_fonte_dados']);
		
		if(!$comando->execute()){
			$erro = $comando->errorInfo();
			$response = new  WP_REST_Response($erro[2], 500);
		}
		else
		{
			if(!isset($colunas_exclusao))
				$colunas_exclusao = [];
			foreach($colunas_exclusao as $chave => $valor){
				
				if($valor != null){
					
					$comando_string = "insert into sistema.fonte_dados_exclusao_coluna (id_fonte_dados, coluna)
																								values (:id_fonte_dados,:coluna)";
					$comando = $pdo->prepare($comando_string);
					
					$comando->bindParam(':id_fonte_dados',$fonte_dados['id_fonte_dados']);
					$comando->bindParam(':coluna',$valor);
					
					if(!$comando->execute()){
						$erro = $comando->errorInfo();
						$response = new  WP_REST_Response($erro[2], 500);
					}
				}
			}			
		}		
		
		$response = new WP_REST_Response( true );
	}
	
	return $response;
} 


function carregar_fonte_dados(WP_REST_Request $request){
	$parametros = $request->get_params();
	
	function loadFile($url) {
	  $ch = curl_init();

	  curl_setopt($ch, CURLOPT_HEADER, 0);
	  curl_setopt($ch, CURLOPT_VERBOSE, true);
	  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	  curl_setopt($ch, CURLOPT_URL, $url);

	  $data = curl_exec($ch);
	  curl_close($ch);

	  return $data;
	}
	//  nome da fonte não presente nos parâmetros - buscando manualmente:
	$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https://" : "http://";
	// PROTOCOLO RETORNANDO INCORRETAMENTE. DECLARADO FORÇOSAMENTE:
	// $protocol = "https://";
	
	$url = $protocol.$_SERVER["SERVER_NAME"]."/wp-json/monitoramento_pde/v1/fontes_dados?fonte_dados=".$parametros['id'];
	
	$jsonFile = loadFile($url);

    // $jsonFile = file_get_contents($protocol.$_SERVER["SERVER_NAME"]."/wp-json/monitoramento_pde/v1/fontes_dados?fonte_dados=".$parametros['id']);    

	$jsonStr = json_decode($jsonFile, true);
	$nomeElemento = $jsonStr[0]['nome'];

	alterLog(array(
		'acao'=>'carregou',
		'tipoElemento'=> 'fonte de dados', 
		'idElemento'=>$parametros['id'],
		'nomeElemento'=>$nomeElemento
	));
	global $DbConfig;
	try {
		$pdo = new PDO('pgsql:host='.$DbConfig['host'].';port='.$DbConfig['port'].';user='.$DbConfig['user'].';dbname='.$DbConfig['dbname'].';password='.$DbConfig['password']);
	} catch (PDOException $e) {
		die("Conexão ao banco de dados falhou: " . $e->getMessage());
	}

	$comando_string = 
	"select * 
	from sistema.fonte_dados 
	where id_fonte_dados = :id_fonte_dados";
	
	$comando = $pdo->prepare($comando_string);
	
	if(array_key_exists('id_fonte_dados',$parametros))
		$comando->bindParam(':id_fonte_dados',$parametros['id_fonte_dados']);
	
 	if(!$comando->execute()){
		$erro = $comando->errorInfo();
		return $erro[2]; 
	} else {
		$dados = $comando->fetchAll(PDO::FETCH_ASSOC);
	}

	$nome_fonte = $dados[0]['nome'];
	
	$diretorio = wp_upload_dir()['basedir'].'/'.$dados[0]['nome_tabela'];
	$result = wp_mkdir_p($diretorio);
	$data = date('Ymd');
	$nome_arquivo = $data.'_'.$_FILES['arquivo']['name'];
	
	move_uploaded_file($_FILES['arquivo']['tmp_name'], $diretorio.'/'.$nome_arquivo);

	$id_fonte_dados = $parametros['id_fonte_dados'];
	
	
	$role = '';
	$usuario = wp_get_current_user();
	$roleMonitoramento = '';
	foreach($usuario->roles as $role) {
		if(strtolower($role) == 'mantenedor' && $roleMonitoramento != 'administrator'){
			$roleMonitoramento = 'mantenedor';
			
		}else 
			if(strtolower($role) == 'administrator'){
				$roleMonitoramento = 'administrator';
				
			}
	}
	if($roleMonitoramento == 'administrator')
	{
		$output = 0;
		$response = 0;
		
		putenv('KETTLE_HOME=/var/www/pentaho/Pentaho/Configuracao');
		$varia_amb = getenv('KETTLE_HOME');

		$linha_comando = '/var/www/pentaho/Pentaho/data-integration/kitchen.sh -rep=MonitoramentoPDE -job=Carga -dir=/ -param:ID_FONTE_DADOS='.$id_fonte_dados.' -param:"FORMATO_ARQUIVO='.$nome_arquivo.'"  -param:"DIRETORIO_FONTE='.$diretorio.'" -param:TIPO_ARQUIVO=2';
		
		set_time_limit(90);

		exec($linha_comando,$output,$response);
		
		$listaErros = [];
		foreach ($output as $key => $value) {
			if (strpos($value, "ERROR")) {
				array_push($listaErros, $value);				
			}
		}

		// SE FOREM IDENTIFICADOS ERROS, RETORNA LISTA DE ERROS
		if (sizeof($listaErros) > 0) {
			$objErros = new stdClass();
			$objErros = (object) $listaErros;
			$objErros->message = $listaErros[0];

			// FACILITA DEPURAÇÃO DE ERROS PARA O USUÁRIO
			// Coluna com mesmo nome
			$inicioMsg = "guy) : Field";
			$finalMsg = "is specified twice with the same name!";
			if(strpos($listaErros[0], $finalMsg) !== false) {
				$msgSimples = explode($inicioMsg, $listaErros[0])[1];
				$msgSimples = 'Coluna' . str_replace($finalMsg, 'duplicada!', $msgSimples);
				$objErros->message = $msgSimples;
			}
			
			$response = new  WP_REST_Response($objErros,500);
			return $response;
		}		
		
		// DEBUG / DEPURAÇÃO DO PENTAHO/KITCHEN JOB/TRANSFORMATION
		/*
		var_dump($linha_comando);
		echo "------------------------ <br /> Output:";
		echo "<pre>";
		print_r($output);
		echo "</pre>";
		echo "**************************** <br /> Response: <br />";
		var_dump($response);
		*/
		
		$comando_string = 
		"select distinct id_indicador 
		from sistema.fonte_dados fonte
		inner join sistema.variavel var
			on var.id_fonte_dados = fonte.id_fonte_dados
		inner join sistema.indicador_x_variavel indic_var
			on indic_var.id_variavel = var.id_variavel
			where fonte.id_fonte_dados = :id_fonte_dados";
		$comando = $pdo->prepare($comando_string);
		
		$comando->bindParam(':id_fonte_dados',$parametros['id_fonte_dados']);

		if(!$comando->execute()){
			$erro = $comando->errorInfo();
			$response = new  WP_REST_Response($erro[2],500);
			return $response;
		}
		else
		{
			$dados = $comando->fetchAll(PDO::FETCH_ASSOC);
			$indicadoresSucesso = [];

			foreach($dados as $indicador){
				$id_indicador = $indicador['id_indicador'];
				$comando_string = "select sistema.calcular_indicador(:id_indicador)";
				
				$comando = $pdo->prepare($comando_string);
				
				$comando->bindParam(':id_indicador',$indicador['id_indicador']);
				// echo "\nid_indicador: ".$id_indicador;
				if(!$comando->execute()){
					$erro = $comando->errorInfo();
					$debug = var_export($indicador,true);
					// return $debug;
					$response = new  WP_REST_Response($erro[2], 500);//new  WP_REST_Response($erro[2],500);
					return $response;
				}
			}
		}		
	}
	
	/** NOTIFICAÇÃO DE ATUALIZAÇÃO DESATIVADA DURANTE TESTES **/
	/**
	$headers = 'From: Monitoramento PDE <apache@c4v3i.localdomain>'. "\r\n";
	$headers .= "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type:text/html;charset=UTF-8";
	
	$administradores = get_users('role=administrator');
	foreach($administradores as $usuario){
		$msg = 'A fonte de dados '.$nome_fonte.' foi atualizada por um mantenedor. <br><br> É necessário realizar a validação e carga do arquivo.';
		mail($usuario->data->user_email,"Monitoramento PDE - Aviso de carga de fonte de dados",$msg,$headers);
	}
	*/
	$comando_string = 
	"update	sistema.fonte_dados
	set nome_arquivo = '".$nome_arquivo."'
	where id_fonte_dados = :id_fonte_dados";
	
	$comando = $pdo->prepare($comando_string);
	
	if(array_key_exists('id_fonte_dados',$parametros))
		$comando->bindParam(':id_fonte_dados',$parametros['id_fonte_dados']);
	
	if(!$comando->execute()){
		$erro = $comando->errorInfo();
		return $erro[2]; 
	}
	atualizar_view_dado_aberto($pdo, $parametros['id_fonte_dados']);
	
	// var_dump(array_reverse($output));
	
	return $response;
} 

// INSERIR ARQUIVOS ATRELADOS À FONTE DE DADOS
function carregar_arquivo_mapas(WP_REST_Request $request){
	$parametros = $request->get_params();
	global $DbConfig;
	try {
		$pdo = new PDO('pgsql:host='.$DbConfig['host'].';port='.$DbConfig['port'].';user='.$DbConfig['user'].';dbname='.$DbConfig['dbname'].';password='.$DbConfig['password']);
	} catch (PDOException $e) {
		die("Conexão ao banco de dados falhou: " . $e->getMessage());
	}
	
	wp_verify_nonce( $_SERVER['X-WP-Nonce'], "wp_rest" );
	
	$comando_string = 
	"select * 
	from sistema.fonte_dados 
	where id_fonte_dados = :id_fonte_dados";
	
	 $comando = $pdo->prepare($comando_string);

	if(array_key_exists('id_fonte_dados',$parametros))
		$comando->bindParam(':id_fonte_dados',$parametros['id_fonte_dados']);
	
 	if(!$comando->execute()){
		$erro = $comando->errorInfo();
		return $erro[2]; 
	} else {
		$dados = $comando->fetchAll(PDO::FETCH_ASSOC);
	}
		
	$diretorio = wp_upload_dir()['basedir'].'/'.$dados[0]['nome_tabela'];
	$result = wp_mkdir_p($diretorio);
	$data = date('Ymd');
	$arquivo_mapas = $data.'_'.$_FILES['arquivo']['name'];
	
	move_uploaded_file($_FILES['arquivo']['tmp_name'], $diretorio.'/'.$arquivo_mapas);
	
	$id_fonte_dados = $parametros['id_fonte_dados'];
	
	$comando_string = 
	"update	sistema.fonte_dados
	set arquivo_mapas = '".$arquivo_mapas."'
	where id_fonte_dados = :id_fonte_dados";
	
	 $comando = $pdo->prepare($comando_string);

	if(array_key_exists('id_fonte_dados',$parametros))
		$comando->bindParam(':id_fonte_dados',$parametros['id_fonte_dados']);
	
	if(!$comando->execute()){
		$erro = $comando->errorInfo();
		return $erro[2]; 
	}
	
	atualizar_view_dado_aberto($pdo, $parametros['id_fonte_dados']);
	
	return $response;
}

function carregar_arquivo_metadados(WP_REST_Request $request){
	$parametros = $request->get_params();
	global $DbConfig;
	try {
		$pdo = new PDO('pgsql:host='.$DbConfig['host'].';port='.$DbConfig['port'].';user='.$DbConfig['user'].';dbname='.$DbConfig['dbname'].';password='.$DbConfig['password']);
	} catch (PDOException $e) {
		die("Conexão ao banco de dados falhou: " . $e->getMessage());
	}
	
	wp_verify_nonce( $_SERVER['X-WP-Nonce'], "wp_rest" );
	
	$comando_string = 
	"select * 
	from sistema.fonte_dados 
	where id_fonte_dados = :id_fonte_dados";
	
	 $comando = $pdo->prepare($comando_string);

	if(array_key_exists('id_fonte_dados',$parametros))
		$comando->bindParam(':id_fonte_dados',$parametros['id_fonte_dados']);
	
 	if(!$comando->execute()){
		$erro = $comando->errorInfo();
		return $erro[2]; 
	} else {
		$dados = $comando->fetchAll(PDO::FETCH_ASSOC);
	}
	
	$nome_fonte = $dados[0]['nome'];
	
	$diretorio = wp_upload_dir()['basedir'].'/'.$dados[0]['nome_tabela'];
	$result = wp_mkdir_p($diretorio);
	$data = date('Ymd');
	$nome_arquivo = $data.'_'.$_FILES['arquivo']['name'];
	
	move_uploaded_file($_FILES['arquivo']['tmp_name'], $diretorio.'/'.$nome_arquivo);
	
	$id_fonte_dados = $parametros['id_fonte_dados'];
	
	
	$role = '';
	$usuario = wp_get_current_user();
	$roleMonitoramento = '';
	foreach($usuario->roles as $role) {
		if(strtolower($role) == 'mantenedor' && $roleMonitoramento != 'administrator'){
			$roleMonitoramento = 'mantenedor';
			
		}else 
			if(strtolower($role) == 'administrator'){
				$roleMonitoramento = 'administrator';
				
			}
	}
	
	
	
	$headers = 'From: Monitoramento PDE <apache@c4v3i.localdomain>'. "\r\n";
	$headers .= "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type:text/html;charset=UTF-8";
	
	$administradores = get_users('role=administrator');
	foreach($administradores as $usuario){
		$msg = 'A fonte de dados '.$nome_fonte.' foi atualizada por um mantenedor. <br><br> É necessário realizar a validação e carga do arquivo.';
		mail($usuario->data->user_email,"Monitoramento PDE - Aviso de carga de fonte de dados",$msg,$headers);
	}
	
	$comando_string = 
	"update	sistema.fonte_dados
	set arquivo_metadados = '".$nome_arquivo."'
	where id_fonte_dados = :id_fonte_dados";
	
	 $comando = $pdo->prepare($comando_string);

	if(array_key_exists('id_fonte_dados',$parametros))
		$comando->bindParam(':id_fonte_dados',$parametros['id_fonte_dados']);
	
	if(!$comando->execute()){
		$erro = $comando->errorInfo();
		return $erro[2]; 
	}
	
	atualizar_view_dado_aberto($pdo, $parametros['id_fonte_dados']);
	
	return $response;
}
// FIM ADICIONA ARQUIVOS ATRELADOS À FONTE DE DADOS
 
function inserir_indicador(WP_REST_Request $request){
	$parametros = $request->get_params();
	alterLog(array(
		'acao'=>'cadastrou',
		'tipoElemento'=> 'indicador', 
		'nomeElemento'=>$parametros['indicador']['nome'],
		'usuario'=>$parametros['usuario']
	));
	global $DbConfig;
	try {
		$pdo = new PDO('pgsql:host='.$DbConfig['host'].';port='.$DbConfig['port'].';user='.$DbConfig['user'].';dbname='.$DbConfig['dbname'].';password='.$DbConfig['password']);
	} catch (PDOException $e) {
		die("Conexão ao banco de dados falhou: " . $e->getMessage());
	}
	
	$indicador = $parametros['indicador'];
	
	$comando_string = 
	"insert into sistema.indicador( nome, periodicidade, tipo_valor, nota_tecnica, nota_tecnica_resumida, apresentacao, simbolo_valor, ativo, homologacao, fonte, id_territorio_padrao, observacao, preencher_zero)
													values(:nome,:periodicidade,:tipo_valor,:nota_tecnica,:nota_tecnica_resumida,:apresentacao,:simbolo_valor,:ativo,:homologacao,:fonte,:id_territorio_padrao,:observacao,:preencher_zero)
	returning id_indicador;";
	
	$comando = $pdo->prepare($comando_string);
	
	$comando->bindParam(':nome',$indicador['nome']);
	$comando->bindParam(':periodicidade',$indicador['periodicidade']);
	$comando->bindParam(':tipo_valor',$indicador['tipo_valor']);
	$comando->bindParam(':nota_tecnica',$indicador['nota_tecnica']);
	$comando->bindParam(':nota_tecnica_resumida',$indicador['nota_tecnica_resumida']);
	$comando->bindParam(':apresentacao',$indicador['apresentacao']);
	$comando->bindParam(':simbolo_valor',$indicador['simbolo_valor']);
	$indicador['ativo'] = (isset($indicador['ativo']) && $indicador['ativo'])?'t':'f';
	$indicador['homologacao'] = (isset($indicador['homologacao']) && $indicador['homologacao'])?'t':'f';
	$indicador['preencher_zero'] = (isset($indicador['preencher_zero']) && $indicador['preencher_zero'])?'t':'f';
	$comando->bindParam(':ativo',$indicador['ativo']);
	$comando->bindParam(':homologacao',$indicador['homologacao']);
	$comando->bindParam(':fonte',$indicador['fonte']);
	$comando->bindParam(':id_territorio_padrao',$indicador['id_territorio_padrao']);
	$comando->bindParam(':observacao',$indicador['observacao']);
	$comando->bindParam(':preencher_zero',$indicador['preencher_zero']);
 
  if(!$comando->execute()){
		$erro = $comando->errorInfo();
		$response = new  WP_REST_Response($erro[2], 500);
		return $response;
	}
	else
	{
		$dados = $comando->fetchAll(PDO::FETCH_ASSOC);
		
		if(isset($indicador['id_instrumento']) && !is_null($indicador['id_instrumento'])){
			$comando_string = 
			"insert into sistema.indicador_x_grupo( id_grupo_indicador, id_indicador, ordem)
																			values(:id_grupo_indicador,:id_indicador,:ordem)";
		
			$comando = $pdo->prepare($comando_string);
			
			$comando->bindParam(':id_grupo_indicador',$indicador['id_instrumento']);
			$comando->bindParam(':id_indicador',$dados[0]['id_indicador']);
			$comando->bindParam(':ordem',$indicador['ordem_instrumento']);
			 
			if(!$comando->execute()){
				$erro = $comando->errorInfo();
				$response = new  WP_REST_Response($erro[2], 500);
				return $response;
			}
		}
		
		if(isset($indicador['id_objetivo']) && !is_null($indicador['id_objetivo']) ){
			$comando_string = 
			"insert into sistema.indicador_x_grupo( id_grupo_indicador, id_indicador, ordem)
																			values(:id_grupo_indicador,:id_indicador,:ordem)";
		
			$comando = $pdo->prepare($comando_string);
			
			$comando->bindParam(':id_grupo_indicador',$indicador['id_objetivo']);
			$comando->bindParam(':id_indicador',$dados[0]['id_indicador']);
			$comando->bindParam(':ordem',$indicador['ordem_instrumento']);
			 
			if(!$comando->execute()){
				$erro = $comando->errorInfo();
				$response = new  WP_REST_Response($erro[2], 500);
				return $response;
			}
		}
		
		if(isset($indicador['estrategias']) && !is_null($indicador['estrategias'][0]['id_grupo_indicador']) ){
			$comando_string = 
			"insert into sistema.indicador_x_grupo( id_grupo_indicador, id_indicador, ordem)
																			values(:id_grupo_indicador,:id_indicador,:ordem)";
		
			$comando = $pdo->prepare($comando_string);
			
			$comando->bindParam(':id_grupo_indicador',$indicador['estrategias'][0]['id_grupo_indicador']);
			$comando->bindParam(':id_indicador',$dados[0]['id_indicador']);
			$comando->bindParam(':ordem',$indicador['estrategias'][0]['ordem']);
			 
			if(!$comando->execute()){
				$erro = $comando->errorInfo();
				$response = new  WP_REST_Response($erro[2], 500);
				return $response;
			}
		}
		
		if(isset($indicador['estrategias']) && !is_null($indicador['estrategias'][1]['id_grupo_indicador']) ){
			$comando_string = 
			"insert into sistema.indicador_x_grupo( id_grupo_indicador, id_indicador, ordem)
																			values(:id_grupo_indicador,:id_indicador,:ordem)";
		
			$comando = $pdo->prepare($comando_string);
			
			$comando->bindParam(':id_grupo_indicador',$indicador['estrategias'][1]['id_grupo_indicador']);
			$comando->bindParam(':id_indicador',$dados[0]['id_indicador']);
			$comando->bindParam(':ordem',$indicador['estrategias'][1]['ordem']);
			 
			if(!$comando->execute()){
				$erro = $comando->errorInfo();
				$response = new  WP_REST_Response($erro[2], 500);
				return $response;
			}
		}
	}
	$response = new WP_REST_Response( $dados[0] );
	return $response;
} 
 
 
function inserir_variavel(WP_REST_Request $request){
	$parametros = $request->get_params();

	alterLog(array(
		'acao'=>'inseriu',
		'tipoElemento'=>'variável', 
		'nomeElemento'=>$parametros['variavel']['nome'],
		'usuario'=>$parametros['usuario']
	));
	
	global $DbConfig;
	try {
		$pdo = new PDO('pgsql:host='.$DbConfig['host'].';port='.$DbConfig['port'].';user='.$DbConfig['user'].';dbname='.$DbConfig['dbname'].';password='.$DbConfig['password']);
	} catch (PDOException $e) {
		die("Conexão ao banco de dados falhou: " . $e->getMessage());
	}
	
	$variavel = $parametros['variavel'];
	
	$comando_string = 
	"insert into sistema.variavel ( nome, coluna_valor, operacao_agregacao, coluna_data, coluna_dimensao, id_fonte_dados, distribuicao, periodicidade, desabilitar_dim_raiz, acumulativa, crescimento, tipo_cruzamento, tipo_valor, tipo_territorio)
													values(:nome,:coluna_valor,:operacao_agregacao,:coluna_data,:coluna_dimensao,:id_fonte_dados,:distribuicao,:periodicidade,:desabilitar_dim_raiz,:acumulativa,:crescimento,:tipo_cruzamento,:tipo_valor,:tipo_territorio)
		returning id_variavel";
	
	$comando = $pdo->prepare($comando_string);
	
	 $variavel['desabilitar_dim_raiz'] = (isset($variavel['desabilitar_dim_raiz']) && $variavel['desabilitar_dim_raiz'])?'t':'f';
	 $variavel['distribuicao'] = (isset($variavel['distribuicao']) && $variavel['distribuicao'])?'t':'f';
	 $variavel['acumulativa'] = (isset($variavel['acumulativa']) && $variavel['acumulativa'])?'t':'f';
	 $variavel['crescimento'] = (isset($variavel['crescimento']) && $variavel['crescimento'])?'t':'f';
	 
	if(!isset($variavel['tipo_cruzamento']) || is_null($variavel['tipo_cruzamento']))
		$variavel['tipo_cruzamento'] = 'inner';
 
	$comando->bindParam(':nome',$variavel['nome']);
	$comando->bindParam(':coluna_valor',$variavel['coluna_valor']);
	$comando->bindParam(':operacao_agregacao',$variavel['operacao_agregacao']);
	$comando->bindParam(':coluna_data',$variavel['coluna_data']);
	$comando->bindParam(':coluna_dimensao',$variavel['coluna_dimensao']);
	$comando->bindParam(':id_fonte_dados',$variavel['id_fonte_dados']);
	$comando->bindParam(':distribuicao',$variavel['distribuicao']);
	$comando->bindParam(':periodicidade',$variavel['periodicidade']);
	$comando->bindParam(':desabilitar_dim_raiz',$variavel['desabilitar_dim_raiz']);
	$comando->bindParam(':acumulativa',$variavel['acumulativa']);
	$comando->bindParam(':crescimento',$variavel['crescimento']);
	$comando->bindParam(':tipo_cruzamento',$variavel['tipo_cruzamento']);
	$comando->bindParam(':tipo_territorio',$variavel['tipo_territorio']);
	$comando->bindParam(':tipo_valor',$variavel['tipo_valor']);
 
  if(!$comando->execute()){
		$erro = $comando->errorInfo();
		$response = new  WP_REST_Response($erro[2], 500);
	}
	else
	{
		$dados = $comando->fetchAll(PDO::FETCH_ASSOC);
		$response = new WP_REST_Response( $dados[0] );
	}

	return $response;
} 


// TODO: ISSUE 1.X - ERRO AO CADASTRAR NOVO BANCO DE DADOS
function inserir_fonte_dados(WP_REST_Request $request){
	$parametros = $request->get_params();
	alterLog(array(
		'acao'=>'inseriu',
		'tipoElemento'=> 'nova fonte de dados', 
		'nomeElemento'=>$parametros['fonte_dados']['nome'],
		'usuario'=>$parametros['usuario']
	));

	global $DbConfig;
	try {
		$pdo = new PDO('pgsql:host='.$DbConfig['host'].';port='.$DbConfig['port'].';user='.$DbConfig['user'].';dbname='.$DbConfig['dbname'].';password='.$DbConfig['password']);
	} catch (PDOException $e) {
		die("Conexão ao banco de dados falhou: " . $e->getMessage());
	}
	
	$fonte_dados = $parametros['fonte_dados'];
	// corrige bug 'UNDEFINED INDEX: ativa'
	if (!isset($fonte_dados['ativa']))
		$fonte_dados['ativa'] = false;

	$comando_string = 
	"insert into sistema.fonte_dados ( nome, delimitador, diretorio, data_atualizacao, formato_arquivo, nome_tabela, linha_cabecalho, data_inicial, data_final, tipo, periodicidade, origem, link ,ativa)
														 values(:nome,:delimitador,:diretorio,:data_atualizacao,:formato_arquivo,:nome_tabela,:linha_cabecalho,:data_inicial,:data_final,:tipo,:periodicidade,:origem,:link,:ativa)
	 returning id_fonte_dados";
	
	$comando = $pdo->prepare($comando_string);
 
	$comando->bindParam(':nome',$fonte_dados['nome']);
	$comando->bindParam(':delimitador',$fonte_dados['delimitador']);
	$comando->bindParam(':diretorio',$fonte_dados['diretorio']);
	$comando->bindParam(':data_atualizacao',$fonte_dados['data_atualizacao']);
	$comando->bindParam(':formato_arquivo',$fonte_dados['formato_arquivo']);
	$comando->bindParam(':nome_tabela',$fonte_dados['nome_tabela']);
	$comando->bindParam(':linha_cabecalho',$fonte_dados['linha_cabecalho']);
	$comando->bindParam(':data_inicial',$fonte_dados['data_inicial']);
	$comando->bindParam(':data_final',$fonte_dados['data_final']);
	$comando->bindParam(':tipo',$fonte_dados['tipo']);
	$fonte_dados['ativa'] = ($fonte_dados['ativa'])?'t':'f';
	$comando->bindParam(':ativa',$fonte_dados['ativa']);
	$comando->bindParam(':periodicidade',$fonte_dados['periodicidade']);
	$comando->bindParam(':origem',$fonte_dados['origem']);
	$comando->bindParam(':link',$fonte_dados['link']);
 
  if(!$comando->execute()){
		$erro = $comando->errorInfo();
		$response = new  WP_REST_Response($erro[2], 500);
	}
	else
	{
		
		$dados = $comando->fetchAll(PDO::FETCH_ASSOC);
		
		$colunas_exclusao = isset($fonte_dados['colunas_exclusao']) ? $fonte_dados['colunas_exclusao'] : [];
		
		$response = new WP_REST_Response( true );
		
		$comando_string = "delete from sistema.fonte_dados_exclusao_coluna where id_fonte_dados = :id_fonte_dados";
		$comando = $pdo->prepare($comando_string);
	 
		$comando->bindParam(':id_fonte_dados',$fonte_dados['id_fonte_dados']);
		
		if(!$comando->execute()){
			$erro = $comando->errorInfo();
			$response = new  WP_REST_Response($erro[2], 500);
		}
		else
		{
			foreach($colunas_exclusao as $chave => $valor){
				
				if($valor != null){
					
					$comando_string = "insert into sistema.fonte_dados_exclusao_coluna (id_fonte_dados, coluna)
																								values (:id_fonte_dados,:coluna)";
					$comando = $pdo->prepare($comando_string);
					
					$comando->bindParam(':id_fonte_dados',$fonte_dados['id_fonte_dados']);
					$comando->bindParam(':coluna',$valor);
					
					if(!$comando->execute()){
						$erro = $comando->errorInfo();
						$response = new  WP_REST_Response($erro[2], 500);
					}
				}
			}
		}
		
		$response = new WP_REST_Response( $dados[0] );
	}

	return $response;
} 
 
function ficha_tecnica_instrumento(WP_REST_Request $request){
	$parametros = $request->get_params();
	global $DbConfig;
	try {
		$pdo = new PDO('pgsql:host='.$DbConfig['host'].';port='.$DbConfig['port'].';user='.$DbConfig['user'].';dbname='.$DbConfig['dbname'].';password='.$DbConfig['password']);
	} catch (PDOException $e) {
		die("Conexão ao banco de dados falhou: " . $e->getMessage());
	}
	
	$comando_string = "select * from fonte_dados.ficha_tecnica_instrumento where id_instrumento = :instrumento";
 $comando = $pdo->prepare($comando_string);
 
  if(array_key_exists('instrumento',$parametros))
		if($parametros['instrumento'] != '')
			$comando->bindParam(':instrumento',$parametros['instrumento']);
 
 
 	if(!$comando->execute()){
		$erro = $comando->errorInfo();
		return $erro[2]; 
	} else {
		$dados = $comando->fetchAll(PDO::FETCH_ASSOC);
	}
	
	$response = new WP_REST_Response( $dados[0] );
	return $response;
}
 
function indicador_memoria(WP_REST_Request $request){
	$parametros = $request->get_params();
	global $DbConfig;
	try {
		$pdo = new PDO('pgsql:host='.$DbConfig['host'].';port='.$DbConfig['port'].';user='.$DbConfig['user'].';dbname='.$DbConfig['dbname'].';password='.$DbConfig['password']);
	} catch (PDOException $e) {
		die("Conexão ao banco de dados falhou: " . $e->getMessage());
	}
	
	$comando_where = '';
	
	if(array_key_exists('data_inicio',$parametros))
		if($parametros['data_inicio']!=null)
			$comando_where = $comando_where.' and calc.data >= :data_inicio';
	
	if(array_key_exists('data',$parametros))
		if($parametros['data']!=null)
			$comando_where = $comando_where.' and calc.data = :data';
	
	if(array_key_exists('data_fim',$parametros))
		if($parametros['data_fim']!=null)
			$comando_where = $comando_where.' and calc.data <= :data_fim';
	
	if(array_key_exists('id_regiao',$parametros))
		if($parametros['id_regiao']!=null)
			$comando_where = $comando_where.' and calc.id_regiao = :id_regiao';
	
	if(array_key_exists('id_territorio',$parametros))
		if($parametros['id_territorio']!=null)
			$comando_where = $comando_where.' and calc.id_territorio = :id_territorio';
	
	$comando_string = 
	'
select indic.nome as "Nome"
	, indic.tipo_valor as "Unidade de medida"
	, indic.simbolo_valor as "Símbolo de medida"
	, calc.dimensao as "Categoria"
	, terr.nome as "Unidade Territorial de Análise"
	, reg.nome as "Região"
	, calc.data as "Data"
	, calc.valor as "Valor"
	, var_calc1.valor as "Variavel1"
	, var_calc2.valor as "Variavel2"
	, var_calc3.valor as "Variavel3"
	, var_calc4.valor as "Variavel4"
	, var_calc5.valor as "Variavel5"
	, var_calc6.valor as "Variavel6"
	, var_calc7.valor as "Variavel7"
	, var_calc8.valor as "Variavel8"
	
	from sistema.indicador_calculo calc
inner join sistema.indicador indic on indic.id_indicador = calc.id_indicador
inner join fonte_dados.territorio terr on terr.id_territorio = calc.id_territorio
inner join fonte_dados.regiao reg on reg.id_territorio = terr.id_territorio 
	and reg.id_regiao = calc.id_regiao
left join sistema.indicador_x_variavel indic_var1 
	on indic_var1.id_indicador = indic.id_indicador 
	and indic_var1.ordem = 1
left join sistema.indicador_x_variavel indic_var2
	on indic_var2.id_indicador = indic.id_indicador 
	and indic_var2.ordem = 2
left join sistema.indicador_x_variavel indic_var3 
	on indic_var3.id_indicador = indic.id_indicador 
	and indic_var3.ordem = 3
left join sistema.indicador_x_variavel indic_var4 
	on indic_var4.id_indicador = indic.id_indicador 
	and indic_var4.ordem = 4
left join sistema.indicador_x_variavel indic_var5 
	on indic_var5.id_indicador = indic.id_indicador 
	and indic_var5.ordem = 5
left join sistema.indicador_x_variavel indic_var6 
	on indic_var6.id_indicador = indic.id_indicador 
	and indic_var6.ordem = 6
left join sistema.indicador_x_variavel indic_var7 
	on indic_var7.id_indicador = indic.id_indicador 
	and indic_var7.ordem = 7
left join sistema.indicador_x_variavel indic_var8 
	on indic_var8.id_indicador = indic.id_indicador 
	and indic_var8.ordem = 8

left join sistema.variavel var1 
	on var1.id_variavel = indic_var1.id_variavel
left join sistema.variavel var2 
	on var2.id_variavel = indic_var2.id_variavel
left join sistema.variavel var3 
	on var3.id_variavel = indic_var3.id_variavel
left join sistema.variavel var4 
	on var4.id_variavel = indic_var4.id_variavel
left join sistema.variavel var5 
	on var5.id_variavel = indic_var5.id_variavel
left join sistema.variavel var6 
	on var6.id_variavel = indic_var6.id_variavel
left join sistema.variavel var7 
	on var7.id_variavel = indic_var7.id_variavel
left join sistema.variavel var8 
	on var8.id_variavel = indic_var8.id_variavel

left join sistema.variavel_calculo var_calc1
	on 		indic_var1.id_variavel = var_calc1.id_variavel
		and (var_calc1.data = calc.data 		or var_calc1.data is null)
		and (var_calc1.dimensao = calc.dimensao or var_calc1.dimensao is null)
		and ((var_calc1.id_territorio = calc.id_territorio
				and var_calc1.id_regiao = calc.id_regiao
				)
			  or var1.distribuicao = true
			)
left join sistema.variavel_calculo var_calc2
	on 		indic_var2.id_variavel = var_calc2.id_variavel
		and (var_calc2.data = calc.data 		or var_calc2.data is null)
		and (var_calc2.dimensao = calc.dimensao or var_calc2.dimensao is null)
		and ((var_calc2.id_territorio = calc.id_territorio
				and var_calc2.id_regiao = calc.id_regiao
				)
			  or var2.distribuicao = true
			)
left join sistema.variavel_calculo var_calc3
	on 		indic_var3.id_variavel = var_calc3.id_variavel
		and (var_calc3.data = calc.data 		or var_calc3.data is null)
		and (var_calc3.dimensao = calc.dimensao or var_calc3.dimensao is null)
		and ((var_calc3.id_territorio = calc.id_territorio
				and var_calc3.id_regiao = calc.id_regiao
				)
			  or var3.distribuicao = true
			)
left join sistema.variavel_calculo var_calc4
	on 		indic_var4.id_variavel = var_calc4.id_variavel
		and (var_calc4.data = calc.data 		or var_calc4.data is null)
		and (var_calc4.dimensao = calc.dimensao or var_calc4.dimensao is null)
		and ((var_calc4.id_territorio = calc.id_territorio
				and var_calc4.id_regiao = calc.id_regiao
				)
			  or var4.distribuicao = true
			)
left join sistema.variavel_calculo var_calc5
	on 		indic_var5.id_variavel = var_calc5.id_variavel
		and (var_calc5.data = calc.data 		or var_calc5.data is null)
		and (var_calc5.dimensao = calc.dimensao or var_calc5.dimensao is null)
		and ((var_calc5.id_territorio = calc.id_territorio
				and var_calc5.id_regiao = calc.id_regiao
				)
			  or var5.distribuicao = true
			)
left join sistema.variavel_calculo var_calc6
	on 		indic_var6.id_variavel = var_calc6.id_variavel
		and (var_calc6.data = calc.data 		or var_calc6.data is null)
		and (var_calc6.dimensao = calc.dimensao or var_calc6.dimensao is null)
		and ((var_calc6.id_territorio = calc.id_territorio
				and var_calc6.id_regiao = calc.id_regiao
				)
			  or var6.distribuicao = true
			)
left join sistema.variavel_calculo var_calc7
	on 		indic_var7.id_variavel = var_calc7.id_variavel
		and (var_calc7.data = calc.data 		or var_calc7.data is null)
		and (var_calc7.dimensao = calc.dimensao or var_calc7.dimensao is null)
		and ((var_calc7.id_territorio = calc.id_territorio
				and var_calc7.id_regiao = calc.id_regiao
				)
			  or var7.distribuicao = true
			)
left join sistema.variavel_calculo var_calc8
	on 		indic_var8.id_variavel = var_calc8.id_variavel
		and (var_calc8.data = calc.data 		or var_calc8.data is null)
		and (var_calc8.dimensao = calc.dimensao or var_calc8.dimensao is null)
		and ((var_calc8.id_territorio = calc.id_territorio
				and var_calc8.id_regiao = calc.id_regiao
				)
			  or var8.distribuicao = true
			)
where indic.id_indicador = :indicador and calc.valor is not null '
.$comando_where.'
order by indic.nome
	,terr.id_territorio
	,reg.nome
	,calc.dimensao
	,calc.data';
		
		$comando = $pdo->prepare($comando_string);
		
	if(array_key_exists('indicador',$parametros))
		if($parametros['indicador']!=null)
			$comando->bindParam(':indicador', $parametros['indicador']);

	if(array_key_exists('data_inicio',$parametros))
		if($parametros['data_inicio']!=null)
			$comando->bindParam(':data_inicio', $parametros['data_inicio']);
	
	if(array_key_exists('data_fim',$parametros))
		if($parametros['data_fim']!=null)
			$comando->bindParam(':data_fim', $parametros['data_fim']);
	
	if(array_key_exists('data',$parametros))
		if($parametros['data']!=null)
			$comando->bindParam(':data', $parametros['data']);
	
	if(array_key_exists('id_regiao',$parametros))
		if($parametros['id_regiao']!=null)
			$comando->bindParam(':id_regiao', $parametros['id_regiao']);
	
	if(array_key_exists('id_territorio',$parametros))
		if($parametros['id_territorio']!=null)
			$comando->bindParam(':id_territorio', $parametros['id_territorio']);
	
 	if(!$comando->execute()){
		$erro = $comando->errorInfo();
		return $erro[2]; 
	} else {
		$dados['dados'] = $comando->fetchAll(PDO::FETCH_ASSOC);
	}
	
	$comando_string = 'select count(id_variavel) as qtd_variavel from sistema.indicador_x_variavel where id_indicador = :indicador';
	
	$comando = $pdo->prepare($comando_string);
	
	if(array_key_exists('indicador',$parametros))
		if($parametros['indicador']!=null)
			$comando->bindParam(':indicador', $parametros['indicador']);
	
	 if(!$comando->execute()){
		$erro = $comando->errorInfo();
		return $erro[2]; 
	} else {
		$dados['qtd_variavel'] = $comando->fetchAll(PDO::FETCH_ASSOC)[0]['qtd_variavel'];
	}
	
	$response = new WP_REST_Response( $dados );
	return $response;
}
 
 
function dado_aberto(WP_REST_Request $request){
	 global $DbConfig;
	try {
		$pdo = new PDO('pgsql:host='.$DbConfig['host'].';port='.$DbConfig['port'].';user='.$DbConfig['user'].';dbname='.$DbConfig['dbname'].';password='.$DbConfig['password']);
	} catch (PDOException $e) {
		die("Conexão ao banco de dados falhou: " . $e->getMessage());
	}
	 
	 $parametros = $request->get_params();
	 
	 $comando_string = 
	 "select * from sistema.fonte_dados where id_fonte_dados = :fonte_dados;";
		
		$comando = $pdo->prepare($comando_string);
		
	if(array_key_exists('fonte_dados',$parametros))
	{
		$comando->bindParam(':fonte_dados',$parametros['fonte_dados']);
	}
	
	 if(!$comando->execute()){
		$erro = $comando->errorInfo();
		return $erro[2]; 
	} else {
		$dados = $comando->fetchAll(PDO::FETCH_ASSOC);
	}
	
		 $comando_string = 
	 "select * from fonte_dados.vw_".$dados[0]['nome_tabela'].";";
		
		$comando = $pdo->prepare($comando_string);
	
	 if(!$comando->execute()){
		$erro = $comando->errorInfo();
		return $erro[2]; 
	} else {
		$dados = $comando->fetchAll(PDO::FETCH_ASSOC);
	}
	
	$response = new WP_REST_Response( $dados );
	return $response;
	
 }
 
function fontes_dados(WP_REST_Request $request){
	$parametros = $request->get_params();
	
	/* wp_verify_nonce( $_SERVER['X-WP-Nonce'], "wp_rest" ); */
	
	global $DbConfig;
	try {
		$pdo = new PDO('pgsql:host='.$DbConfig['host'].';port='.$DbConfig['port'].';user='.$DbConfig['user'].';dbname='.$DbConfig['dbname'].';password='.$DbConfig['password']);
	} catch (PDOException $e) {
		die("Conexão ao banco de dados falhou: " . $e->getMessage());
	}
	
	$comando_where = ' where 1 = 1';
	
	if(array_key_exists('fonte_dados',$parametros))
	{
		$comando_where = $comando_where." and fonte.id_fonte_dados = :fonte_dados ";
	}else{
		$comando_where = $comando_where."";
	}
	
	if(array_key_exists('ativa',$parametros))
	{
		$comando_where = $comando_where." and fonte.ativa = true ";
	}else{
		$comando_where = $comando_where."";
	}
	
	if(array_key_exists('link',$parametros))
	{
		$comando_where = $comando_where." and link is not null ";
	}else{
		$comando_where = $comando_where."";
	}
	
	$role = '';
	$usuario = wp_get_current_user();
	$roleMonitoramento = '';
	foreach($usuario->roles as $role) {
		if(strtolower($role) == 'mantenedor' && $roleMonitoramento != 'administrator'){
			$roleMonitoramento = 'mantenedor';
			
		}else 
			if(strtolower($role) == 'administrator'){
				$roleMonitoramento = 'administrator';
				
			}
	}
	
	if($roleMonitoramento == 'mantenedor')
	{
		$comando_where = $comando_where." and id_usuario_mantenedor = :id_usuario";
	}else{
		$comando_where = $comando_where."";
	}
	
	
	//colocar parametro do link
	$comando_string = "select 
fonte.*
,json_agg(col.column_name) as colunas
,json_agg(distinct tipo_territorio) filter (where tipo_territorio is not null) as tipos_territorio
,json_agg(distinct fonte_exclusao.coluna) filter (where fonte_exclusao.coluna is not null) as colunas_exclusao
from sistema.fonte_dados fonte 
left join information_schema.columns col
	on col.table_name = fonte.nome_tabela
left join sistema.fonte_dados_exclusao_coluna fonte_exclusao
	on fonte_exclusao.id_fonte_dados = fonte.id_fonte_dados
	and col.column_name = fonte_exclusao.coluna
left join sistema.coluna tip_terr
	on tip_terr.id_fonte_dados = fonte.id_fonte_dados
	and tip_terr.nome = col.column_name
	and tip_terr.tipo_territorio is not null
	".$comando_where."
group by
fonte.id_fonte_dados
,fonte.data_carga
,fonte.nome
,fonte.ativa
,fonte.delimitador
,fonte.diretorio
,fonte.formato_arquivo
,fonte.nome_tabela
,fonte.linha_cabecalho
,fonte.data_inicial
,fonte.data_final
,fonte.tipo
,fonte.periodicidade
,fonte.origem
,fonte.data_atualizacao";
	
	 $comando = $pdo->prepare($comando_string);
	
	if(array_key_exists('fonte_dados',$parametros))
		$comando->bindParam(':fonte_dados',$parametros['fonte_dados']);
	
	if($roleMonitoramento == 'mantenedor')
		$comando->bindParam(':id_usuario',$usuario->ID);
	
 	if(!$comando->execute()){
		$erro = $comando->errorInfo();
		return $erro[2]; 
	} else {
		$dados = $comando->fetchAll(PDO::FETCH_ASSOC);
		foreach($dados as &$linha){
			$linha['colunas'] = json_decode($linha['colunas']);
			$linha['colunas_exclusao'] = json_decode($linha['colunas_exclusao']);
			$linha['tipos_territorio'] = json_decode($linha['tipos_territorio']);
		}
	}
	
	$response = new WP_REST_Response( $dados );
	return $response;
}

function instrumentos(WP_REST_Request $request){
	$parametros = $request->get_params();
	global $DbConfig;
	try {
		$pdo = new PDO('pgsql:host='.$DbConfig['host'].';port='.$DbConfig['port'].';user='.$DbConfig['user'].';dbname='.$DbConfig['dbname'].';password='.$DbConfig['password']);
	} catch (PDOException $e) {
		die("Conexão ao banco de dados falhou: " . $e->getMessage());
	}
	
	$comando_string = "
select id_grupo_indicador, nome from sistema.grupo_indicador where tipo = 'instrumento'";

 $comando = $pdo->prepare($comando_string);
 
 	if(!$comando->execute()){
		$erro = $comando->errorInfo();
		return $erro[2]; 
	} else {
		$dados = $comando->fetchAll(PDO::FETCH_ASSOC);
	}
	
	$response = new WP_REST_Response( $dados );
	return $response;
}

/*
	Issue 45
*/
function carregar_mapa_tematico(WP_REST_Request $request){
	$parametros = $request->get_params();
	global $DbConfig;
	try {
		$pdo = new PDO('pgsql:host='.$DbConfig['host'].';port='.$DbConfig['port'].';user='.$DbConfig['user'].';dbname='.$DbConfig['dbname'].';password='.$DbConfig['password']);
	} catch (PDOException $e) {
		die("Conexão ao banco de dados falhou: " . $e->getMessage());
	}
	
	if(isset($_SERVER['X-WP-Nonce']))
		wp_verify_nonce( $_SERVER['X-WP-Nonce'], "wp_rest" );
	
	$comando_string = 
	"select * 
	from sistema.grupo_indicador 
	where id_grupo_indicador = :id_grupo_indicador";
	
	 $comando = $pdo->prepare($comando_string);

	if(array_key_exists('id_grupo_indicador',$parametros))
		$comando->bindParam(':id_grupo_indicador',$parametros['id_grupo_indicador']);
	
 	if(!$comando->execute()){
		$erro = $comando->errorInfo();
		return $erro[2]; 
	} else {
		$dados = $comando->fetchAll(PDO::FETCH_ASSOC);
	}
	$diretorio = wp_upload_dir()['basedir'].'/instrumentos';
	$result = wp_mkdir_p($diretorio);
	$data = date('Ymd');
	$mapa_tematico = $data.'_'.$_FILES['arquivo']['name'];
	
	move_uploaded_file($_FILES['arquivo']['tmp_name'], $diretorio.'/'.$mapa_tematico);
	
	$id_grupo_indicador = $parametros['id_grupo_indicador'];
	
	$comando_string = 
	"update	sistema.grupo_indicador
	set mapa_tematico = '".$mapa_tematico."'
	where id_grupo_indicador = :id_grupo_indicador";
	
	 $comando = $pdo->prepare($comando_string);

	if(array_key_exists('id_grupo_indicador',$parametros))
		$comando->bindParam(':id_grupo_indicador',$parametros['id_grupo_indicador']);
	
	if(!$comando->execute()){
		$erro = $comando->errorInfo();
		return $erro[2]; 
	}
	else {
		$dados = $comando->fetchAll(PDO::FETCH_ASSOC);
	}
	
	if(sizeof($dados) == 1 && sizeof($dados[0]) == 0)
		$dados = 1;	
	else if(is_array($dados))
		$dados = json_encode($dados);

	$response = new WP_REST_Response( $dados );
	return $response;
}

function obter_mapa(WP_REST_Request $request){
	$parametros = $request->get_params();
	global $DbConfig;
	try {
		$pdo = new PDO('pgsql:host='.$DbConfig['host'].';port='.$DbConfig['port'].';user='.$DbConfig['user'].';dbname='.$DbConfig['dbname'].';password='.$DbConfig['password']);
	} catch (PDOException $e) {
		die("Conexão ao banco de dados falhou: " . $e->getMessage());
	}
	
	$comando_string = "
	select mapa_tematico, parametros_mapa
	from sistema.grupo_indicador
	where id_grupo_indicador = :id_grupo_indicador";

	$comando = $pdo->prepare($comando_string);
	 
	if(array_key_exists('id_grupo_indicador',$parametros))
			$comando->bindParam(':id_grupo_indicador',$parametros['id_grupo_indicador']);
 
 	if(!$comando->execute()){
		$erro = $comando->errorInfo();
		var_dump($erro);
		return $erro[2]; 
	} else {
		$dados = $comando->fetchAll(PDO::FETCH_ASSOC);
	}

	$response = new WP_REST_Response( $dados[0] );
	return $response;
}

function gravar_parametros_mapa(WP_REST_Request $request){
	$parametros = $request->get_params();
	global $DbConfig;
	try {
		$pdo = new PDO('pgsql:host='.$DbConfig['host'].';port='.$DbConfig['port'].';user='.$DbConfig['user'].';dbname='.$DbConfig['dbname'].';password='.$DbConfig['password']);
	} catch (PDOException $e) {
		die("Conexão ao banco de dados falhou: " . $e->getMessage());
	}
	
	$comando_string = "
	update sistema.grupo_indicador
	set parametros_mapa = :parametros_mapa
	where id_grupo_indicador = :id_grupo_indicador;";

	$comando = $pdo->prepare($comando_string);
	 
	if(array_key_exists('id_grupo_indicador',$parametros))
			$comando->bindParam(':id_grupo_indicador',$parametros['id_grupo_indicador']);
	if(array_key_exists('parametros_mapa',$parametros))
			$comando->bindParam(':parametros_mapa',$parametros['parametros_mapa']);
 
 	if(!$comando->execute()){
		$erro = $comando->errorInfo();
		var_dump($erro);
		return $erro[2]; 
	} else {
		$dados = $comando->fetchAll(PDO::FETCH_ASSOC);
	}
	
	$response = new WP_REST_Response( $dados[0] );
	return $response;
}

// END Issue 45

function indicador_fusao(WP_REST_Request $request){
	$parametros = $request->get_params();
	global $DbConfig;
	try {
		$pdo = new PDO('pgsql:host='.$DbConfig['host'].';port='.$DbConfig['port'].';user='.$DbConfig['user'].';dbname='.$DbConfig['dbname'].';password='.$DbConfig['password']);
	} catch (PDOException $e) {
		die("Conexão ao banco de dados falhou: " . $e->getMessage());
	}
	
	if(array_key_exists('id_indicador',$parametros))
	{
		$comando_where = $comando_where." where id_indicador_pai = :id_indicador ";
	}else{
		$comando_where = $comando_where."";
	}
	
	$comando_string = "
		select
		id_indicador_pai
		,indic_pai.nome as nome_indicador_pai
		,json_agg(json_build_object('id_indicador_filho',id_indicador_filho,'nome_indicador_filho',indic_filho.nome,'dimensao',dimensao)) as composicao
		from sistema.indicador_composicao comp
			inner join sistema.indicador indic_pai
				on indic_pai.id_indicador = comp.id_indicador_pai
			inner join sistema.indicador indic_filho
				on indic_filho.id_indicador = comp.id_indicador_filho
			".$comando_where."
		group by 
		id_indicador_pai
		,indic_pai.nome";
		
 $comando = $pdo->prepare($comando_string);
 
  if(array_key_exists('id_indicador',$parametros))
		$comando->bindParam(':id_indicador',$parametros['id_indicador']);
 
 	if(!$comando->execute()){
		$erro = $comando->errorInfo();
		return $erro[2]; 
	} else {
		$dados = $comando->fetchAll(PDO::FETCH_ASSOC);
		foreach($dados as &$linha){
			$linha['composicao'] = json_decode($linha['composicao']);
		}
	}
	
	$response = new WP_REST_Response( $dados );
	return $response;
}

function fonte_dados_coluna(WP_REST_Request $request){
	$parametros = $request->get_params();
	global $DbConfig;
	try {
		$pdo = new PDO('pgsql:host='.$DbConfig['host'].';port='.$DbConfig['port'].';user='.$DbConfig['user'].';dbname='.$DbConfig['dbname'].';password='.$DbConfig['password']);
	} catch (PDOException $e) {
		die("Conexão ao banco de dados falhou: " . $e->getMessage());
	}
	
	$fonte_dados = $parametros['id_fonte_dados'];
	
	$comando_string = "
	select 
	id_coluna
	,id_fonte_dados
	,tipo
	,nome
	,formato
	,id_territorio
	,tipo_territorio
	from sistema.coluna where id_fonte_dados = :id_fonte_dados";

 $comando = $pdo->prepare($comando_string);

  if(array_key_exists('id_fonte_dados',$parametros))
		$comando->bindParam(':id_fonte_dados',$parametros['id_fonte_dados']);
 
 	if(!$comando->execute()){
		$erro = $comando->errorInfo();
		return $erro[2]; 
	} else {
		$dados = $comando->fetchAll(PDO::FETCH_ASSOC);
	}
	
	$response = new WP_REST_Response( $dados );
	return $response;
}


function variavel_filtro(WP_REST_Request $request){
	$parametros = $request->get_params();
	global $DbConfig;
	try {
		$pdo = new PDO('pgsql:host='.$DbConfig['host'].';port='.$DbConfig['port'].';user='.$DbConfig['user'].';dbname='.$DbConfig['dbname'].';password='.$DbConfig['password']);
	} catch (PDOException $e) {
		die("Conexão ao banco de dados falhou: " . $e->getMessage());
	}
	
	$variavel = $parametros['variavel'];
	
	$comando_string = "
	select 
	id_filtro
	,id_variavel
	,coluna
	,valor
	,operador_comparador
	,ordem
	,aninhamento
	,trim(both from operador_logico) as operador_logico
	,excluir_regiao_raiz
	from sistema.variavel_filtro where id_variavel = :variavel";

 $comando = $pdo->prepare($comando_string);

  if(array_key_exists('variavel',$parametros))
		$comando->bindParam(':variavel',$parametros['variavel']);
 
 	if(!$comando->execute()){
		$erro = $comando->errorInfo();
		return $erro[2]; 
	} else {
		$dados = $comando->fetchAll(PDO::FETCH_ASSOC);
	}
	
	$response = new WP_REST_Response( $dados );
	return $response;
}

function indicador_composicao(WP_REST_Request $request){
	$parametros = $request->get_params();
	global $DbConfig;
	try {
		$pdo = new PDO('pgsql:host='.$DbConfig['host'].';port='.$DbConfig['port'].';user='.$DbConfig['user'].';dbname='.$DbConfig['dbname'].';password='.$DbConfig['password']);
	} catch (PDOException $e) {
		die("Conexão ao banco de dados falhou: " . $e->getMessage());
	}
	
	$comando_string = "
	select ind.* from sistema.indicador_x_variavel ind
	where id_indicador = :indicador";

 $comando = $pdo->prepare($comando_string);

 	if(array_key_exists('indicador',$parametros))
		$comando->bindParam(':indicador',$parametros['indicador']);
 
 	if(!$comando->execute()){
		$erro = $comando->errorInfo();
		return $erro[2]; 
	} else {
		$dados = $comando->fetchAll(PDO::FETCH_ASSOC);
	}
	
	$response = new WP_REST_Response( $dados );
	return $response;
}


function territorios(WP_REST_Request $request){
	$parametros = $request->get_params();
	global $DbConfig;
	try {
		$pdo = new PDO('pgsql:host='.$DbConfig['host'].';port='.$DbConfig['port'].';user='.$DbConfig['user'].';dbname='.$DbConfig['dbname'].';password='.$DbConfig['password']);
	} catch (PDOException $e) {
		die("Conexão ao banco de dados falhou: " . $e->getMessage());
	}
	
	$comando_string = "
select id_territorio, 
			nome,
			hierarquia,
			id_territorio_pai,
			data_referencia,
			data_carga
			from fonte_dados.territorio";

 $comando = $pdo->prepare($comando_string);
 
 	if(!$comando->execute()){
		$erro = $comando->errorInfo();
		return $erro[2]; 
	} else {
		$dados = $comando->fetchAll(PDO::FETCH_ASSOC);
	}
	
	$response = new WP_REST_Response( $dados );
	return $response;
}


// Retorna objetivos do indicador
function objetivo_indicador(WP_REST_Request $request){
	$parametros = $request->get_params();
	global $DbConfig;
	try {
		$pdo = new PDO('pgsql:host='.$DbConfig['host'].';port='.$DbConfig['port'].';user='.$DbConfig['user'].';dbname='.$DbConfig['dbname'].';password='.$DbConfig['password']);
	} catch (PDOException $e) {
		die("Conexão ao banco de dados falhou: " . $e->getMessage());
	}
	
	$comando_string = "
		SELECT id_grupo_indicador FROM sistema.indicador_x_grupo WHERE id_indicador=:id AND id_grupo_indicador IN (SELECT id_grupo_indicador FROM sistema.grupo_indicador WHERE tipo='objetivo')";

	$comando = $pdo->prepare($comando_string);
	$comando->bindParam(':id',$parametros['id']);

 	if(!$comando->execute()){
		$erro = $comando->errorInfo();
		return $erro[2]; 
	} else {
		$dados = $comando->fetchAll(PDO::FETCH_ASSOC);
	}
	
	$response = new WP_REST_Response( $dados );
	return $response;
}

function grupo_indicador(WP_REST_Request $request){
	$parametros = $request->get_params();
	global $DbConfig;
	try {
		$pdo = new PDO('pgsql:host='.$DbConfig['host'].';port='.$DbConfig['port'].';user='.$DbConfig['user'].';dbname='.$DbConfig['dbname'].';password='.$DbConfig['password']);
	} catch (PDOException $e) {
		die("Conexão ao banco de dados falhou: " . $e->getMessage());
	}

	if(array_key_exists('grupo',$parametros))
		if($parametros['grupo'] != '')
			$comando_where = " and grp.id_grupo_indicador = :grupo";
	
	if(array_key_exists('tipo',$parametros))
		if($parametros['tipo'] != '')
			/*INICIO - IF incluído na internalização por conta do erro na tentativa de preenchimento da lista de instrumentos*/
			if(!empty($comando_where)){
				$comando_where = $comando_where." and grp.tipo = :tipo";
			} else{
				$comando_where = " and grp.tipo = :tipo";
			}
			/*FIM*/
		
	if(array_key_exists('tipo_retorno',$parametros))
		if($parametros['tipo_retorno'] != '')
			if($parametros['tipo_retorno'] == 'array')
				$comando_propriedades =  "json_agg(json_build_object('chave',prop.chave,'valor',prop.valor,'ordem',prop.ordem) order by prop.ordem)";
			if($parametros['tipo_retorno'] == 'object')
				$comando_propriedades = "json_object_agg(coalesce(prop.chave,'null'),prop.valor)";
		
	$comando_string = "
		select grp.id_grupo_indicador, nome, ".$comando_propriedades."   as propriedades
		from sistema.grupo_indicador grp

		left join sistema.grupo_propriedade prop on grp.id_grupo_indicador = prop.id_grupo_indicador
		where 1=1 ".$comando_where.
		" group by grp.id_grupo_indicador,grp.nome
		order by grp.id_grupo_indicador";

	$comando = $pdo->prepare($comando_string);
 
 	if(array_key_exists('grupo',$parametros))
		if($parametros['grupo'] != '')
			$comando->bindParam(':grupo',$parametros['grupo']);
 
	if(array_key_exists('tipo',$parametros))
		if($parametros['tipo'] != '')
			$comando->bindParam(':tipo',$parametros['tipo']);
 
 	if(!$comando->execute()){
		$erro = $comando->errorInfo();
		return $erro[2]; 
	} else {
		$dados = $comando->fetchAll(PDO::FETCH_ASSOC);
	}
	foreach($dados as &$linha){
		$linha['propriedades'] = json_decode($linha['propriedades']);
	}
		
	if(array_key_exists('formato_retorno',$parametros)){
		if($parametros['formato_retorno'] != '')
			if($parametros['formato_retorno'] == 'array')
				$retorno = $dados;
			else
				$retorno = $dados[0];
	}else
		$retorno = count($dados)==1? $dados[0]:$dados;
	
	$response = new WP_REST_Response($retorno);
	return $response;
}
 
function acoes_prioritarias(WP_REST_Request $request){
	$parametros = $request->get_params();
	global $DbConfig;
	try {
		$pdo = new PDO('pgsql:host='.$DbConfig['host'].';port='.$DbConfig['port'].';user='.$DbConfig['user'].';dbname='.$DbConfig['dbname'].';password='.$DbConfig['password']);
	} catch (PDOException $e) {
		die("Conexão ao banco de dados falhou: " . $e->getMessage());
	}
	
	$comando_string = "select * from fonte_dados.acoes_prioritarias where 1 = 1";
	
	if(array_key_exists('categoria',$parametros))
		if($parametros['categoria'] != '')
			$comando_string = $comando_string.' and categoria = :categoria';
			
	if(array_key_exists('estrategia',$parametros))
		if($parametros['estrategia'] != '')
			$comando_string = $comando_string.' and objetivo_relacionado = :estrategia';
			
	if(array_key_exists('andamento',$parametros))
		if($parametros['andamento'] != '')
			$comando_string = $comando_string.' and andamento = :andamento';
	
	if(array_key_exists('artigo',$parametros))
		if($parametros['artigo'] != '')
			$comando_string = $comando_string.' and artigo = :artigo';
	
	if(array_key_exists('tema',$parametros))
		if($parametros['tema'] != '')
			$comando_string = $comando_string.' and tema = :tema';
		
	if(array_key_exists('estagio_implementacao',$parametros))
		if($parametros['estagio_implementacao'] != '')
			$comando_string = $comando_string.' and estagio_implementacao = :estagio_implementacao';
	
	$comando = $pdo->prepare($comando_string);
	
	if(array_key_exists('andamento',$parametros))
		if($parametros['andamento'] != '')
			$comando->bindParam(':andamento',$parametros['andamento']);
	
	if(array_key_exists('estrategia',$parametros))
		if($parametros['estrategia'] != '')
			$comando->bindParam(':estrategia',$parametros['estrategia']);
	
	if(array_key_exists('categoria',$parametros))
		if($parametros['categoria'] != '')
			$comando->bindParam(':categoria',$parametros['categoria']);
	
	if(array_key_exists('artigo',$parametros))
		if($parametros['artigo'] != '')
			$comando->bindParam(':artigo',$parametros['artigo']);
	
	if(array_key_exists('tema',$parametros))
		if($parametros['tema'] != '')
			$comando->bindParam(':tema',$parametros['tema']);
	
	if(array_key_exists('estagio_implementacao',$parametros))
		if($parametros['estagio_implementacao'] != '')
			$comando->bindParam(':estagio_implementacao',$parametros['estagio_implementacao']);
	
	if(!$comando->execute()){
		$erro = $comando->errorInfo();
		return $erro[2]; 
	} else {
		$dados = $comando->fetchAll(PDO::FETCH_ASSOC);
	}
	
	$response = new WP_REST_Response( $dados );
	return $response;
}

function indicador_historico(WP_REST_Request $request){
	$parametros = $request->get_params();
	global $DbConfig;
	try {
		$pdo = new PDO('pgsql:host='.$DbConfig['host'].';port='.$DbConfig['port'].';user='.$DbConfig['user'].';dbname='.$DbConfig['dbname'].';password='.$DbConfig['password']);
	} catch (PDOException $e) {
		die("Conexão ao banco de dados falhou: " . $e->getMessage());
	}
	
	$comando_string = 
	"select comb.dimensao as name
		,json_agg(case when lower(indic.tipo_valor) = 'percentual' 
			then case when indic.preencher_zero = 't' then coalesce(calc.valor,0) else calc.valor end * 100 
			else case when indic.preencher_zero = 't' then coalesce(calc.valor,0) else calc.valor end 
			end order by comb.data asc) as data
	from sistema.indicador_calculo calc
	inner join fonte_dados.regiao reg on reg.id_territorio = calc.id_territorio and reg.id_regiao = calc.id_regiao and calc.id_territorio = :territorio and calc.id_regiao = :regiao
	
	right join 
		(select distinct data
				,dimen as dimensao
				,id_indicador
		from sistema.indicador_calculo 
		cross join 
			(select distinct coalesce(calc.dimensao,reg.nome) as dimen from sistema.indicador_calculo calc inner join fonte_dados.regiao reg on reg.id_regiao = calc.id_regiao and calc.id_territorio = reg.id_territorio and calc.id_territorio = :territorio and calc.id_regiao = :regiao where id_indicador = :indicador) dim 
		where id_indicador = :indicador
		order by dimen
				,data
		) comb on comb.data = calc.data and comb.dimensao = coalesce(calc.dimensao,reg.nome) and comb.id_indicador = calc.id_indicador
	left join sistema.indicador indic on indic.id_indicador = comb.id_indicador and indic.id_indicador = :indicador
	where comb.data between :dataMinima and :dataMaxima 
	group by comb.dimensao";
	
	$comando = $pdo->prepare($comando_string);
	
	$comando->bindParam(':dataMinima', $parametros['dataMinima']);
	$comando->bindParam(':dataMaxima', $parametros['dataMaxima']);
	$comando->bindParam(':indicador', $parametros['indicador']);
	$comando->bindParam(':territorio', $parametros['territorio']);
	$comando->bindParam(':regiao', $parametros['regiao']);

	
	$dados = [];
	if (!$comando->execute()) {
		$erro = $comando->errorInfo();
		return $erro[2]; 
	} else {
		$dados_calculo = $comando->fetchAll(PDO::FETCH_ASSOC);
		foreach($dados_calculo as &$linha){
			$linha['data'] = json_decode($linha['data']);
		}
		$dados['series'] = $dados_calculo;
	}
	
	$response = new WP_REST_Response( $dados );
	return $response;
	
}

function variavel_historico(WP_REST_Request $request){
	$parametros = $request->get_params();
	global $DbConfig;
	try {
		$pdo = new PDO('pgsql:host='.$DbConfig['host'].';port='.$DbConfig['port'].';user='.$DbConfig['user'].';dbname='.$DbConfig['dbname'].';password='.$DbConfig['password']);
	} catch (PDOException $e) {
		die("Conexão ao banco de dados falhou: " . $e->getMessage());
	}
	
	$comando_string = 
	"select v.nome, v.coluna_data,v.distribuicao, v.tipo_valor, calc.* 
	from sistema.variavel_calculo calc
	inner join sistema.variavel v
		on v.id_variavel = calc.id_variavel
	inner join sistema.indicador_x_variavel ind_var 
		on 	ind_var.id_variavel = v.id_variavel
			and ind_var.id_indicador = :indicador
	inner join sistema.indicador ind on
		ind.id_indicador = ind_var.id_indicador
		
	where (calc.id_territorio = :territorio or (calc.id_regiao is null and v.distribuicao = true))
	and ((v.coluna_data is not null and calc.data is not null) or (v.coluna_data is null))
	order by ind_var.ordem";
	
	$comando = $pdo->prepare($comando_string);
	
	$comando->bindParam(':indicador', $parametros['indicador']);
	$comando->bindParam(':territorio', $parametros['territorio']);
	
	$dados = [];
	if (!$comando->execute()) {
		$erro = $comando->errorInfo();
		return $erro[2]; 
	} else {
		$dados = $comando->fetchAll(PDO::FETCH_ASSOC);
	}
	
	$response = new WP_REST_Response( $dados );
	return $response;
	
}
	
	
function variavel_cadastro(WP_REST_Request $request){
	$parametros = $request->get_params();
	global $DbConfig;
	try {
		$pdo = new PDO('pgsql:host='.$DbConfig['host'].';port='.$DbConfig['port'].';user='.$DbConfig['user'].';dbname='.$DbConfig['dbname'].';password='.$DbConfig['password']);
	} catch (PDOException $e) {
		die("Conexão ao banco de dados falhou: " . $e->getMessage());
	}
	
	$comando_where = ' where 1 = 1';
	
	if(array_key_exists('variavel',$parametros))
	{
		$comando_where = $comando_where." and var.id_variavel = :variavel ";
	}else{
		$comando_where = $comando_where."";
	}
	
	$comando_string = 
	"select * from sistema.variavel".$comando_where;

	$comando = $pdo->prepare($comando_string);
	
	if(array_key_exists('variavel',$parametros))
		$comando->bindParam(':variavel',$parametros['variavel']);
	
	if(!$comando->execute()){
		$erro = $comando->errorInfo();
		return $erro[2]; 
	} else {
		$dados = $comando->fetchAll(PDO::FETCH_ASSOC);
	}
	
	$response = new WP_REST_Response( count($dados)==1? $dados[0]:$dados );
	return $response;
}
	
	
function indicador_cadastro(WP_REST_Request $request){
	$parametros = $request->get_params();
	
/*	wp_verify_nonce( $_SERVER['X-WP-Nonce'], "wp_rest" ); */
	
	global $DbConfig;
	try {
		$pdo = new PDO('pgsql:host='.$DbConfig['host'].';port='.$DbConfig['port'].';user='.$DbConfig['user'].';dbname='.$DbConfig['dbname'].';password='.$DbConfig['password']);
	} catch (PDOException $e) {
		die("Conexão ao banco de dados falhou: " . $e->getMessage() . "], dados=[" . 'pgsql:host='.$DbConfig['host'].';port='.$DbConfig['port'].';user='.$DbConfig['user'].';dbname='.$DbConfig['dbname'].';password='.$DbConfig['password'] . "]");
	}
	
	$comando_where = ' where 1 = 1 ';
	
	if(array_key_exists('somente_ativos',$parametros))
		$comando_where = $comando_where.' and (indic.ativo = true or indic.homologacao = true)';
	
	if(array_key_exists('indicador',$parametros))
	{
		$comando_where = $comando_where." and indic.id_indicador = :indicador ";
	}else{
		$comando_where = $comando_where."";
	}
	
	$comando_join = '';
	$comando_group = '';
	
	if(array_key_exists('grupo_indicador',$parametros))
	{
		$comando_where = $comando_where." and indic.id_indicador in (select distinct id_indicador from sistema.indicador_x_grupo where id_grupo_indicador = :grupo_indicador)";
		$comando_join = $comando_join."left join sistema.indicador_x_grupo grp_indic
			on grp_indic.id_grupo_indicador = :grupo_indicador
			and grp_indic.id_indicador = indic.id_indicador";
		$comando_group = $comando_group.",grp_indic.ordem 
		order by grp_indic.ordem";
	}
	
	$role = '';
	$roleMonitoramento = 'usuario';
	if(is_user_logged_in()){
		$usuario = wp_get_current_user();
		$roleMonitoramento = '';
		foreach($usuario->roles as $role) {
			if(strtolower($role) == 'mantenedor' && $roleMonitoramento != 'administrator'){
				$roleMonitoramento = 'mantenedor';
				
			}else 
				if(strtolower($role) == 'administrator'){
					$roleMonitoramento = 'administrator';
					
				}
		}
	}
	
	if($roleMonitoramento != 'administrator'){
		$comando_where = $comando_where." and indic.homologacao != true";
		
	}
	
	
	$comando_string = 
	"select indic.id_indicador as id_indicador
			,indic.nome
			,indic.ativo
			,indic.homologacao
			,indic.periodicidade
			,indic.tipo_valor
			,indic.simbolo_valor
			,indic.nota_tecnica
			,indic.nota_tecnica_resumida
			,indic.apresentacao
			,indic.fonte as origem
			,indic.id_territorio_padrao
			,indic.observacao
			,indic.tipo_valor
			,indic.fonte
			,indic.preencher_zero
			,json_agg(distinct jsonb_build_object('id',exc.id_territorio,'label',exc.nome)) as territorio_exclusao
			,max(case when grupo.tipo = 'instrumento' then grupo.nome else null end) as instrumento
			,max(case when grupo.tipo = 'instrumento' then grupo.id_grupo_indicador else null end) as id_instrumento
			,max(case when grupo.tipo = 'instrumento' then grupo.ordem else null end) as ordem_instrumento
			,fonte_var.formula_calculo ||  case when indic.tipo_valor = 'Percentual' then ' * 100' else '' end as formula_calculo
			,json_agg(distinct case when grupo.tipo = 'estrategia' then jsonb_build_object('id_grupo_indicador',grupo.id_grupo_indicador,'nome',grupo.nome,'ordem',grupo.ordem) else null end ) FILTER (WHERE grupo.tipo = 'estrategia') as estrategias
			,json_agg(distinct case when grupo.tipo = 'estrategia' then grupo.id_grupo_indicador else null end ) FILTER (WHERE grupo.tipo = 'estrategia') as id_estrategia
			,fonte_var.data_atualizacao 
			,json_agg(distinct calc.data order by calc.data desc) FILTER (WHERE (calc.data >= indic.data_inicio or indic.data_inicio is null) and (calc.data <= indic.data_fim or indic.data_fim is null)) as datas
			,json_agg(distinct cast(row_to_json(ter) as jsonb)) as territorios
	from sistema.indicador indic
		left join sistema.indicador_calculo calc
			on indic.id_indicador = calc.id_indicador
		left join lateral 
		 (select indic_var.id_indicador
			,max(fonte.data_atualizacao) as data_atualizacao
			
			,string_agg(coalesce(indic_var.aninhamento,'') || coalesce(var.nome,'') || '' || coalesce('\n (' || indic_var.operador || ') \n','') || '', ' ' order by indic_var.ordem) as formula_calculo
			from sistema.indicador_x_variavel indic_var
				left join sistema.variavel var 
					on var.id_variavel = indic_var.id_variavel
				left join sistema.fonte_dados fonte
					on var.id_fonte_dados = fonte.id_fonte_dados
			group by indic_var.id_indicador
			order by indic_var.id_indicador
			) fonte_var 
				on fonte_var.id_indicador = indic.id_indicador
		left join 
			(select grp.*,grp_indic.id_indicador,grp_indic.ordem from sistema.indicador_x_grupo grp_indic
				inner join sistema.grupo_indicador grp
				on grp.id_grupo_indicador = grp_indic.id_grupo_indicador
			) grupo 
			on grupo.id_indicador = indic.id_indicador
		".$comando_join."
		left join (select id_territorio as id_territorio,
						nome,ordem from fonte_dados.territorio 
				   order by ordem) ter
			on ter.id_territorio = calc.id_territorio
		left join (select id_indicador, sub_exc.id_territorio, ter_exc.nome from 
					sistema.indicador_territorio_exclusao sub_exc
					inner join fonte_dados.territorio ter_exc
						on ter_exc.id_territorio = sub_exc.id_territorio) exc
			on exc.id_indicador = indic.id_indicador".
	$comando_where.
	"	and ter.id_territorio not in (select exc.id_territorio from sistema.indicador_territorio_exclusao exc where exc.id_indicador = indic.id_indicador and exc.id_territorio is not null)"
	." group by indic.id_indicador
						,indic.nome
						,indic.periodicidade
						,indic.tipo_valor
						,indic.simbolo_valor
						,indic.nota_tecnica
						,indic.nota_tecnica_resumida
						,indic.apresentacao
						,indic.fonte
						,indic.preencher_zero
						,fonte_var.data_atualizacao 
						,fonte_var.formula_calculo".
	$comando_group;
	
error_log( "--->comando_string=[", 0);
error_log( $comando_string , 0);
error_log( "]", 0);

	$comando = $pdo->prepare($comando_string);
	
	if(array_key_exists('indicador',$parametros))
		$comando->bindParam(':indicador',$parametros['indicador']);
	
	if(array_key_exists('grupo_indicador',$parametros))
		$comando->bindParam(':grupo_indicador',$parametros['grupo_indicador']);
	
	
	if(!$comando->execute()){
		$erro = $comando->errorInfo();
		return $erro[2]; 
	} else {
		$dados = $comando->fetchAll(PDO::FETCH_ASSOC);
	}
	
	foreach($dados as &$linha){
		$linha['datas'] = json_decode($linha['datas']);
		$linha['territorios'] = json_decode($linha['territorios']);
		$linha['territorio_exclusao'] = json_decode($linha['territorio_exclusao']);
		$linha['estrategias'] = json_decode($linha['estrategias']);
	}
	
	$response = new WP_REST_Response(
		//count($dados)==1? $dados[0]:$dados
		$dados
	);
	return $response;
}
 
function indicador_dados( WP_REST_Request $request ) {

	// You can get the combined, merged set of parameters:
	$parametros = $request->get_params();
	global $DbConfig;
	try {
		$pdo = new PDO('pgsql:host='.$DbConfig['host'].';port='.$DbConfig['port'].';user='.$DbConfig['user'].';dbname='.$DbConfig['dbname'].';password='.$DbConfig['password']);
	} catch (PDOException $e) {
		die("Conexão ao banco de dados falhou: " . $e->getMessage());
	}
	
	if(array_key_exists('indicador',$parametros) && array_key_exists('data', $parametros) && array_key_exists('territorio',$parametros))
	{
		
		//esse select abaixo é somente para debug, na interface uso somente os dados que puxa pelo cadastro
		$comando = $pdo->prepare("
			select nome
					,periodicidade
			from sistema.indicador
			where id_indicador = :indicador;");
		
		$comando->bindParam(':indicador',$parametros['indicador']);
		
		if(!$comando->execute()){
			$erro = $comando->errorInfo();
			return $erro[2]; 
		} else {
			$dados_indicador = $comando->fetchAll(PDO::FETCH_ASSOC);
			$dados = $dados_indicador[0];
		}
		
		if($parametros['data'] == "")
			$parametros['data'] = NULL;
		
		$comando = $pdo->prepare("
			select 	coalesce(dim.dimensao,'Não categorizado') as name,
					json_agg(coalesce(case when lower(indic.tipo_valor) = 'percentual' 
									  then calc.valor * 100 
									  else calc.valor 
									  end,0) order by rank_reg.rank asc) as data
			from 	fonte_dados.regiao reg
					cross join (select distinct dimensao 
								from sistema.indicador_calculo 
								where id_indicador = :indicador) dim
					left join sistema.indicador_calculo calc
						on reg.id_regiao = calc.id_regiao
						and reg.id_territorio = calc.id_territorio
						and coalesce(calc.dimensao,'') = coalesce(dim.dimensao,'')
						and calc.data ".(is_null($parametros['data'])?"is null":"= :data")."  
						and calc.id_indicador = :indicador
					left join	(select reg.id_regiao
										,rank() OVER (order by case when reg.id_territorio = 3 then cast(reg.id_regiao as text) else reg.nome end asc) as rank
								from fonte_dados.regiao reg 
								left join sistema.indicador_calculo calc
										on reg.id_regiao = calc.id_regiao
										and reg.id_territorio = calc.id_territorio
										and calc.data ".(is_null($parametros['data'])?"is null":"= :data")."  
										and calc.id_indicador = :indicador
								where reg.id_territorio = :territorio
								group by reg.id_regiao
										,reg.id_territorio
										,reg.nome) rank_reg 
						on rank_reg.id_regiao = reg.id_regiao
					left join sistema.indicador indic
						on indic.id_indicador = calc.id_indicador
			where reg.id_territorio = :territorio
			group by dim.dimensao
			order by name desc;");
		
		$comando->bindParam(':indicador', $parametros['indicador']);
		if(!is_null($parametros['data']))
			$comando->bindParam(':data', $parametros['data']);
		$comando->bindParam(':territorio', $parametros['territorio']);
	
		if (!$comando->execute()) {
			$erro = $comando->errorInfo();
			return $erro[2]; 
		} else {
			$dados_calculo = $comando->fetchAll(PDO::FETCH_ASSOC);
			foreach($dados_calculo as &$linha){
				$linha['data'] = json_decode($linha['data']);
			}
			$dados['series'] = $dados_calculo;
		}
		
		$comando = $pdo->prepare("
			select reg.nome
			,max(case when reg.id_territorio = 3 then cast(reg.id_regiao as text) else reg.nome end) as ordem 
			from fonte_dados.regiao reg 
			left join sistema.indicador_calculo calc
					on reg.id_regiao = calc.id_regiao
					and reg.id_territorio = calc.id_territorio
					and calc.data ".(is_null($parametros['data'])?"is null":"= :data ")." 
					and calc.id_indicador = :indicador
			where reg.id_territorio = :territorio
			group by reg.id_regiao
					,reg.nome
			order by ordem asc");
			

		$comando->bindParam(':indicador', $parametros['indicador']);
		if(!is_null($parametros['data']))
			$comando->bindParam(':data', $parametros['data']);
		$comando->bindParam(':territorio', $parametros['territorio']);
		
		if(!$comando->execute()){
			$erro = $comando->errorInfo();
			return $erro[2]; 
		} else {
			$dados_categoria = $comando->fetchAll(PDO::FETCH_NUM);
			foreach($dados_categoria as &$linha){
				$linha = $linha[0];
			}
			$dados['categorias'] = $dados_categoria;
		}
		
		$comando = $pdo->prepare("
			select reg.id_regiao as codigo
			,max(case when reg.id_territorio = 3 then cast(reg.id_regiao as text) else reg.nome end) as ordem 
			from fonte_dados.regiao reg 
			left join sistema.indicador_calculo calc
					on reg.id_regiao = calc.id_regiao
					and reg.id_territorio = calc.id_territorio
					and calc.data ".(is_null($parametros['data'])?"is null":"= :data ")."  
					and calc.id_indicador = :indicador
			where reg.id_territorio = :territorio
			group by reg.id_regiao
					,reg.nome
			order by ordem asc");
			

		$comando->bindParam(':indicador', $parametros['indicador']);
		if(!is_null($parametros['data']))
			$comando->bindParam(':data', $parametros['data']);
		$comando->bindParam(':territorio', $parametros['territorio']);
		
		if(!$comando->execute()){
			$erro = $comando->errorInfo();
			return $erro[2]; 
		} else {
			$dados_codigo = $comando->fetchAll(PDO::FETCH_NUM);
			foreach($dados_codigo as &$linha){
				$linha = $linha[0];
			}
			$dados['codigos'] = $dados_codigo;
		}
		
		$response = new WP_REST_Response( $dados );
		return $response;
		
	}
}
