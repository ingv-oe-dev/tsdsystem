--
-- PostgreSQL database dump
--

-- Dumped from database version 13.4
-- Dumped by pg_dump version 13.4

-- Started on 2022-11-30 18:00:31

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
-- TOC entry 18 (class 2615 OID 32255)
-- Name: tsd_pnet; Type: SCHEMA; Schema: -; Owner: postgres
--

CREATE SCHEMA tsd_pnet;


ALTER SCHEMA tsd_pnet OWNER TO postgres;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- TOC entry 306 (class 1259 OID 32440)
-- Name: channels; Type: TABLE; Schema: tsd_pnet; Owner: postgres
--

CREATE TABLE tsd_pnet.channels (
    id integer NOT NULL,
    name character varying(255) NOT NULL,
    sensor_id integer,
    digitizer_id integer,
    additional_info jsonb,
    create_time timestamp without time zone DEFAULT timezone('utc'::text, now()),
    update_time timestamp without time zone,
    remove_time timestamp without time zone,
    create_user integer,
    update_user integer,
    remove_user integer
);


ALTER TABLE tsd_pnet.channels OWNER TO postgres;

--
-- TOC entry 305 (class 1259 OID 32438)
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
-- TOC entry 4390 (class 0 OID 0)
-- Dependencies: 305
-- Name: channels_id_seq; Type: SEQUENCE OWNED BY; Schema: tsd_pnet; Owner: postgres
--

ALTER SEQUENCE tsd_pnet.channels_id_seq OWNED BY tsd_pnet.channels.id;


--
-- TOC entry 304 (class 1259 OID 32426)
-- Name: digitizers; Type: TABLE; Schema: tsd_pnet; Owner: postgres
--

CREATE TABLE tsd_pnet.digitizers (
    id integer NOT NULL,
    name character varying(255) NOT NULL,
    serial_number character varying(255),
    digitizertype_id integer,
    start_datetime timestamp without time zone,
    end_datetime timestamp without time zone,
    additional_info jsonb,
    create_time timestamp without time zone DEFAULT timezone('utc'::text, now()),
    update_time timestamp without time zone,
    remove_time timestamp without time zone,
    create_user integer,
    update_user integer,
    remove_user integer
);


ALTER TABLE tsd_pnet.digitizers OWNER TO postgres;

--
-- TOC entry 303 (class 1259 OID 32424)
-- Name: digitizers_id_seq; Type: SEQUENCE; Schema: tsd_pnet; Owner: postgres
--

CREATE SEQUENCE tsd_pnet.digitizers_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE tsd_pnet.digitizers_id_seq OWNER TO postgres;

--
-- TOC entry 4391 (class 0 OID 0)
-- Dependencies: 303
-- Name: digitizers_id_seq; Type: SEQUENCE OWNED BY; Schema: tsd_pnet; Owner: postgres
--

ALTER SEQUENCE tsd_pnet.digitizers_id_seq OWNED BY tsd_pnet.digitizers.id;


--
-- TOC entry 302 (class 1259 OID 32414)
-- Name: digitizertypes; Type: TABLE; Schema: tsd_pnet; Owner: postgres
--

CREATE TABLE tsd_pnet.digitizertypes (
    id integer NOT NULL,
    name character varying(255) NOT NULL,
    model character varying(255),
    final_sample_rate real,
    final_sample_rate_measure_unit real,
    sensitivity real,
    sensitivity_measure_unit real,
    dynamical_range real,
    dynamical_range_measure_unit real,
    additional_info jsonb,
    create_time timestamp without time zone DEFAULT timezone('utc'::text, now()),
    update_time timestamp without time zone,
    remove_time timestamp without time zone,
    create_user integer,
    update_user integer,
    remove_user integer
);


ALTER TABLE tsd_pnet.digitizertypes OWNER TO postgres;

--
-- TOC entry 301 (class 1259 OID 32412)
-- Name: digitizertypes_id_seq; Type: SEQUENCE; Schema: tsd_pnet; Owner: postgres
--

CREATE SEQUENCE tsd_pnet.digitizertypes_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE tsd_pnet.digitizertypes_id_seq OWNER TO postgres;

--
-- TOC entry 4392 (class 0 OID 0)
-- Dependencies: 301
-- Name: digitizertypes_id_seq; Type: SEQUENCE OWNED BY; Schema: tsd_pnet; Owner: postgres
--

ALTER SEQUENCE tsd_pnet.digitizertypes_id_seq OWNED BY tsd_pnet.digitizertypes.id;


--
-- TOC entry 308 (class 1259 OID 32457)
-- Name: nets; Type: TABLE; Schema: tsd_pnet; Owner: postgres
--

CREATE TABLE tsd_pnet.nets (
    id integer NOT NULL,
    name character varying(255) NOT NULL,
    owner_id integer,
    additional_info jsonb,
    create_time timestamp without time zone DEFAULT timezone('utc'::text, now()),
    update_time timestamp without time zone,
    remove_time timestamp without time zone,
    create_user integer,
    update_user integer,
    remove_user integer
);


ALTER TABLE tsd_pnet.nets OWNER TO postgres;

--
-- TOC entry 307 (class 1259 OID 32455)
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
-- TOC entry 4393 (class 0 OID 0)
-- Dependencies: 307
-- Name: nets_id_seq; Type: SEQUENCE OWNED BY; Schema: tsd_pnet; Owner: postgres
--

ALTER SEQUENCE tsd_pnet.nets_id_seq OWNED BY tsd_pnet.nets.id;


--
-- TOC entry 290 (class 1259 OID 32333)
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
-- TOC entry 289 (class 1259 OID 32331)
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
-- TOC entry 4394 (class 0 OID 0)
-- Dependencies: 289
-- Name: owners_id_seq; Type: SEQUENCE OWNED BY; Schema: tsd_pnet; Owner: postgres
--

ALTER SEQUENCE tsd_pnet.owners_id_seq OWNED BY tsd_pnet.owners.id;


--
-- TOC entry 300 (class 1259 OID 32402)
-- Name: sensors; Type: TABLE; Schema: tsd_pnet; Owner: postgres
--

CREATE TABLE tsd_pnet.sensors (
    id integer NOT NULL,
    name character varying(255) NOT NULL,
    serial_number character varying(255),
    station_id integer,
    sensortype_id integer,
    start_datetime timestamp without time zone,
    end_datetime timestamp without time zone,
    additional_info jsonb,
    create_time timestamp without time zone DEFAULT timezone('utc'::text, now()),
    update_time timestamp without time zone,
    remove_time timestamp without time zone,
    create_user integer,
    update_user integer,
    remove_user integer
);


ALTER TABLE tsd_pnet.sensors OWNER TO postgres;

--
-- TOC entry 299 (class 1259 OID 32400)
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
-- TOC entry 4395 (class 0 OID 0)
-- Dependencies: 299
-- Name: sensors_id_seq; Type: SEQUENCE OWNED BY; Schema: tsd_pnet; Owner: postgres
--

ALTER SEQUENCE tsd_pnet.sensors_id_seq OWNED BY tsd_pnet.sensors.id;


--
-- TOC entry 296 (class 1259 OID 32375)
-- Name: sensortype_categories; Type: TABLE; Schema: tsd_pnet; Owner: postgres
--

CREATE TABLE tsd_pnet.sensortype_categories (
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


ALTER TABLE tsd_pnet.sensortype_categories OWNER TO postgres;

--
-- TOC entry 295 (class 1259 OID 32373)
-- Name: sensortype_categories_id_seq; Type: SEQUENCE; Schema: tsd_pnet; Owner: postgres
--

CREATE SEQUENCE tsd_pnet.sensortype_categories_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE tsd_pnet.sensortype_categories_id_seq OWNER TO postgres;

--
-- TOC entry 4396 (class 0 OID 0)
-- Dependencies: 295
-- Name: sensortype_categories_id_seq; Type: SEQUENCE OWNED BY; Schema: tsd_pnet; Owner: postgres
--

ALTER SEQUENCE tsd_pnet.sensortype_categories_id_seq OWNED BY tsd_pnet.sensortype_categories.id;


--
-- TOC entry 298 (class 1259 OID 32387)
-- Name: sensortypes; Type: TABLE; Schema: tsd_pnet; Owner: postgres
--

CREATE TABLE tsd_pnet.sensortypes (
    id integer NOT NULL,
    name character varying(255) NOT NULL,
    model character varying(255),
    n_components integer,
    sensortype_category_id integer,
    response_parameters jsonb,
    additional_info jsonb,
    create_time timestamp without time zone DEFAULT timezone('utc'::text, now()),
    update_time timestamp without time zone,
    remove_time timestamp without time zone,
    create_user integer,
    update_user integer,
    remove_user integer
);


ALTER TABLE tsd_pnet.sensortypes OWNER TO postgres;

--
-- TOC entry 297 (class 1259 OID 32385)
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
-- TOC entry 4397 (class 0 OID 0)
-- Dependencies: 297
-- Name: sensortypes_id_seq; Type: SEQUENCE OWNED BY; Schema: tsd_pnet; Owner: postgres
--

ALTER SEQUENCE tsd_pnet.sensortypes_id_seq OWNED BY tsd_pnet.sensortypes.id;


--
-- TOC entry 292 (class 1259 OID 32351)
-- Name: sites; Type: TABLE; Schema: tsd_pnet; Owner: postgres
--

CREATE TABLE tsd_pnet.sites (
    id integer NOT NULL,
    name character varying(255) NOT NULL,
    coords public.geometry,
    quote real,
    additional_info jsonb,
    create_time timestamp without time zone DEFAULT timezone('utc'::text, now()),
    update_time timestamp without time zone,
    remove_time timestamp without time zone,
    create_user integer,
    update_user integer,
    remove_user integer
);


ALTER TABLE tsd_pnet.sites OWNER TO postgres;

--
-- TOC entry 291 (class 1259 OID 32349)
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
-- TOC entry 4398 (class 0 OID 0)
-- Dependencies: 291
-- Name: sites_id_seq; Type: SEQUENCE OWNED BY; Schema: tsd_pnet; Owner: postgres
--

ALTER SEQUENCE tsd_pnet.sites_id_seq OWNED BY tsd_pnet.sites.id;


--
-- TOC entry 294 (class 1259 OID 32363)
-- Name: stations; Type: TABLE; Schema: tsd_pnet; Owner: postgres
--

CREATE TABLE tsd_pnet.stations (
    id integer NOT NULL,
    name character varying(255) NOT NULL,
    coords public.geometry,
    quote real,
    site_id integer,
    net_id integer,
    additional_info jsonb,
    create_time timestamp without time zone DEFAULT timezone('utc'::text, now()),
    update_time timestamp without time zone,
    remove_time timestamp without time zone,
    create_user integer,
    update_user integer,
    remove_user integer
);


ALTER TABLE tsd_pnet.stations OWNER TO postgres;

--
-- TOC entry 293 (class 1259 OID 32361)
-- Name: stations_id_seq; Type: SEQUENCE; Schema: tsd_pnet; Owner: postgres
--

CREATE SEQUENCE tsd_pnet.stations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE tsd_pnet.stations_id_seq OWNER TO postgres;

--
-- TOC entry 4399 (class 0 OID 0)
-- Dependencies: 293
-- Name: stations_id_seq; Type: SEQUENCE OWNED BY; Schema: tsd_pnet; Owner: postgres
--

ALTER SEQUENCE tsd_pnet.stations_id_seq OWNED BY tsd_pnet.stations.id;


--
-- TOC entry 4195 (class 2604 OID 32443)
-- Name: channels id; Type: DEFAULT; Schema: tsd_pnet; Owner: postgres
--

ALTER TABLE ONLY tsd_pnet.channels ALTER COLUMN id SET DEFAULT nextval('tsd_pnet.channels_id_seq'::regclass);


--
-- TOC entry 4193 (class 2604 OID 32429)
-- Name: digitizers id; Type: DEFAULT; Schema: tsd_pnet; Owner: postgres
--

ALTER TABLE ONLY tsd_pnet.digitizers ALTER COLUMN id SET DEFAULT nextval('tsd_pnet.digitizers_id_seq'::regclass);


--
-- TOC entry 4191 (class 2604 OID 32417)
-- Name: digitizertypes id; Type: DEFAULT; Schema: tsd_pnet; Owner: postgres
--

ALTER TABLE ONLY tsd_pnet.digitizertypes ALTER COLUMN id SET DEFAULT nextval('tsd_pnet.digitizertypes_id_seq'::regclass);


--
-- TOC entry 4197 (class 2604 OID 32460)
-- Name: nets id; Type: DEFAULT; Schema: tsd_pnet; Owner: postgres
--

ALTER TABLE ONLY tsd_pnet.nets ALTER COLUMN id SET DEFAULT nextval('tsd_pnet.nets_id_seq'::regclass);


--
-- TOC entry 4179 (class 2604 OID 32336)
-- Name: owners id; Type: DEFAULT; Schema: tsd_pnet; Owner: postgres
--

ALTER TABLE ONLY tsd_pnet.owners ALTER COLUMN id SET DEFAULT nextval('tsd_pnet.owners_id_seq'::regclass);


--
-- TOC entry 4189 (class 2604 OID 32405)
-- Name: sensors id; Type: DEFAULT; Schema: tsd_pnet; Owner: postgres
--

ALTER TABLE ONLY tsd_pnet.sensors ALTER COLUMN id SET DEFAULT nextval('tsd_pnet.sensors_id_seq'::regclass);


--
-- TOC entry 4185 (class 2604 OID 32378)
-- Name: sensortype_categories id; Type: DEFAULT; Schema: tsd_pnet; Owner: postgres
--

ALTER TABLE ONLY tsd_pnet.sensortype_categories ALTER COLUMN id SET DEFAULT nextval('tsd_pnet.sensortype_categories_id_seq'::regclass);


--
-- TOC entry 4187 (class 2604 OID 32390)
-- Name: sensortypes id; Type: DEFAULT; Schema: tsd_pnet; Owner: postgres
--

ALTER TABLE ONLY tsd_pnet.sensortypes ALTER COLUMN id SET DEFAULT nextval('tsd_pnet.sensortypes_id_seq'::regclass);


--
-- TOC entry 4181 (class 2604 OID 32354)
-- Name: sites id; Type: DEFAULT; Schema: tsd_pnet; Owner: postgres
--

ALTER TABLE ONLY tsd_pnet.sites ALTER COLUMN id SET DEFAULT nextval('tsd_pnet.sites_id_seq'::regclass);


--
-- TOC entry 4183 (class 2604 OID 32366)
-- Name: stations id; Type: DEFAULT; Schema: tsd_pnet; Owner: postgres
--

ALTER TABLE ONLY tsd_pnet.stations ALTER COLUMN id SET DEFAULT nextval('tsd_pnet.stations_id_seq'::regclass);


--
-- TOC entry 4382 (class 0 OID 32440)
-- Dependencies: 306
-- Data for Name: channels; Type: TABLE DATA; Schema: tsd_pnet; Owner: postgres
--

COPY tsd_pnet.channels (id, name, sensor_id, digitizer_id, additional_info, create_time, update_time, remove_time, create_user, update_user, remove_user) FROM stdin;
1	EHZ	1	\N	\N	2022-11-30 16:45:39.185747	\N	\N	\N	\N	\N
2	EHN	1	\N	\N	2022-11-30 16:45:39.185747	\N	\N	\N	\N	\N
3	EHE	1	\N	\N	2022-11-30 16:45:39.185747	\N	\N	\N	\N	\N
4	EHZ	2	\N	\N	2022-11-30 16:45:39.185747	\N	\N	\N	\N	\N
5	EHN	2	\N	\N	2022-11-30 16:45:39.185747	\N	\N	\N	\N	\N
6	EHE	2	\N	\N	2022-11-30 16:45:39.185747	\N	\N	\N	\N	\N
7	EHZ	3	\N	\N	2022-11-30 16:45:39.185747	\N	\N	\N	\N	\N
8	EHN	3	\N	\N	2022-11-30 16:45:39.185747	\N	\N	\N	\N	\N
9	EHE	3	\N	\N	2022-11-30 16:45:39.185747	\N	\N	\N	\N	\N
10	EHZ	4	\N	\N	2022-11-30 16:45:39.185747	\N	\N	\N	\N	\N
11	EHN	4	\N	\N	2022-11-30 16:45:39.185747	\N	\N	\N	\N	\N
12	EHE	4	\N	\N	2022-11-30 16:45:39.185747	\N	\N	\N	\N	\N
13	EHZ	5	\N	\N	2022-11-30 16:45:39.185747	\N	\N	\N	\N	\N
14	EHN	5	\N	\N	2022-11-30 16:45:39.185747	\N	\N	\N	\N	\N
15	EHE	5	\N	\N	2022-11-30 16:45:39.185747	\N	\N	\N	\N	\N
16	EHZ	6	\N	\N	2022-11-30 16:45:39.185747	\N	\N	\N	\N	\N
17	EHN	6	\N	\N	2022-11-30 16:45:39.185747	\N	\N	\N	\N	\N
18	EHE	6	\N	\N	2022-11-30 16:45:39.185747	\N	\N	\N	\N	\N
19	EHZ	7	\N	\N	2022-11-30 16:45:39.185747	\N	\N	\N	\N	\N
20	EHN	7	\N	\N	2022-11-30 16:45:39.185747	\N	\N	\N	\N	\N
21	EHE	7	\N	\N	2022-11-30 16:45:39.185747	\N	\N	\N	\N	\N
22	EHZ	8	\N	\N	2022-11-30 16:45:39.185747	\N	\N	\N	\N	\N
23	EHN	8	\N	\N	2022-11-30 16:45:39.185747	\N	\N	\N	\N	\N
24	EHE	8	\N	\N	2022-11-30 16:45:39.185747	\N	\N	\N	\N	\N
25	EHZ	9	\N	\N	2022-11-30 16:45:39.185747	\N	\N	\N	\N	\N
26	EHN	9	\N	\N	2022-11-30 16:45:39.185747	\N	\N	\N	\N	\N
27	EHE	9	\N	\N	2022-11-30 16:45:39.185747	\N	\N	\N	\N	\N
28	EHZ	10	\N	\N	2022-11-30 16:45:39.185747	\N	\N	\N	\N	\N
29	EHN	10	\N	\N	2022-11-30 16:45:39.185747	\N	\N	\N	\N	\N
30	EHE	10	\N	\N	2022-11-30 16:45:39.185747	\N	\N	\N	\N	\N
31	EHZ	11	\N	\N	2022-11-30 16:45:39.185747	\N	\N	\N	\N	\N
32	EHN	11	\N	\N	2022-11-30 16:45:39.185747	\N	\N	\N	\N	\N
33	EHE	11	\N	\N	2022-11-30 16:45:39.185747	\N	\N	\N	\N	\N
34	EHZ	12	\N	\N	2022-11-30 16:45:39.185747	\N	\N	\N	\N	\N
35	EHN	12	\N	\N	2022-11-30 16:45:39.185747	\N	\N	\N	\N	\N
36	EHE	12	\N	\N	2022-11-30 16:45:39.185747	\N	\N	\N	\N	\N
37	EHZ	13	\N	\N	2022-11-30 16:45:39.185747	\N	\N	\N	\N	\N
38	EHN	13	\N	\N	2022-11-30 16:45:39.185747	\N	\N	\N	\N	\N
39	EHE	13	\N	\N	2022-11-30 16:45:39.185747	\N	\N	\N	\N	\N
40	EHZ	14	\N	\N	2022-11-30 16:45:39.185747	\N	\N	\N	\N	\N
41	EHN	14	\N	\N	2022-11-30 16:45:39.185747	\N	\N	\N	\N	\N
42	EHE	14	\N	\N	2022-11-30 16:45:39.185747	\N	\N	\N	\N	\N
43	EHZ	15	\N	\N	2022-11-30 16:45:39.185747	\N	\N	\N	\N	\N
44	EHN	15	\N	\N	2022-11-30 16:45:39.185747	\N	\N	\N	\N	\N
45	EHE	15	\N	\N	2022-11-30 16:45:39.185747	\N	\N	\N	\N	\N
46	EHZ	16	\N	\N	2022-11-30 16:45:39.185747	\N	\N	\N	\N	\N
47	EHN	16	\N	\N	2022-11-30 16:45:39.185747	\N	\N	\N	\N	\N
48	EHE	16	\N	\N	2022-11-30 16:45:39.185747	\N	\N	\N	\N	\N
49	EHZ	17	\N	\N	2022-11-30 16:45:39.185747	\N	\N	\N	\N	\N
50	EHN	17	\N	\N	2022-11-30 16:45:39.185747	\N	\N	\N	\N	\N
51	EHE	17	\N	\N	2022-11-30 16:45:39.185747	\N	\N	\N	\N	\N
\.


--
-- TOC entry 4380 (class 0 OID 32426)
-- Dependencies: 304
-- Data for Name: digitizers; Type: TABLE DATA; Schema: tsd_pnet; Owner: postgres
--

COPY tsd_pnet.digitizers (id, name, serial_number, digitizertype_id, start_datetime, end_datetime, additional_info, create_time, update_time, remove_time, create_user, update_user, remove_user) FROM stdin;
\.


--
-- TOC entry 4378 (class 0 OID 32414)
-- Dependencies: 302
-- Data for Name: digitizertypes; Type: TABLE DATA; Schema: tsd_pnet; Owner: postgres
--

COPY tsd_pnet.digitizertypes (id, name, model, final_sample_rate, final_sample_rate_measure_unit, sensitivity, sensitivity_measure_unit, dynamical_range, dynamical_range_measure_unit, additional_info, create_time, update_time, remove_time, create_user, update_user, remove_user) FROM stdin;
\.


--
-- TOC entry 4384 (class 0 OID 32457)
-- Dependencies: 308
-- Data for Name: nets; Type: TABLE DATA; Schema: tsd_pnet; Owner: postgres
--

COPY tsd_pnet.nets (id, name, owner_id, additional_info, create_time, update_time, remove_time, create_user, update_user, remove_user) FROM stdin;
1	Seismic	1	\N	2022-11-30 16:55:21.356491	\N	\N	\N	\N	\N
\.


--
-- TOC entry 4366 (class 0 OID 32333)
-- Dependencies: 290
-- Data for Name: owners; Type: TABLE DATA; Schema: tsd_pnet; Owner: postgres
--

COPY tsd_pnet.owners (id, name, create_time, update_time, remove_time, create_user, update_user, remove_user) FROM stdin;
1	INGV	2022-11-30 16:55:47.443765	\N	\N	\N	\N	\N
\.


--
-- TOC entry 4376 (class 0 OID 32402)
-- Dependencies: 300
-- Data for Name: sensors; Type: TABLE DATA; Schema: tsd_pnet; Owner: postgres
--

COPY tsd_pnet.sensors (id, name, serial_number, station_id, sensortype_id, start_datetime, end_datetime, additional_info, create_time, update_time, remove_time, create_user, update_user, remove_user) FROM stdin;
1	STR1_velocimeter	\N	1	1	2020-01-01 00:00:00	\N	\N	2022-11-30 16:43:44.97336	\N	\N	\N	\N	\N
2	STR3_velocimeter	\N	2	1	2020-01-01 00:00:00	\N	\N	2022-11-30 16:43:44.97336	\N	\N	\N	\N	\N
3	STR4_velocimeter	\N	3	1	2020-01-01 00:00:00	\N	\N	2022-11-30 16:43:44.97336	\N	\N	\N	\N	\N
4	STR5_velocimeter	\N	4	1	2020-01-01 00:00:00	\N	\N	2022-11-30 16:43:44.97336	\N	\N	\N	\N	\N
5	STR6_velocimeter	\N	5	1	2020-01-01 00:00:00	\N	\N	2022-11-30 16:43:44.97336	\N	\N	\N	\N	\N
6	STR8_velocimeter	\N	6	1	2020-01-01 00:00:00	\N	\N	2022-11-30 16:43:44.97336	\N	\N	\N	\N	\N
7	STR9_velocimeter	\N	7	1	2020-01-01 00:00:00	\N	\N	2022-11-30 16:43:44.97336	\N	\N	\N	\N	\N
8	STRA_velocimeter	\N	8	1	2020-01-01 00:00:00	\N	\N	2022-11-30 16:43:44.97336	\N	\N	\N	\N	\N
9	STRB_velocimeter	\N	9	1	2020-01-01 00:00:00	\N	\N	2022-11-30 16:43:44.97336	\N	\N	\N	\N	\N
10	STRC_velocimeter	\N	10	1	2020-01-01 00:00:00	\N	\N	2022-11-30 16:43:44.97336	\N	\N	\N	\N	\N
11	STRD_velocimeter	\N	11	1	2020-01-01 00:00:00	\N	\N	2022-11-30 16:43:44.97336	\N	\N	\N	\N	\N
12	STRE_velocimeter	\N	12	1	2020-01-01 00:00:00	\N	\N	2022-11-30 16:43:44.97336	\N	\N	\N	\N	\N
13	STRG_velocimeter	\N	13	1	2020-01-01 00:00:00	\N	\N	2022-11-30 16:43:44.97336	\N	\N	\N	\N	\N
14	IST3_velocimeter	\N	14	1	2020-01-01 00:00:00	\N	\N	2022-11-30 16:43:44.97336	\N	\N	\N	\N	\N
15	ISTP_velocimeter	\N	15	1	2020-01-01 00:00:00	\N	\N	2022-11-30 16:43:44.97336	\N	\N	\N	\N	\N
16	ISTR_velocimeter	\N	16	1	2020-01-01 00:00:00	\N	\N	2022-11-30 16:43:44.97336	\N	\N	\N	\N	\N
17	SVO_velocimeter	\N	17	1	2020-01-01 00:00:00	\N	\N	2022-11-30 16:43:44.97336	\N	\N	\N	\N	\N
\.


--
-- TOC entry 4372 (class 0 OID 32375)
-- Dependencies: 296
-- Data for Name: sensortype_categories; Type: TABLE DATA; Schema: tsd_pnet; Owner: postgres
--

COPY tsd_pnet.sensortype_categories (id, name, json_schema, create_time, update_time, remove_time, create_user, update_user, remove_user) FROM stdin;
\.


--
-- TOC entry 4374 (class 0 OID 32387)
-- Dependencies: 298
-- Data for Name: sensortypes; Type: TABLE DATA; Schema: tsd_pnet; Owner: postgres
--

COPY tsd_pnet.sensortypes (id, name, model, n_components, sensortype_category_id, response_parameters, additional_info, create_time, update_time, remove_time, create_user, update_user, remove_user) FROM stdin;
\.


--
-- TOC entry 4368 (class 0 OID 32351)
-- Dependencies: 292
-- Data for Name: sites; Type: TABLE DATA; Schema: tsd_pnet; Owner: postgres
--

COPY tsd_pnet.sites (id, name, coords, quote, additional_info, create_time, update_time, remove_time, create_user, update_user, remove_user) FROM stdin;
\.


--
-- TOC entry 4370 (class 0 OID 32363)
-- Dependencies: 294
-- Data for Name: stations; Type: TABLE DATA; Schema: tsd_pnet; Owner: postgres
--

COPY tsd_pnet.stations (id, name, coords, quote, site_id, net_id, additional_info, create_time, update_time, remove_time, create_user, update_user, remove_user) FROM stdin;
1	STR1	0101000000A69BC420B0722E40598638D6C5654340	560.7	\N	1	\N	2022-11-30 16:58:05.393628	\N	\N	\N	\N	\N
2	STR3	01010000004BEA043411762E40AED85F764F664340	235.9	\N	1	\N	2022-11-30 16:58:05.393628	\N	\N	\N	\N	\N
3	STR4	01010000003F355EBA496C2E4089D2DEE00B634340	86.5	\N	1	\N	2022-11-30 16:58:05.393628	\N	\N	\N	\N	\N
4	STR5	01010000007FD93D7958682E404D158C4AEA644340	653.6	\N	1	\N	2022-11-30 16:58:05.393628	\N	\N	\N	\N	\N
5	STR6	01010000003BDF4F8D976E2E40696FF085C9644340	807.8	\N	1	\N	2022-11-30 16:58:05.393628	\N	\N	\N	\N	\N
6	STR8	010100000039454772F96F2E40D8F0F44A59664340	569.2	\N	1	\N	2022-11-30 16:58:05.393628	\N	\N	\N	\N	\N
7	STR9	01010000007C613255306A2E40CCEEC9C342654340	781.9	\N	1	\N	2022-11-30 16:58:05.393628	\N	\N	\N	\N	\N
8	STRA	010100000057EC2FBB276F2E40BDE3141DC9654340	842.8	\N	1	\N	2022-11-30 16:58:05.393628	\N	\N	\N	\N	\N
9	STRB	01010000002B1895D409682E40CCEEC9C342654340	632	\N	1	\N	2022-11-30 16:58:05.393628	\N	\N	\N	\N	\N
10	STRC	0101000000C3D32B6519622E406891ED7C3F654340	183.5	\N	1	\N	2022-11-30 16:58:05.393628	\N	\N	\N	\N	\N
11	STRD	0101000000E63FA4DFBE6E2E40DCD7817346644340	559	\N	1	\N	2022-11-30 16:58:05.393628	\N	\N	\N	\N	\N
12	STRE	0101000000CA32C4B12E6E2E40BB270F0BB5664340	435.8	\N	1	\N	2022-11-30 16:58:05.393628	\N	\N	\N	\N	\N
13	STRG	010100000020D26F5F076E2E40BA6B09F9A0674340	128	\N	1	\N	2022-11-30 16:58:05.393628	\N	\N	\N	\N	\N
14	IST3	01010000002EFF21FDF6752E404A7B832F4C664340	254	\N	1	\N	2022-11-30 16:58:05.393628	\N	\N	\N	\N	\N
15	ISTP	0101000000CB10C7BAB86D2E4005C58F3177654340	962	\N	1	\N	2022-11-30 16:58:05.393628	\N	\N	\N	\N	\N
16	ISTR	0101000000E0BE0E9C33622E40B1E1E995B2644340	114	\N	1	\N	2022-11-30 16:58:05.393628	\N	\N	\N	\N	\N
17	SVO	\N	\N	\N	1	\N	2022-11-30 16:58:05.393628	\N	\N	\N	\N	\N
\.


--
-- TOC entry 4400 (class 0 OID 0)
-- Dependencies: 305
-- Name: channels_id_seq; Type: SEQUENCE SET; Schema: tsd_pnet; Owner: postgres
--

SELECT pg_catalog.setval('tsd_pnet.channels_id_seq', 1, false);


--
-- TOC entry 4401 (class 0 OID 0)
-- Dependencies: 303
-- Name: digitizers_id_seq; Type: SEQUENCE SET; Schema: tsd_pnet; Owner: postgres
--

SELECT pg_catalog.setval('tsd_pnet.digitizers_id_seq', 1, false);


--
-- TOC entry 4402 (class 0 OID 0)
-- Dependencies: 301
-- Name: digitizertypes_id_seq; Type: SEQUENCE SET; Schema: tsd_pnet; Owner: postgres
--

SELECT pg_catalog.setval('tsd_pnet.digitizertypes_id_seq', 1, false);


--
-- TOC entry 4403 (class 0 OID 0)
-- Dependencies: 307
-- Name: nets_id_seq; Type: SEQUENCE SET; Schema: tsd_pnet; Owner: postgres
--

SELECT pg_catalog.setval('tsd_pnet.nets_id_seq', 1, true);


--
-- TOC entry 4404 (class 0 OID 0)
-- Dependencies: 289
-- Name: owners_id_seq; Type: SEQUENCE SET; Schema: tsd_pnet; Owner: postgres
--

SELECT pg_catalog.setval('tsd_pnet.owners_id_seq', 1, true);


--
-- TOC entry 4405 (class 0 OID 0)
-- Dependencies: 299
-- Name: sensors_id_seq; Type: SEQUENCE SET; Schema: tsd_pnet; Owner: postgres
--

SELECT pg_catalog.setval('tsd_pnet.sensors_id_seq', 1, false);


--
-- TOC entry 4406 (class 0 OID 0)
-- Dependencies: 295
-- Name: sensortype_categories_id_seq; Type: SEQUENCE SET; Schema: tsd_pnet; Owner: postgres
--

SELECT pg_catalog.setval('tsd_pnet.sensortype_categories_id_seq', 1, false);


--
-- TOC entry 4407 (class 0 OID 0)
-- Dependencies: 297
-- Name: sensortypes_id_seq; Type: SEQUENCE SET; Schema: tsd_pnet; Owner: postgres
--

SELECT pg_catalog.setval('tsd_pnet.sensortypes_id_seq', 1, false);


--
-- TOC entry 4408 (class 0 OID 0)
-- Dependencies: 291
-- Name: sites_id_seq; Type: SEQUENCE SET; Schema: tsd_pnet; Owner: postgres
--

SELECT pg_catalog.setval('tsd_pnet.sites_id_seq', 1, false);


--
-- TOC entry 4409 (class 0 OID 0)
-- Dependencies: 293
-- Name: stations_id_seq; Type: SEQUENCE SET; Schema: tsd_pnet; Owner: postgres
--

SELECT pg_catalog.setval('tsd_pnet.stations_id_seq', 1, false);


--
-- TOC entry 4216 (class 2606 OID 32449)
-- Name: channels channels_pkey; Type: CONSTRAINT; Schema: tsd_pnet; Owner: postgres
--

ALTER TABLE ONLY tsd_pnet.channels
    ADD CONSTRAINT channels_pkey PRIMARY KEY (id);


--
-- TOC entry 4214 (class 2606 OID 32435)
-- Name: digitizers digitizers_pkey; Type: CONSTRAINT; Schema: tsd_pnet; Owner: postgres
--

ALTER TABLE ONLY tsd_pnet.digitizers
    ADD CONSTRAINT digitizers_pkey PRIMARY KEY (id);


--
-- TOC entry 4212 (class 2606 OID 32423)
-- Name: digitizertypes digitizertypes_pkey; Type: CONSTRAINT; Schema: tsd_pnet; Owner: postgres
--

ALTER TABLE ONLY tsd_pnet.digitizertypes
    ADD CONSTRAINT digitizertypes_pkey PRIMARY KEY (id);


--
-- TOC entry 4218 (class 2606 OID 32466)
-- Name: nets nets_pkey; Type: CONSTRAINT; Schema: tsd_pnet; Owner: postgres
--

ALTER TABLE ONLY tsd_pnet.nets
    ADD CONSTRAINT nets_pkey PRIMARY KEY (id);


--
-- TOC entry 4200 (class 2606 OID 32339)
-- Name: owners owners_pkey; Type: CONSTRAINT; Schema: tsd_pnet; Owner: postgres
--

ALTER TABLE ONLY tsd_pnet.owners
    ADD CONSTRAINT owners_pkey PRIMARY KEY (id);


--
-- TOC entry 4210 (class 2606 OID 32411)
-- Name: sensors sensors_pkey; Type: CONSTRAINT; Schema: tsd_pnet; Owner: postgres
--

ALTER TABLE ONLY tsd_pnet.sensors
    ADD CONSTRAINT sensors_pkey PRIMARY KEY (id);


--
-- TOC entry 4206 (class 2606 OID 32384)
-- Name: sensortype_categories sensortype_categories_pkey; Type: CONSTRAINT; Schema: tsd_pnet; Owner: postgres
--

ALTER TABLE ONLY tsd_pnet.sensortype_categories
    ADD CONSTRAINT sensortype_categories_pkey PRIMARY KEY (id);


--
-- TOC entry 4208 (class 2606 OID 32396)
-- Name: sensortypes sensortypes_pkey; Type: CONSTRAINT; Schema: tsd_pnet; Owner: postgres
--

ALTER TABLE ONLY tsd_pnet.sensortypes
    ADD CONSTRAINT sensortypes_pkey PRIMARY KEY (id);


--
-- TOC entry 4202 (class 2606 OID 32360)
-- Name: sites sites_pkey; Type: CONSTRAINT; Schema: tsd_pnet; Owner: postgres
--

ALTER TABLE ONLY tsd_pnet.sites
    ADD CONSTRAINT sites_pkey PRIMARY KEY (id);


--
-- TOC entry 4204 (class 2606 OID 32372)
-- Name: stations stations_pkey; Type: CONSTRAINT; Schema: tsd_pnet; Owner: postgres
--

ALTER TABLE ONLY tsd_pnet.stations
    ADD CONSTRAINT stations_pkey PRIMARY KEY (id);


-- Completed on 2022-11-30 18:00:32

--
-- PostgreSQL database dump complete
--

