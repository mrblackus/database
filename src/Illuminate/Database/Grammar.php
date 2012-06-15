<?php namespace Illuminate\Database;

abstract class Grammar {

	/**
	 * The grammar table prefix.
	 *
	 * @var string
	 */
	protected $tablePrefix = '';

	/**
	 * Wrap a table in keyword identifiers.
	 *
	 * @param  string  $table
	 * @return string
	 */
	public function wrapTable($table)
	{
		if ($this->isExpression($table)) return $this->getValue($table);

		return $this->wrap($this->tablePrefix.$table);
	}

	/**
	 * Wrap a value in keyword identifiers.
	 *
	 * @param  string  $value
	 * @return string
	 */
	public function wrap($value)
	{
		if ($this->isExpression($value)) return $this->getValue($value);

		// If the value being wrapped has a column alias we will need to separate out
		// the pieces so we can wrap each of the segments of the expression on its
		// own, and then join them both back together with the "as" connectors.
		if (strpos(strtolower($value), ' as ') !== false)
		{
			$segments = explode(' ', $value);

			return $this->wrap($segments[0]).' as '.$this->wrap($segments[2]);
		}

		$wrapped = array();

		$segments = explode('.', $value);

		// If the value is not a aliased table expression, we'll just wrap it like
		// normal, so if there is more than one segment, we will wrap the first
		// segments as if it was a table and the rest as just regulsr values.
		foreach ($segments as $key => $value)
		{
			if ($key == 0 and count($segments) > 1)
			{
				$wrapped[] = $this->wrapTable($value);
			}
			else
			{
				$wrapped[] = $this->wrapValue($value);
			}
		}

		return implode('.', $wrapped);
	}

	/**
	 * Wrap a single string in keyword identifiers.
	 *
	 * @param  string  $value
	 * @return string
	 */
	protected function wrapValue($value)
	{
		return $value !== '*' ? sprintf($this->wrapper, $value) : $value;
	}

	/**
	 * Convert an array of column names into a delimited string.
	 *
	 * @param  array   $columns
	 * @return string
	 */
	public function columnize(array $columns)
	{
		return implode(', ', array_map(array($this, 'wrap'), $columns));
	}

	/**
	 * Create query parameter place-holders for an array.
	 *
	 * @param  array   $values
	 * @return string
	 */
	public function parameterize(array $values)
	{
		return implode(', ', array_map(array($this, 'parameter'), $values));
	}

	/**
	 * Get the appropriate query parameter place-holder for a value.
	 *
	 * @param  mixed   $value
	 * @return string
	 */
	public function parameter($value)
	{
		return $this->isExpression($value) ? $this->getValue($value) : '?';
	}

	/**
	 * Get the value of a raw expression.
	 *
	 * @param  string  $expression
	 * @return string
	 */
	public function getValue($expression)
	{
		return substr($value, 4);
	}

	/**
	 * Determine if the given value is a raw expression.
	 *
	 * @param  mixed  $value
	 * @return bool
	 */
	public function isExpression($value)
	{
		return is_string($value) and strpos($value, 'raw|') === 0;
	}

	/**
	 * Get the grammar's table prefix.
	 *
	 * @return string
	 */
	public function getTablePrefix()
	{
		return $this->tablePrefix;
	}

	/**
	 * Set the grammar's table prefix.
	 *
	 * @param  string  $prefix
	 * @return void
	 */
	public function setTablePrefix($prefix)
	{
		$this->tablePrefix = $prefix;
	}

}