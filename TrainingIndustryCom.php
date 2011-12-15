<?php
  require_once('./simple_html_dom.php'); 
  require_once('./GoogleGeocode.php');

//loadUrls();
//getContactLinks();
// getContactInfo();
getAddys();

  function loadUrls() {
   $dom = new simple_html_dom();
   $fh = fopen( "URLList1.csv", "r" );
   $fh2 = fopen( "ProfileURLs.csv", "w" );

   while (($buffer = fgets($fh, 4096)) !== false) {
    $urlString = "http://www.trainingindustry.com/learning-communities/". trim($buffer);
     echo $urlString . "\n";
    $dom->load_file( $urlString );
    $divList = $dom->find('div[class=company-info]');
    foreach ( $divList as &$div ) {
      $aList = $div->find('a');
      fwrite( $fh2, trim($aList[0]->getAttribute("href")) . "\n" );
    }
   
   }
   
   fclose( $fh2 );
   fclose( $fh );
  }

  function getContactLinks() {
    $fh2 = fopen( "ProfileURLs.csv", "r" );
    $dom = new simple_html_dom();
    $fh = fopen( "contact_urls.csv", "w" );

    while (($buffer = fgets($fh2, 4096)) !== false) {
      $pUrl = trim($buffer);
      echo $pUrl . "\n";
      $dom->load_file( 'http://www.trainingindustry.com' . $pUrl );
      $pList = $dom->find('p[class=btn contact-sponsor]');
      if ( count($pList) > 0 ) {
        $aList = $pList[0]->find('a'); 
        $contact_url =  'http://www.trainingindustry.com' . trim($aList[0]->getAttribute('href')) . "\n"; 
	fwrite( $fh, $contact_url );
      }
    }
    
    fclose( $fh );
    fclose ( $fh2 );
  }

  function getContactInfo () {
    $fh = fopen( "ProfileURLs.csv", "r" );
    $dom = new simple_html_dom();
    $fh2 = fopen( "contactInfo.csv", "w" );

    while (($buffer = fgets($fh, 4096)) !== false) {
      $url1 = trim($buffer);
      echo $url1 . "\n";

      $cmd = '/usr/bin/wget -O file.html  -t 3 -U "Mozilla/5.0" "http://www.trainingindustry.com' .  trim($url1) . '"';
      echo $cmd . "\n";
      system( $cmd );

      $dom->load_file( './file.html' );
     
      $company_name = "";
      $address = "";
      $cName = "";
      $phone = "";
      $company_desc = "";
      $website_url = '';

      if ( !isset($dom) ) {
        continue;
      }      

      $divCList = $dom->find('div[class=company-heading]');
      if ( count($divCList) < 1 ) continue;
      $h2List = $divCList[0]->find('h2');
      if ( count($h2List) > 0 ) {
	$company_name = trim($h2List[0]->plaintext);
      }

      $descList = $dom->find('div[class=company-desc]');
      if ( count( $descList ) > 0 ) {
          $company_desc = trim($descList[0]->plaintext);
          //echo trim($descList[0]->plaintext);
      }

      
      $aList = $dom->find('a[class=url]');
      if ( count($aList) > 0 ) {
        $website_url = trim($aList[0]->href);
      }

      $divList = $dom->find('div[class=supplier-v-card]');
      foreach ( $divList as &$div ) {
	$contactDiv = $div->find('div[class=company-address]');
        $address =  trim($contactDiv[0]->plaintext);
        $contact_name_span = $div->find('span[class=fn]');
        $contactName =  trim($contact_name_span[0]->plaintext);
        $phoneLi = $div->find('li[class=tel]');
        echo $phoneLi[0]->plaintext . "\n";
        $pieces =  explode( ":", ($phoneLi[0]->plaintext) );
        if ( count($pieces) > 1 ) {
          $phone = trim(substr(trim($pieces[1]), 1 ));
        }
      }

      fwrite( $fh2, '"' . trim($company_name) . '","' . trim($contactName) . '","' . trim($address) . '","' . trim($phone) . '","' . trim($company_desc) . '","' . trim($website_url) . '"' . "\n" );
    }

    fclose ( $fh );
    fclose( $fh2 );
  }


  function getAddys() {
    $fh = fopen( "TIDirectAddys.csv", "r" );
    $fh2 = fopen( "TIAddressParts.csv", "w" ); 

    $apiKey = 'ABQIAAAAI1oIsi6Dv7MlmxUm1lRR_xTmarcuMJj81CoryY3grjEx5dFcyxQoeQTublWNe-B1iLVnHNrRuJD6_w';
    $geo = new GoogleGeocode( $apiKey );

    //$result = $geo->geocode( "124 Merrydale RD San Rafael, CA 94903" );
    //print_r( $result );
     
    while (($pieces = fgetcsv($fh, 1000, ",", '"')) !== FALSE) {
      $dirty_addy = str_replace( "\n", " ", $pieces[1] );
      $dirty_addy = utf8_encode($dirty_addy);
      $addy_array = $geo->geocode( $dirty_addy );
      $country = '';
      if ( isset($addy_array['Placemarks'][0]['Country']) ) {
        $country = $addy_array['Placemarks'][0]['Country'];
      }  
      $state = '';
      if ( isset($addy_array['Placemarks'][0]['AdministrativeArea']) ) {
        $state = $addy_array['Placemarks'][0]['AdministrativeArea']; 
      }
      $city = '';
      if ( isset($addy_array['Placemarks'][0]['Locality']) ) {
        $city = $addy_array['Placemarks'][0]['Locality']; 
      }
      $street_address = '';
      if ( isset($addy_array['Placemarks'][0]['Thoroughfare']) ) {
        $street_address = $addy_array['Placemarks'][0]['Thoroughfare'];
      }
      $zip = '';
      if ( isset($addy_array['Placemarks'][0]['PostalCode']) ) {
        $zip = $addy_array['Placemarks'][0]['PostalCode'];
      }
            
      echo  '"' . trim($pieces[0]) . '","' . $street_address . '","' . $city . '","' . $state . '","' . $zip . '","' . $country . '"' . "\n";
      fwrite( $fh2, '"' . trim($pieces[0]) . '","' . $street_address . '","' . $city . '","' . $state . '","' . $zip . '","' . $country . '"' . "\n" );
      usleep( 750000 );
    }
    fclose( $fh );
    fclose( $fh2 );
  }

?>
