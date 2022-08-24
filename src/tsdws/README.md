# TSDSystem web service

## Environment variables to set

To control debug level:
- `ENV` ('*development*' or '*production*')

Secret key to generate/decode JWT tokens:
- `SERVER_KEY` (string)

Admin (super-user) settings:
- `ADMIN_ID` (administrator user identifier - choose any integer <= 0)
- `ADMIN_EMAIL` (used also as recipient for email communication - i.e. on members registration)
- `ADMIN_PASSWORD` (coupled with `ADMIN_EMAIL` to access the web interface as administrator)

### Optionals

SMTP settings (for emails sending):
- `SMTP_HOST`
- `SMTP_USERNAME`
- `SMTP_PASSWORD`
- `SMTP_AUTH`
- `SMTP_SECURE`

Regexp pattern for users' email registration:
- `REG_PATTERN` (PCRE2 [PHP >=7.3] - if not set, it allows any string)

To enable requests originated from other websites:
- `Access-Control-Allow-Origin` (string - ex. all: '*' or a list of addresses: '127.0.0.1, 192.168.1.1, etc.')