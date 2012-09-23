This again has been a very handy bundle for me,

The way it works is by giving priority to uris(or routes).

NOTE: The logic for extracting roles out of user comes from a closure regitered in IoC in start.php. The default logic is:

	IoC::register('bouncer: roles_extractor', function () {
		return function ($user) {
			return array_map(function ($r) { return $r->name; }, $user->roles);
		};
	});

In config/rules.php, you define an array of uri-heads with the roles required for them
example: 

	return array(
		'admin/acl/users/new' => array('superadmin'),
		'admin/acl/users/create' => array('superadmin'),
		'admin/acl/users/destroy' => array('superadmin'),
		'admin'       => array('admin', 'superadmin')
	);

the Bouncer finds the best matched uri-head for the current uri and checks if current user is allowed to
access that uri or not.

If a user doesn't have appropriate roles, it either shows a 403 forbidden page or (the view for the page
is in bouncer/views/blocked.blade.php), or it throws a json-response with data ['error' => 'forbidden']
and header 403 based on whether the current uri is an api call or not (which works )

You can easily attach it to the before filter of the routes and be done with it like this:

	Route::filter('before', function()
	{
		if($user = Auth::user())
		{
			$result = Bouncer::investigate($user)->allow_or_block_on(URI::current());
			if($result !== true) return $result;
		}
		else
		{
			//do whatever
		}
	});


This bundle plays really well with my gatekeeper bundle like this:

	Route::filter('before', function()
	{
		$result = Gatekeeper::inspect(URI::current())->result();
		if($result !== true) return $result;

		if($user = Auth::user())
		{
			$result = Bouncer::investigate($user)->allow_or_block_on(URI::current());
			if($result !== true) return $result;
		}
	});