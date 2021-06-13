<?php include 'api-client/api-connector.php'?>
<link rel="stylesheet" href="bootstrap-5/css/bootstrap.css">

<form method='post' action="index.php">
  <div class="form-group">
    <label for="email">Usuario:</label>
    <input type="text" class="form-control" name="user">
  </div>
  <div class="form-group">
    <label for="pwd">Contraseña:</label>
    <input type="password" class="form-control" name="passwd">
  </div>

  <button type="submit" class="btn btn-default">Submit</button>
</form>

<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
$data[] = ([
  'user' => $_POST['user'],
  'password' => $_POST['passwd']
]);
echo "<div>";
$result=ApiRequest($data,"http://localhost:5000","login","POST");
echo '<pre>' . htmlentities(print_r($result, true), ENT_QUOTES, 'UTF-8') . '</pre>';
}
echo "</div>";
$decoded_result=json_decode($result,true);
$data=$decoded_result['data'];
echo "<div>";
echo '<pre>' . htmlentities(print_r($data, true), ENT_QUOTES, 'UTF-8') . '</pre>';
echo "</div>";
foreach ($data['route_networks'] as $ip) {
  $allowed_ips.=$ip.",";
}

echo <<<EOF
<div>
[Interface]
PrivateKey = {$data['private_key']}
ListenPort = 10000
Address = {$data['ip']}

[Peer]
PublicKey = {$data['server_public_key']}
AllowedIPs ={$allowed_ips}{$data['server_ip']}
Endpoint = {$data['host']}:{$data['port']}
PersistentKeepalive = 10
</div>
EOF;
?>