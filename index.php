<?php 
// inicia as sessions
session_save_path('./');
session_start();

// pega o id da session
$chat_id = session_id();

//pega o nome
$username = (isset($_SESSION['nome'])) ? $_SESSION['nome'] : "user";

// Carrega o arquivo historico.xml
$filename = 'historico.xml';

//data e hora formatada
$dataAtual = date("d/m/Y H:i:s");

//verifica se XML existe
if (!file_exists($filename))
{
  // cria o XML
  $xml = new SimpleXMLElement('<historico><mensagem><id></id><usuario></usuario><texto></texto></mensagem></historico>');
  $xmlString = $xml->asXML();
  file_put_contents($filename, $xmlString);
}

// registra a entrada
$xml = simplexml_load_file($filename);
$novoRegistro = $xml->addChild('mensagem');
$novoRegistro->addChild('id', $chat_id);
$novoRegistro->addChild('usuario', $username);
$novoRegistro->addChild('texto', "<font color=\"grey\">" . $dataAtual . ":<br></font>" . $username . " entrou.");
$xml->asXML($filename);

?>
<!DOCTYPE html>
<html>
  <head>
  	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
    <script type="text/javascript">
      // pega o nome do usuario
      username = "<?php echo $username; ?>";

      // função para manter a DIV no scroll 0
      function fimConversa()
      {
        $('#historico').scrollTop($('#historico')[0].scrollHeight);
        return "";
      }

      // função para atualizar a div com o histórico
      function atualizaMensagem()
      {
        $.ajax(
        {
           url: 'ajax.php?x=pega',
           type: 'POST',
           data: {},
           success: function(response)
           {
            //foi
            $('#historico').html(response);
            fimConversa();

           }
        }
        );
      }

      // função que envia uma mensagem nova para o ajax
      function enviaMensagem()
      {
        mensagem = $('#mensagem').val();
        $.ajax(
        {
           url: 'ajax.php?x=envia',
           type: 'POST',
           data: {valor: mensagem},
           success: function(response)
           {
            //foi
            atualizaMensagem();
           }
        }
        );
        $('#mensagem').val("");
        // previne o comportamento padrão do Enter no textarea
        event.preventDefault();
      }

      // carregar junto com a pagina
      $(document).ready(function(){

        // chamada de troca de nome
        $('#altera').click(function(){
          username = prompt();
          $.ajax(
          {
             url: 'ajax.php?x=altera',
             type: 'POST',
             data: {valor: username},
             success: function(response)
             {
              //foi
              $('#nome').html(response);
             }
          }
          );
        });

        // pega historico
        atualizaMensagem();

        // alinha o comportamento do textarea (enter envia, enter+shift não)
        $('#mensagem').on('keydown', function(event) 
        {
          if (event.key === 'Enter' && !event.shiftKey)
          {
            enviaMensagem();
          }
        });

        // ação do botão enviar
        $('#enviar').click(function() 
        {
          enviaMensagem();
        });

        // intervalo de atualização do histórico
        setInterval(atualizaMensagem, 5000);
      });
    </script>
    <!-- W3.css -->
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <!-- otimização para celular -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  </head>
  <body style="border: 0px;">
    <div class="w3-container w3-light-green">
      <h1>Chat - <div id="nome" style="display: inline;"><?php echo $username; ?></div> <a id="altera" href="#">[alterar]</a></h1>
    </div>
    <div class="w3-container w3-pale-green" id="historico" style="overflow-y: scroll;max-height: 70%;display: block;position: fixed; max-width: 100%;min-width: 100%;">
      
    </div>
    <div class="w3-container" style="bottom: 10px; display: flex; position: fixed; width: 100%;">
      <textarea id="mensagem" class="w3-light-green" style="width: 80%;"></textarea>
      <input type="button" id="enviar" class="w3-button w3-light-green" style="width: 20%;" value="Enviar!">
    </div>
    
  </body>
</html>