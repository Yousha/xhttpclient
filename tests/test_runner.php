<?php

/**
 * Test runner.
 */
function run_tests()
{
   $test_files = glob('tests/*.phpt');
   $passed = 0;
   $failed = 0;

   foreach ($test_files as $file) {
      echo "Running $file...\n";
      $output = array();
      $return_var = 0;

      // Execute the test file.
      exec("php $file", $output, $return_var);

      // Check if test passed.
      if ($return_var === 0) {
         echo "PASSED\n";
         $passed++;
      } else {
         echo "FAILED\n";
         $failed++;
      }

      // Display test output.
      echo implode("\n", $output) . "\n\n";
   }

   echo "\nResults: $passed passed, $failed failed\n";
   exit($failed > 0 ? 1 : 0);
}

run_tests();
