# Dynamicfield

**This module is working, but in a very alpha version. Using is at your own risk!**
**You are welcome to work on it**

## Introduction
To make it work, you need to alter the Page Module.

in: 
Page/Resources/views/admin/create.blade.php
Page/Resources/views/admin/edit.blade.php

add to @section('style')
Line ~17
```php
	@if($dynamicFieldCss)
		{!! $dynamicFieldCss !!}
	@endif
```

add to @section('scripts')
Line ~90
```php
	@if($dynamicFieldCss)
		{!! $dynamicFieldCss !!}
	@endif
```

in: 
Page/Resources/views/admin/create-fields.blade.php 	Line ~19
Page/Resources/views/admin/edit-fields.blade.php 	Line ~24

above <div class="box-group" id="accordion">

```php
    @if($dynamicfield)
        {!! $dynamicfield->render($lang) !!}
    @endif
```

## Usage

Creat a new Dynamicfield and select on the right side on which Page Template you wanna use it.

After you've created your first Dynamicfield, you can go to the Page create/edit view, select your desired Template and then, the Dynamicfield should appear automatically.

If nothing happens, make sure the dynamic-fields.js is loaded. Clear Cache and run the "module:publish dynamicfield" command
