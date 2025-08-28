<?php

namespace App\Auth\Sanctum;

class Sanctum
{
    /**
     * The abilities that are available to all API tokens.
     *
     * @var array<int, string>
     */
    protected array $abilities = [];

    /**
     * Define the abilities that are available to all API tokens.
     *
     * @param  array<int, string>  $abilities
     * @return $this
     */
    public function defineAbilities(array $abilities): self
    {
        $this->abilities = $abilities;

        return $this;
    }

    /**
     * Get the abilities that are available to all API tokens.
     *
     * @return array<int, string>
     */
    public function abilities(): array
    {
        return $this->abilities;
    }
}
