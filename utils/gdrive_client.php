<?php

require_once './vendor/autoload.php';

function getClient() {
  $client = new Google_Client();
  $scopes = array(
    'https://www.googleapis.com/auth/drive.file',
    'https://www.googleapis.com/auth/userinfo.email'
  );

  $client->setAuthConfig('./credentials.json');
  $client->setScopes($scopes);

  return $client;
}
