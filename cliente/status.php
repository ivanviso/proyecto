<?php include 'api-client/api-connector.php'?>
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

<table class="table">
  <thead>
    <tr>
      <th scope="col">USUARIO</th>
      <th scope="col">IP</th>
      <th scope="col">CLAVE PUBLICA</th>
    </tr>
  </thead>
<tbody>
<?php
$result=json_decode(ApiRequest($stub,"http://localhost:5000","status","GET"),true);
$peers=$result['peers'];

foreach ($peers as $row => $td) {
    if (!empty($td['usuario']) || !empty($td['usuario'] )) {
    echo <<<EOF
    <tr>
        <td>{$td['usuario'][0]}</td>
        <td>{$td['allowed ips'][0]}</td>
        <td>{$row}</td>
    </tr>
    EOF;
}
}
  
?>


</tbody>
</table>

<?php
echo "<hr><div>";
echo '<pre>' . htmlentities(print_r($peers, true), ENT_QUOTES, 'UTF-8') . '</pre>';
echo "</div>";
?>