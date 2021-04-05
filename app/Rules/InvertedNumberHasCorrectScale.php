<?php

namespace App\Rules;

use Brick\Math\BigDecimal;
use Illuminate\Contracts\Validation\Rule;
use Brick\Math\Exception\DivisionByZeroException;
use Brick\Math\Exception\RoundingNecessaryException;

class InvertedNumberHasCorrectScale implements Rule
{
    protected $divisor;

    protected $scale;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($divisor, $scale)
    {
        $this->divisor = $divisor;
        $this->scale = $scale;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (!$value) {
            return;
        }

        try {
            $test = BigDecimal::of($this->divisor)->dividedBy($value, $this->scale);
            return true;
        } catch (RoundingNecessaryException $ex) {
            return false;
        } catch (DivisionByZeroException $ex) {
            return false;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Rounding issue detected. Please choose another value.';
    }
}
