<?php
if (!isset($num)) return false;
$r = floatval($num);
if ($r >= 9)
    return 'Превосходно';
else if ($r >= 8)
    return 'Отлично';
else if ($r >= 7.5)
    return 'Очень хорошо';
else if ($r >= 7)
    return 'Хорошо';
else
    return 'Плохо';
