<?php

class MakerErrors
{
    protected $errors;

    public function has()
    {
        return !empty($this->errors);
    }

    public function hasSection($section)
    {
        return !empty($this->errors[$section]);
    }

    public function getSection($section)
    {
        if ($this->hasSection($section))
        {
            return $this->errors[$section];
        }

        return array();
    }

    /**
     * @param string $section
     * @param string $error
     * @param string $key
     */
    public function add($section, $error, $key = '')
    {
        $this->errors[$section][] = array(
            'error' => $error,
            'key' => $key,
        );
    }

    public function get()
    {
        return $this->errors;
    }

    public function getAll()
    {
        foreach ($this->errors as $error)
        {
            $return[] = $error;
        }

        return $return;
    }
}