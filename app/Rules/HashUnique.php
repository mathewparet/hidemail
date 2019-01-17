<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use DB;

class HashUnique implements Rule
{
    private $table, $field, $omitId, $omitField;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($table, $field=null, $omitField='id', $omitId=null)
    {
        $this->table = $table;
        $this->field = $field;
        $this->omitId = $omitId;
        $this->omitField = $omitField;
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
        $hashed_field = $attribute."_hash";

        $this->field = $this->field ? : $attribute;

        $num_rows_in_db = DB::table($this->table)
                            ->where($hashed_field, sha1($value))
                            ->where($this->omitField, '<>',$this->omitId)
                            ->count();

        return $num_rows_in_db > 0 ? false : true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute already exists';
    }
}
