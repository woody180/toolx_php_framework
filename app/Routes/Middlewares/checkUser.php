<?php

function checkUser() {
    helpers(['Auth/checkAuth']);
    
    if (!checkAuth([1, 2, 3])) return abort();
}