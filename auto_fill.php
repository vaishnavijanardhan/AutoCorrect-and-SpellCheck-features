<?php

		function objectToArray ($object) {
			if(!is_object($object) && !is_array($object))
				return $object;

			return array_map('objectToArray', (array) $object);
		}
		require './Apache/Solr/Service.php';
		include 'SpellCorrector.php';

		//require './SpellCorrector.php';
		header('Content-­‐Type:text/html; charset=utf-­‐8');
		$start = 0;
		$rows = 10;	
		$results	= " ";
		$suggestionResults = " ";
		$words = " ";
		$correctionResults = " ";
		$correction = " ";
		$server = 'localhost';
		$port = 8983;
		$core = '/solr/myexample/';
		if(isset($_GET['q'])) {
			$query = $_GET['q'];
		} else {
			$query = false;
		}
		
		if($query) 
		{
			$query = strtolower($query);
			require_once('./Apache/Solr/Service.php');	
	  		$solr =	new Apache_Solr_Service($server, $port,$core);	
			
			if(get_magic_quotes_gpc()==1)	
				$query = stripslahes($query);
			if(get_magic_quotes_gpc()==1)	
				$rank = stripslahes($rank);
			try	
			{
				$words = explode(" ",$query);
				$suggestionResults = array();
				foreach($words as $word) {
					//echo trim(SpellCorrector::correct($word));
					//$suggestionResults[$word] = $solr->suggest(trim(SpellCorrector::correct($word)));
					$suggestionResults[$word] = $solr->suggest(trim($word));
				}					
			}
			catch(Exception $e)
			{		
	  			$suggestionResults = " ";
				echo "";
			}
			$w = 0;
			if(count($suggestionResults) == count($words))
			{
				$suggestions = array();
				$w = 0;
				$s = 0;
				foreach($words as $word)
				{
					$suggestions['suggests'][$w] = array();
					$s = 0;
					foreach($suggestionResults[$word]->suggest->mySuggester->$word->suggestions as $suggestionResult)
					{	
						foreach($suggestionResult as $field => $value)
						{
							if(strcasecmp($field,"term")==0) 
							{
								$suggestions['suggests'][$w][$s] = $value;
							}
						}
						$s += 1;
					}
					$w += 1;
				}
			}
			$flag = false;
			$correctedSentence = "";
			foreach($words as $word) {
				$ret = SpellCorrector::correct($word);
				
				//$ret = $word;
				if(!strcmp($ret, $word) == 0) {
					$flag = true;
				}
				$correctedSentence .= $ret . " ";
			}
			if($flag == true) {
				$suggestions['spellCheck'] = trim($correctedSentence);
			}
			else {
				$suggestions['spellCheck'] = null;
			}
			echo json_encode($suggestions);
		}
		
?>