<?php 
	//Make request, here is requesting current users checkins with default parameters
	$json_file = file_get_contents('https://api.foursquare.com/v2/users/self/checkins?oauth_token=YOUR TOKEN');
	$history_array = get_object_vars(json_decode($json_file));
	$response_array = get_object_vars($history_array['response']);
	$checkin_array = get_object_vars($response_array['checkins']);
	$checkin_array = $checkin_array['items'];
	$count_checkins = count($checkin_array);
	$checkins_array = [];
	$count_key = [];
	$multiple_checkins = [];

	for ($i=0;$i<$count_checkins;$i++) {
			
			$checkin_info = get_object_vars($checkin_array[$i]);
			$venue_info = get_object_vars($checkin_info['venue']);
			$name = $venue_info['name'];
			
			if (!in_array($name, $multiple_checkins)) {

				$location_info = get_object_vars($venue_info['location']);
				$category_info = $venue_info['categories'];
				$category_info = get_object_vars($category_info[0]);
				$like = get_object_vars($venue_info['beenHere']);

				$city = $location_info['city'];
				$state = $location_info['state'];
				$lat = $location_info['lat'];
				$lng = $location_info['lng'];

				$type_name = $category_info['name'];
				$type_name_plural = $category_info['pluralName'];
				$short_name = $category_info['shortName'];

			   	$beenhere = $like['count'];

			   	
				$checkins_array[] = array('name'=>$name, 'city'=>$city, 'state'=>$state, 'count'=>$beenhere, 'lat'=>$lat, 'lng'=>$lng, 'category'=>$type_name, "shortName"=>$short_name);
				$multiple_checkins[] = $name;

			}
	}	

	$most_recent = array_slice($checkins_array, 0, 3);
	$type_array = [];
	foreach ($checkins_array as $array) {
		$count[] = $array['count'];
		$type_array[] = $array['category'];
	}



	array_multisort($count, SORT_DESC, $checkins_array);
	$most_visited = array_slice($checkins_array,0,3);	
	echo "<h1>Recents</h1>";
	for($i=0; $i<3; $i++) {
		echo "<h3>".$most_recent[$i]['name']." in ".$most_recent[$i]['city']."</h3>";
	}
	echo "<h1>Most visited  venues</h1>";
	for ($i=0; $i<3 ; $i++) { 
		echo "<h3>".$most_visited[$i]['name']." in ".$most_visited[$i]['city']."(".$most_visited[$i]['count'].")</h3>";

	}

	$unique_type_array = array_unique($type_array);

	$unique_type_count = count($unique_type_array);

	$category_count = [];

	for ($i=0; $i<$unique_type_count; $i++) {
		for ($j=0; $j<$count_checkins; $j++) {
			if ($unique_type_array[$i]===$checkins_array[$j]['category']) {
				$category_count[$unique_type_array[$i]] = $category_count[$unique_type_array[$i]]+$checkins_array[$j]['count'];
			} 
		}
	}

	echo "<h1>Top Categories</h1>";

	arsort($category_count);

	$sorted_top_categories = [];
	foreach($category_count as $key => $value) {
		if(!$key==''){
			$sorted_top_categories[] = $key;
		}
	}
	for ($i=0; $i<3 ; $i++) { 

			echo "<h3>".$sorted_top_categories[$i]."</h3>";

	}
	

?>