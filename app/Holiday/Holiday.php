<?php

namespace App\Holiday;

use Illuminate\Contracts\Support\Arrayable;

class Holiday implements Arrayable
{
    protected $title;

    protected $meta;

    public function __construct(string $title, array $meta)
    {
        $this->title = $title;
        $this->meta = $meta;
    }

    public function toArray()
    {
        return [
            'title' => $this->title,
            'day' => $this->meta['datum']
        ];
    }
}
