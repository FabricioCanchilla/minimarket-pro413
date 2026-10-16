<?php
// URL base del backend (API REST). Se puede cambiar con la variable de entorno API_URL.
define('API_URL', rtrim(getenv('API_URL') ?: 'http://localhost:8080', '/'));