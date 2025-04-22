<?php 

require_once('ApiConfig.php');

$pgto = $_POST['pgto'];

$query = $pdo->query("SELECT * from forma_pgtos where codigo = '$pgto'  ");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$nome_pgto = @$res[0]['nome'];

if($nome_pgto != 'Pix' and $nome_pgto != 'pix'){
    exit();
}


if($access_token == ""){
	echo 'Configure o Token da Api Pix no arquivo ApiConfig';
	exit();
}

$valor = $_POST['valor'];

if($valor <= 0){   
    exit();
}

$curl = curl_init();

    $dados["transaction_amount"]                    = (float)$valor;
    $dados["description"]                           = "Venda";
    $dados["external_reference"]                    = "2";
    $dados["payment_method_id"]                     = "pix";
    $dados["notification_url"]                      = "https://google.com";
    $dados["payer"]["email"]                        = "Email";
    $dados["payer"]["first_name"]                   = "Nome";
    $dados["payer"]["last_name"]                    = "SobreNome";
    
    $dados["payer"]["identification"]["type"]       = "CPF";
    $dados["payer"]["identification"]["number"]     = "numeroCPF";
    
    $dados["payer"]["address"]["zip_code"]          = "cep";
    $dados["payer"]["address"]["street_name"]       = "Logradouro";
    $dados["payer"]["address"]["street_number"]     = "Numero";
    $dados["payer"]["address"]["neighborhood"]      = "Bairro";
    $dados["payer"]["address"]["city"]              = "Cidade";
    $dados["payer"]["address"]["federal_unit"]      = "Estado";

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.mercadopago.com/v1/payments',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($dados),
    CURLOPT_HTTPHEADER => array(
        'accept: application/json',
        'content-type: application/json',
        'X-Idempotency-Key: ' . date('Y-m-d-H:i:s-') . rand(0, 1500),
        'Authorization: Bearer '.$access_token
    ),
    ));
    $response = curl_exec($curl);
    $resultado = json_decode($response);

    $id = $dados["external_reference"];
   //var_dump($response);
curl_close($curl);
$codigo_pix = isset($resultado->point_of_interaction->transaction_data->qr_code);

$id_ref = isset($resultado->id);

echo "
<img  style='display:block;' width='250px' id='base64image'
       src='data:image/jpeg;base64, ".$resultado->point_of_interaction->transaction_data->qr_code_base64."'/>";
echo '<small><small><span id="span_ctrl">Pressione Ctrl para consultar pagamento<span></small></small>';

echo '<input type="hidden" id="id_ref" value="'.$id_ref.'">';


?>   


       
