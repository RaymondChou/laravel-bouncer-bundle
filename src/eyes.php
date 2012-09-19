<?php

namespace Bouncer;

class Eyes
{
	protected $_user = null;
	protected $_roles = array();

	public static function on($user)
	{
		return new static($user);
	}

	public function __construct($user)
	{
		$this->_user = $user;
	}

	public function user()
	{
		return $this->_user;
	}

	public function roles()
	{
		if($this->_roles)
			return $this->_roles;

		return $this->_roles = array_map(function ($r) { return $r->name; }, $this->_user->roles);
	}

	public function is_allowed_on($uri)
	{
		if( null === $matched_path = $this->find_best_matched_path(Rules::all_paths(), $uri) )
			return true;

		return in_array($matched_path, $this->allowed_paths());
	}

	protected function find_best_matched_path($paths, $uri)
	{
		$match = null;
		foreach($paths as $p)
		{
			if( starts_with($uri, $p) and ( $match === null or count(explode('/', $p)) > count(explode('/', $match)) ) ) {
				$match = $p;
			}
		}

		return $match;
	}

	public function allowed_paths()
	{
		$paths = array();
		foreach($this->roles() as $role)
			$paths = array_merge($paths, Rules::paths_for_role($role));

		return array_values(array_unique($paths));
	}
}