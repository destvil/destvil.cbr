<?php

namespace destvil\cbr\Entity;

use Bitrix\Main\Type\DateTime;

class Currency
{
    private string $code;
    private DateTime $updatedAt;
    private float $course;

    /**
     * @param string $code
     * @param float $course
     * @param ?DateTime $updatedAt
     *
     */
    public function __construct(string $code, float $course, DateTime $updatedAt = null)
    {
        $this->code = $code;
        $this->updatedAt = $updatedAt ?? new DateTime();
        $this->course = $course;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return float
     */
    public function getCourse(): float
    {
        return $this->course;
    }

    /**
     * @return DateTime
     */
    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    public function toArray(): array
    {
        return [
            'code' => $this->getCode(),
            'course' => $this->getCourse(),
            'date' => $this->getUpdatedAt()
        ];
    }
}