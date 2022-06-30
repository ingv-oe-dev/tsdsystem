# TSDSystem web service

## Environment variables to set:

To control debug level:
- ENV (`development` or `production`)

Secret key to generate JWT tokens:
- SERVER_KEY (string)

Admin (super-user) settings:
- ADMIN_ID (integer <= 0)
- ADMIN_EMAIL
- ADMIN_PASSWORD

### Optionals:

SMTP settings (for emails sending)
- SMTP_HOST
- SMTP_USERNAME
- SMTP_PASSWORD
- SMTP_AUTH
- SMTP_SECURE

Other:
- PUBLIC_URL (of the web service)