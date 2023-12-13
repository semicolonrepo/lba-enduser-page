<?php

function formatCurrency($value, $beforeValue = 'Rp', $afterValue = '') {
  $output = $beforeValue . number_format($value, 0, ',', '.') . $afterValue;

  return $output;
}
