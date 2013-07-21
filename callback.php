<?php
// Foursquare login step 2, echo back $code from QUERY_STRING
  require_once('secrets.php'); // defines CLIENT_ID & CLIENT_SECRET

// get $code from QUERY_STRING
  parse_str($_SERVER['QUERY_STRING'], $query);
  $code = $query['code'];

// build url
  $url = 'https://foursquare.com/oauth2/access_token';
  $url .= '?client_id='.CLIENT_ID;
  $url .= '&client_secret='.CLIENT_SECRET;
  $url .= '&grant_type=authorization_code';
  $url .= '&redirect_uri=YOUR CALLBACK URL'; //change to your 4sq callback
  $url .= '&code='.$code;

// call to https://foursquare.com/oauth2/access_token with $code
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_URL, $url);
  $result = curl_exec($ch);
  curl_close($ch);

// $result value is json {access_token: ACCESS_TOKEN}
  $values = json_decode($result, true);
  $token = $values['access_token'];

// set access_token cookie
  $expire = time()+10368000; 
  setcookie("foursquare_token", $token, $expire, '/');

// crosswindow scripting to pass back $token
  echo('<script type="text/javascript">');
  echo('opener.set4sqKey("'.$token.'");');
  echo('self.close();'); // close self
  echo('</script>');

  ?>