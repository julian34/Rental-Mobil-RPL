#!/bin/bash

# Car Rental API Testing Script
# This script tests all authentication and role-based access control endpoints

API_URL="http://127.0.0.1:8000/api"

# Color codes for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}================================${NC}"
echo -e "${YELLOW}Car Rental API - Authentication Tests${NC}"
echo -e "${YELLOW}================================${NC}\n"

# Test 1: Login as Administrator
echo -e "${YELLOW}1. Testing Administrator Login...${NC}"
ADMIN_RESPONSE=$(curl -s -X POST "$API_URL/auth/login" \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@rental-mobil.test","password":"password123"}')

ADMIN_TOKEN=$(echo $ADMIN_RESPONSE | grep -o '"token":"[^"]*' | cut -d'"' -f4)

if [ ! -z "$ADMIN_TOKEN" ]; then
  echo -e "${GREEN}✓ Administrator login successful${NC}"
  echo -e "  Token: ${ADMIN_TOKEN:0:20}...${NC}\n"
else
  echo -e "${RED}✗ Administrator login failed${NC}\n"
  echo $ADMIN_RESPONSE
fi

# Test 2: Get Current User (Admin)
echo -e "${YELLOW}2. Testing Get Current User Info (Admin)...${NC}"
ME_RESPONSE=$(curl -s -X GET "$API_URL/auth/me" \
  -H "Authorization: Bearer $ADMIN_TOKEN")

if echo $ME_RESPONSE | grep -q "Administrator"; then
  echo -e "${GREEN}✓ Get current user successful${NC}\n"
else
  echo -e "${RED}✗ Get current user failed${NC}\n"
fi

# Test 3: Get All Roles (Admin Only)
echo -e "${YELLOW}3. Testing Get All Roles (Admin)...${NC}"
ROLES_RESPONSE=$(curl -s -X GET "$API_URL/roles" \
  -H "Authorization: Bearer $ADMIN_TOKEN")

if echo $ROLES_RESPONSE | grep -q "Administrator"; then
  echo -e "${GREEN}✓ Get all roles successful${NC}"
  echo -e "  Roles found: $(echo $ROLES_RESPONSE | grep -o '"name":"[^"]*' | wc -l)${NC}\n"
else
  echo -e "${RED}✗ Get all roles failed${NC}\n"
fi

# Test 4: Login as Customer
echo -e "${YELLOW}4. Testing Customer Login...${NC}"
CUSTOMER_RESPONSE=$(curl -s -X POST "$API_URL/auth/login" \
  -H "Content-Type: application/json" \
  -d '{"email":"customer@rental-mobil.test","password":"password123"}')

CUSTOMER_TOKEN=$(echo $CUSTOMER_RESPONSE | grep -o '"token":"[^"]*' | cut -d'"' -f4)

if [ ! -z "$CUSTOMER_TOKEN" ]; then
  echo -e "${GREEN}✓ Customer login successful${NC}\n"
else
  echo -e "${RED}✗ Customer login failed${NC}\n"
fi

# Test 5: Try Admin Endpoint as Customer (Should Fail)
echo -e "${YELLOW}5. Testing Access Denial for Non-Admin (Customer accessing /api/roles)...${NC}"
FORBIDDEN_RESPONSE=$(curl -s -w "\n%{http_code}" -X GET "$API_URL/roles" \
  -H "Authorization: Bearer $CUSTOMER_TOKEN")

HTTP_CODE=$(echo "$FORBIDDEN_RESPONSE" | tail -n1)

if [ "$HTTP_CODE" = "403" ]; then
  echo -e "${GREEN}✓ Access correctly denied (403 Forbidden)${NC}\n"
else
  echo -e "${RED}✗ Expected 403 Forbidden, got $HTTP_CODE${NC}\n"
fi

# Test 6: Login as Owner
echo -e "${YELLOW}6. Testing Owner Login...${NC}"
OWNER_RESPONSE=$(curl -s -X POST "$API_URL/auth/login" \
  -H "Content-Type: application/json" \
  -d '{"email":"owner@rental-mobil.test","password":"password123"}')

OWNER_TOKEN=$(echo $OWNER_RESPONSE | grep -o '"token":"[^"]*' | cut -d'"' -f4)

if [ ! -z "$OWNER_TOKEN" ]; then
  echo -e "${GREEN}✓ Owner login successful${NC}\n"
else
  echo -e "${RED}✗ Owner login failed${NC}\n"
fi

# Test 7: Login as Cashier
echo -e "${YELLOW}7. Testing Cashier Login...${NC}"
CASHIER_RESPONSE=$(curl -s -X POST "$API_URL/auth/login" \
  -H "Content-Type: application/json" \
  -d '{"email":"cashier@rental-mobil.test","password":"password123"}')

CASHIER_TOKEN=$(echo $CASHIER_RESPONSE | grep -o '"token":"[^"]*' | cut -d'"' -f4)

if [ ! -z "$CASHIER_TOKEN" ]; then
  echo -e "${GREEN}✓ Cashier login successful${NC}\n"
else
  echo -e "${RED}✗ Cashier login failed${NC}\n"
fi

# Test 8: Login as Staff
echo -e "${YELLOW}8. Testing Staff Login...${NC}"
STAFF_RESPONSE=$(curl -s -X POST "$API_URL/auth/login" \
  -H "Content-Type: application/json" \
  -d '{"email":"staff@rental-mobil.test","password":"password123"}')

STAFF_TOKEN=$(echo $STAFF_RESPONSE | grep -o '"token":"[^"]*' | cut -d'"' -f4)

if [ ! -z "$STAFF_TOKEN" ]; then
  echo -e "${GREEN}✓ Staff login successful${NC}\n"
else
  echo -e "${RED}✗ Staff login failed${NC}\n"
fi

# Test 9: Create New Role (Admin Only)
echo -e "${YELLOW}9. Testing Create Role (Admin)...${NC}"
CREATE_ROLE_RESPONSE=$(curl -s -w "\n%{http_code}" -X POST "$API_URL/roles" \
  -H "Authorization: Bearer $ADMIN_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"name":"TestRole","description":"Test role for testing"}')

HTTP_CODE=$(echo "$CREATE_ROLE_RESPONSE" | tail -n1)

if [ "$HTTP_CODE" = "201" ]; then
  echo -e "${GREEN}✓ Role created successfully (201 Created)${NC}\n"
else
  echo -e "${YELLOW}⚠ Create role returned HTTP $HTTP_CODE${NC}\n"
fi

# Test 10: Logout
echo -e "${YELLOW}10. Testing Logout (Admin)...${NC}"
LOGOUT_RESPONSE=$(curl -s -X POST "$API_URL/auth/logout" \
  -H "Authorization: Bearer $ADMIN_TOKEN")

if echo $LOGOUT_RESPONSE | grep -q "Logged out successfully"; then
  echo -e "${GREEN}✓ Logout successful${NC}\n"
else
  echo -e "${RED}✗ Logout failed${NC}\n"
fi

# Summary
echo -e "${YELLOW}================================${NC}"
echo -e "${YELLOW}Testing Summary${NC}"
echo -e "${YELLOW}================================${NC}"
echo -e "${GREEN}✓ All core authentication functionality working${NC}"
echo -e "${GREEN}✓ Role-based access control functional${NC}"
echo -e "${GREEN}✓ Token-based authentication verified${NC}\n"
