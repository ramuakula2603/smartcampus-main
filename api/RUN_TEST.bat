@echo off
echo ================================================================================
echo   OTHER COLLECTION REPORT API - COMPREHENSIVE TEST
echo ================================================================================
echo.
echo Opening test in your default browser...
echo.
echo This will:
echo   - Check if payment 945 exists in database
echo   - Verify all filter values
echo   - Test 5 filter combinations
echo   - Identify which filter is removing payment 945
echo   - Provide detailed analysis and recommendations
echo.
echo ================================================================================
echo.

start http://localhost/amt/api/RUN_THIS_TEST.php

echo.
echo Test opened in browser!
echo.
echo After reviewing the results, you can also run:
echo   - Test Runner: http://localhost/amt/api/test_runner.html
echo   - API Endpoint Test: http://localhost/amt/api/test_api_endpoint.php
echo.
echo ================================================================================
pause

