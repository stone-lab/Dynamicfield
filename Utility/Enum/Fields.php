<?php

namespace Modules\Dynamicfield\Utility\Enum;

abstract class Fields extends BasicEnum
{
    const TEXT = 'Text';
    const NUMBER = 'Number';
    const TEXTAREA = 'TextArea';
    const WYSIWYG = 'Wysiwyg Editor';
    const FILE = 'File';
    const IMAGE = 'Image';
    const REPEATER = 'Repeater';
}
