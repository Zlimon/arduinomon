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

<!DOCTYPE html>
<html>
<head>
	<title>Arduinomon - Pretend you are going to catch 'em all</title>
</head>
<body>
	<h1>Captured Pokemon:</h1>

	<?php
	    /**
	     * To properly display images, we need to append 0's to certain ID's
	     *
	     * @return integer
	     */
		function appendId($id) {
			if ($id <= 9) {
				return $id = '00'.$id;
			} elseif ($id <= 99) {
				return $id = '0'.$id;
			} else {
				return $id;
			}
		}
	?>

	<?php
		$getCapturedPokemons = $dbh->prepare("SELECT * FROM catches"); // Retrieves every captured Pokemon
		$getCapturedPokemons->execute();

		while ($pokemon = $getCapturedPokemons->fetch()) {
			$name = $pokemon["pokemon_id"];

			echo '<div><p>'.$name.'</p> <img style="width: 150px;" src="https://assets.pokemon.com/assets/cms2/img/pokedex/full/'.appendId($name).'.png"></div>';
		}
	?>
</body>
</html>