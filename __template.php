<?php
include('__template_api.php');

ExecAPI(XXX::class,
        $_GET,
        API::FETCH_ALL,
        USER_AWARE
);
