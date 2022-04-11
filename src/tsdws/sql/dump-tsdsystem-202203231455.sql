--
-- PostgreSQL database dump
--

-- Dumped from database version 13.4
-- Dumped by pg_dump version 13.4

-- Started on 2022-03-23 14:55:35

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- TOC entry 14 (class 2615 OID 18692)
-- Name: tsd_main; Type: SCHEMA; Schema: -; Owner: postgres
--

CREATE SCHEMA tsd_main;


ALTER SCHEMA tsd_main OWNER TO postgres;

--
-- TOC entry 15 (class 2615 OID 18731)
-- Name: tsd_pnet; Type: SCHEMA; Schema: -; Owner: postgres
--

CREATE SCHEMA tsd_pnet;


ALTER SCHEMA tsd_pnet OWNER TO postgres;

--
-- TOC entry 16 (class 2615 OID 18781)
-- Name: tsd_users; Type: SCHEMA; Schema: -; Owner: postgres
--

CREATE SCHEMA tsd_users;


ALTER SCHEMA tsd_users OWNER TO postgres;

--
-- TOC entry 1172 (class 1255 OID 18728)
-- Name: updateTimeseriesLastTime(character varying, character varying); Type: PROCEDURE; Schema: tsd_main; Owner: postgres
--

CREATE PROCEDURE tsd_main."updateTimeseriesLastTime"(my_schema character varying DEFAULT NULL::character varying, my_name character varying DEFAULT NULL::character varying)
    LANGUAGE plpgsql
    AS $$
BEGIN
    EXECUTE CONCAT('UPDATE tsd_main.timeseries SET last_time = (
      SELECT LAST(time, time) 
      FROM ', my_schema, '.', my_name, 
    ') WHERE schema = ', quote_literal(my_schema), 
     ' AND name = ' , quote_literal(my_name)
    ); 
END;
$$;


ALTER PROCEDURE tsd_main."updateTimeseriesLastTime"(my_schema character varying, my_name character varying) OWNER TO postgres;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- TOC entry 258 (class 1259 OID 18704)
-- Name: timeseries; Type: TABLE; Schema: tsd_main; Owner: postgres
--

CREATE TABLE tsd_main.timeseries (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    schema character varying(63) NOT NULL,
    name character varying(63) NOT NULL,
    sampling integer,
    metadata jsonb,
    last_time timestamp without time zone,
    create_time timestamp without time zone DEFAULT timezone('utc'::text, now()),
    update_time timestamp without time zone,
    remove_time timestamp without time zone,
    create_user integer,
    update_user integer,
    remove_user integer
);


ALTER TABLE tsd_main.timeseries OWNER TO postgres;

--
-- TOC entry 259 (class 1259 OID 18723)
-- Name: timeseries_mapping_channels; Type: TABLE; Schema: tsd_main; Owner: postgres
--

CREATE TABLE tsd_main.timeseries_mapping_channels (
    timeseries_id uuid NOT NULL,
    channel_id integer NOT NULL
);


ALTER TABLE tsd_main.timeseries_mapping_channels OWNER TO postgres;

--
-- TOC entry 269 (class 1259 OID 19042)
-- Name: channels; Type: TABLE; Schema: tsd_pnet; Owner: postgres
--

CREATE TABLE tsd_pnet.channels (
    id integer NOT NULL,
    name character varying(255) NOT NULL,
    sensor_id integer,
    info jsonb,
    create_time timestamp without time zone DEFAULT timezone('utc'::text, now()),
    update_time timestamp without time zone,
    remove_time timestamp without time zone,
    create_user integer,
    update_user integer,
    remove_user integer
);


ALTER TABLE tsd_pnet.channels OWNER TO postgres;

--
-- TOC entry 270 (class 1259 OID 19049)
-- Name: channels_id_seq; Type: SEQUENCE; Schema: tsd_pnet; Owner: postgres
--

CREATE SEQUENCE tsd_pnet.channels_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE tsd_pnet.channels_id_seq OWNER TO postgres;

--
-- TOC entry 4350 (class 0 OID 0)
-- Dependencies: 270
-- Name: channels_id_seq; Type: SEQUENCE OWNED BY; Schema: tsd_pnet; Owner: postgres
--

ALTER SEQUENCE tsd_pnet.channels_id_seq OWNED BY tsd_pnet.channels.id;


--
-- TOC entry 271 (class 1259 OID 19051)
-- Name: nets; Type: TABLE; Schema: tsd_pnet; Owner: postgres
--

CREATE TABLE tsd_pnet.nets (
    id integer NOT NULL,
    name character varying(255) NOT NULL,
    owner_id integer,
    create_time timestamp without time zone DEFAULT timezone('utc'::text, now()),
    update_time timestamp without time zone,
    remove_time timestamp without time zone,
    create_user integer,
    update_user integer,
    remove_user integer
);


ALTER TABLE tsd_pnet.nets OWNER TO postgres;

--
-- TOC entry 272 (class 1259 OID 19055)
-- Name: nets_id_seq; Type: SEQUENCE; Schema: tsd_pnet; Owner: postgres
--

CREATE SEQUENCE tsd_pnet.nets_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE tsd_pnet.nets_id_seq OWNER TO postgres;

--
-- TOC entry 4351 (class 0 OID 0)
-- Dependencies: 272
-- Name: nets_id_seq; Type: SEQUENCE OWNED BY; Schema: tsd_pnet; Owner: postgres
--

ALTER SEQUENCE tsd_pnet.nets_id_seq OWNED BY tsd_pnet.nets.id;


--
-- TOC entry 273 (class 1259 OID 19057)
-- Name: owners; Type: TABLE; Schema: tsd_pnet; Owner: postgres
--

CREATE TABLE tsd_pnet.owners (
    id integer NOT NULL,
    name character varying(255) NOT NULL,
    create_time timestamp without time zone DEFAULT timezone('utc'::text, now()),
    update_time timestamp without time zone,
    remove_time timestamp without time zone,
    create_user integer,
    update_user integer,
    remove_user integer
);


ALTER TABLE tsd_pnet.owners OWNER TO postgres;

--
-- TOC entry 274 (class 1259 OID 19061)
-- Name: owners_id_seq; Type: SEQUENCE; Schema: tsd_pnet; Owner: postgres
--

CREATE SEQUENCE tsd_pnet.owners_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE tsd_pnet.owners_id_seq OWNER TO postgres;

--
-- TOC entry 4352 (class 0 OID 0)
-- Dependencies: 274
-- Name: owners_id_seq; Type: SEQUENCE OWNED BY; Schema: tsd_pnet; Owner: postgres
--

ALTER SEQUENCE tsd_pnet.owners_id_seq OWNED BY tsd_pnet.owners.id;


--
-- TOC entry 275 (class 1259 OID 19063)
-- Name: sensors; Type: TABLE; Schema: tsd_pnet; Owner: postgres
--

CREATE TABLE tsd_pnet.sensors (
    id integer NOT NULL,
    name character varying(255) NOT NULL,
    coords public.geometry,
    quote real,
    metadata jsonb,
    custom_props jsonb,
    sensortype_id integer,
    net_id integer,
    site_id integer,
    create_time timestamp without time zone DEFAULT timezone('utc'::text, now()),
    update_time timestamp without time zone,
    remove_time timestamp without time zone,
    create_user integer,
    update_user integer,
    remove_user integer
);


ALTER TABLE tsd_pnet.sensors OWNER TO postgres;

--
-- TOC entry 276 (class 1259 OID 19070)
-- Name: sensors_id_seq; Type: SEQUENCE; Schema: tsd_pnet; Owner: postgres
--

CREATE SEQUENCE tsd_pnet.sensors_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE tsd_pnet.sensors_id_seq OWNER TO postgres;

--
-- TOC entry 4353 (class 0 OID 0)
-- Dependencies: 276
-- Name: sensors_id_seq; Type: SEQUENCE OWNED BY; Schema: tsd_pnet; Owner: postgres
--

ALTER SEQUENCE tsd_pnet.sensors_id_seq OWNED BY tsd_pnet.sensors.id;


--
-- TOC entry 277 (class 1259 OID 19072)
-- Name: sensortypes; Type: TABLE; Schema: tsd_pnet; Owner: postgres
--

CREATE TABLE tsd_pnet.sensortypes (
    id integer NOT NULL,
    name character varying(255) NOT NULL,
    json_schema jsonb,
    create_time timestamp without time zone DEFAULT timezone('utc'::text, now()),
    update_time timestamp without time zone,
    remove_time timestamp without time zone,
    create_user integer,
    update_user integer,
    remove_user integer
);


ALTER TABLE tsd_pnet.sensortypes OWNER TO postgres;

--
-- TOC entry 278 (class 1259 OID 19079)
-- Name: sensortypes_id_seq; Type: SEQUENCE; Schema: tsd_pnet; Owner: postgres
--

CREATE SEQUENCE tsd_pnet.sensortypes_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE tsd_pnet.sensortypes_id_seq OWNER TO postgres;

--
-- TOC entry 4354 (class 0 OID 0)
-- Dependencies: 278
-- Name: sensortypes_id_seq; Type: SEQUENCE OWNED BY; Schema: tsd_pnet; Owner: postgres
--

ALTER SEQUENCE tsd_pnet.sensortypes_id_seq OWNED BY tsd_pnet.sensortypes.id;


--
-- TOC entry 279 (class 1259 OID 19081)
-- Name: sites; Type: TABLE; Schema: tsd_pnet; Owner: postgres
--

CREATE TABLE tsd_pnet.sites (
    id integer NOT NULL,
    name character varying(255) NOT NULL,
    coords public.geometry,
    quote real,
    info jsonb,
    create_time timestamp without time zone DEFAULT timezone('utc'::text, now()),
    update_time timestamp without time zone,
    remove_time timestamp without time zone,
    create_user integer,
    update_user integer,
    remove_user integer
);


ALTER TABLE tsd_pnet.sites OWNER TO postgres;

--
-- TOC entry 280 (class 1259 OID 19088)
-- Name: sites_id_seq; Type: SEQUENCE; Schema: tsd_pnet; Owner: postgres
--

CREATE SEQUENCE tsd_pnet.sites_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE tsd_pnet.sites_id_seq OWNER TO postgres;

--
-- TOC entry 4355 (class 0 OID 0)
-- Dependencies: 280
-- Name: sites_id_seq; Type: SEQUENCE OWNED BY; Schema: tsd_pnet; Owner: postgres
--

ALTER SEQUENCE tsd_pnet.sites_id_seq OWNED BY tsd_pnet.sites.id;


--
-- TOC entry 261 (class 1259 OID 18784)
-- Name: members; Type: TABLE; Schema: tsd_users; Owner: postgres
--

CREATE TABLE tsd_users.members (
    id integer NOT NULL,
    email character varying(255) NOT NULL,
    password character(128) DEFAULT NULL::bpchar,
    salt character(128) DEFAULT NULL::bpchar,
    deleted timestamp without time zone,
    registered timestamp without time zone DEFAULT timezone('utc'::text, now()),
    confirmed timestamp without time zone
);


ALTER TABLE tsd_users.members OWNER TO postgres;

--
-- TOC entry 260 (class 1259 OID 18782)
-- Name: members_id_seq; Type: SEQUENCE; Schema: tsd_users; Owner: postgres
--

CREATE SEQUENCE tsd_users.members_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE tsd_users.members_id_seq OWNER TO postgres;

--
-- TOC entry 4356 (class 0 OID 0)
-- Dependencies: 260
-- Name: members_id_seq; Type: SEQUENCE OWNED BY; Schema: tsd_users; Owner: postgres
--

ALTER SEQUENCE tsd_users.members_id_seq OWNED BY tsd_users.members.id;


--
-- TOC entry 264 (class 1259 OID 18897)
-- Name: members_mapping_roles; Type: TABLE; Schema: tsd_users; Owner: postgres
--

CREATE TABLE tsd_users.members_mapping_roles (
    member_id integer NOT NULL,
    role_id integer NOT NULL
);


ALTER TABLE tsd_users.members_mapping_roles OWNER TO postgres;

--
-- TOC entry 266 (class 1259 OID 18904)
-- Name: members_permissions; Type: TABLE; Schema: tsd_users; Owner: postgres
--

CREATE TABLE tsd_users.members_permissions (
    id integer NOT NULL,
    member_id integer NOT NULL,
    settings jsonb,
    active boolean,
    create_time timestamp without time zone DEFAULT timezone('utc'::text, now()),
    update_time timestamp without time zone,
    remove_time timestamp without time zone
);


ALTER TABLE tsd_users.members_permissions OWNER TO postgres;

--
-- TOC entry 265 (class 1259 OID 18902)
-- Name: members_permissions_id_seq; Type: SEQUENCE; Schema: tsd_users; Owner: postgres
--

CREATE SEQUENCE tsd_users.members_permissions_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE tsd_users.members_permissions_id_seq OWNER TO postgres;

--
-- TOC entry 4357 (class 0 OID 0)
-- Dependencies: 265
-- Name: members_permissions_id_seq; Type: SEQUENCE OWNED BY; Schema: tsd_users; Owner: postgres
--

ALTER SEQUENCE tsd_users.members_permissions_id_seq OWNED BY tsd_users.members_permissions.id;


--
-- TOC entry 263 (class 1259 OID 18863)
-- Name: roles; Type: TABLE; Schema: tsd_users; Owner: postgres
--

CREATE TABLE tsd_users.roles (
    id integer NOT NULL,
    name character varying(255) NOT NULL,
    description text,
    create_time timestamp without time zone DEFAULT timezone('utc'::text, now()),
    update_time timestamp without time zone,
    remove_time timestamp without time zone
);


ALTER TABLE tsd_users.roles OWNER TO postgres;

--
-- TOC entry 262 (class 1259 OID 18861)
-- Name: roles_id_seq; Type: SEQUENCE; Schema: tsd_users; Owner: postgres
--

CREATE SEQUENCE tsd_users.roles_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE tsd_users.roles_id_seq OWNER TO postgres;

--
-- TOC entry 4358 (class 0 OID 0)
-- Dependencies: 262
-- Name: roles_id_seq; Type: SEQUENCE OWNED BY; Schema: tsd_users; Owner: postgres
--

ALTER SEQUENCE tsd_users.roles_id_seq OWNED BY tsd_users.roles.id;


--
-- TOC entry 268 (class 1259 OID 18918)
-- Name: roles_permissions; Type: TABLE; Schema: tsd_users; Owner: postgres
--

CREATE TABLE tsd_users.roles_permissions (
    id integer NOT NULL,
    role_id integer NOT NULL,
    settings jsonb,
    active boolean,
    create_time timestamp without time zone DEFAULT timezone('utc'::text, now()),
    update_time timestamp without time zone,
    remove_time timestamp without time zone
);


ALTER TABLE tsd_users.roles_permissions OWNER TO postgres;

--
-- TOC entry 267 (class 1259 OID 18916)
-- Name: roles_permissions_id_seq; Type: SEQUENCE; Schema: tsd_users; Owner: postgres
--

CREATE SEQUENCE tsd_users.roles_permissions_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE tsd_users.roles_permissions_id_seq OWNER TO postgres;

--
-- TOC entry 4359 (class 0 OID 0)
-- Dependencies: 267
-- Name: roles_permissions_id_seq; Type: SEQUENCE OWNED BY; Schema: tsd_users; Owner: postgres
--

ALTER SEQUENCE tsd_users.roles_permissions_id_seq OWNED BY tsd_users.roles_permissions.id;


--
-- TOC entry 282 (class 1259 OID 19131)
-- Name: tokens; Type: TABLE; Schema: tsd_users; Owner: postgres
--

CREATE TABLE tsd_users.tokens (
    id integer NOT NULL,
    token text,
    remote_addr character varying(255) NOT NULL,
    create_time timestamp without time zone DEFAULT timezone('utc'::text, now())
);


ALTER TABLE tsd_users.tokens OWNER TO postgres;

--
-- TOC entry 281 (class 1259 OID 19129)
-- Name: tokens_id_seq; Type: SEQUENCE; Schema: tsd_users; Owner: postgres
--

CREATE SEQUENCE tsd_users.tokens_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE tsd_users.tokens_id_seq OWNER TO postgres;

--
-- TOC entry 4360 (class 0 OID 0)
-- Dependencies: 281
-- Name: tokens_id_seq; Type: SEQUENCE OWNED BY; Schema: tsd_users; Owner: postgres
--

ALTER SEQUENCE tsd_users.tokens_id_seq OWNED BY tsd_users.tokens.id;


--
-- TOC entry 4124 (class 2604 OID 19090)
-- Name: channels id; Type: DEFAULT; Schema: tsd_pnet; Owner: postgres
--

ALTER TABLE ONLY tsd_pnet.channels ALTER COLUMN id SET DEFAULT nextval('tsd_pnet.channels_id_seq'::regclass);


--
-- TOC entry 4126 (class 2604 OID 19091)
-- Name: nets id; Type: DEFAULT; Schema: tsd_pnet; Owner: postgres
--

ALTER TABLE ONLY tsd_pnet.nets ALTER COLUMN id SET DEFAULT nextval('tsd_pnet.nets_id_seq'::regclass);


--
-- TOC entry 4128 (class 2604 OID 19092)
-- Name: owners id; Type: DEFAULT; Schema: tsd_pnet; Owner: postgres
--

ALTER TABLE ONLY tsd_pnet.owners ALTER COLUMN id SET DEFAULT nextval('tsd_pnet.owners_id_seq'::regclass);


--
-- TOC entry 4130 (class 2604 OID 19093)
-- Name: sensors id; Type: DEFAULT; Schema: tsd_pnet; Owner: postgres
--

ALTER TABLE ONLY tsd_pnet.sensors ALTER COLUMN id SET DEFAULT nextval('tsd_pnet.sensors_id_seq'::regclass);


--
-- TOC entry 4132 (class 2604 OID 19094)
-- Name: sensortypes id; Type: DEFAULT; Schema: tsd_pnet; Owner: postgres
--

ALTER TABLE ONLY tsd_pnet.sensortypes ALTER COLUMN id SET DEFAULT nextval('tsd_pnet.sensortypes_id_seq'::regclass);


--
-- TOC entry 4134 (class 2604 OID 19095)
-- Name: sites id; Type: DEFAULT; Schema: tsd_pnet; Owner: postgres
--

ALTER TABLE ONLY tsd_pnet.sites ALTER COLUMN id SET DEFAULT nextval('tsd_pnet.sites_id_seq'::regclass);


--
-- TOC entry 4114 (class 2604 OID 18787)
-- Name: members id; Type: DEFAULT; Schema: tsd_users; Owner: postgres
--

ALTER TABLE ONLY tsd_users.members ALTER COLUMN id SET DEFAULT nextval('tsd_users.members_id_seq'::regclass);


--
-- TOC entry 4120 (class 2604 OID 18907)
-- Name: members_permissions id; Type: DEFAULT; Schema: tsd_users; Owner: postgres
--

ALTER TABLE ONLY tsd_users.members_permissions ALTER COLUMN id SET DEFAULT nextval('tsd_users.members_permissions_id_seq'::regclass);


--
-- TOC entry 4118 (class 2604 OID 18866)
-- Name: roles id; Type: DEFAULT; Schema: tsd_users; Owner: postgres
--

ALTER TABLE ONLY tsd_users.roles ALTER COLUMN id SET DEFAULT nextval('tsd_users.roles_id_seq'::regclass);


--
-- TOC entry 4122 (class 2604 OID 18921)
-- Name: roles_permissions id; Type: DEFAULT; Schema: tsd_users; Owner: postgres
--

ALTER TABLE ONLY tsd_users.roles_permissions ALTER COLUMN id SET DEFAULT nextval('tsd_users.roles_permissions_id_seq'::regclass);


--
-- TOC entry 4136 (class 2604 OID 19134)
-- Name: tokens id; Type: DEFAULT; Schema: tsd_users; Owner: postgres
--

ALTER TABLE ONLY tsd_users.tokens ALTER COLUMN id SET DEFAULT nextval('tsd_users.tokens_id_seq'::regclass);


--
-- TOC entry 4320 (class 0 OID 18704)
-- Dependencies: 258
-- Data for Name: timeseries; Type: TABLE DATA; Schema: tsd_main; Owner: postgres
--

COPY tsd_main.timeseries (id, schema, name, sampling, metadata, last_time, create_time, update_time, remove_time, create_user, update_user, remove_user) FROM stdin;
f838f90f-d899-47bb-877f-bdbe69330bc1	test	weather_metrics	600	[{"name": "temp_c", "type": "double precision"}, {"name": "pressure_hpa", "type": "double precision"}, {"name": "humidity_percent", "type": "double precision"}, {"name": "wind_speed_ms", "type": "double precision"}]	2022-01-04 15:28:00	2022-02-09 14:44:33.633358	\N	\N	\N	\N	\N
5750fdc2-8ddc-46ec-ad45-ef41c3ec6d68	test	test	60	\N	\N	2022-03-10 14:08:04.992209	\N	\N	\N	\N	\N
\.


--
-- TOC entry 4321 (class 0 OID 18723)
-- Dependencies: 259
-- Data for Name: timeseries_mapping_channels; Type: TABLE DATA; Schema: tsd_main; Owner: postgres
--

COPY tsd_main.timeseries_mapping_channels (timeseries_id, channel_id) FROM stdin;
f838f90f-d899-47bb-877f-bdbe69330bc1	1
f838f90f-d899-47bb-877f-bdbe69330bc1	4
\.


--
-- TOC entry 4331 (class 0 OID 19042)
-- Dependencies: 269
-- Data for Name: channels; Type: TABLE DATA; Schema: tsd_pnet; Owner: postgres
--

COPY tsd_pnet.channels (id, name, sensor_id, info, create_time, update_time, remove_time, create_user, update_user, remove_user) FROM stdin;
1	EHZ	1	\N	2022-02-04 14:16:11.735728	\N	\N	\N	\N	\N
2	EHN	1	\N	2022-02-04 14:16:19.96655	\N	\N	\N	\N	\N
3	EHE	1	\N	2022-02-04 14:16:23.795494	\N	\N	\N	\N	\N
4	EHZ	2	\N	2022-02-04 14:34:12.773792	\N	\N	\N	\N	\N
5	EHN	2	\N	2022-02-04 14:34:12.773792	\N	\N	\N	\N	\N
6	EHE	2	\N	2022-02-04 14:34:12.773792	\N	\N	\N	\N	\N
7	EHZ	3	\N	2022-02-04 14:34:12.773792	\N	\N	\N	\N	\N
8	EHN	3	\N	2022-02-04 14:34:12.773792	\N	\N	\N	\N	\N
9	EHE	3	\N	2022-02-04 14:34:12.773792	\N	\N	\N	\N	\N
10	EHZ	4	\N	2022-02-04 14:34:12.773792	\N	\N	\N	\N	\N
11	EHN	4	\N	2022-02-04 14:34:12.773792	\N	\N	\N	\N	\N
12	EHE	4	\N	2022-02-04 14:34:12.773792	\N	\N	\N	\N	\N
13	EHZ	5	\N	2022-02-04 14:34:12.773792	\N	\N	\N	\N	\N
14	EHN	5	\N	2022-02-04 14:34:12.773792	\N	\N	\N	\N	\N
15	EHE	5	\N	2022-02-04 14:34:12.773792	\N	\N	\N	\N	\N
16	EHZ	6	\N	2022-02-04 14:34:12.773792	\N	\N	\N	\N	\N
17	EHN	6	\N	2022-02-04 14:34:12.773792	\N	\N	\N	\N	\N
18	EHE	6	\N	2022-02-04 14:34:12.773792	\N	\N	\N	\N	\N
19	EHZ	7	\N	2022-02-04 14:34:12.773792	\N	\N	\N	\N	\N
20	EHN	7	\N	2022-02-04 14:34:12.773792	\N	\N	\N	\N	\N
21	EHE	7	\N	2022-02-04 14:34:12.773792	\N	\N	\N	\N	\N
22	EHZ	8	\N	2022-02-04 14:34:12.773792	\N	\N	\N	\N	\N
23	EHN	8	\N	2022-02-04 14:34:12.773792	\N	\N	\N	\N	\N
24	EHE	8	\N	2022-02-04 14:34:12.773792	\N	\N	\N	\N	\N
25	EHZ	9	\N	2022-02-04 14:34:12.773792	\N	\N	\N	\N	\N
26	EHN	9	\N	2022-02-04 14:34:12.773792	\N	\N	\N	\N	\N
27	EHE	9	\N	2022-02-04 14:34:12.773792	\N	\N	\N	\N	\N
28	EHZ	10	\N	2022-02-04 14:34:12.773792	\N	\N	\N	\N	\N
29	EHN	10	\N	2022-02-04 14:34:12.773792	\N	\N	\N	\N	\N
30	EHE	10	\N	2022-02-04 14:34:12.773792	\N	\N	\N	\N	\N
31	EHZ	11	\N	2022-02-04 14:34:12.773792	\N	\N	\N	\N	\N
32	EHN	11	\N	2022-02-04 14:34:12.773792	\N	\N	\N	\N	\N
33	EHE	11	\N	2022-02-04 14:34:12.773792	\N	\N	\N	\N	\N
34	EHZ	12	\N	2022-02-04 14:34:12.773792	\N	\N	\N	\N	\N
35	EHN	12	\N	2022-02-04 14:34:12.773792	\N	\N	\N	\N	\N
36	EHE	12	\N	2022-02-04 14:34:12.773792	\N	\N	\N	\N	\N
37	EHZ	13	\N	2022-02-04 14:34:12.773792	\N	\N	\N	\N	\N
38	EHN	13	\N	2022-02-04 14:34:12.773792	\N	\N	\N	\N	\N
39	EHE	13	\N	2022-02-04 14:34:12.773792	\N	\N	\N	\N	\N
40	EHZ	14	\N	2022-02-04 14:42:54.852555	\N	\N	\N	\N	\N
41	EHN	14	\N	2022-02-04 14:42:54.852555	\N	\N	\N	\N	\N
42	EHE	14	\N	2022-02-04 14:42:54.852555	\N	\N	\N	\N	\N
43	EHZ	15	\N	2022-02-04 14:42:54.852555	\N	\N	\N	\N	\N
44	EHN	15	\N	2022-02-04 14:42:54.852555	\N	\N	\N	\N	\N
45	EHE	15	\N	2022-02-04 14:42:54.852555	\N	\N	\N	\N	\N
46	EHZ	16	\N	2022-02-04 14:42:54.852555	\N	\N	\N	\N	\N
47	EHN	16	\N	2022-02-04 14:42:54.852555	\N	\N	\N	\N	\N
48	EHE	16	\N	2022-02-04 14:42:54.852555	\N	\N	\N	\N	\N
49	EHZ	17	\N	2022-02-04 14:42:54.852555	\N	\N	\N	\N	\N
50	EHN	17	\N	2022-02-04 14:42:54.852555	\N	\N	\N	\N	\N
51	EHE	17	\N	2022-02-04 14:42:54.852555	\N	\N	\N	\N	\N
\.


--
-- TOC entry 4333 (class 0 OID 19051)
-- Dependencies: 271
-- Data for Name: nets; Type: TABLE DATA; Schema: tsd_pnet; Owner: postgres
--

COPY tsd_pnet.nets (id, name, owner_id, create_time, update_time, remove_time, create_user, update_user, remove_user) FROM stdin;
1	Seismic	\N	2022-02-04 14:57:41.91819	\N	\N	\N	\N	\N
\.


--
-- TOC entry 4335 (class 0 OID 19057)
-- Dependencies: 273
-- Data for Name: owners; Type: TABLE DATA; Schema: tsd_pnet; Owner: postgres
--

COPY tsd_pnet.owners (id, name, create_time, update_time, remove_time, create_user, update_user, remove_user) FROM stdin;
\.


--
-- TOC entry 4337 (class 0 OID 19063)
-- Dependencies: 275
-- Data for Name: sensors; Type: TABLE DATA; Schema: tsd_pnet; Owner: postgres
--

COPY tsd_pnet.sensors (id, name, coords, quote, metadata, custom_props, sensortype_id, net_id, site_id, create_time, update_time, remove_time, create_user, update_user, remove_user) FROM stdin;
1	STR1	0101000000A69BC420B0722E40598638D6C5654340	560.7	\N	\N	1	1	\N	2022-02-04 13:59:13.645443	\N	\N	\N	\N	\N
2	STR3	01010000004BEA043411762E40AED85F764F664340	235.9	\N	\N	1	1	\N	2022-02-04 14:19:14.570472	\N	\N	\N	\N	\N
3	STR4	01010000003F355EBA496C2E4089D2DEE00B634340	86.5	\N	\N	1	1	\N	2022-02-04 14:22:35.110289	\N	\N	\N	\N	\N
4	STR5	01010000007FD93D7958682E404D158C4AEA644340	653.6	\N	\N	1	1	\N	2022-02-04 14:22:58.407605	\N	\N	\N	\N	\N
5	STR6	01010000003BDF4F8D976E2E40696FF085C9644340	807.8	\N	\N	1	1	\N	2022-02-04 14:23:18.341414	\N	\N	\N	\N	\N
6	STR8	010100000039454772F96F2E40D8F0F44A59664340	569.2	\N	\N	1	1	\N	2022-02-04 14:23:42.470703	\N	\N	\N	\N	\N
7	STR9	01010000007C613255306A2E40CCEEC9C342654340	781.9	\N	\N	1	1	\N	2022-02-04 14:24:08.952379	\N	\N	\N	\N	\N
8	STRA	010100000057EC2FBB276F2E40BDE3141DC9654340	842.8	\N	\N	1	1	\N	2022-02-04 14:24:33.023306	\N	\N	\N	\N	\N
9	STRB	01010000002B1895D409682E40CCEEC9C342654340	632	\N	\N	1	1	\N	2022-02-04 14:25:56.263067	\N	\N	\N	\N	\N
10	STRC	0101000000C3D32B6519622E406891ED7C3F654340	183.5	\N	\N	1	1	\N	2022-02-04 14:26:53.696251	\N	\N	\N	\N	\N
11	STRD	0101000000E63FA4DFBE6E2E40DCD7817346644340	559	\N	\N	1	1	\N	2022-02-04 14:27:12.444407	\N	\N	\N	\N	\N
12	STRE	0101000000CA32C4B12E6E2E40BB270F0BB5664340	435.8	\N	\N	1	1	\N	2022-02-04 14:27:34.174965	\N	\N	\N	\N	\N
13	STRG	010100000020D26F5F076E2E40BA6B09F9A0674340	128	\N	\N	1	1	\N	2022-02-04 14:27:54.774388	\N	\N	\N	\N	\N
14	IST3	01010000002EFF21FDF6752E404A7B832F4C664340	255	\N	\N	1	1	\N	2022-02-04 14:37:24.822637	\N	\N	\N	\N	\N
15	ISTP	0101000000CB10C7BAB86D2E4005C58F3177654340	962	\N	\N	1	1	\N	2022-02-04 14:38:13.646156	\N	\N	\N	\N	\N
16	ISTR	0101000000E0BE0E9C33622E40B1E1E995B2644340	114	\N	\N	1	1	\N	2022-02-04 14:39:01.458941	\N	\N	\N	\N	\N
17	SVO	\N	\N	\N	\N	1	1	\N	2022-02-04 14:41:27.726719	\N	\N	\N	\N	\N
\.


--
-- TOC entry 4339 (class 0 OID 19072)
-- Dependencies: 277
-- Data for Name: sensortypes; Type: TABLE DATA; Schema: tsd_pnet; Owner: postgres
--

COPY tsd_pnet.sensortypes (id, name, json_schema, create_time, update_time, remove_time, create_user, update_user, remove_user) FROM stdin;
1	Velocimeter	\N	2022-02-04 14:58:08.458761	\N	\N	\N	\N	\N
\.


--
-- TOC entry 4341 (class 0 OID 19081)
-- Dependencies: 279
-- Data for Name: sites; Type: TABLE DATA; Schema: tsd_pnet; Owner: postgres
--

COPY tsd_pnet.sites (id, name, coords, quote, info, create_time, update_time, remove_time, create_user, update_user, remove_user) FROM stdin;
\.


--
-- TOC entry 4323 (class 0 OID 18784)
-- Dependencies: 261
-- Data for Name: members; Type: TABLE DATA; Schema: tsd_users; Owner: postgres
--

COPY tsd_users.members (id, email, password, salt, deleted, registered, confirmed) FROM stdin;
1	tsd_ov@ingv.it	c35c331d6212e50514dc5861cd28e09ffa646c701e572d0d7b8e8413503911e4c5500de7077429c443135081b398d72bdf42d0f5bc9e9a192ad70ebadd115514	28f84608d8cb4df4fe1cb5c354223545a9adc11f6e3fae30060a0e96fc96db4276cc0fb9ed41e0d044e42286d9448e8787c11ab2bb15c013240e1c01e63c0b72	\N	2022-01-19 14:29:44.078613	2022-01-19 14:29:44.078
\.


--
-- TOC entry 4326 (class 0 OID 18897)
-- Dependencies: 264
-- Data for Name: members_mapping_roles; Type: TABLE DATA; Schema: tsd_users; Owner: postgres
--

COPY tsd_users.members_mapping_roles (member_id, role_id) FROM stdin;
1	1
\.


--
-- TOC entry 4328 (class 0 OID 18904)
-- Dependencies: 266
-- Data for Name: members_permissions; Type: TABLE DATA; Schema: tsd_users; Owner: postgres
--

COPY tsd_users.members_permissions (id, member_id, settings, active, create_time, update_time, remove_time) FROM stdin;
\.


--
-- TOC entry 4325 (class 0 OID 18863)
-- Dependencies: 263
-- Data for Name: roles; Type: TABLE DATA; Schema: tsd_users; Owner: postgres
--

COPY tsd_users.roles (id, name, description, create_time, update_time, remove_time) FROM stdin;
1	INGV_OV	INGV_OV permissions to write timeseries	2022-02-01 14:44:28.783879	\N	\N
\.


--
-- TOC entry 4330 (class 0 OID 18918)
-- Dependencies: 268
-- Data for Name: roles_permissions; Type: TABLE DATA; Schema: tsd_users; Owner: postgres
--

COPY tsd_users.roles_permissions (id, role_id, settings, active, create_time, update_time, remove_time) FROM stdin;
1	1	{"resources": {"nets": {"edit": {"ip": [], "enabled": false, "permissions": {"id": null}}, "read": {"ip": [], "enabled": true}}, "sites": {"edit": {"ip": [], "enabled": true, "permissions": {"id": null}}, "read": {"ip": [], "enabled": true}}, "owners": {"edit": {"ip": [], "enabled": true, "permissions": {"id": null}}, "read": {"ip": [], "enabled": true}}, "sensors": {"edit": {"ip": [], "enabled": true, "permissions": {"id": null, "net_id": null}}, "read": {"ip": [], "enabled": true}}, "channels": {"edit": {"ip": [], "enabled": true, "permissions": {"id": null, "net_id": null, "sensor_id": null}}, "read": {"ip": [], "enabled": true}}, "timeseries": {"edit": {"ip": [], "enabled": true, "permissions": {"id": ["f838f90f-d899-47bb-877f-bdbe69330bc1"], "net_id": null, "sensor_id": null, "channel_id": null}}, "read": {"ip": [], "enabled": true, "permissions": {"id": {}, "all": {"last_days": true, "end_period": null, "start_period": null, "number_of_days": 1}, "net_id": {"1": {"last_days": true, "end_period": null, "start_period": null, "number_of_days": 999}, "2": {"last_days": true, "end_period": null, "start_period": null, "number_of_days": 7}, "3": {"last_days": true, "end_period": null, "start_period": null, "number_of_days": 30}}, "sensor_id": {}, "channel_id": {}}}}, "sensortypes": {"edit": {"ip": [], "enabled": true, "permissions": {"id": null}}, "read": {"ip": [], "enabled": true}}}}	t	2022-02-01 16:23:04.239178	\N	\N
\.


--
-- TOC entry 4344 (class 0 OID 19131)
-- Dependencies: 282
-- Data for Name: tokens; Type: TABLE DATA; Schema: tsd_users; Owner: postgres
--

COPY tsd_users.tokens (id, token, remote_addr, create_time) FROM stdin;
1	eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VySWQiOjEsInJpZ2h0cyI6eyJyZXNvdXJjZXMiOnsibmV0cyI6eyJyZWFkIjp7ImlwIjpbXSwiZW5hYmxlZCI6dHJ1ZX19fX0sIm5iZiI6MTY0NDM1NzIwNiwiZXhwIjoxNjQ2OTQ5MjA2fQ.nL8AZguZfZ9bmTzFHX8V1l6NLmpPrIsq0da5z96IoKM	::1	2022-02-08 21:53:26.117669
2	eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VySWQiOjEsInJpZ2h0cyI6eyJyZXNvdXJjZXMiOnsidGltZXNlcmllcyI6eyJlZGl0Ijp7ImlwIjpbXSwiZW5hYmxlZCI6dHJ1ZSwicGVybWlzc2lvbnMiOnsiaWQiOm51bGwsIm5ldF9pZCI6bnVsbCwic2Vuc29yX2lkIjpudWxsLCJjaGFubmVsX2lkIjpudWxsfX19fX0sIm5iZiI6MTY0NDQwNjg3NSwiZXhwIjoxNjQ2OTk4ODc1fQ.qAU9mrvuCB08-X38omCnwgq4UxYvwuBkUMBLMCjP1bA	::1	2022-02-09 11:41:15.733181
3	eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VySWQiOjEsIm5iZiI6MTY0NDQwNzc2OCwiZXhwIjoxNjQ2OTk5NzY4fQ.N-WiAvcgeoTOw77cDhuAi3X_6MMK4yNOB7oQKyUQgbo	::1	2022-02-09 11:56:08.808849
4	eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VySWQiOjEsInJpZ2h0cyI6eyJyZXNvdXJjZXMiOnsidGltZXNlcmllcyI6eyJlZGl0Ijp7ImlwIjpbXSwiZW5hYmxlZCI6dHJ1ZSwicGVybWlzc2lvbnMiOnsiaWQiOm51bGwsIm5ldF9pZCI6bnVsbCwic2Vuc29yX2lkIjpudWxsLCJjaGFubmVsX2lkIjpudWxsfX19fX0sIm5iZiI6MTY0NDQwNzgwNywiZXhwIjoxNjQ2OTk5ODA3fQ.PoRbdTbE_pvdYdZyV1u5pbNzAFczyls6gTbfHAurww8	::1	2022-02-09 11:56:48.017124
5	eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VySWQiOjEsInJpZ2h0cyI6eyJyZXNvdXJjZXMiOnsidGltZXNlcmllcyI6eyJyZWFkIjp7ImlwIjpbXSwiZW5hYmxlZCI6dHJ1ZSwicGVybWlzc2lvbnMiOnsiaWQiOltdLCJhbGwiOnsibGFzdF9kYXlzIjp0cnVlLCJlbmRfcGVyaW9kIjpudWxsLCJzdGFydF9wZXJpb2QiOm51bGwsIm51bWJlcl9vZl9kYXlzIjoxfSwibmV0X2lkIjp7IjEiOnsibGFzdF9kYXlzIjp0cnVlLCJlbmRfcGVyaW9kIjpudWxsLCJzdGFydF9wZXJpb2QiOm51bGwsIm51bWJlcl9vZl9kYXlzIjo3fSwiMiI6eyJsYXN0X2RheXMiOnRydWUsImVuZF9wZXJpb2QiOm51bGwsInN0YXJ0X3BlcmlvZCI6bnVsbCwibnVtYmVyX29mX2RheXMiOjd9LCIzIjp7Imxhc3RfZGF5cyI6dHJ1ZSwiZW5kX3BlcmlvZCI6bnVsbCwic3RhcnRfcGVyaW9kIjpudWxsLCJudW1iZXJfb2ZfZGF5cyI6MzB9fSwic2Vuc29yX2lkIjpbXSwiY2hhbm5lbF9pZCI6W119fX19fSwibmJmIjoxNjQ0NDA3ODE0LCJleHAiOjE2NDY5OTk4MTR9.X4LBBiKrheEBGj9wubAnnsNqm921I8KS-HFUDjbYchk	::1	2022-02-09 11:56:54.493903
6	eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VySWQiOjEsInJpZ2h0cyI6eyJyZXNvdXJjZXMiOnsidGltZXNlcmllcyI6eyJyZWFkIjp7ImlwIjpbXSwiZW5hYmxlZCI6dHJ1ZSwicGVybWlzc2lvbnMiOnsiaWQiOltdLCJhbGwiOnsibGFzdF9kYXlzIjp0cnVlLCJlbmRfcGVyaW9kIjpudWxsLCJzdGFydF9wZXJpb2QiOm51bGwsIm51bWJlcl9vZl9kYXlzIjoxfSwibmV0X2lkIjp7IjEiOnsibGFzdF9kYXlzIjp0cnVlLCJlbmRfcGVyaW9kIjpudWxsLCJzdGFydF9wZXJpb2QiOm51bGwsIm51bWJlcl9vZl9kYXlzIjo3fSwiMiI6eyJsYXN0X2RheXMiOnRydWUsImVuZF9wZXJpb2QiOm51bGwsInN0YXJ0X3BlcmlvZCI6bnVsbCwibnVtYmVyX29mX2RheXMiOjd9LCIzIjp7Imxhc3RfZGF5cyI6dHJ1ZSwiZW5kX3BlcmlvZCI6bnVsbCwic3RhcnRfcGVyaW9kIjpudWxsLCJudW1iZXJfb2ZfZGF5cyI6MzB9fSwic2Vuc29yX2lkIjpbXSwiY2hhbm5lbF9pZCI6W119fX19fSwibmJmIjoxNjQ0NDA4MTY0LCJleHAiOjE2NDcwMDAxNjR9.6p4jLfsUNxAzniPvWFwCIG_U1A8W67OZ7PzzkSNpbR8	::1	2022-02-09 12:02:44.385731
7	eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VySWQiOjEsInJpZ2h0cyI6eyJyZXNvdXJjZXMiOnsidGltZXNlcmllcyI6eyJyZWFkIjp7ImlwIjpbXSwiZW5hYmxlZCI6dHJ1ZSwicGVybWlzc2lvbnMiOnsiaWQiOltdLCJhbGwiOnsibGFzdF9kYXlzIjp0cnVlLCJlbmRfcGVyaW9kIjpudWxsLCJzdGFydF9wZXJpb2QiOm51bGwsIm51bWJlcl9vZl9kYXlzIjoxfSwibmV0X2lkIjp7IjEiOnsibGFzdF9kYXlzIjp0cnVlLCJlbmRfcGVyaW9kIjpudWxsLCJzdGFydF9wZXJpb2QiOm51bGwsIm51bWJlcl9vZl9kYXlzIjo3fSwiMiI6eyJsYXN0X2RheXMiOnRydWUsImVuZF9wZXJpb2QiOm51bGwsInN0YXJ0X3BlcmlvZCI6bnVsbCwibnVtYmVyX29mX2RheXMiOjd9LCIzIjp7Imxhc3RfZGF5cyI6dHJ1ZSwiZW5kX3BlcmlvZCI6bnVsbCwic3RhcnRfcGVyaW9kIjpudWxsLCJudW1iZXJfb2ZfZGF5cyI6MzB9fSwic2Vuc29yX2lkIjpbXSwiY2hhbm5lbF9pZCI6W119fX19fSwibmJmIjoxNjQ0NDA4NTg5LCJleHAiOjE2NDcwMDA1ODl9.19Y2dgoHxvLJR-4RPHuNUdcX-WKVuSUz6HfmnvCAoEs	::1	2022-02-09 12:09:49.466095
8	eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VySWQiOjEsIm5iZiI6MTY0NDQwODY2NSwiZXhwIjoxNjQ3MDAwNjY1fQ.ldmWrxIFVnZH2jfF6NSwfg71uREEFJLJFvqwsuNjcFg	::1	2022-02-09 12:11:05.418915
9	eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VySWQiOjEsInJpZ2h0cyI6eyJyZXNvdXJjZXMiOnsidGltZXNlcmllcyI6eyJyZWFkIjp7ImlwIjpbXSwiZW5hYmxlZCI6dHJ1ZSwicGVybWlzc2lvbnMiOnsiaWQiOltdLCJhbGwiOnsibGFzdF9kYXlzIjp0cnVlLCJlbmRfcGVyaW9kIjpudWxsLCJzdGFydF9wZXJpb2QiOm51bGwsIm51bWJlcl9vZl9kYXlzIjoxfSwibmV0X2lkIjp7IjEiOnsibGFzdF9kYXlzIjp0cnVlLCJlbmRfcGVyaW9kIjpudWxsLCJzdGFydF9wZXJpb2QiOm51bGwsIm51bWJlcl9vZl9kYXlzIjo3fSwiMiI6eyJsYXN0X2RheXMiOnRydWUsImVuZF9wZXJpb2QiOm51bGwsInN0YXJ0X3BlcmlvZCI6bnVsbCwibnVtYmVyX29mX2RheXMiOjd9LCIzIjp7Imxhc3RfZGF5cyI6dHJ1ZSwiZW5kX3BlcmlvZCI6bnVsbCwic3RhcnRfcGVyaW9kIjpudWxsLCJudW1iZXJfb2ZfZGF5cyI6MzB9fSwic2Vuc29yX2lkIjpbXSwiY2hhbm5lbF9pZCI6W119fX19fSwibmJmIjoxNjQ0NDA4ODk0LCJleHAiOjE2NDcwMDA4OTR9.JAsLZJyVfNxGnKpdma4auJi8STCSEHiY6ReSIGtA6MA	::1	2022-02-09 12:14:54.197986
10	eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VySWQiOjEsInJpZ2h0cyI6eyJyZXNvdXJjZXMiOnsidGltZXNlcmllcyI6eyJlZGl0Ijp7ImlwIjpbXSwiZW5hYmxlZCI6dHJ1ZSwicGVybWlzc2lvbnMiOnsiaWQiOlsiZjgzOGY5MGYtZDg5OS00N2JiLTg3N2YtYmRiZTY5MzMwYmMxIl0sIm5ldF9pZCI6bnVsbCwic2Vuc29yX2lkIjpudWxsLCJjaGFubmVsX2lkIjpudWxsfX19fX0sIm5iZiI6MTY0NzUwODU4NiwiZXhwIjoxNjUwMTAwNTg2fQ.qLkzXwoYW1M4VSeW0SXb9EOxD2mzKbVPUC882l3KdCQ	::1	2022-03-17 09:16:27.053558
11	eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VySWQiOjEsInJpZ2h0cyI6eyJyZXNvdXJjZXMiOnsidGltZXNlcmllcyI6eyJyZWFkIjp7ImlwIjpbXSwiZW5hYmxlZCI6dHJ1ZSwicGVybWlzc2lvbnMiOnsiaWQiOltdLCJhbGwiOnsibGFzdF9kYXlzIjp0cnVlLCJlbmRfcGVyaW9kIjpudWxsLCJzdGFydF9wZXJpb2QiOm51bGwsIm51bWJlcl9vZl9kYXlzIjoxfSwibmV0X2lkIjp7IjEiOnsibGFzdF9kYXlzIjp0cnVlLCJlbmRfcGVyaW9kIjpudWxsLCJzdGFydF9wZXJpb2QiOm51bGwsIm51bWJlcl9vZl9kYXlzIjo5OTl9LCIyIjp7Imxhc3RfZGF5cyI6dHJ1ZSwiZW5kX3BlcmlvZCI6bnVsbCwic3RhcnRfcGVyaW9kIjpudWxsLCJudW1iZXJfb2ZfZGF5cyI6N30sIjMiOnsibGFzdF9kYXlzIjp0cnVlLCJlbmRfcGVyaW9kIjpudWxsLCJzdGFydF9wZXJpb2QiOm51bGwsIm51bWJlcl9vZl9kYXlzIjozMH19LCJzZW5zb3JfaWQiOltdLCJjaGFubmVsX2lkIjpbXX19fX19LCJuYmYiOjE2NDc1MDg2MzMsImV4cCI6MTY1MDEwMDYzM30.mvXjn1tuZpZk6idY72gGKD2SLXaoZX2nkPHvY0gnoN8	::1	2022-03-17 09:17:13.63052
12	eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VySWQiOjEsIm5iZiI6MTY0NzUzNDcxNiwiZXhwIjoxNjUwMTI2NzE2fQ.QIH8_Gw4vhy1AP081hmjrFqMRbTYhobBGZNgpG-aUyw	::1	2022-03-17 16:31:56.323719
\.


--
-- TOC entry 4361 (class 0 OID 0)
-- Dependencies: 270
-- Name: channels_id_seq; Type: SEQUENCE SET; Schema: tsd_pnet; Owner: postgres
--

SELECT pg_catalog.setval('tsd_pnet.channels_id_seq', 52, true);


--
-- TOC entry 4362 (class 0 OID 0)
-- Dependencies: 272
-- Name: nets_id_seq; Type: SEQUENCE SET; Schema: tsd_pnet; Owner: postgres
--

SELECT pg_catalog.setval('tsd_pnet.nets_id_seq', 1, true);


--
-- TOC entry 4363 (class 0 OID 0)
-- Dependencies: 274
-- Name: owners_id_seq; Type: SEQUENCE SET; Schema: tsd_pnet; Owner: postgres
--

SELECT pg_catalog.setval('tsd_pnet.owners_id_seq', 1, false);


--
-- TOC entry 4364 (class 0 OID 0)
-- Dependencies: 276
-- Name: sensors_id_seq; Type: SEQUENCE SET; Schema: tsd_pnet; Owner: postgres
--

SELECT pg_catalog.setval('tsd_pnet.sensors_id_seq', 22, true);


--
-- TOC entry 4365 (class 0 OID 0)
-- Dependencies: 278
-- Name: sensortypes_id_seq; Type: SEQUENCE SET; Schema: tsd_pnet; Owner: postgres
--

SELECT pg_catalog.setval('tsd_pnet.sensortypes_id_seq', 1, true);


--
-- TOC entry 4366 (class 0 OID 0)
-- Dependencies: 280
-- Name: sites_id_seq; Type: SEQUENCE SET; Schema: tsd_pnet; Owner: postgres
--

SELECT pg_catalog.setval('tsd_pnet.sites_id_seq', 1, false);


--
-- TOC entry 4367 (class 0 OID 0)
-- Dependencies: 260
-- Name: members_id_seq; Type: SEQUENCE SET; Schema: tsd_users; Owner: postgres
--

SELECT pg_catalog.setval('tsd_users.members_id_seq', 1, true);


--
-- TOC entry 4368 (class 0 OID 0)
-- Dependencies: 265
-- Name: members_permissions_id_seq; Type: SEQUENCE SET; Schema: tsd_users; Owner: postgres
--

SELECT pg_catalog.setval('tsd_users.members_permissions_id_seq', 1, false);


--
-- TOC entry 4369 (class 0 OID 0)
-- Dependencies: 262
-- Name: roles_id_seq; Type: SEQUENCE SET; Schema: tsd_users; Owner: postgres
--

SELECT pg_catalog.setval('tsd_users.roles_id_seq', 1, true);


--
-- TOC entry 4370 (class 0 OID 0)
-- Dependencies: 267
-- Name: roles_permissions_id_seq; Type: SEQUENCE SET; Schema: tsd_users; Owner: postgres
--

SELECT pg_catalog.setval('tsd_users.roles_permissions_id_seq', 1, true);


--
-- TOC entry 4371 (class 0 OID 0)
-- Dependencies: 281
-- Name: tokens_id_seq; Type: SEQUENCE SET; Schema: tsd_users; Owner: postgres
--

SELECT pg_catalog.setval('tsd_users.tokens_id_seq', 12, true);


--
-- TOC entry 4142 (class 2606 OID 18727)
-- Name: timeseries_mapping_channels timeseries_mapping_channels_pkey; Type: CONSTRAINT; Schema: tsd_main; Owner: postgres
--

ALTER TABLE ONLY tsd_main.timeseries_mapping_channels
    ADD CONSTRAINT timeseries_mapping_channels_pkey PRIMARY KEY (timeseries_id, channel_id);


--
-- TOC entry 4139 (class 2606 OID 18713)
-- Name: timeseries timeseries_pkey; Type: CONSTRAINT; Schema: tsd_main; Owner: postgres
--

ALTER TABLE ONLY tsd_main.timeseries
    ADD CONSTRAINT timeseries_pkey PRIMARY KEY (id);


--
-- TOC entry 4156 (class 2606 OID 19097)
-- Name: channels channels_pkey; Type: CONSTRAINT; Schema: tsd_pnet; Owner: postgres
--

ALTER TABLE ONLY tsd_pnet.channels
    ADD CONSTRAINT channels_pkey PRIMARY KEY (id);


--
-- TOC entry 4159 (class 2606 OID 19099)
-- Name: nets nets_pkey; Type: CONSTRAINT; Schema: tsd_pnet; Owner: postgres
--

ALTER TABLE ONLY tsd_pnet.nets
    ADD CONSTRAINT nets_pkey PRIMARY KEY (id);


--
-- TOC entry 4162 (class 2606 OID 19101)
-- Name: owners owners_pkey; Type: CONSTRAINT; Schema: tsd_pnet; Owner: postgres
--

ALTER TABLE ONLY tsd_pnet.owners
    ADD CONSTRAINT owners_pkey PRIMARY KEY (id);


--
-- TOC entry 4165 (class 2606 OID 19103)
-- Name: sensors sensors_pkey; Type: CONSTRAINT; Schema: tsd_pnet; Owner: postgres
--

ALTER TABLE ONLY tsd_pnet.sensors
    ADD CONSTRAINT sensors_pkey PRIMARY KEY (id);


--
-- TOC entry 4168 (class 2606 OID 19105)
-- Name: sensortypes sensortypes_pkey; Type: CONSTRAINT; Schema: tsd_pnet; Owner: postgres
--

ALTER TABLE ONLY tsd_pnet.sensortypes
    ADD CONSTRAINT sensortypes_pkey PRIMARY KEY (id);


--
-- TOC entry 4171 (class 2606 OID 19107)
-- Name: sites sites_pkey; Type: CONSTRAINT; Schema: tsd_pnet; Owner: postgres
--

ALTER TABLE ONLY tsd_pnet.sites
    ADD CONSTRAINT sites_pkey PRIMARY KEY (id);


--
-- TOC entry 4144 (class 2606 OID 18797)
-- Name: members members_email_key; Type: CONSTRAINT; Schema: tsd_users; Owner: postgres
--

ALTER TABLE ONLY tsd_users.members
    ADD CONSTRAINT members_email_key UNIQUE (email);


--
-- TOC entry 4150 (class 2606 OID 18901)
-- Name: members_mapping_roles members_mapping_roles_pkey; Type: CONSTRAINT; Schema: tsd_users; Owner: postgres
--

ALTER TABLE ONLY tsd_users.members_mapping_roles
    ADD CONSTRAINT members_mapping_roles_pkey PRIMARY KEY (member_id, role_id);


--
-- TOC entry 4152 (class 2606 OID 18913)
-- Name: members_permissions members_permissions_pkey; Type: CONSTRAINT; Schema: tsd_users; Owner: postgres
--

ALTER TABLE ONLY tsd_users.members_permissions
    ADD CONSTRAINT members_permissions_pkey PRIMARY KEY (id);


--
-- TOC entry 4146 (class 2606 OID 18795)
-- Name: members members_pkey; Type: CONSTRAINT; Schema: tsd_users; Owner: postgres
--

ALTER TABLE ONLY tsd_users.members
    ADD CONSTRAINT members_pkey PRIMARY KEY (id);


--
-- TOC entry 4154 (class 2606 OID 18927)
-- Name: roles_permissions roles_permissions_pkey; Type: CONSTRAINT; Schema: tsd_users; Owner: postgres
--

ALTER TABLE ONLY tsd_users.roles_permissions
    ADD CONSTRAINT roles_permissions_pkey PRIMARY KEY (id);


--
-- TOC entry 4148 (class 2606 OID 18872)
-- Name: roles roles_pkey; Type: CONSTRAINT; Schema: tsd_users; Owner: postgres
--

ALTER TABLE ONLY tsd_users.roles
    ADD CONSTRAINT roles_pkey PRIMARY KEY (id);


--
-- TOC entry 4174 (class 2606 OID 19140)
-- Name: tokens tokens_pkey; Type: CONSTRAINT; Schema: tsd_users; Owner: postgres
--

ALTER TABLE ONLY tsd_users.tokens
    ADD CONSTRAINT tokens_pkey PRIMARY KEY (id);


--
-- TOC entry 4140 (class 1259 OID 18714)
-- Name: tsd_main_timeseries_lower_schema_lower_name_idx; Type: INDEX; Schema: tsd_main; Owner: postgres
--

CREATE UNIQUE INDEX tsd_main_timeseries_lower_schema_lower_name_idx ON tsd_main.timeseries USING btree (lower((schema)::text), lower((name)::text));


--
-- TOC entry 4157 (class 1259 OID 19108)
-- Name: tsd_pnet_channels_lower_name_sensor_id_idx; Type: INDEX; Schema: tsd_pnet; Owner: postgres
--

CREATE UNIQUE INDEX tsd_pnet_channels_lower_name_sensor_id_idx ON tsd_pnet.channels USING btree (lower((name)::text), sensor_id);


--
-- TOC entry 4160 (class 1259 OID 19109)
-- Name: tsd_pnet_nets_lower_name_idx; Type: INDEX; Schema: tsd_pnet; Owner: postgres
--

CREATE UNIQUE INDEX tsd_pnet_nets_lower_name_idx ON tsd_pnet.nets USING btree (lower((name)::text));


--
-- TOC entry 4163 (class 1259 OID 19110)
-- Name: tsd_pnet_owners_lower_name_idx; Type: INDEX; Schema: tsd_pnet; Owner: postgres
--

CREATE UNIQUE INDEX tsd_pnet_owners_lower_name_idx ON tsd_pnet.owners USING btree (lower((name)::text));


--
-- TOC entry 4166 (class 1259 OID 19111)
-- Name: tsd_pnet_sensors_lower_name_idx; Type: INDEX; Schema: tsd_pnet; Owner: postgres
--

CREATE UNIQUE INDEX tsd_pnet_sensors_lower_name_idx ON tsd_pnet.sensors USING btree (lower((name)::text));


--
-- TOC entry 4169 (class 1259 OID 19112)
-- Name: tsd_pnet_sensortypes_lower_name_idx; Type: INDEX; Schema: tsd_pnet; Owner: postgres
--

CREATE UNIQUE INDEX tsd_pnet_sensortypes_lower_name_idx ON tsd_pnet.sensortypes USING btree (lower((name)::text));


--
-- TOC entry 4172 (class 1259 OID 19113)
-- Name: tsd_pnet_sites_lower_name_idx; Type: INDEX; Schema: tsd_pnet; Owner: postgres
--

CREATE UNIQUE INDEX tsd_pnet_sites_lower_name_idx ON tsd_pnet.sites USING btree (lower((name)::text));


-- Completed on 2022-03-23 14:55:35

--
-- PostgreSQL database dump complete
--

