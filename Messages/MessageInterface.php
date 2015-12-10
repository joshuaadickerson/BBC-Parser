<?php

interface MessageInterface
{
    /**
     * What the user inputs.
     * Used as input for the preparser
     *
     * @return string
     */
    public static function input();

    /**
     * What is stored in the database
     * Used to check if the preparser works
     * Also used as input for the parser
     *
     * @return string
     */
    public static function stored();

    /**
     * What is displayed to the user
     * Used to check if the parser works
     *
     * @return string
     */
    public static function output();

    /**
     * Get a human readable name of this message
     * @return string
     */
    public static function name();
}