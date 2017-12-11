<?php

namespace App\Service;

use Faker;

class FakerService
{
    public function generate() {
        return Faker\Factory::create('fr_FR');
    }
}