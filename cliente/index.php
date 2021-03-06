<?php include 'api-client/api-connector.php'?>
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<link rel="stylesheet" href="bootstrap-5/css/bootstrap.css">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark justify-content-between">
  <a class="navbar-brand" href="index.php">VPN</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <a class="navbar-brand" href="status.php">STATUS</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  </nav>
<form method='post' action="index.php">
  <div class="form-group">
    <label for="email">Usuario:</label>
    <input type="text" class="form-control" placeholder="usuario" value="<?php if ($_SERVER['REQUEST_METHOD'] == 'POST') {echo $_POST['user'];}?>" name="user">
  </div>
  <div class="form-group">
    <label for="pwd">Contraseña:</label>
    <input type="password" class="form-control" name="passwd">
  </div>
<div>  
<br><button type="submit" class="btn btn-primary">Enviar</button>
</div>
</form>

<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
$data[] = ([
  'user' => $_POST['user'],
  'password' => $_POST['passwd']
]);
$result=ApiRequest($data,"http://localhost:5000","login","POST");

$decoded_result=json_decode($result,true);
$data=$decoded_result['data'];
$status=$decoded_result['status'];

if ($status[0] == 10)  {
foreach ($data['route_networks'] as $ip) {
  $allowed_ips.=$ip.",";
}
$file= <<<EOF
[Interface]
PrivateKey = {$data['private_key']}
ListenPort = 10000
Address = {$data['ip']}

[Peer]
PublicKey = {$data['server_public_key']}
AllowedIPs ={$allowed_ips}{$data['server_ip']}
Endpoint = {$data['host']}:{$data['port']}
PersistentKeepalive = 10
EOF;

$out=sha1(rand()).".conf";
file_put_contents($out,$file);
echo "<div><a href=$out class='btn btn-success' download>Descargar Configuración</a></div>";
}
else {echo "<h1>ERROR";}
echo "<hr><div class='collapse' id='collapseData'><div class='card card-body'>";
echo '<pre>' . htmlentities(print_r($result, true), ENT_QUOTES, 'UTF-8') . '</pre>';
echo "</div></div>";

}
?>

<button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapseData" aria-expanded="false" aria-controls="collapseData">
  Datos API
  </button>