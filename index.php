<?php
require 'functions.php';

if (isAdmin()) {
    header('Location: admin.php');
} elseif (isLogged()) {
    header('Location: cabinet.php');
} else {
    header('Location: login.php');
}
