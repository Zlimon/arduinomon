<?php
	header('Content-type: application/json');

	$getPokemon = file_get_contents('https://pokeapi.co/api/v2/pokemon-species/'.$_GET["id"]);
	$pokemon = json_decode($getPokemon);

	$name = str_replace('"', '', json_encode($pokemon->name));
	$capture_rate = json_encode($pokemon->capture_rate);

	$attributes = array('id' => (int)$_GET["id"], 'name' => $name, 'capture_rate' => (int)$capture_rate);

	echo json_encode($attributes);
?>