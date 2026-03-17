# Frontend Integration – Google Login

## Obtaining the ID Token

### Option 1: Google One Tap
```html
<script src="https://accounts.google.com/gsi/client" async defer></script>
<div id="g_id_onload"
     data-client_id="YOUR_GOOGLE_CLIENT_ID"
     data-callback="handleCredentialResponse">
</div>
<script>
function handleCredentialResponse(response) {
  const idToken = response.credential;
  // POST idToken to your backend
  fetch('/api/auth/google', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ id_token: idToken })
  })
  .then(r => r.json())
  .then(data => {
    localStorage.setItem('token', data.token);
    // Handle success
  });
}
</script>
```

### Option 2: Google Sign-In Button
```javascript
// Initialize
google.accounts.id.initialize({
  client_id: 'YOUR_GOOGLE_CLIENT_ID',
  callback: (response) => {
    const idToken = response.credential;
    // POST to backend as above
  }
});
google.accounts.id.prompt();
```

## Backend Request

```javascript
const response = await fetch('https://your-api.com/api/auth/google', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
  body: JSON.stringify({ id_token: idToken })
});

const { user, token } = await response.json();
```

## Using the Token

```javascript
// All subsequent requests
fetch('/api/user/profile', {
  headers: {
    'Authorization': `Bearer ${token}`,
    'Accept': 'application/json',
  }
});
```
