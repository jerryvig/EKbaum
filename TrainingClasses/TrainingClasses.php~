<?php
  require_once('./simple_html_dom.php'); 
  require_once('./GoogleGeocode.php');

loadUrls();
//getWebsiteUrls();
//getAddys();

  function loadUrls() {
    $dom = new simple_html_dom();    

    $dom->load_file("TCLinkList.html");
    $aList = $dom->find('a');

    foreach ( $aList as $myA ) {
      $urlString = "http://www.training-classes.com" . trim($myA->href);
      $company_name = trim($myA->title);
      getPage( $urlString, $company_name );  
    }
  }

  function getPage( $urlString, $companyName ) { 
    $domII = new simple_html_dom();

    $domII->load_file( $urlString );    

    $divList = $domII->find('div');
    
    $contact_name = '';
    $address = '';
    $website_url = '';

    foreach ( $divList as &$div ) {
     
      if ( $div->id == 'trainers' ) {
        $contact_name = trim($div->plaintext);
      }
      if ( $div->class == 'tc_address' ) {
	$spList = $div->find('span');
        $addy = "";
        foreach ( $spList as &$mySpan ) { 
          $addy .= trim($mySpan->plaintext) . " ";
        }
        $address = trim($addy);
      }
      if ( $div->class == 'moreInfoSection' ) {
        $aList = $div->find('a');
        if ( count($aList) > 0 ) {
          $website_url = trim($aList[0]->href);
        }
      }
    }

    $keywords = '';
    $spanList = $domII->find('span');
    foreach ( $spanList as $mySpan ) {
     
      if ( trim($mySpan->class) == 'tc_summary_text' ) {
        $bList = $mySpan->find('b');
        foreach ( $bList as $myB ) {
          $keywords .= trim($myB->plaintext) . ' ';
        }
      }
    }
    $keywords = trim($keywords);

    echo '"' . $companyName . '","' . $urlString . '","' . $contact_name . '","' . $addy . '","' . $website_url . '","' . $keywords . '"' . "\n";
  }

  function getWebsiteUrls() {
    $fh = fopen( "TCDirectURLs.csv", "r" );
    $fh2 = fopen( "TCWebsiteURLs.csv", "w" );
    $dom = new simple_html_dom();            

    while (($pieces = fgetcsv($fh, 1000, ",", '"')) !== FALSE) {
      //$line = trim($buffer);
      //$pieces = explode(',',$line);
      $cmd = '/usr/bin/wget -O file.html  -t 3 -U "Mozilla/5.0" "' .  trim($pieces[1]) . '"';
      echo $cmd . "\n";
      system( $cmd );
     
      echo $pieces[0] . "\n";

       $dom->load_file('file.html');
       $fList = $dom->find('frame');
       if ( count($fList) < 1 ) continue;
       $cmd = '/usr/bin/wget -O file.html  -t 3 -U "Mozilla/5.0" "http://www.training-classes.com' .  trim($fList[0]->src) . '"';
       system( $cmd ); 

      $dom->clear();
      if ( file_exists('file.html') ) {
      
       $dom->load_file('file.html');
       $divList = $dom->find('a[href=#]');
       $onclick = trim($divList[0]->onclick);
       $piecesII = explode( '=', $onclick );
       $quoted = explode("'",$piecesII[1]);
       fwrite( $fh2,  $pieces[0] . ',' . trim($quoted[1]) . "\n" );
      }
      usleep( 750000 );
    }

    fclose( $fh );
    fclose( $fh2 );
  }

  function getAddys() {
    $fh = fopen( "TCDirectAddys.csv", "r" );
    $fh2 = fopen( "TCAddressParts.csv", "w" ); 

    $apiKey = 'ABQIAAAAI1oIsi6Dv7MlmxUm1lRR_xTmarcuMJj81CoryY3grjEx5dFcyxQoeQTublWNe-B1iLVnHNrRuJD6_w';
    $geo = new GoogleGeocode( $apiKey );

    //$result = $geo->geocode( "124 Merrydale RD San Rafael, CA 94903" );
    //print_r( $result );
    
    while (($pieces = fgetcsv($fh, 1000, ",", '"')) !== FALSE) {
      $addy_array = $geo->geocode( trim($pieces[1]) );
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