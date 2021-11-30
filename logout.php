<?php

include 'boot.php';
session_destroy();
header('Location: login.php');
exit;