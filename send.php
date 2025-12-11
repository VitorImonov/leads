<?php

// ================================
// CONFIGURAÇÕES DE LOG
// ================================
$LOG_ATIVO = true;        // Exibe erros na tela (desative em produção)
$LOG_ARQUIVO = true;      // Salva logs em arquivo

$logFile = __DIR__ . "/logs/leads.log";

function registrarLog($mensagem) {
    global $LOG_ARQUIVO, $logFile;

    if ($LOG_ARQUIVO) {
        $timestamp = date("Y-m-d H:i:s");
        file_put_contents($logFile, "[$timestamp] $mensagem\n", FILE_APPEND);
    }
}

function erroFatal($mensagem) {
    global $LOG_ATIVO;

    registrarLog("ERRO FATAL: " . $mensagem);

    if ($LOG_ATIVO) {
        die("<pre><strong>ERRO:</strong> $mensagem</pre>");
    } else {
        die("Ocorreu um erro. Tente novamente mais tarde.");
    }
}

// ================================
// RECEBENDO DADOS DO FORMULÁRIO
// ================================
$name         = $_POST['name'] ?? null;
$email        = $_POST['email'] ?? null;
$phone        = $_POST['phone'] ?? null;
$observation  = $_POST['observation'] ?? null;
$propertyId   = $_POST['property_id'] ?? null;
$user         = $_POST['user'] ?? null;

// Log de entrada
registrarLog("Recebido formulário: " . json_encode($_POST));


// ================================
// CONEXÃO COM BANCO
// ================================
$conecta = mysqli_connect("mysql18.novon.com.br", "novon16", "nov372188", "novon14");

if (!$conecta) {
    erroFatal("Erro conexão MySQL: " . mysqli_connect_error());
}


// ================================
// CONSULTA TOKEN
// ================================
$consulta_token = "
    SELECT token 
    FROM tokens_api 
    WHERE token_codigo = (
        SELECT CadCodigo 
        FROM tcadastro 
        WHERE CadUsuario = ?
    )
";

$stmt = mysqli_prepare($conecta, $consulta_token);
if (!$stmt) {
    erroFatal("Erro prepare: " . mysqli_error($conecta));
}

mysqli_stmt_bind_param($stmt, "s", $user);

if (!mysqli_stmt_execute($stmt)) {
    erroFatal("Erro execute: " . mysqli_stmt_error($stmt));
}

mysqli_stmt_bind_result($stmt, $token);
$fetch_ok = mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

if (!$fetch_ok || empty($token)) {
    erroFatal("Token não encontrado para o usuário: $user");
}

registrarLog("Token localizado com sucesso para usuário $user");


// ================================
// MONTANDO PAYLOAD
// ================================
$data = array(
    "name" => $name,
    "classification" => "high",
    "interestedIn" => "buy",
    "source" => "Formulário do Site",

    "phones" => [
        [
            "phoneNumber" => $phone,
            "phoneType" => "comercial"
        ]
    ],

    "emails" => [$email],
    "observation" => $observation,
    "observationLead" => $observation,

    "user" => [
        "id" => 1,
        "username" => "envio",
        "email" => "envio@email.com",
        "name" => "envio"
    ],

    "contacts" => [
        [
            "propertyId" => $propertyId,
            "observation" => $observation
        ]
    ]
);

$json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

// Log da requisição
registrarLog("Payload enviado: " . $json);


// ================================
// ENVIANDO PARA API SI9
// ================================
$url = "https://apiv2.si9sistemas.com.br/api-prd/lead";

$ch = curl_init($url);

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $json,
    CURLOPT_HTTPHEADER => [
        "Content-Type: application/json",
        "X-Api-Key: Bearer " . $token
    ]
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);


// LOG bruto da resposta
registrarLog("HTTP CODE: $httpCode");
registrarLog("RESPOSTA API: " . $response);


// ================================
// TRATAMENTO DE ERROS DA API
// ================================
if (curl_errno($ch)) {
    erroFatal("cURL Error: " . curl_error($ch));
}

curl_close($ch);

$responseData = json_decode($response, true);

if ($responseData === null) {
    erroFatal("Resposta inválida da API: " . $response);
}

if ($httpCode >= 400) {

    registrarLog("Erro API: " . json_encode($responseData));

    erroFatal(
        "Erro ao enviar lead. (HTTP $httpCode)<br>" .
        (isset($responseData['message']) ? $responseData['message'] : "Sem mensagem de erro")
    );
}


// ================================
// SUCESSO
// ================================
registrarLog("Lead enviado com sucesso!");

echo "<script>
        alert('Seu contato foi enviado com sucesso!');
        window.history.go(-1);
      </script>";

?>
