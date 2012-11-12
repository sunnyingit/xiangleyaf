<?php

class Model_Static
{
    public function __get($var)
    {
        return Dao('Static_' . ucfirst($var));
    }
}