<?php
// receber as variaveis

	// x = variavel com a ação
$x = (isset($_GET['x'])) ? $_GET['x'] : "0";

	// texto = texto da mensagem
$texto = (isset($_POST['valor'])) ? $_POST['valor'] : "0";

// inicia as sessions
session_save_path('./');
session_start();

// pega o nome
$username = (isset($_SESSION['nome'])) ? $_SESSION['nome'] : "user";

// pega o id da session
$chat_id = session_id();

// endereço do xml
$filename = 'historico.xml';

// se a ação for para alterar o nome do usuario
if($x == "altera")
{
	// verifica se não tem um nome definido
	if(!isset($_SESSION['nome']))
	{
		// se não, defina como "user"
		$_SESSION['nome'] = "user";
	}

	// atribui o valor recebido ao nome do usuario dentro da session
	$_SESSION['nome'] = $_POST['valor'];
	$username = $_SESSION['nome'];

	// devolve valor do nome
	echo $username;
}

// se a ação for para enviar uma mensagem e registrar no XML
if($x == "envia")
{
	// data formatada
	$dataAtual = date("d/m/Y H:i:s");

	// carrega xml
	$xml = simplexml_load_file($filename);

	// registra no xml
	$novoRegistro = $xml->addChild('mensagem');
	$novoRegistro->addChild('id', $chat_id);
	$novoRegistro->addChild('usuario', $username);
	$novoRegistro->addChild('texto', "<font color=\"grey\">" . $dataAtual . ":<br></font>" . $texto);

	// salva xml
	$xml->asXML($filename);

	// muda valor de X (ação) para atualizar a DIV de histórico
	$x = "pega";
}

// se a ação for para pegar as mensagens do XML 
if ($x == "pega") 
{
	// carrega xml
	$xml = simplexml_load_file($filename);

	// pega o numero de mensagens no xml
	$numRegistros = count($xml->mensagem);

	// Se houver mais de 100 registros, remove o mais antigo
	if ($numRegistros > 100) 
	{
		unset($xml->mensagem[0]);
		$xml->asXML($filename);
	}
	// Cria uma div de resposta para exibir as mensagens
	$resposta = '<div class="mensagens">';

	// Percorre os nós de mensagem
	foreach ($xml->mensagem as $mensagem) 
	{
		// Obtém os valores dos elementos
		$id = $mensagem->id;
		$usuario = $mensagem->usuario;
		$texto = $mensagem->texto;

		// Cria uma div para cada mensagem
		if($id > "0")
		{
			// se a mensagem em questão for do seu usuario
			if($id == $chat_id)
			{
				$resposta .= '<div class="mensagem w3-pale-yellow">';
			}

			// se não for do seu usuario
			else
			{
				$resposta .= '<div class="mensagem w3-pale-blue">';
			}

			// termina a montagem da div de resposta
			$resposta .= '<b>Usuário: ' . $usuario . ':</b> <br>';
			$resposta .= nl2br($texto) . '<br>';
			$resposta .= '</div><br>';
		}
		
	}
	// Fecha a div das mensagens
	$resposta .= '</div>';

	// devolve a div de resposta
	echo $resposta;
}