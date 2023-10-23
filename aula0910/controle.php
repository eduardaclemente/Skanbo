<?php
/*
Antes de acessar, execute os comandos do arquivo bsaude.sql
*/
$SERVIDOR = "localhost";
$USUARIO = "root";
$SENHA = "";
$BASE = "skanbo";

if (isset($_GET['op'])) {
	$op = $_GET['op'];
} else {
	$op = "Non";
}

?>
<!DOCTYPE html>
<html>
 <head><title>Novo agendamento</title></head>
 <body>
<?php
switch ($op) {
	case 'nova':
?>
	 <form action="controle.php" method="GET">
		 <input type="hidden" name="op" value="new">
   <p><label for="cliente">Nome do Cliente:</label>
    <select id="cliente" name="cliente">
     <option value="0">Novo cliente</option>
<?php
  $con = mysqli_connect($SERVIDOR, $USUARIO, $SENHA, $BASE); 
  $dados = mysqli_query($con, "select * from vClientesPorNome");
  mysqli_close($con);
  while ($linha = mysqli_fetch_assoc($dados)) {
  	$id = $linha["cpf_cliente"];
  	$nome = $linha["nome"];
  	echo "     <option value=\"$id\">$nome</option>\n";
  }
?>
    </select></p>
   <p><label for="prestador">Nome do Prestador:</label>
    <select id="prestador" name="prestador">
<?php
  $con = mysqli_connect($SERVIDOR, $USUARIO, $SENHA, $BASE); 
  $dados = mysqli_query($con, "select * from vPrestadorPorNome");
  mysqli_close($con);
  while($linha = mysqli_fetch_assoc($dados)){
  	$crm = $linha["cnpj"];
  	$nome = $linha["nomeFantasia"];
  	$espec = $linha["especialidade"];
  	echo "     <option value=\"$crm\">$nome ($espec)</option>\n";
  }
?>
    </select></p>
   <p><label for="data">Data da Agendamento:</label>
    <input type="date" id="data" name="data"></p>
   <p><label for="hora">Hora da Agendamento:</label>
    <input type="time" id="hora" name="hora"></p>
   <p><label for="categoria">Categoria: </label>
    <select id="categoria" name="categoria">
<?php
  $con = mysqli_connect($SERVIDOR, $USUARIO, $SENHA, $BASE); 
  $dados = mysqli_query($con, "select * from vCategorias");
  mysqli_close($con);
  while($linha = mysqli_fetch_assoc($dados)){
  	$categoria = $linha["categoria"];
  	echo "     <option value=\"$categoria\">$categoria</option>\n";
  }
?>
    </select></p> 
   <p><input type="submit" value="Agendar Serviço"></p>
  </form>
<?php	 
		break;
	case 'new':
	 $cpf_cliente = $_GET["cliente"];
	 $cnpj = $_GET["prestador"];
  $data = $_GET["data"];
  $hora = $_GET["hora"];
	 $datahora = $data . ' ' . $hora;
	 $categoria = $_GET["categoria"];
   if ($cpf_cliente > 0) {
    $con = mysqli_connect($SERVIDOR, $USUARIO, $SENHA, $BASE); 
    mysqli_query($con, "call spIncluiAgendamento('$datahora', '$cpf_cliente', '$cnpj', '$categoria')");
    mysqli_close($con);
    header("Location: index.php");
   } else {
?>
  <form action="controle.php" method="GET">
   <input type="hidden" name="op" value="newP">
   <input type="hidden" name="prestador" value="<?php echo $cnpj ?>">
   <input type="hidden" name="data" value="<?php echo $data ?>">
   <input type="hidden" name="hora" value="<?php echo $hora ?>">
   <input type="hidden" name="categoria" value="<?php echo $categoria ?>">
   <p><label for="nome">Nome do Cliente:</label>
    <input type="text" id="cli" name="cli"></p>
   <p><input type="submit" value="Incluir e agendar"></p>
  </form>
<?php
   }
	 break;
 case 'newP':
   $prestador =  $_GET["prestador"];
   $datahora = $_GET["data"] . ' ' . $_GET["hora"];
   $categoria = $_GET["categoria"];
   $cliente = $_GET["cli"];
   $con = mysqli_connect($SERVIDOR, $USUARIO, $SENHA, $BASE); 
   $dados = mysqli_query($con, "call spIncluiCliente('$cliente', @id)");
   $linha = mysqli_fetch_array($dados);
   mysqli_close($con);
   $cli = $linha[0];
   $con = mysqli_connect($SERVIDOR, $USUARIO, $SENHA, $BASE);
   mysqli_query($con, "call spIncluiAgendamento('$datahora', $cli, '$prestador', '$categoria')");
   mysqli_close($con);
   header("Location: index.php");
  break;
 case 'newM':
?>
   <form>
    <input type="hidden" name="op" value="nPres">
    <p><label for="cnpj">CNPJ do Prestador:</label>
     <input type="text" id="crm" name="crm"></p>
    <p><label for="nomeFantasia">Nome do Prestador:</label>
     <input type="text" id="nomeFantasia" name="nomeFantasia"></p>
    <p><label for="espec">Especialidade:</label>
     <input type="text" id="espec" name="espec"></p>
    <p><input type="submit" value="Incluir"></p>
   </form> 
<?php
  break;
 case 'nPres':
   $cnpj =  $_GET["cnpj"];
   $nomeFantasia =  $_GET["nomeFantasia"];
   $espec = $_GET["espec"];
   $con = mysqli_connect($SERVIDOR, $USUARIO, $SENHA, $BASE);
   mysqli_query($con, "call spIncluiPrestador('$cnpj', '$nomefantasia', '$espec')");
   mysqli_close($con);
   header("Location: index.php");
  break; 
 case 'canc':
   $id = $_GET["id"];
   $con = mysqli_connect($SERVIDOR, $USUARIO, $SENHA, $BASE);
   mysqli_query($con, "call spCancelaAgendamento($id)");
   mysqli_close($con);
   header("Location: index.php");
  break; 
 case 'alt':
   $id = $_GET["id"];
   $con = mysqli_connect($SERVIDOR, $USUARIO, $SENHA, $BASE);
   $dados = mysqli_query($con, "call spConsultaPorId($id)");
   $linha = mysqli_fetch_array($dados);
   $cliente = $linha["cliente"];
   $prestador = $linha["prestador"];
?>
  <form action="controle.php" method="GET">
   <input type="hidden" name="op" value="altCons">
   <input type="hidden" name="id" value="<?php echo $id ?>">
   <p><label for="cliente">Nome do Cliente:</label>
    <input type="text" name="cliente" id="cliente" value="<?php echo $cliente ?>" disabled></p>
   <p><label for="prestador">Nome do Prestador:</label>
    <input type="text" name="prestador" id="prestador" value="<?php echo $medico ?>" disabled></p>
   <p><label for="data">Data da Agendamento:</label>
    <input type="date" id="data" name="data"></p>
   <p><label for="hora">Hora da Agendamento:</label>
    <input type="time" id="hora" name="hora"></p>
   <p><input type="submit" value="Alterar Agendamento"></p>
  </form>
<?php
  break; 
 case 'altCons':
   $id = $_GET["id"];
   $datahora = $_GET["data"] . ' ' . $_GET["hora"];
   $con = mysqli_connect($SERVIDOR, $USUARIO, $SENHA, $BASE);
   mysqli_query($con, "call spAlteraAgendamento ($id, '$datahora')");
   mysqli_close($con);
   header("Location: index.php");
  break; 
	default:
		die("Operação desconhecida");
		break;
}
?>
 </body>
</html>