<?php
/**
 * @license MIT
 * @author denis909 <denis909@mail.ru>
 * @link http://denis909.spb.ru
 */
namespace denis909\yii;

use Closure;
use yii\db\ActiveRecord;
use yii\db\Expression;

class AttributeChangedBehavior extends \yii\base\Behavior
{

    public $attributes = [];

    public $event;

    public $onInsert = true;

    public $onUpdate = true;

    public function events()
    {
        return [    
            ActiveRecord::EVENT_AFTER_INSERT => 'afterInsert',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterUpdate'
        ];
    }

    public function afterInsert($event)
    {
        if ($this->onInsert)
        {
            $this->attributeChanged($event);
        }
    }

    public function afterUpdate($event)
    {
        if ($this->onUpdate)
        {
            $this->attributeChanged($event);
        }
    }

    public function attributeChanged($event)
    {
        $oldValues = $event->changedAttributes;

        $currentValues = $event->sender->oldAttributes;

        $event = $this->event;

        foreach($oldValues as $attr => $value)
        {
            if ($value == $currentValues[$attr])
            {
                continue; // attribute not changed
            }

            if (count($this->attributes) > 0)
            {
                if (array_search($attr, $this->attributes) === false)
                {
                    continue;
                }
            }

            $e = new AttributeChangedEvent;

            $e->sender = $this->owner;

            $e->attribute = (string) $attr;

            $e->oldValue = (string) $oldValues[$attr];

            $e->value = (string) $currentValues[$attr];

            if ($event instanceof Closure)
            {
                $event($e);
            }
            else
            {
                if (is_callable($event))
                {
                    call_user_func($event, $e);
                }
                else
                {
                    $this->owner->trigger($event, $e);
                }
            }
        }
    }

}