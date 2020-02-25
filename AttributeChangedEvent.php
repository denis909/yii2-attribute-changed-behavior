<?php
/**
 * @license MIT
 * @author denis909 <denis909@mail.ru>
 * @link http://denis909.spb.ru
 */
namespace denis909\yii;

class AttributeChangedEvent extends \yii\base\Event
{

    public $attribute;

    public $value;

    public $oldValue;

}