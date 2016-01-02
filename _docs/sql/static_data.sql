--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

SET search_path = public, pg_catalog;

--
-- Data for Name: certificate; Type: TABLE DATA; Schema: public; Owner: movies
--

INSERT INTO certificate VALUES (3, '12A', 3);
INSERT INTO certificate VALUES (1, '18', 1);
INSERT INTO certificate VALUES (2, '15', 2);
INSERT INTO certificate VALUES (4, '12', 4);
INSERT INTO certificate VALUES (5, 'PG', 5);
INSERT INTO certificate VALUES (6, 'U', 6);


--
-- Data for Name: genre; Type: TABLE DATA; Schema: public; Owner: movies
--

INSERT INTO genre VALUES (6, 'Adventure');
INSERT INTO genre VALUES (7, 'Drama');
INSERT INTO genre VALUES (8, 'Romance');
INSERT INTO genre VALUES (9, 'Thriller');
INSERT INTO genre VALUES (10, 'Mystery');
INSERT INTO genre VALUES (11, 'Action');
INSERT INTO genre VALUES (12, 'Comedy');
INSERT INTO genre VALUES (13, 'Biography');
INSERT INTO genre VALUES (14, 'Crime');
INSERT INTO genre VALUES (15, 'Horror');
INSERT INTO genre VALUES (17, 'Sport');
INSERT INTO genre VALUES (18, 'Western');
INSERT INTO genre VALUES (19, 'War');
INSERT INTO genre VALUES (20, 'Animation');
INSERT INTO genre VALUES (21, 'Fantasy');
INSERT INTO genre VALUES (22, 'History');
INSERT INTO genre VALUES (23, 'Family');
INSERT INTO genre VALUES (24, 'Music');
INSERT INTO genre VALUES (25, 'Musical');
INSERT INTO genre VALUES (26, 'Documentary');
INSERT INTO genre VALUES (16, 'Sci-Fi');
INSERT INTO genre VALUES (40, 'Film-Noir');


--
-- Data for Name: role; Type: TABLE DATA; Schema: public; Owner: movies
--

INSERT INTO role VALUES (1, 'director');
INSERT INTO role VALUES (2, 'actor');

--
-- Data for Name: provider; Type: TABLE DATA; Schema: public; Owner: movies
--

INSERT INTO provider VALUES (1, 'amazon_prime', 'Amazon Prime', 'https://www.amazon.co.uk/gp/video/library');
INSERT INTO provider VALUES (2, 'flixster', 'Flixster', 'https://video.flixster.com/collection#/sort/date-added/desc');
INSERT INTO provider VALUES (2, 'google_video', 'Google Video', 'https://play.google.com/store/account');


--
-- PostgreSQL database dump complete
--

INSERT INTO "user" VALUES(1, 'admin', 'ce674eaa43dc2f8e8bd9431f02ced457d744ae63', true, NOW(), 'admin');

