#!/bin/bash

# Test script for Combined Collection Report API fixes
# Date: 2025-10-11

BASE_URL="http://localhost/amt/api/combined-collection-report"
HEADERS='-H "Content-Type: application/json" -H "Client-Service: smartschool" -H "Auth-Key: schoolAdmin@"'

echo "=========================================="
echo "Combined Collection Report API - Fix Tests"
echo "=========================================="
echo ""

# Test 1: Verify correct amount calculation
echo "Test 1: Verify Correct Amount Calculation"
echo "------------------------------------------"
echo "Request: Get this month's collections"
echo ""
curl -X POST "${BASE_URL}/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"search_type": "this_month"}' \
  2>/dev/null | python -m json.tool | grep -A 10 "summary"
echo ""
echo "✓ Check: grand_total = total_amount + total_fine - total_discount"
echo ""
echo ""

# Test 2: Verify other fees are included
echo "Test 2: Verify Other Fees Are Included"
echo "---------------------------------------"
echo "Request: Get this year's collections"
echo ""
curl -X POST "${BASE_URL}/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"search_type": "this_year"}' \
  2>/dev/null | python -m json.tool | grep -E "(regular_fees_count|other_fees_count|total_records)"
echo ""
echo "✓ Check: other_fees_count > 0 (if other fees exist in database)"
echo "✓ Check: total_records = regular_fees_count + other_fees_count"
echo ""
echo ""

# Test 3: Verify feetype_id filter is ignored
echo "Test 3: Verify feetype_id Filter Is Ignored"
echo "--------------------------------------------"
echo "Request 1: With feetype_id=5"
RESULT1=$(curl -X POST "${BASE_URL}/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"search_type": "this_year", "feetype_id": 5}' \
  2>/dev/null | python -m json.tool | grep "total_records" | head -1)
echo "$RESULT1"
echo ""

echo "Request 2: Without feetype_id"
RESULT2=$(curl -X POST "${BASE_URL}/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"search_type": "this_year"}' \
  2>/dev/null | python -m json.tool | grep "total_records" | head -1)
echo "$RESULT2"
echo ""
echo "✓ Check: Both requests should return the SAME total_records"
echo ""
echo ""

# Test 4: Verify grouped results calculate correctly
echo "Test 4: Verify Grouped Results Calculate Correctly"
echo "---------------------------------------------------"
echo "Request: Group by class"
echo ""
curl -X POST "${BASE_URL}/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"search_type": "this_month", "group": "class"}' \
  2>/dev/null | python -m json.tool | grep -A 6 "subtotal"
echo ""
echo "✓ Check: subtotal_total = subtotal_amount + subtotal_fine - subtotal_discount"
echo ""
echo ""

# Test 5: Verify feetype_id shows 'all' in response
echo "Test 5: Verify feetype_id Shows 'all' in Response"
echo "--------------------------------------------------"
echo "Request: Check filters_applied"
echo ""
curl -X POST "${BASE_URL}/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"search_type": "this_month"}' \
  2>/dev/null | python -m json.tool | grep -A 10 "filters_applied"
echo ""
echo "✓ Check: feetype_id should be 'all'"
echo ""
echo ""

echo "=========================================="
echo "All tests completed!"
echo "=========================================="
echo ""
echo "Manual Verification Steps:"
echo "1. Check that grand_total = total_amount + total_fine - total_discount"
echo "2. Check that other_fees_count > 0 (if other fees exist)"
echo "3. Check that both requests with/without feetype_id return same count"
echo "4. Check that grouped subtotals calculate correctly"
echo "5. Check that feetype_id is always 'all' in response"
echo ""

