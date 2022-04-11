--------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------
-- GRANT privileges to webapp_user

GRANT CREATE ON DATABASE tsdsytem TO webapp_user;

GRANT ALL PRIVILEGES ON DATABASE tsdsystem TO webapp_user;

GRANT SELECT, INSERT, UPDATE ON ALL TABLES IN SCHEMA tsd_main, tsd_pnet, tsd_users TO webapp_user;

GRANT USAGE ON ALL SEQUENCES IN SCHEMA tsd_main, tsd_pnet, tsd_users to webapp_user;