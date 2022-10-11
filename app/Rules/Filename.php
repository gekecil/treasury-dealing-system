<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Carbon\Carbon;
use Storage;

class Filename implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
		$storage = Storage::disk('local');
		$filename = 'uploads/'.Carbon::now()->format('Y').'/'.Carbon::now()->format('M').'/'.$value->getClientOriginalName();
		
		return !$storage->exists($filename);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The file name already exists. Please rename the file!';
    }
}
