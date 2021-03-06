<?php
/**
 * Created by PhpStorm.
 * User: luhaoz
 * Date: 2017/8/3
 * Time: 10:21
 */


namespace luhaoz\cpl\prototype;

use luhaoz\cpl\dependence\Dependence;
use luhaoz\cpl\event\traits\Event;
use luhaoz\cpl\prototype\behavior\BehaviorManager;
use luhaoz\cpl\prototype\behavior\types\Behavior;
use luhaoz\cpl\prototype\method\MethodManager;
use luhaoz\cpl\prototype\plugin\PluginManager;
use luhaoz\cpl\prototype\property\PropertyManager;
use luhaoz\cpl\pubsub\traits\PubSub;
use luhaoz\cpl\reflection\ReflectionClass;

/**
 * Class Prototype
 * @package luhaoz\cpl\prototype
 */
class Prototype
{
    use \luhaoz\cpl\prototype\traits\Prototype;
    use PubSub;
    use Event;
    protected $_owner = null;
    protected $_reflection = null;
    protected $_propertyManager = null;
    protected $_methodManager = null;
    protected $_pluginManager = null;
    protected $_behaviorManager = null;

    public function owner($owner = null)
    {
        if ($owner !== null) {
            $this->_owner = $owner;
        }
        return $this->_owner;
    }

    /**
     * @return ReflectionClass
     */
    public function reflection()
    {
        if ($this->_reflection === null) {
            $this->_reflection = new ReflectionClass(get_class($this->owner()));
        }
        return $this->_reflection;
    }


    /**
     * @return PropertyManager
     */
    public function properties($properties = null)
    {
        if ($properties instanceof PropertyManager) {
            $this->_propertyManager = $properties;
        }

        if ($this->_propertyManager === null) {
            $this->_propertyManager = new PropertyManager();
            $this->_propertyManager->owner($this->owner());
        }
        return $this->_propertyManager;
    }

    /**
     * @return MethodManager
     */
    public function methods($methods = null)
    {
        if ($methods instanceof MethodManager) {
            $this->_methodManager = $methods;
        }
        if ($this->_methodManager === null) {
            $this->_methodManager = new MethodManager();
            $this->_methodManager->owner($this->owner());
        }
        return $this->_methodManager;
    }

    /**
     * @return PluginManager
     */
    public function plugins($plugins = null)
    {
        if ($plugins instanceof PluginManager) {
            $this->_pluginManager = $plugins;
        }
        if ($this->_pluginManager === null) {
            $this->_pluginManager = new PluginManager();
            $this->_pluginManager->owner($this->owner());
        }
        return $this->_pluginManager;
    }

    /**
     * @return BehaviorManager
     */
    public function behaviors($behaviors = null)
    {
        if ($behaviors instanceof BehaviorManager) {
            $this->_behaviorManager = $behaviors;
        }
        if ($this->_behaviorManager === null) {
            $this->_behaviorManager = new BehaviorManager();
            $this->_behaviorManager->owner($this->owner());
            $this->_behaviorManager->configs([
                '__set'             => Dependence::dependenceConfig(Behavior::class, [
                    'behavior' => function ($name, $value) {
                        return $this->prototype()->properties()->property($name)->set($value);
                    },
                ]),
                '__get'             => Dependence::dependenceConfig(Behavior::class, [
                    'behavior' => function ($name) {
                        return $this->prototype()->properties()->property($name)->get();
                    },
                ]),
                '__call'            => Dependence::dependenceConfig(Behavior::class, [
                    'behavior' => function ($name, $arguments) {
                        return $this->prototype()->methods()->method($name)->callArray($arguments);
                    },
                ]),
                '__property_exists' => Dependence::dependenceConfig(Behavior::class, [
                    'behavior' => function ($name) {
                        return $this->prototype()->properties()->is($name);
                    },
                ]),
                '__method_exists'   => Dependence::dependenceConfig(Behavior::class, [
                    'behavior' => function ($name) {
                        return $this->prototype()->methods()->is($name);
                    },
                ]),
            ]);
        }
        return $this->_behaviorManager;
    }
}
