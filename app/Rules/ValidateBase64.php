<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidateBase64 implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    private $type;
    public function __construct($type = 'image')
    {
        $this->type = $type;
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
        $ext = ext_base64($value);
        $types = ['jpg', 'png', 'jpeg', 'gif', 'png', 'pdf', 'doc', 'docx', 'mp4', 'mov'];
        return in_array($ext, $types);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'File type not allowed';
    }
}
