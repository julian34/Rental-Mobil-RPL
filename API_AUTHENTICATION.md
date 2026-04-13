# Car Rental API - Authentication & Role-Based Access Control

## Overview
The Car Rental API uses custom token-based authentication with role-based access control (RBAC). Five roles are available with different permission levels.

## Available Roles

1. **Administrator** - Full system access with all permissions
2. **Owner** - Business owner with business management access
3. **Cashier** - Handle payment and transaction processing
4. **Staff** - General staff with limited access
5. **Customer** - Regular customer with booking access

## Test Users

| Email | Password | Role |
|-------|----------|------|
| admin@rental-mobil.test | password123 | Administrator |
| owner@rental-mobil.test | password123 | Owner |
| cashier@rental-mobil.test | password123 | Cashier |
| staff@rental-mobil.test | password123 | Staff |
| customer@rental-mobil.test | password123 | Customer |

## API Endpoints

### Authentication Endpoints (Public)

#### Login
```
POST /api/auth/login
Content-Type: application/json

{
  "email": "admin@rental-mobil.test",
  "password": "password123"
}

Response (200 OK):
{
  "message": "Login successful",
  "user": {
    "id": 1,
    "name": "Administrator",
    "email": "admin@rental-mobil.test",
    "roles": [...]
  },
  "token": "MbI3ZsXyXlPIxnjT9qjAlQNStVlFCP6eF3GGg8r83RaC799pOHDL0JxVWBrUHIcUQKQKr9dP1UsRA9K0"
}
```

#### Register
```
POST /api/auth/register
Content-Type: application/json

{
  "name": "New User",
  "email": "newuser@rental-mobil.test",
  "password": "password123",
  "password_confirmation": "password123"
}

Response (201 Created):
{
  "message": "Registration successful",
  "user": { ... },
  "token": "..."
}
```

### Protected Endpoints (Require Authentication)

Add header: `Authorization: Bearer {token}`

#### Get Current User Info
```
GET /api/auth/me
Authorization: Bearer {token}

Response (200 OK):
{
  "user": {
    "id": 1,
    "name": "Administrator",
    "email": "admin@rental-mobil.test",
    "roles": [...]
  }
}
```

#### Logout
```
POST /api/auth/logout
Authorization: Bearer {token}

Response (200 OK):
{
  "message": "Logged out successfully"
}
```

#### Assign Role to User
```
POST /api/users/{user_id}/assign-role
Authorization: Bearer {token}
Content-Type: application/json

{
  "role_name": "Owner"
}

Response (200 OK):
{
  "message": "Role assigned to user",
  "user": { ... }
}
```

#### Remove Role from User
```
POST /api/users/{user_id}/remove-role
Authorization: Bearer {token}
Content-Type: application/json

{
  "role_name": "Owner"
}

Response (200 OK):
{
  "message": "Role removed from user",
  "user": { ... }
}
```

#### Get User Roles
```
GET /api/users/{user_id}/roles
Authorization: Bearer {token}

Response (200 OK):
{
  "roles": [...]
}
```

### Admin-Only Endpoints (Requires Administrator Role)

#### Get All Roles
```
GET /api/roles
Authorization: Bearer {token}

Response (200 OK):
{
  "roles": [
    {
      "id": 1,
      "name": "Administrator",
      "description": "Full system access with all permissions",
      "permissions": [...]
    },
    ...
  ]
}
```

#### Get Role Details
```
GET /api/roles/{role_id}
Authorization: Bearer {token}

Response (200 OK):
{
  "role": {
    "id": 1,
    "name": "Administrator",
    "description": "Full system access with all permissions",
    "users": [...],
    "permissions": [...]
  }
}
```

#### Create Role
```
POST /api/roles
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Manager",
  "description": "Manager role description"
}

Response (201 Created):
{
  "message": "Role created successfully",
  "role": { ... }
}
```

#### Update Role
```
PUT /api/roles/{role_id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Manager",
  "description": "Updated description"
}

Response (200 OK):
{
  "message": "Role updated successfully",
  "role": { ... }
}
```

#### Delete Role
```
DELETE /api/roles/{role_id}
Authorization: Bearer {token}

Response (200 OK):
{
  "message": "Role deleted successfully"
}
```

#### Assign Permission to Role
```
POST /api/roles/{role_id}/permissions
Authorization: Bearer {token}
Content-Type: application/json

{
  "permission_id": 1
}

Response (200 OK):
{
  "message": "Permission assigned to role",
  "role": { ... }
}
```

#### Remove Permission from Role
```
DELETE /api/roles/{role_id}/permissions
Authorization: Bearer {token}
Content-Type: application/json

{
  "permission_id": 1
}

Response (200 OK):
{
  "message": "Permission removed from role",
  "role": { ... }
}
```

## Authentication Flow

1. **Register** - Create new user account with `/api/auth/register`
2. **Login** - Authenticate with `/api/auth/login` to receive token
3. **Use Token** - Include token in `Authorization: Bearer {token}` header for protected endpoints
4. **Role-Based Access** - Admin endpoints automatically check user role and return 403 Forbidden if insufficient privileges
5. **Logout** - Clear token with `/api/auth/logout`

## Response Codes

- **200 OK** - Successful request
- **201 Created** - Successful creation
- **400 Bad Request** - Invalid input
- **401 Unauthorized** - Missing or invalid token
- **403 Forbidden** - Insufficient role/permissions
- **404 Not Found** - Resource not found
- **422 Unprocessable Entity** - Validation failed
- **500 Internal Server Error** - Server error

## Error Handling

All errors follow this format:

```json
{
  "message": "Error description",
  "errors": {
    "field": ["Error message"]
  }
}
```

## Database Schema

### Users Table
- id (Primary Key)
- name
- email (Unique)
- password (Hashed)
- api_token (Unique, Nullable)
- created_at, updated_at

### Roles Table
- id (Primary Key)
- name (Unique)
- description
- created_at, updated_at

### Permissions Table
- id (Primary Key)
- name (Unique)
- description
- created_at, updated_at

### Pivot Tables
- role_user (user_id, role_id) - Many-to-Many relationship
- role_permission (role_id, permission_id) - Many-to-Many relationship

## Testing the API

### Quick Test with Administrator
```bash
# Login
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@rental-mobil.test","password":"password123"}'

# Save the token from response
TOKEN="your_token_here"

# Get current user
curl -X GET http://localhost:8000/api/auth/me \
  -H "Authorization: Bearer $TOKEN"

# Get all roles (admin only)
curl -X GET http://localhost:8000/api/roles \
  -H "Authorization: Bearer $TOKEN"

# Logout
curl -X POST http://localhost:8000/api/auth/logout \
  -H "Authorization: Bearer $TOKEN"
```

### Testing Role-Based Access
```bash
# Login as customer
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"customer@rental-mobil.test","password":"password123"}'

# Try to access admin endpoint (should get 403 Forbidden)
curl -X GET http://localhost:8000/api/roles \
  -H "Authorization: Bearer $CUSTOMER_TOKEN"
```

## Implementation Details

### Custom Token Authentication
- Tokens stored in `api_token` column on users table
- 80-character random tokens generated using `Str::random(80)`
- Token extracted from `Authorization: Bearer {token}` header
- Middleware `AuthenticateToken` handles token validation

### Role-Based Access Control
- Middleware `CheckRole` validates user roles against protected routes
- Routes configured with `check.role:RoleName` middleware
- Multiple roles supported: `check.role:Administrator,Owner`
- Returns 403 Forbidden for insufficient privileges

### Database Relationships
- User → Many Roles (through role_user pivot)
- Role → Many Users (through role_user pivot)
- Role → Many Permissions (through role_permission pivot)
- Permission → Many Roles (through role_permission pivot)

## Future Enhancements

1. Permission-based access control alongside roles
2. Token expiration and refresh tokens
3. Rate limiting on authentication endpoints
4. Audit logging for authentication events
5. Two-factor authentication support
6. OAuth2 integration
