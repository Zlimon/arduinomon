<?php
	header('Content-type: application/json'); // Sets page header as JSON to serve as RESTful API

	$getPokemon = file_get_contents('https://pokeapi.co/api/v2/pokemon-species/'.$_GET["id"]); // Retrieve Pokemon data from PokeAPI
	$pokemon = json_decode($getPokemon);

	$name = str_replace('"', '', json_encode($pokemon->name)); // Pokemon name
	$capture_rate = json_encode($pokemon->capture_rate); // Capture rate of Pokemon

	// Create an array to store Pokemon data
	$attributes = array(
		'id' => (int)$_GET["id"],
		'name' => $name,
		'capture_rate' => (int)$capture_rate,
		'arduino' => (string)$_GET["arduino"] // WIP
	);

	echo json_encode($attributes); // Decode array into JSON object
?>