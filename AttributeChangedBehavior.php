<?php
/**
 * @license MIT
 * @author denis909 <denis909@mail.ru>
 * @link http://denis909.spb.ru
 */
namespace denis909\yii;

use yii\db\ActiveRecord;

class AttributeChangedBehavior extends \yii\base\Behavior
{

	public $attributeEvents = [];

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

		foreach($this->attributeEvents as $attr => $eventName)
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

					$this->owner->trigger($eventName, $e);
				}
			}
		}
	}

}