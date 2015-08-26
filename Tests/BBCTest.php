<?php

namespace BBC\Tests;

interface BBCTest
{
	public function __construct();
	public function getName();
	public function parseMessage($message);
	public function codes();
}