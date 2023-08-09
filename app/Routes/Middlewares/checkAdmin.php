<?php

function checkAdmin() {
    helpers(['Auth/checkAuth']);
    
    if (!checkAuth([1])) return abort();
}