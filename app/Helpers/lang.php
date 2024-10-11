<?php

function lang(string $text) {
    if ($text === 'active') return App\Engine\Libraries\Languages::active();
    if ($text === 'list') return App\Engine\Libraries\Languages::list();
    return App\Engine\Libraries\Languages::translate($text);
}