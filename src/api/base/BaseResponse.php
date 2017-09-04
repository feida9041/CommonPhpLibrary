<?php
/**
 * Created by PhpStorm.
 * User: luhaoz
 * Date: 2017/5/17
 * Time: 12:00
 */

namespace luhaoz\cpl\api\base;

use luhaoz\cpl\dependence\Dependence;
use luhaoz\cpl\prototype\property\plugin\filter\base\BaseFilter;
use luhaoz\cpl\prototype\property\types\Value;
use luhaoz\cpl\prototype\traits\Prototype;

/**
 * Class BaseResponse
 * @package luhaoz\cpl\api\base
 */
class BaseResponse
{
    use Prototype;
    const RESPONSE_TYPE = '';

    protected function _constructed(\luhaoz\cpl\prototype\Prototype $prototype)
    {
        $prototype->properties()->configs(array_merge([
            'type' => Dependence::dependenceConfig(Value::class, ['default' => static::RESPONSE_TYPE]),
            'code' => Dependence::dependenceConfig(Value::class, ['default' => 200]),
        ], $this->properties()));
    }

    public function properties()
    {
        return [];
    }

    public function toData()
    {
        return $this->prototype()->properties()->filter(BaseFilter::instantiate())->values();
    }
}