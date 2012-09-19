<?php

Autoloader::namespaces([
	'Bouncer' => Bundle::path('bouncer') . 'src'
]);

Autoloader::alias('Bouncer\\Bouncer', 'Bouncer');