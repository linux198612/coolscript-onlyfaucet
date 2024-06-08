<?php
session_start(); // Munkamenet kezdése vagy folytatása

// Munkamenet törlése a felhasználó kijelentkeztetésekor
session_unset();
session_destroy();

// Átirányítás a bejelentkezési oldalra vagy más elérhető oldalra
header("Location: ./");
exit;
?>