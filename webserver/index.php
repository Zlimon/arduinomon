<?php
	/* ---- MySQL ---- */
	$db['host'] = '<database connection>';	// IP, domain or localhost
	$db['port'] = '3306';					// The default port number is 3306
	$db['user'] = '<database username>';	// Database username
	$db['pass'] = '<database password>';	// Database password
	$db['db'] = '<database>';				// Database name

	try {
		$now = new DateTime();
		$mins = $now->getOffset() / 60;
		$sgn = ($mins < 0 ? -1 : 1);
		$mins = abs($mins);
		$hrs = floor($mins / 60);
		$mins -= $hrs * 60;
		$offset = sprintf('%+d:%02d', $hrs*$sgn, $mins);

		$dbh = new PDO('mysql:host=' . $db['host'] . ':' . $db['port'] . ';dbname=' . $db['db'] . '', $db['user'], $db['pass']);
		$dbh->exec("SET time_zone='$offset';");
		$dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	catch (PDOException $e) {
		echo $e;
		die();
	}
?>

<?php
	function appendId($id) {
		if ($id <= 9) {
			return $id = '00'.$id;
		} elseif ($id <= 99) {
			return $id = '0'.$id;
		} else {
			return $id;
		}
	}

	function getPokemonData($pokemonId) {
		$getPokemon = file_get_contents('https://pokeapi.co/api/v2/pokemon-species/'.$pokemonId);
		return $pokemon = json_decode($getPokemon);
	}

	function preg($url) {
		preg_match('/\/\d+\//', $url, $pokemonId);

		return $pokemonId;
	}

	function getJson($url) {
		$getData = file_get_contents($url);
		return $readData = json_decode($getData);
	}
?>

<!DOCTYPE html>
<html lang="nb">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<title>Arduinomon</title>

	<!-- Scripts -->
	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

	<!-- Styles -->
	<link rel="stylesheet" href="style.css">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.1/css/all.css" integrity="sha384-5sAR7xN1Nv6T6+dT2mhtzEpVJvfS3NScPQTrOxhwjIuvcA67KV2R5Jz6kr4abQsz" crossorigin="anonymous">

	<style type="text/css">
		@media (min-width: 1200px) {
			.container {
				max-width: 1800px;
			}
		}
	</style>
</head>

<body>
	<div id="app">
		<main class="py-4">
			<div class="container">
				<div class="row">
					<div class="col-md-5">
						<div class="col-md-12 rounded-left mb-4 bg-right">
							<nav class="navbar navbar-expand-lg navbar-dark">
								<a class="navbar-brand" href="#">Arduinomon</a>
								<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
									<span class="navbar-toggler-icon"></span>
								</button>

								<div class="collapse navbar-collapse" id="navbarSupportedContent">
									<ul class="navbar-nav mr-auto">
										<li class="nav-item active">
											<a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
										</li>
										<li class="nav-item">
											<a class="nav-link" href="/api/index.php?id=1">Local API</a>
										</li>
										<li class="nav-item dropdown">
											<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Generation</a>
											<div class="dropdown-menu" aria-labelledby="navbarDropdown">
												<?php
													$pokemonGenerations = getJson('https://pokeapi.co/api/v2/generation/');

													$generationList = $pokemonGenerations->results;

													foreach ($generationList as $key => $generation) {
														echo '
															<a class="dropdown-item" href="/'.$generation->name.'">Generation '.strtoupper(str_replace('generation-', '', $generation->name)).'</a>
														';
													}
												?>
											</div>
										</li>
									</ul>
								</div>
							</nav>
						</div>

						<div class="col-md-12 rounded mb-4 p-4 bg-left">
							<h1 class="border-bottom mb-4">Search for captured Pokemon</h1>

							<div class="row d-flex justify-content-center">
								<div class="col-md-6">
									<form>
										<div class="form-group">
											<input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Try Pikachu or 25">
										</div>

										<button type="submit" class="btn btn-block btn-success">Submit</button>
									</form>
								</div>
							</div>
						</div>

						<div class="col-md-12 rounded p-4 bg-left">
							<h1 class="border-bottom mb-4">Pokedex</h1>

							<?php
								$rePattern = '/\d+/';

								echo '
									<ul class="nav nav-tabs" id="myTab" role="tablist">
								';
										foreach ($generationList as $key => $generation) {
											echo '
												<li class="nav-item">
													<a class="nav-link '.(!$generationButton++ ? 'active' : '').'" id="'.$generation->name.'-tab" data-toggle="tab" href="#'.$generation->name.'" role="tab" aria-controls="'.$generation->name.'" aria-selected="false">'.strtoupper(str_replace('generation-', '', $generation->name)).'</a>
												</li>
											';
										}
								echo '
									</ul>
									<div class="tab-content" id="myTabContent">
								';
										foreach ($generationList as $key => $generation) {
											$generationData = getJson($generation->url);

											$pokemonList = $generationData->pokemon_species;

											echo '
												<div class="tab-pane fade '.(!$generationCount++ ? 'show active' : '').'" id="'.$generation->name.'" role="tabpanel" aria-labelledby="'.$generation->name.'-tab">
													<div class="item-container">
											';
														foreach ($pokemonList as $key => $value) {
															echo '
																<div>
																	<div class="text-center bg-right rounded m-2 p-2">
																		<img src="/images/sprites/front/'.str_replace('/', '', preg($value->url)[0]).'.png" style="width: 125px;">
																	</div>
																	<p class="text-left ml-2">
																		<span class="text-dark">#'.appendId(str_replace('/', '', preg($value->url)[0])).'</span>
																		<br>
																		'.ucfirst($value->name).'
																	</p>
																</div>
															';
														}
											echo '
													</div>
												</div>
											';
										}
								echo '
									</div>
								';
							?>
						</div>
					</div>

					<div class="col-md-7">
						<div class="col-md-12 rounded-right bg-right">
							<h1>Your captured pokemon</h1>

							<div id="carouselExampleControls" class="carousel slide" data-ride="carousel" data-interval="false">
								<div class="carousel-inner">
									<?php
										$getCapturedPokemons = $dbh->prepare("SELECT pokemon_id, COUNT(id) AS count FROM catches GROUP BY pokemon_id ORDER BY id");
										$getCapturedPokemons->execute();

										$capturedPokemon = $getCapturedPokemons->fetchAll();

										foreach ($capturedPokemon as $pokemon) {
											// ucfirst(getPokemonData($pokemon["pokemon_id"])->name)
											echo '
												<div class="carousel-item '.(!$capturedAmount++ ? 'active' : '').'">
													<p class="border-bottom">pokemonName #'.appendId($pokemon["pokemon_id"]).'</p>
													<img class="d-block bg-left" style="width: 25%; border-radius: 1rem;" src="https://assets.pokemon.com/assets/cms2/img/pokedex/full/'.appendId($pokemon["pokemon_id"]).'.png" alt="'.$capturedAmount.' slide">

													<div class="item-container">';
														$pokemonData = getJson('https://pokeapi.co/api/v2/pokemon-species/'.$pokemon["pokemon_id"]);

														$evoChainUrl = $pokemonData->evolution_chain->url;
														$evoData = getJson($evoChainUrl)->chain;

														$rePattern = '/\d+/';

														if (!empty($evoData->evolves_to)) {
															echo '
																<div>
																	<a href="?id='.str_replace('/', '', preg($evoData->species->url)[0]).'">
																		<p>#'.appendId(str_replace('/', '', preg($evoData->species->url)[0])).' '.ucfirst($evoData->species->name).'</p>
																		<img src="https://assets.pokemon.com/assets/cms2/img/pokedex/full/'.appendId(str_replace('/', '', preg($evoData->species->url)[0])).'.png" style="width: 150px;">
																	</a>
																</div>
															';
															while ($evoData->evolves_to) {
																$evoData = $evoData->evolves_to[0];

																echo '
																	<div>
																		<i class="icon-arrow icon-arrow-e"></i>
																	</div>
																	<div>
																		<a href="?id='.str_replace('/', '', preg($evoData->species->url)[0]).'">
																			<p>#'.appendId(str_replace('/', '', preg($evoData->species->url)[0])).' '.ucfirst($evoData->species->name).'</p>
																			<img src="https://assets.pokemon.com/assets/cms2/img/pokedex/full/'.appendId(str_replace('/', '', preg($evoData->species->url)[0])).'.png" style="width: 150px;">
																		</a>
																	</div>
																';
															}
														} else {
															echo '<p style="height: 175px;">No evolution chain!</p>';
														}
											echo '
													</div>
												</div>
											';
										}
									?>
								</div>

								<a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
									<span class="carousel-control-prev-icon" aria-hidden="true"></span>
									<span class="sr-only">Previous</span>
								</a>
								<a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
									<span class="carousel-control-next-icon" aria-hidden="true"></span>
									<span class="sr-only">Next</span>
								</a>
							</div>

							<div class="item-container">
								<?php
									foreach ($capturedPokemon as $pokemon) {
										echo '
											<div>
												<div class="item item-tooltip text-center bg-left rounded m-2 p-2">
													<img class="mx-auto d-block" src="/images/sprites/front/'.$pokemon["pokemon_id"].'.png" style="width: 100px;">
													'.($pokemon["count"] > 1 ? '<span class="item-counter bg-light">'.$pokemon["count"].'</span>' : '').'
												</div>
												<p class="text-left ml-2">
													<span class="text-dark">#'.appendId($pokemon["pokemon_id"]).'</span>
													<br>
												</p>
											</div>
										';
									}
								?>
							</div>

							<!-- <table class="center">
								<tr>
									<td>
										<img style="vertical-align: middle;" src="http://www.pkparaiso.com/imagenes/xy/sprites/animados/magnemite.gif">
									</td>
									<td>
										<img style="vertical-align: middle;" src="http://www.pkparaiso.com/imagenes/xy/sprites/animados-shiny/magnemite.gif">
									</td>
								</tr>
							</table>

							<div class="infoBox">
								<table>
									<tr>
										<td>Høyde: <span class="highlight">{{.Height}}</span> m</td>
										<td>Vekt: <span class="highlight">{{.Weight}}</span> kg</td>
									</tr>
									<tr>
										<td>Type:</td>
										<td>Evner:</td>
									</tr>
									<tr>
										<td><div class="primaryTypeColor">{{.PrimaryType}}</div> <div class="secondaryTypeColor">{{.SecondaryType}}</div></td>
										<td>{{.Abilities}}</td>
									</tr>
								</table>
							</div> -->
						</div>
					</div>
				</div>
			</div>
		</main>
	</div>
</body>