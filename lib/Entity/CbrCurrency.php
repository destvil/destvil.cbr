<?php

namespace destvil\cbr\Entity;

class CbrCurrency
{
    private string $code;
    private string $name;
    private float $course;

    /**
     * @param string $code
     * @param string $name
     * @param float $rate
     */
    public function __construct(string $code, string $name, float $rate)
    {
        $this->code = $code;
        $this->name = $name;
        $this->course = $rate;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return float
     */
    public function getCourse(): float
    {
        return $this->course;
    }
}