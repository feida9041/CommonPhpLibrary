<?php
/**
 * Created by PhpStorm.
 * User: luhaoz
 * Date: 2017/8/23
 * Time: 10:07
 */

namespace luhaoz\cpl\prototypeplugin\validate;

use luhaoz\cpl\dependence\Dependence;
use luhaoz\cpl\error\Error;
use luhaoz\cpl\prototype\method\types\Method;
use luhaoz\cpl\prototype\plugin\base\BasePlugin;
use luhaoz\cpl\prototype\property\types\Value;
use luhaoz\cpl\validate\ValidateManager;
use luhaoz\cpl\validate\ValidateResult;

/**
 * Class ValidatePlugin
 * @package luhaoz\cpl\prototypeplugin\validate
 */
class ValidatePlugin extends BasePlugin
{
    const PLUGIN_NAME = 'validate';

    /**
     * @param null $owner
     * @return \luhaoz\cpl\prototype\property\PropertyManager
     */
    public function owner($owner = null)
    {
        return parent::owner($owner); // TODO: Change the autogenerated stub
    }

    public function initialise()
    {
        $this->owner()->prototype()->pubSubs()->on('propertyInstantiate', [$this, '__propertyInstantiate']);
        $this->owner()->prototype()->methods()->config('validate', Dependence::dependenceConfig(Method::class, [
            '::method' => [[$this, '__validate']],
        ]));
    }

    public function __propertyInstantiate(Value $property)
    {
        $property->prototype()->plugins()->setup(PropertyValidatePlugin::PLUGIN_NAME, Dependence::dependenceConfig(PropertyValidatePlugin::class));
    }

    public function __validate()
    {
        $validateResult = new ValidateResult();
        $properties = $this->memberIterator();
        foreach ($properties as $property) {
            $propertyValidateResult = $property->validate();
            if (!$propertyValidateResult->valid()) {
                $validateResult->valid(false);
                foreach ($propertyValidateResult->errors() as $error) {
                    if ($error instanceof Error) {
                        $error->prototype()->properties()->config('property', Dependence::dependenceConfig(Value::class));
                        $error->property = $property->name;
                    }
                    $validateResult->errors()->add($error);
                }
            }
        }
        return $validateResult;
    }


    public function __propertyValidate()
    {
        return $this->validator()->validate($this->toData());
    }

    public function __propertyValidator()
    {
        if ($this->__validator === null) {
            $this->__validator = new ValidateManager();
            $this->__validator->configs($this->validator);
        }
        return $this->__validator;
    }
}