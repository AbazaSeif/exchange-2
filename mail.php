<?php
echo 111;
mail('dddd@mail.ru', 'My Subject', 'dddddddddddddd');
error_reporting(E_ALL);
ini_set('display_errors', '1');
if (mail('nusno@mail.ru', 'My Subject', '11111 Content 11111')) {
      echo "ok";
} else {
       echo "error";
}

