<?php

foreach (glob(base_path('Modules/*/Routes/api.php')) as $routeFile) {
    require $routeFile;
}
