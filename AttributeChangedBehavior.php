<?php
/**
 * @license MIT
 * @author denis909 <denis909@mail.ru>
 * @link http://denis909.spb.ru
 */
namespace denis909\yii;

use Closure;
use yii\db\ActiveRecord;

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
            $this->attributeChanged();
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

        foreach($this->attributes as $attr)
        {
            if (array_key_exists($attr, $oldValues))
            {
                if ($oldValues[$attr] != $currentValues[$attr])
                {
                    $e = new AttributeChangedEvent;

                    $e->sender = $this->owner;

                    $e->attribute = $attr;

                    $e->oldValue = $oldValues[$attr];

                    $e->value = $currentValues[$attr];

                    if ($event instanceof Closure)
                    {
                        $event($e);
                    }
                    else
                    {
                       $this->owner->trigger($event, $e);
                    }
                }
            }
        }
    }

}