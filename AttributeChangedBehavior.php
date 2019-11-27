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

    public function events()
    {
        return [   	
            ActiveRecord::EVENT_AFTER_INSERT => 'afterSave',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterSave'
        ];
    }

	public function afterSave($event)
	{
		$oldValues = $event->changedAttributes;

		$currentValues = $event->sender->oldAttributes;

        $eventName = $this->event;

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

                    if ($eventName instanceof Closure)
                    {
                        $eventName($e);
                    }
                    else
                    {
					   $this->owner->trigger($eventName, $e);
                    }
				}
			}
		}
	}

}