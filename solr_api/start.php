<?php

elgg_register_event_handler('init','system','solr_api_init');


function solr_api_init() {

	// @TODO limit access to only the specified user agent string for more strict security
	// @TODO list all subtypes and types

	// http://192.168.245.130/gcconnex/services/api/rest/json/?method=get.entity_list&type=object&subtype=blog
	// http://192.168.245.130/gcconnex/services/api/rest/json/?method=get.user_list
	// http://192.168.245.130/gcconnex/services/api/rest/json/?method=get.group_list

	// api expose function calls
	elgg_ws_expose_function(
        'get.entity_list',
        'get_entity_list',
        [
            'type' => [
                    'type' => 'string',
                    'required' => true,
                    'description' => 'the type of entity in string format',
            ],
            'subtype' => [
                    'type' => 'string',
                    'required' => false,
                    'description' => 'the subtype of entity in string format, not required',
            ],

        ],
        'retrieves all entities filtered by type [and subtype]',
        'GET',
        false,
        false
	);


	elgg_ws_expose_function(
        'get.user_list',
        'get_user_list',
        null,
        'retrieves a user list',
        'GET',
        false,
        false
	);

	elgg_ws_expose_function(
        'get.group_list',
        'get_group_list',
        null,
        'retrieves a group list',
        'GET',
        false,
        false
	);
}


// api calls with sample set of limit 15
function get_user_list() {

	$users = elgg_get_entities(array(
		'type' => 'user',
		'limit' => 15
	));

	foreach ($users as $user) {

		$name_array['en'] = $user->name;
		$name_array['fr'] = $user->name;

		$arr[] = array(
			'guid' => $user->getGUID(),
			'name' => $name_array,
			'username' => $user->username,
			'email' => $user->email,
			'type' => $user->getType(),
		);
	}
	
    return $arr;
}

function get_group_list() {

	$groups = elgg_get_entities(array(
		'type' => 'group',
		'limit' => 15
	));

	foreach ($groups as $group) {

		if (isJson($group->name)) {
			$name_array = json_decode($group->name, true);
			$name_array['en'] = str_replace('"', '\"', $name_array['en']);
			$name_array['fr'] = str_replace('"', '\"', $name_array['fr']);
		} else {
			$name_array['en'] = $group->name;
			$name_array['fr'] = $group->name;
		}

		if (isJson($group->description)) {
			$description_array = json_decode($group->description, true);
			$description_array['en'] = str_replace('"', '\"', $description_array['en']);
			$description_array['fr'] = str_replace('"', '\"', $description_array['fr']);
		} else {
			$description_array['en'] = $group->description;
			$description_array['fr'] = $group->description;
		}

		$arr[] = array(
			'guid' => $group->getGUID(),
			'name' => $name_array,
			'description' => $description_array,
			'type' => $group->getType(),
			'access_id' => $group->access_id
		);
	}
	
    return $arr;
}


function get_entity_list($type, $subtype) {

	$entities = elgg_get_entities(array(
		'type' => $type,
		'subtype' => $subtype,
		'limit' => 15
	));

	foreach ($entities as $entity) {

		if (isJson($entity->title)) {
			$title_array = json_decode($entity->title, true);
			$title_array['en'] = str_replace('"', '\"', $title_array['en']);
			$title_array['fr'] = str_replace('"', '\"', $title_array['fr']);
		} else {
			$title_array['en'] = $entity->title;
			$title_array['fr'] = $entity->title;
		}

		if (isJson($entity->description)) {
			$description_array = json_decode($entity->description, true);
			$description_array['en'] = str_replace('"', '\"', $description_array['en']);
			$description_array['fr'] = str_replace('"', '\"', $description_array['fr']);
		} else {
			$description_array['en'] = $entity->description;
			$description_array['fr'] = $entity->description;
		}

		$arr[] = array(
			'guid' => $entity->getGUID(),
			'title' => $title_array,
			'description' => $description_array,
			'type' => $entity->getType(),
			'subtype' => $entity->getSubtype(),
			'access_id' => $entity->access_id
		);
	}
	
    return $arr;
}



function isJson($string) {
	json_decode($string);
	return (json_last_error() == JSON_ERROR_NONE);
}

