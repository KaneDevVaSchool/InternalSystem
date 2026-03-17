# PM Manage - Modular Monolith Architecture

## Project Structure

```
app/
├── Core/                    # Shared abstractions, interfaces
│   └── Contracts/
│       └── RepositoryInterface.php
├── Modules/
│   ├── Auth/                # Authentication module
│   │   ├── Http/
│   │   │   ├── Controllers/Api/
│   │   │   └── Requests/
│   │   ├── Jobs/
│   │   └── Services/
│   ├── User/                # User-facing module
│   │   ├── DTO/
│   │   ├── Http/Controllers/Api/
│   │   ├── Policies/
│   │   ├── Repositories/
│   │   └── Services/
│   └── Admin/                # Admin module
│       ├── DTO/
│       ├── Http/Controllers/Api/
│       ├── Repositories/
│       └── Services/
├── Infrastructure/          # External implementations
│   └── OAuth/Google/
│       ├── DTO/
│       ├── Exceptions/
│       └── GoogleAuthService.php
├── Application/             # CQRS (Commands/Queries)
│   ├── Commands/
│   └── Queries/
└── Http/
    └── Middleware/
```

## Google OAuth Login Flow

1. **Frontend** obtains ID token from Google Sign-In / One Tap
2. **POST /api/auth/google** with `{ "id_token": "..." }`
3. **GoogleAuthService** verifies token via Google API Client
4. Validates: `email_verified=true`, `hd` (domain) matches `GOOGLE_ALLOWED_DOMAIN`
5. **AuthService** creates/updates user, assigns role (admin if in whitelist, else user)
6. **Sanctum** issues bearer token
7. Response: `{ "user": {...}, "token": "..." }`

## API Endpoints

### Auth (Public)
- `POST /api/auth/google` – Login with Google ID token (rate limited: 10/min)

### Auth (Protected)
- `POST /api/auth/logout` – Revoke token
- `GET /api/auth/me` – Current user

### User Module (role: user|admin)
- `GET /api/user/profile` – User profile

### Admin Module (role: admin)
- `GET /api/admin/stats` – Dashboard stats

## Middleware

- `auth:sanctum` – API token authentication
- `role:user|admin` – Role-based access (pipe for multiple roles)
- `throttle:login` – 10 requests/min per IP for login

## Environment Variables

```env
GOOGLE_CLIENT_ID=xxx.apps.googleusercontent.com
GOOGLE_ALLOWED_DOMAIN=your-domain.com
ADMIN_EMAILS=admin@your-domain.com,other@your-domain.com
```

## Performance

- **File cache only** (no Redis)
- `php artisan config:cache`
- `php artisan route:cache`
- `php artisan view:cache`
- Eager loading in repositories

## Security

- Force HTTPS (production)
- CSRF (web)
- Rate limiting on login
- Domain restriction via `hd` claim
- XSS-safe (Blade)
- SQL injection safe (Eloquent)
