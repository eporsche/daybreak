<?php

namespace App\Http\Livewire;

trait TrimAndNullEmptyStrings
{
    /**
     * Check why this is needed with livewire.
     * https://github.com/livewire/livewire/issues/823
     * Sending empty strings instead of null (dafault laravel) is intended by Livewire.
     *
     * @param srting $name
     * @param mixed $value
     * @return void
     */
    public function updatedTrimAndNullEmptyStrings($name, $value)
    {
        if (is_string($value)) {
            $value = trim($value);
            $value = $value === '' ? null : $value;

            data_set($this, $name, $value);
        }
    }
}
