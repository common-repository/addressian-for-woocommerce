<?php
/*
Addressian for Woocommerce is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

Addressian for Woocommerce is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Addressian for Woocommerce. If not, see {License URI}.
*/

class Addressian_Request {

   private $endpoint = "";
   private $api_key = "";
   function __construct($api_key, $endpoint) {
      $this->api_key = $api_key;
      $this->endpoint = $endpoint;
   }
   function addressian_get_response($param, ...$resources) {

      $url = $this->endpoint . "/" . implode("/", $resources) . "?" . http_build_query($param);
      $response = wp_remote_get($url, array('headers' => array('x-api-key' => $this->api_key, 'Origin' => "woo:1.4:" . get_site_url())));
      $body = wp_remote_retrieve_body($response);

      return $body;

   }


}