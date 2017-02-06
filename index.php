<?php
	
	require("keys.php")
	
	$plans = array();
	
	$baseURL = "https://api.planningcenteronline.com/services/v2/service_types/330478";
	
	/***** GET PLANS DATA FROM PLANNING CENTER AND ADD IT TO PLANS ARRAY ******/
			
	$url = $baseURL."/plans/?per_page=100";  // Initial page of 100 plans (default is 25 pages)
 
	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
 
	// send the username and password
	curl_setopt($ch, CURLOPT_USERPWD, "$ApplicationID:$Secret");
	 
	// if you allow redirections
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	// this lets cURL keep sending the username and password
	// after being redirected
	curl_setopt($ch, CURLOPT_UNRESTRICTED_AUTH, 1);
	 
	do {
		
		curl_setopt($ch, CURLOPT_URL, $url);
		 
		$output = curl_exec($ch);
		//echo $output;
		
		$result = json_decode($output);
		//if(isset($result->links->next)) echo "Go to next page."; else echo "Do not go further.";
		//echo $result->links->next;
		//var_dump($result);
		//echo $result->data[1]->attributes->series_title;
	
		foreach($result->data as $plan) {
	    	array_push($plans, $plan);
		}

		$url = $result->links->next;
		
		
	} while(isset($result->links->next)); // While there are still pages left to fetch
	
	
	/***** END GET PLANS DATA FROM PLANNING CENTER AND ADD IT TO PLANS ARRAY ******/
	
	$fourWeeksAgo = new DateTime('-4 week');
	//echo "Four Weeks Ago: ".$fourWeeksAgo->format('Y-m-d H:i:s')."<br />";
	$today = new DateTime();
	
	$x = 1;
	foreach(array_reverse($plans) as $plan) {
		$planDate = new DateTime($plan->attributes->sort_date);
		if( $fourWeeksAgo < $planDate && $today > $planDate) { // date was in the last 4 weeks
			//echo "Date: ".$planDate->format('Y-m-d H:i:s').": ";
    		//echo $x.": ".$plan->id.": ".$plan->attributes->series_title." - ".$plan->attributes->title." - ".$plan->attributes->dates."<br />";
    		echo "<strong>".$plan->attributes->dates."</strong> (".$plan->attributes->series_title." - ".$plan->attributes->title.")<br />";
    		
    		$url = $baseURL."/plans/".$plan->id."/items";
    		curl_setopt($ch, CURLOPT_URL, $url);
    				 
			$output = curl_exec($ch);
			//echo $output;
			$result = json_decode($output);
			foreach($result->data as $item) {
	    		if($item->attributes->item_type == "song") {
		    		echo $item->attributes->title."<br />";
	    		}
			}
			
			echo "<br />";
    		
    		$x++;
    	}
	}
	
	
	curl_close($ch);

?>