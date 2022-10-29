<?php

namespace app\core;

use app\core\Helpers\StringHelper;

abstract class Model
{
    public const ROLE_REQUIRED = 'required';
    public const ROLE_EMAIL = 'email';
    public const ROLE_MIN = 'min';
    public const ROLE_MAX = 'max';
    public const ROLE_MATCH = 'match';
    public const ROLE_UNIQUE = 'unique';
    public function loadData($data)
    {
        foreach ($data as $key => $value){
            if (property_exists($this, $key)){
                $this->{$key} = $value;
            }
        }
    }
    abstract public function rules(): array;
    public function labels(): array
    {
        return [];
    }
    public function getLabel($attribute)
    {
        return $this->labels()[$attribute]??StringHelper::label($attribute);
    }
    public array $errors = [];
    public function validate()
    {
        foreach ($this->rules() as $attribute => $rules) {
            $value = $this->{$attribute};
            foreach ($rules as $rule) {
                $ruleName = $rule;
                if (!is_string($ruleName)){
                    $ruleName = $rule[0];
                }
                if ($ruleName === self::ROLE_REQUIRED && !$value ){
                    $this->addErrorForRule($attribute, self::ROLE_REQUIRED);
                }
                if ($ruleName === self::ROLE_EMAIL && !filter_var($value, FILTER_VALIDATE_EMAIL)){
                    $this->addErrorForRule($attribute, self::ROLE_EMAIL);
                }
                if ($ruleName === self::ROLE_MIN && mb_strlen($value) < $rule['min']){
                    $this->addErrorForRule($attribute, self::ROLE_MIN, $rule);
                }
                if ($ruleName === self::ROLE_MAX && mb_strlen($value) > $rule['max']){
                    $this->addErrorForRule($attribute, self::ROLE_MAX, $rule);
                }
                if ($ruleName === self::ROLE_MATCH && $value !== $this->{$rule['match']}){

                    $rule['match'] = $this->getLabel($rule['match']);
                    $this->addErrorForRule($attribute, self::ROLE_MATCH, $rule);
                }
                if ($ruleName === self::ROLE_UNIQUE) {
                    $className = $rule['class'];
                    $uniqueAttr = $rule['attribute'] ?? $attribute;
                    $tableName = $className::tableName();
                    $statement = Application::$app->db->prepare("SELECT * FROM $tableName WHERE $uniqueAttr=:attr");
                    $statement->bindValue(":attr", $value);
                    $statement->execute();
                    $record = $statement->fetchObject();
                    if ($record){
                        $this->addErrorForRule($attribute,self::ROLE_UNIQUE, [
                            'field' => $this->getLabel($attribute)
                        ]);
                    }
                }
            }
        }
        return empty($this->errors);
    }
    protected function addErrorForRule(string $attribute, string $rule, $params = [])
    {
        $message = $this->errorMessages()[$rule] ?? '';
        foreach ($params as $key => $value) {
            $message = str_replace("{{$key}}", $value, $message);
        }
        $this->errors[$attribute][]=$message;
    }
    public function addError(string $attribute, string $message)
    {
        $this->errors[$attribute][]=$message;
    }
    public function errorMessages()
    {
        return [
            self::ROLE_REQUIRED => 'This field is required',
            self::ROLE_EMAIL => 'This field must be email address',
            self::ROLE_MIN => 'Min length of this field must be {min}',
            self::ROLE_MAX => 'Max length of this field must be {max}',
            self::ROLE_MATCH => 'This field must be the same as {match}',
            self::ROLE_UNIQUE => 'Record with this {field} already exists',
        ];
    }

    public function hasError($attribute)
    {
        return $this->errors[$attribute] ?? false;
    }
    public function getFirstError($attribute)
    {
        return $this->errors[$attribute][0]??false;
    }
}