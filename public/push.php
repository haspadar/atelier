<?php
if (isset($_POST['host']) && isset($_POST['ip']) && isset($_POST['project'])) {
    var_dump($_POST);
} else {
    http_response_code(400);
}
