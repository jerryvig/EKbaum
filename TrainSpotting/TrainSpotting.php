<?php
  require_once('./simple_html_dom.php'); 
 

  //loadUrls();
  getUrl();

  function loadUrls() {
   $fh = fopen('TrainSpottingList.csv','r');
   $fh2 = fopen('TSUrls.csv', 'w');

   while (($buffer = fgets($fh, 4096)) !== false) {
    $dom = new simple_html_dom();
    $line = trim($buffer);
    $cols = explode( ',', $line );
    $dom->load_file( $cols[1] );
    $formList = $dom->find('form');
    foreach ( $formList as &$myForm ) {
      $tableList = $myForm->find('table');
       
      $company_name = '';

      foreach( $tableList as &$myTable ) {
        $strongList = $myTable->find('strong');
        $company_name = trim($strongList[0]->plaintext);
        $aList = $myTable->find('a');
        $pageUrl = 'http://www.trainingspotting.com' . trim($aList[0]->href);
        fwrite( $fh2, $company_name . ',' . $pageUrl . "\n" );
      }
    }
   }
 
   fclose ($fh);
   fclose( $fh2 );
  }

  function getUrl() {
    $fh = fopen('TSUrls2.csv','r');
   
    while (($buffer = fgets($fh, 4096)) !== false) {
      $dom = new simple_html_dom();

      

      $line = trim($buffer);
      $cols = explode(',', $line);
      try {
        $ch = curl_init( $cols[1] );
        $fp = fopen("page.html","w");
        curl_setopt($ch, CURLOPT_FILE, $fp);
	curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_exec($ch);
	curl_close($ch);
	fclose($fp);

        $dom->load_file( 'page.html' );
      } catch ( Exception $e ) { 
        continue;
      }

      $company_name = trim($cols[0]);
      $address = '';
      $city = '';
      $state = '';
      $zip = '';
      $country = '';
      $phone = '';
      $description = '';
      $contact_email = '';
      $contact_name = '';
      $web = '';

      if ( !isset($dom) ) continue;
      $tdList = $dom->find('td');
     
      for ( $i=0; $i<count($tdList); $i++ ) {
        $tdContent = str_replace('"','',trim($tdList[$i]->plaintext));
        
	if ( $tdContent == 'Country:' ) {
	  $country = trim($tdList[$i+1]->plaintext);
        }
        if ( $tdContent == 'State:' ) {
	  $state = trim($tdList[$i+1]->plaintext);
          // echo $state . "\n";
        }
        if ( $tdContent == 'ZIP:' ) {
	  $zip = trim($tdList[$i+1]->plaintext);
          //echo $zip . "\n";
        }
        if ( $tdContent == 'City:' ) {
	  $city = trim($tdList[$i+1]->plaintext);
          //echo $city . "\n";
        }
        if ( $tdContent == 'Address:' ) {
	  $address = trim($tdList[$i+1]->plaintext);
          //echo $address . "\n";
        }
        if ( $tdContent == "Address (con't):" ) {
	  $address .= ' ' . trim($tdList[$i+1]->plaintext);
          //echo $address . "\n";
        }
        if ( $tdContent == 'Description:' ) {
	  $description = trim($tdList[$i+1]->plaintext);
          //echo $description  . "\n";
        }
        if ( $tdContent == 'Contact person:' ) {
	  $contact_name = trim($tdList[$i+1]->plaintext);
          //echo $contact_person  . "\n";
        }
        if ( $tdContent == 'Phone:' ) {
	  $phone = trim($tdList[$i+1]->plaintext);
          // echo $phone . "\n";
        }
        if ( $tdContent == 'E-mail:' ) {
          if ( isset($tdList[$i+1]) ) {
	   $iList = $tdList[$i+1]->find('input[name=to_email]');
           $contact_email = trim($iList[0]->value);
          }
        }
        if ( $tdContent == 'Web:' ) {
          if ( isset($tdList[$i+1]) ) {
	    $aList = $tdList[$i+1]->find('a');
            $web = trim($aList[0]->href);
	  }
        }
        if ( $tdContent == 'Size:' ) {
	   $size = trim($tdList[$i+1]->plaintext);
           // echo $size . "\n";
        }
      }

      echo '"' . trim($company_name) . '","' . trim($address) . '","' . trim($city) . '","' . trim($state) . '","' . trim($zip) . '","' . trim($country) . '","' . trim($description) . '","' . trim($contact_name) . '","' . trim($phone) . '","' . trim($contact_email) . '","' . trim($size) . '","' . trim($web) . '"' . "\n";
      $dom->clear();

    }

    fclose( $fh );
  }

  function fatal_error_handler($buffer){}
?>