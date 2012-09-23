<?php

Autoloader::namespaces(array(
	'Bouncer' => Bundle::path('bouncer') . 'src'
));

Autoloader::alias('Bouncer\\Bouncer', 'Bouncer');