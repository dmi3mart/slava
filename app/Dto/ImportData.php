<?php

namespace App\Dto;

use Illuminate\Contracts\Support\Arrayable;

class ImportData implements Arrayable
{
    /**
     * @var int|null
     */
    public int|null $id;

    /**
     * @var string
     */
    public string $name;

    /**
     * @var string
     */
    public string $date;

    public function __construct($id, $name, $date)
    {
        $this->id = (int)$id;
        $this->name = $name;
        $this->date = $date;
    }

    /**
     * @return int[]|null[]
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->id,
            'date' => $this->id,
        ];
    }
}
