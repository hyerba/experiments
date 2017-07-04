<?php
	$startTime			= microtime(true);
	$fp					= fopen('map.txt', 'r');
	$line				= fgets($fp);
	$map				= array();
	$endPoints			= array();
	$longestPath		= array();
	$lineNum			= 0;

	list($rows, $cols)	= explode(' ', $line);
	while ($line = fgets($fp)) {
		$map[$lineNum++] = explode(' ', trim($line));
	}
	
	// Go through map and get endpoints only to avoid finding path for all points
	for ($y=0; $y < $rows; $y++) {
		for ($x = 0; $x < $cols; $x++) {
			if (isEndPoint($y, $x)) {
				$endPoints[] = array($y, $x);
			}
		}
	}

	//print_r($endPoints);
	echo 'Total endpoints: '.count($endPoints)."\n";

	// Go through endpoints and get path in reverse
	foreach ($endPoints as $index=>$endPoint) {
		$newPath = getPath($endPoint[0], $endPoint[1]);
		if (replacePath($newPath, $longestPath)) {
			$longestPath = $newPath;
			echo "Index $index: ".$endPoint[0].', '.$endPoint[1]."\n";
			echo 'Length: '.count($longestPath)."\n";
			printPath($longestPath);
			echo "\n";
		}
	}

	$endTime = microtime(true);
	echo 'Time taken: '.($endTime - $startTime)."\n";

	function isEndPoint($y, $x) {
		GLOBAL $map;
		GLOBAL $rows;
		GLOBAL $cols;

		if (($y > 0) && ($map[$y][$x] > $map[$y - 1][$x])) {
			return false;
		}

		if (($y < ($rows - 1)) && ($map[$y][$x] > $map[$y + 1][$x])) {
			return false;
		}

		if (($x < ($cols - 1)) && ($map[$y][$x] > $map[$y][$x + 1])) {
			return false;
		}

		if (($x > 0) && ($map[$y][$x] > $map[$y][$x - 1]))  {
			return false;
		}

		return true;
	}

	function getPath($startY, $startX) {
		GLOBAL $map;
		GLOBAL $rows;
		GLOBAL $cols;

		// echo "Starting: $startY $startX\n";
		$longestPath = array();
		if (($startY > 0) && ($map[$startY][$startX] < $map[$startY - 1][$startX])) {
			// echo "going north\n";
			$newPath = getPath($startY - 1, $startX);
			if (replacePath($newPath, $longestPath)) {
				$longestPath = $newPath;
			}
		}

		if (($startY < ($rows - 1)) && ($map[$startY][$startX] < $map[$startY + 1][$startX])) {
			// echo "going south\n";
			$newPath = getPath($startY + 1, $startX);
			if (replacePath($newPath, $longestPath)) {
				$longestPath = $newPath;
			}
		}

		if (($startX < ($cols - 1)) && ($map[$startY][$startX] < $map[$startY][$startX + 1])) {
			// echo "going east\n";
			$newPath = getPath($startY, $startX + 1);
			if (replacePath($newPath, $longestPath)) {
				$longestPath = $newPath;
			}
		}

		if (($startX > 0) && ($map[$startY][$startX] < $map[$startY][$startX - 1]))  {
			// echo "going west\n";
			$newPath = getPath($startY, $startX - 1);
			if (replacePath($newPath, $longestPath)) {
				$longestPath = $newPath;
			}
		}

		// echo "Ending: $startY $startX\n";
		// print_r(array_merge(array(array($startY, $startX)), $longestPath));

		return array_merge($longestPath, array(array($startY, $startX)));
	}

	function replacePath($newPath, $longestPath) {
		GLOBAL $map;

		$lengthNewPath		= count($newPath);
		$lengthLongestPath	= count($longestPath);

		if ($lengthNewPath == 0) {
			return false;
		}

		if ($lengthNewPath > $lengthLongestPath) {
			return true;
		}
		else if ($lengthNewPath == $lengthLongestPath) {
			if ($lengthNewPath == 1) {
				return ($map[$newPath[0][0]][$newPath[0][1]] > $map[$longestPath[0][0]][$longestPath[0][1]]);
			}
			else {
				$newPathDrop		= abs($map[$newPath[0][0]][$newPath[0][1]] - $map[$newPath[$lengthNewPath-1][0]][$newPath[$lengthNewPath-1][1]]);
				$longestPathDrop	= abs($map[$longestPath[0][0]][$longestPath[0][1]] - $map[$longestPath[$lengthLongestPath-1][0]][$longestPath[$lengthLongestPath-1][1]]);
				return ($newPathDrop > $longestPathDrop);

			}

		}
	}

	function printPath($path, $delimiter=' ') {
		GLOBAL $map;

		foreach ($path as $point) {
			echo $map[$point[0]][$point[1]].$delimiter;
		}
		echo "\n";
	}
