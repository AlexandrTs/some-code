<?php

namespace App\Validation;

use Illuminate\Support\MessageBag;
use Illuminate\Validation\Validator;

class BailingValidator extends Validator
{
    /**
     * Determine if the data passes the validation rules.
     *
     * @return bool
     */
    public function passes()
    {
        $this->messages = new MessageBag;

        [$this->distinctValues, $this->failedRules] = [[], []];

        foreach ($this->rules as $attribute => $rules) {
            if ($this->shouldBeExcluded($attribute)) {
                $this->removeAttribute($attribute);

                continue;
            }

            foreach ($rules as $rule) {
                $this->validateAttribute($attribute, $rule);

                if ($this->shouldBeExcluded($attribute)) {
                    $this->removeAttribute($attribute);

                    break;
                }

                if ($this->shouldStopValidating($attribute)) {
                    break 2;
                }
            }
        }

        foreach ($this->after as $after) {
            $after();
        }

        return $this->messages->isEmpty();
    }
}
