<?php

/*
 * Trigger Class if run terminal command $: composer call-app
 * Also see in composer.json "scripts":{<...>}
*/

class AppLoadComposerHelper extends Index
{
    public static function argsHandlerComposer()
    {
        new Index();
    }

}