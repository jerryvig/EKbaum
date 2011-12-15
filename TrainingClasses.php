<?php
  require_once('./simple_html_dom.php'); 

  loadUrls();

  function loadUrls() {
    $dom = new simple_html_dom();    

    $dom->load_file("TCLinkList3.html");
    $aList = $dom->find('a');

    foreach ( $aList as $myA ) {
      $urlString = "http://www.training-classes.com" . trim($myA->href);
      $company_name = trim($myA->title);

      getPage( $urlString, $company_name );  
    }
  }

  function getPage( $urlString, $companyName ) { 
    // echo $urlString . "\n";

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

    echo '"' . $companyName . '","' . $urlString . '","' . $contact_name . '","' . $addy . '","' . $website_url . '"' . "\n";
  }
?>