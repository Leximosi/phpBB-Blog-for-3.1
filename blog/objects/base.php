<?php
/**
 *
 * @package phpBB-Blog
 * @copyright (c) 2012 phpBB-Blog
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

/**
 * base object
 *
 * Base class that is shared by all objects, defines some
 * default behavior and implements the object interface
 */
abstract class phpbb_ext_blog_blog_objects_base implements phpbb_ext_blog_blog_objects_interface
{
	protected $id;

	/**
	 * Construct the object
	 *
	 * @param dbal $db The phpBB DBAL object
	 * @param integer $id
	 */
	public function __construct(dbal $db = null, $id = 0)
	{
		$this->db = $db;
		$this->set_data('id', $id);

		if ($this->id > 0)
		{
			$this->load();
		}
	}

	/**
	 * Set object data
	 *
	 * Set object variable if accessable
	 *
	 * @param array|string $data  An array containing the data that
	 *                            must be set, or the property name
	 * @param string       $value The value for the given property
	 */
	public function set_data($data, $value = '')
	{
		if (!is_array($data))
		{
			$data = array($data => $value);
		}

		foreach ($data as $var => $value)
		{
			// Use a custom setter
			if (method_exists($this, "set_{$var}"))
			{
				call_user_func(array($this, "set_{$var}"), $value);
			}
			else if (property_exists($this, $var))
			{
				$type = gettype($value);
				set_var($this->$var, $value, $type, true);
			}
		}
	}

	protected function _submit(array $data)
	{
		if (!$this->id)
		{
			$mode	= 'INSERT';
			$sql	= 'INSERT INTO %s %s';
		}
		else
		{
			// Set post ID
			$data += array(
				'id' => $this->id,
			);

			$mode	= 'UPDATE';
			$sql	= 'UPDATE %s
					SET %s
					WHERE id = ' . $this->id;
		}

		$sql = sprintf($sql, array_shift($data), $this->db->sql_build_array($mode, $data));
		$this->db->sql_query($sql);

		// Set the new ID
		$this->id = $this->db->sql_nextid();
	}

	/**
	 * __get
	 */
	public function __get($name)
	{
		// Use a custom getter
		if (method_exists($this, "get_{$name}"))
		{
			return call_user_func(array($this, "get_{$name}"));
		}
		// Get the property
		else if (property_exists($this, $name))
		{
			// Don't try to return if private
			if (isset($this->$name))
			{
				return $this->$name;
			}
		}
	}

	//-- Some wrappers that are *only* used during development to not break things
	// @todo remove
	// @codeCoverageIgnoreStart
	public function load()
	{
		throw new Exception("Not implemented!");
	}
	public function parse()
	{
		throw new Exception("Not implemented!");
	}
	public function submit()
	{
		throw new Exception("Not implemented!");
	}
	// @codeCoverageIgnoreEnd
}
