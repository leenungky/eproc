-- Adminer 4.7.7 PostgreSQL dump

\connect "eprocdb";

DROP TABLE IF EXISTS "failed_jobs";
DROP SEQUENCE IF EXISTS failed_jobs_id_seq;
CREATE SEQUENCE failed_jobs_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "vendormgt"."failed_jobs" (
    "id" bigint DEFAULT nextval('failed_jobs_id_seq') NOT NULL,
    "connection" text NOT NULL,
    "queue" text NOT NULL,
    "payload" text NOT NULL,
    "exception" text NOT NULL,
    "failed_at" timestamp(0) DEFAULT CURRENT_TIMESTAMP NOT NULL,
    CONSTRAINT "failed_jobs_pkey" PRIMARY KEY ("id")
) WITH (oids = false);


DROP TABLE IF EXISTS "migrations";
DROP SEQUENCE IF EXISTS migrations_id_seq;
CREATE SEQUENCE migrations_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 2147483647 START 1 CACHE 1;

CREATE TABLE "vendormgt"."migrations" (
    "id" integer DEFAULT nextval('migrations_id_seq') NOT NULL,
    "migration" character varying(255) NOT NULL,
    "batch" integer NOT NULL,
    CONSTRAINT "migrations_pkey" PRIMARY KEY ("id")
) WITH (oids = false);


DROP TABLE IF EXISTS "model_has_permissions";
CREATE TABLE "vendormgt"."model_has_permissions" (
    "permission_id" bigint NOT NULL,
    "model_type" character varying(255) NOT NULL,
    "model_id" bigint NOT NULL,
    CONSTRAINT "model_has_permissions_pkey" PRIMARY KEY ("permission_id", "model_id", "model_type"),
    CONSTRAINT "model_has_permissions_permission_id_foreign" FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE NOT DEFERRABLE
) WITH (oids = false);

CREATE INDEX "model_has_permissions_model_id_model_type_index" ON "vendormgt"."model_has_permissions" USING btree ("model_id", "model_type");


DROP TABLE IF EXISTS "model_has_roles";
CREATE TABLE "vendormgt"."model_has_roles" (
    "role_id" bigint NOT NULL,
    "model_type" character varying(255) NOT NULL,
    "model_id" bigint NOT NULL,
    CONSTRAINT "model_has_roles_pkey" PRIMARY KEY ("role_id", "model_id", "model_type"),
    CONSTRAINT "model_has_roles_role_id_foreign" FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE NOT DEFERRABLE
) WITH (oids = false);

CREATE INDEX "model_has_roles_model_id_model_type_index" ON "vendormgt"."model_has_roles" USING btree ("model_id", "model_type");


DROP TABLE IF EXISTS "page_contents";
DROP SEQUENCE IF EXISTS page_contents_id_seq;
CREATE SEQUENCE page_contents_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "vendormgt"."page_contents" (
    "id" bigint DEFAULT nextval('page_contents_id_seq') NOT NULL,
    "page_id" bigint NOT NULL,
    "language" character varying(64) DEFAULT 'en' NOT NULL,
    "title" character varying(255),
    "content" text,
    "created_by" character varying(255),
    "updated_by" character varying(255),
    "deleted_by" character varying(255),
    "created_at" timestamp(0),
    "updated_at" timestamp(0),
    "deleted_at" timestamp(0),
    CONSTRAINT "page_contents_page_id_language_unique" UNIQUE ("page_id", "language"),
    CONSTRAINT "page_contents_pkey" PRIMARY KEY ("id")
) WITH (oids = false);


DROP TABLE IF EXISTS "pages";
DROP SEQUENCE IF EXISTS pages_id_seq;
CREATE SEQUENCE pages_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "vendormgt"."pages" (
    "id" bigint DEFAULT nextval('pages_id_seq') NOT NULL,
    "name" character varying(64) NOT NULL,
    "parent_id" bigint DEFAULT '0' NOT NULL,
    "type" character varying(8) NOT NULL,
    "created_by" character varying(255),
    "updated_by" character varying(255),
    "deleted_by" character varying(255),
    "created_at" timestamp(0),
    "updated_at" timestamp(0),
    "deleted_at" timestamp(0),
    CONSTRAINT "pages_name_unique" UNIQUE ("name"),
    CONSTRAINT "pages_pkey" PRIMARY KEY ("id")
) WITH (oids = false);


DROP TABLE IF EXISTS "password_resets";
CREATE TABLE "vendormgt"."password_resets" (
    "email" character varying(255) NOT NULL,
    "token" character varying(255) NOT NULL,
    "created_at" timestamp(0)
) WITH (oids = false);

CREATE INDEX "password_resets_email_index" ON "vendormgt"."password_resets" USING btree ("email");


DROP TABLE IF EXISTS "permissions";
DROP SEQUENCE IF EXISTS permissions_id_seq;
CREATE SEQUENCE permissions_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "vendormgt"."permissions" (
    "id" bigint DEFAULT nextval('permissions_id_seq') NOT NULL,
    "name" character varying(255) NOT NULL,
    "guard_name" character varying(255) NOT NULL,
    "created_at" timestamp(0),
    "updated_at" timestamp(0),
    CONSTRAINT "permissions_pkey" PRIMARY KEY ("id")
) WITH (oids = false);


DROP TABLE IF EXISTS "purchase_requisitions";
DROP SEQUENCE IF EXISTS purchase_requisitions_id_seq;
CREATE SEQUENCE purchase_requisitions_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "vendormgt"."purchase_requisitions" (
    "id" bigint DEFAULT nextval('purchase_requisitions_id_seq') NOT NULL,
    "number" character varying(16) NOT NULL,
    "line_number" character varying(8) NOT NULL,
    "product_code" character varying(32),
    "product_group_code" character varying(8),
    "description" character varying(256),
    "requisitioner" character varying(32),
    "purch_group_code" character varying(8),
    "purch_group_name" character varying(32),
    "qty" numeric(19,3),
    "uom" character varying(16),
    "est_unit_price" numeric(19,2),
    "price_unit" integer DEFAULT '1',
    "currency_code" character varying(8),
    "subtotal" numeric(19,2),
    "state" character varying(32),
    "expected_delivery_date" timestamp(0),
    "transfer_date" timestamp(0),
    "account_assignment" character varying(1),
    "item_category" character varying(1),
    "gl_account" character varying(10),
    "wbs_element" character varying(24),
    "cost_center" character varying(10),
    "requisitioner_desc" character varying(80),
    "tracking_number" character varying(80),
    "request_date" date,
    "created_by" character varying(255),
    "updated_by" character varying(255),
    "deleted_by" character varying(255),
    "created_at" timestamp(0),
    "updated_at" timestamp(0),
    "deleted_at" timestamp(0),
    CONSTRAINT "purchase_requisitions_number_line_number_unique" UNIQUE ("number", "line_number"),
    CONSTRAINT "purchase_requisitions_pkey" PRIMARY KEY ("id")
) WITH (oids = false);

COMMENT ON COLUMN "vendormgt"."purchase_requisitions"."subtotal" IS 'qty * est_unit_price';


DROP TABLE IF EXISTS "ref_cities";
CREATE TABLE "vendormgt"."ref_cities" (
    "country_code" character varying(255) NOT NULL,
    "region_code" character varying(255) NOT NULL,
    "city_code" character varying(255) NOT NULL,
    "city_description" character varying(255) NOT NULL,
    "created_by" character varying(255) DEFAULT 'initial' NOT NULL,
    "created_at" timestamp(0),
    "updated_at" timestamp(0),
    "deleted_at" timestamp(0),
    CONSTRAINT "ref_cities_pkey" PRIMARY KEY ("city_code")
) WITH (oids = false);

COMMENT ON COLUMN "vendormgt"."ref_cities"."created_by" IS 'Define row who user created';


DROP TABLE IF EXISTS "ref_company_types";
DROP SEQUENCE IF EXISTS ref_company_types_id_seq;
CREATE SEQUENCE ref_company_types_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "vendormgt"."ref_company_types" (
    "id" bigint DEFAULT nextval('ref_company_types_id_seq') NOT NULL,
    "company_type" character varying(20) NOT NULL,
    "description" character varying(50) NOT NULL,
    "created_by" character varying(255) DEFAULT 'initial' NOT NULL,
    "created_at" timestamp(0),
    "updated_at" timestamp(0),
    "deleted_at" timestamp(0),
    "category" character varying(100) DEFAULT 'local',
    CONSTRAINT "ref_company_types_pkey" PRIMARY KEY ("id")
) WITH (oids = false);

COMMENT ON COLUMN "vendormgt"."ref_company_types"."created_by" IS 'Define row who user created';


DROP TABLE IF EXISTS "ref_countries";
CREATE TABLE "vendormgt"."ref_countries" (
    "country_code" character varying(255) NOT NULL,
    "country_description" character varying(255) NOT NULL,
    "created_by" character varying(255) DEFAULT 'initial' NOT NULL,
    "created_at" timestamp(0) DEFAULT now(),
    "updated_at" timestamp(0) DEFAULT now(),
    "deleted_at" timestamp(0),
    CONSTRAINT "ref_countries_pkey" PRIMARY KEY ("country_code")
) WITH (oids = false);

COMMENT ON COLUMN "vendormgt"."ref_countries"."created_by" IS 'Define row who user created';


DROP TABLE IF EXISTS "ref_list_options";
DROP SEQUENCE IF EXISTS ref_list_options_id_seq;
CREATE SEQUENCE ref_list_options_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "vendormgt"."ref_list_options" (
    "id" bigint DEFAULT nextval('ref_list_options_id_seq') NOT NULL,
    "type" character varying(64) NOT NULL,
    "key" character varying(64) NOT NULL,
    "value" character varying(64) NOT NULL,
    "deleteflg" boolean DEFAULT false NOT NULL,
    CONSTRAINT "ref_list_options_pkey" PRIMARY KEY ("id")
) WITH (oids = false);

CREATE INDEX "ref_list_options_type_index" ON "vendormgt"."ref_list_options" USING btree ("type");


DROP TABLE IF EXISTS "ref_plants";
DROP SEQUENCE IF EXISTS ref_plants_id_seq;
CREATE SEQUENCE ref_plants_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "vendormgt"."ref_plants" (
    "id" bigint DEFAULT nextval('ref_plants_id_seq') NOT NULL,
    "name" character varying(255),
    "created_by" character varying(255),
    "updated_by" character varying(255),
    "deleted_by" character varying(255),
    "created_at" timestamp(0),
    "updated_at" timestamp(0),
    "deleted_at" timestamp(0),
    CONSTRAINT "ref_plants_pkey" PRIMARY KEY ("id")
) WITH (oids = false);


DROP TABLE IF EXISTS "ref_provinces";
CREATE TABLE "vendormgt"."ref_provinces" (
    "country_code" character varying(255) NOT NULL,
    "region_code" character varying(255) NOT NULL,
    "region_description" character varying(255) NOT NULL,
    "created_by" character varying(255) DEFAULT 'initial' NOT NULL,
    "created_at" timestamp(0),
    "updated_at" timestamp(0),
    "deleted_at" timestamp(0),
    CONSTRAINT "ref_provinces_pkey" PRIMARY KEY ("country_code", "region_code")
) WITH (oids = false);

COMMENT ON COLUMN "vendormgt"."ref_provinces"."created_by" IS 'Define row who user created';

TRUNCATE "ref_provinces";
INSERT INTO "ref_provinces" ("country_code", "region_code", "region_description", "created_by", "created_at", "updated_at", "deleted_at") VALUES
('AR',	'00',	'Capital Federal',	'initial',	NULL,	NULL,	NULL),
('AR',	'01',	'Buenos Aires',	'initial',	NULL,	NULL,	NULL),
('AR',	'02',	'Catamarca',	'initial',	NULL,	NULL,	NULL),
('AR',	'03',	'Cordoba',	'initial',	NULL,	NULL,	NULL),
('AR',	'04',	'Corrientes',	'initial',	NULL,	NULL,	NULL),
('AR',	'05',	'Entre Rios',	'initial',	NULL,	NULL,	NULL),
('AR',	'06',	'Jujuy',	'initial',	NULL,	NULL,	NULL),
('AR',	'07',	'Mendoza',	'initial',	NULL,	NULL,	NULL),
('AR',	'08',	'La Rioja',	'initial',	NULL,	NULL,	NULL),
('AR',	'09',	'Salta',	'initial',	NULL,	NULL,	NULL),
('AR',	'10',	'San Juan',	'initial',	NULL,	NULL,	NULL),
('AR',	'11',	'San Luis',	'initial',	NULL,	NULL,	NULL),
('AR',	'12',	'Santa Fe',	'initial',	NULL,	NULL,	NULL),
('AR',	'13',	'Santiago del Estero',	'initial',	NULL,	NULL,	NULL),
('AR',	'14',	'Tucuman',	'initial',	NULL,	NULL,	NULL),
('AR',	'16',	'Chaco',	'initial',	NULL,	NULL,	NULL),
('AR',	'17',	'Chubut',	'initial',	NULL,	NULL,	NULL),
('AR',	'18',	'Formosa',	'initial',	NULL,	NULL,	NULL),
('AR',	'19',	'Misiones',	'initial',	NULL,	NULL,	NULL),
('AR',	'20',	'Neuquen',	'initial',	NULL,	NULL,	NULL),
('AR',	'21',	'La Pampa',	'initial',	NULL,	NULL,	NULL),
('AR',	'22',	'Rio Negro',	'initial',	NULL,	NULL,	NULL),
('AR',	'23',	'Santa Cruz',	'initial',	NULL,	NULL,	NULL),
('AR',	'24',	'Tierra de Fuego',	'initial',	NULL,	NULL,	NULL),
('AT',	'B',	'Burgenland',	'initial',	NULL,	NULL,	NULL),
('AT',	'K',	'Carinthia',	'initial',	NULL,	NULL,	NULL),
('AT',	'NOE',	'Lower Austria',	'initial',	NULL,	NULL,	NULL),
('AT',	'OOE',	'Upper Austria',	'initial',	NULL,	NULL,	NULL),
('AT',	'S',	'Salzburg',	'initial',	NULL,	NULL,	NULL),
('AT',	'ST',	'Styria',	'initial',	NULL,	NULL,	NULL),
('AT',	'T',	'Tyrol',	'initial',	NULL,	NULL,	NULL),
('AT',	'V',	'Vorarlberg',	'initial',	NULL,	NULL,	NULL),
('AT',	'W',	'Vienna',	'initial',	NULL,	NULL,	NULL),
('AU',	'ACT',	'Aust Capital Terr',	'initial',	NULL,	NULL,	NULL),
('AU',	'NSW',	'New South Wales',	'initial',	NULL,	NULL,	NULL),
('AU',	'NT',	'Northern Territory',	'initial',	NULL,	NULL,	NULL),
('AU',	'QLD',	'Queensland',	'initial',	NULL,	NULL,	NULL),
('AU',	'SA',	'South Australia',	'initial',	NULL,	NULL,	NULL),
('AU',	'TAS',	'Tasmania',	'initial',	NULL,	NULL,	NULL),
('AU',	'VIC',	'Victoria',	'initial',	NULL,	NULL,	NULL),
('AU',	'WA',	'Western Australia',	'initial',	NULL,	NULL,	NULL),
('BE',	'01',	'Antwerp',	'initial',	NULL,	NULL,	NULL),
('BE',	'02',	'Brabant',	'initial',	NULL,	NULL,	NULL),
('BE',	'03',	'Hainaut',	'initial',	NULL,	NULL,	NULL),
('BE',	'04',	'Liege',	'initial',	NULL,	NULL,	NULL),
('BE',	'05',	'Limburg',	'initial',	NULL,	NULL,	NULL),
('BE',	'06',	'Luxembourg',	'initial',	NULL,	NULL,	NULL),
('BE',	'07',	'Namur',	'initial',	NULL,	NULL,	NULL),
('BE',	'08',	'Oost-Vlaanderen',	'initial',	NULL,	NULL,	NULL),
('BE',	'09',	'West-Vlaanderen',	'initial',	NULL,	NULL,	NULL),
('BE',	'10',	'Brabant (Walloon)',	'initial',	NULL,	NULL,	NULL),
('BE',	'11',	'Brussels (Capital)',	'initial',	NULL,	NULL,	NULL),
('BG',	'01',	'Burgas',	'initial',	NULL,	NULL,	NULL),
('BG',	'02',	'Grad Sofiya',	'initial',	NULL,	NULL,	NULL),
('BG',	'03',	'Khaskovo',	'initial',	NULL,	NULL,	NULL),
('BG',	'04',	'Lovech',	'initial',	NULL,	NULL,	NULL),
('BG',	'05',	'Montana',	'initial',	NULL,	NULL,	NULL),
('BG',	'06',	'Plovdiv',	'initial',	NULL,	NULL,	NULL),
('BG',	'07',	'Ruse',	'initial',	NULL,	NULL,	NULL),
('BG',	'08',	'Sofiya',	'initial',	NULL,	NULL,	NULL),
('BG',	'09',	'Varna',	'initial',	NULL,	NULL,	NULL),
('BR',	'AC',	'Acre',	'initial',	NULL,	NULL,	NULL),
('BR',	'AL',	'Alagoas',	'initial',	NULL,	NULL,	NULL),
('BR',	'AM',	'Amazon',	'initial',	NULL,	NULL,	NULL),
('BR',	'AP',	'Amapa',	'initial',	NULL,	NULL,	NULL),
('BR',	'BA',	'Bahia',	'initial',	NULL,	NULL,	NULL),
('BR',	'CE',	'Ceara',	'initial',	NULL,	NULL,	NULL),
('BR',	'DF',	'Brasilia',	'initial',	NULL,	NULL,	NULL),
('BR',	'ES',	'Espirito Santo',	'initial',	NULL,	NULL,	NULL),
('BR',	'GO',	'Goias',	'initial',	NULL,	NULL,	NULL),
('BR',	'MA',	'Maranhao',	'initial',	NULL,	NULL,	NULL),
('BR',	'MG',	'Minas Gerais',	'initial',	NULL,	NULL,	NULL),
('BR',	'MS',	'Mato Grosso do Sul',	'initial',	NULL,	NULL,	NULL),
('BR',	'MT',	'Mato Grosso',	'initial',	NULL,	NULL,	NULL),
('BR',	'PA',	'Para',	'initial',	NULL,	NULL,	NULL),
('BR',	'PB',	'Paraiba',	'initial',	NULL,	NULL,	NULL),
('BR',	'PE',	'Pernambuco',	'initial',	NULL,	NULL,	NULL),
('BR',	'PI',	'Piaui',	'initial',	NULL,	NULL,	NULL),
('BR',	'PR',	'Parana',	'initial',	NULL,	NULL,	NULL),
('BR',	'RJ',	'Rio de Janeiro',	'initial',	NULL,	NULL,	NULL),
('BR',	'RN',	'Rio Grande do Norte',	'initial',	NULL,	NULL,	NULL),
('BR',	'RO',	'Rondonia',	'initial',	NULL,	NULL,	NULL),
('BR',	'RR',	'Roraima',	'initial',	NULL,	NULL,	NULL),
('BR',	'RS',	'Rio Grande do Sul',	'initial',	NULL,	NULL,	NULL),
('BR',	'SC',	'Santa Catarina',	'initial',	NULL,	NULL,	NULL),
('BR',	'SE',	'Sergipe',	'initial',	NULL,	NULL,	NULL),
('BR',	'SP',	'Sao Paulo',	'initial',	NULL,	NULL,	NULL),
('BR',	'TO',	'Tocantins',	'initial',	NULL,	NULL,	NULL),
('CA',	'AB',	'Alberta',	'initial',	NULL,	NULL,	NULL),
('CA',	'BC',	'British Columbia',	'initial',	NULL,	NULL,	NULL),
('CA',	'MB',	'Manitoba',	'initial',	NULL,	NULL,	NULL),
('CA',	'NB',	'New Brunswick',	'initial',	NULL,	NULL,	NULL),
('CA',	'NL',	'Newfoundland & Labr.',	'initial',	NULL,	NULL,	NULL),
('CA',	'NS',	'Nova Scotia',	'initial',	NULL,	NULL,	NULL),
('CA',	'NT',	'Northwest Territory',	'initial',	NULL,	NULL,	NULL),
('CA',	'NU',	'Nunavut',	'initial',	NULL,	NULL,	NULL),
('CA',	'ON',	'Ontario',	'initial',	NULL,	NULL,	NULL),
('CA',	'PE',	'Prince Edward Island',	'initial',	NULL,	NULL,	NULL),
('CA',	'QC',	'Quebec',	'initial',	NULL,	NULL,	NULL),
('CA',	'SK',	'Saskatchewan',	'initial',	NULL,	NULL,	NULL),
('CA',	'YT',	'Yukon Territory',	'initial',	NULL,	NULL,	NULL),
('CH',	'AG',	'Aargau',	'initial',	NULL,	NULL,	NULL),
('CH',	'AI',	'Inner-Rhoden',	'initial',	NULL,	NULL,	NULL),
('CH',	'AR',	'Ausser-Rhoden',	'initial',	NULL,	NULL,	NULL),
('CH',	'BE',	'Bern',	'initial',	NULL,	NULL,	NULL),
('CH',	'BL',	'Basel Land',	'initial',	NULL,	NULL,	NULL),
('CH',	'BS',	'Basel Stadt',	'initial',	NULL,	NULL,	NULL),
('CH',	'FR',	'Fribourg',	'initial',	NULL,	NULL,	NULL),
('CH',	'GE',	'Geneva',	'initial',	NULL,	NULL,	NULL),
('CH',	'GL',	'Glarus',	'initial',	NULL,	NULL,	NULL),
('CH',	'GR',	'Graubuenden',	'initial',	NULL,	NULL,	NULL),
('CH',	'JU',	'Jura',	'initial',	NULL,	NULL,	NULL),
('CH',	'LU',	'Lucerne',	'initial',	NULL,	NULL,	NULL),
('CH',	'NE',	'Neuchatel',	'initial',	NULL,	NULL,	NULL),
('CH',	'NW',	'Nidwalden',	'initial',	NULL,	NULL,	NULL),
('CH',	'OW',	'Obwalden',	'initial',	NULL,	NULL,	NULL),
('CH',	'SG',	'St. Gallen',	'initial',	NULL,	NULL,	NULL),
('CH',	'SH',	'Schaffhausen',	'initial',	NULL,	NULL,	NULL),
('CH',	'SO',	'Solothurn',	'initial',	NULL,	NULL,	NULL),
('CH',	'SZ',	'Schwyz',	'initial',	NULL,	NULL,	NULL),
('CH',	'TG',	'Thurgau',	'initial',	NULL,	NULL,	NULL),
('CH',	'TI',	'Ticino',	'initial',	NULL,	NULL,	NULL),
('CH',	'UR',	'Uri',	'initial',	NULL,	NULL,	NULL),
('CH',	'VD',	'Vaud',	'initial',	NULL,	NULL,	NULL),
('CH',	'VS',	'Valais',	'initial',	NULL,	NULL,	NULL),
('CH',	'ZG',	'Zug',	'initial',	NULL,	NULL,	NULL),
('CH',	'ZH',	'Zurich',	'initial',	NULL,	NULL,	NULL),
('CL',	'01',	'I - Iquique',	'initial',	NULL,	NULL,	NULL),
('CL',	'02',	'II - Antofagasta',	'initial',	NULL,	NULL,	NULL),
('CL',	'03',	'III - Copiapo',	'initial',	NULL,	NULL,	NULL),
('CL',	'04',	'IV - La Serena',	'initial',	NULL,	NULL,	NULL),
('CL',	'05',	'V - Valparaiso',	'initial',	NULL,	NULL,	NULL),
('CL',	'06',	'VI - Rancagua',	'initial',	NULL,	NULL,	NULL),
('CL',	'07',	'VII - Talca',	'initial',	NULL,	NULL,	NULL),
('CL',	'08',	'VIII - Concepcion',	'initial',	NULL,	NULL,	NULL),
('CL',	'09',	'IX - Temuco',	'initial',	NULL,	NULL,	NULL),
('CL',	'10',	'X - Puerto Montt',	'initial',	NULL,	NULL,	NULL),
('CL',	'11',	'XI - Coyhaique',	'initial',	NULL,	NULL,	NULL),
('CL',	'12',	'XII - Punta Arenas',	'initial',	NULL,	NULL,	NULL),
('CL',	'13',	'RM - Santiago',	'initial',	NULL,	NULL,	NULL),
('CL',	'14',	'XIV - Los Rios',	'initial',	NULL,	NULL,	NULL),
('CL',	'15',	'XV - Arica y Parinac',	'initial',	NULL,	NULL,	NULL),
('CN',	'010',	'Beijing',	'initial',	NULL,	NULL,	NULL),
('CN',	'020',	'Shanghai',	'initial',	NULL,	NULL,	NULL),
('CN',	'030',	'Tianjin',	'initial',	NULL,	NULL,	NULL),
('CN',	'040',	'Nei Mongol',	'initial',	NULL,	NULL,	NULL),
('CN',	'050',	'Shanxi',	'initial',	NULL,	NULL,	NULL),
('CN',	'060',	'Hebei',	'initial',	NULL,	NULL,	NULL),
('CN',	'070',	'Liaoning',	'initial',	NULL,	NULL,	NULL),
('CN',	'080',	'Jilin',	'initial',	NULL,	NULL,	NULL),
('CN',	'090',	'Heilongjiang',	'initial',	NULL,	NULL,	NULL),
('CN',	'100',	'Jiangsu',	'initial',	NULL,	NULL,	NULL),
('CN',	'110',	'Anhui',	'initial',	NULL,	NULL,	NULL),
('CN',	'120',	'Shandong',	'initial',	NULL,	NULL,	NULL),
('CN',	'130',	'Zhejiang',	'initial',	NULL,	NULL,	NULL),
('CN',	'140',	'Jiangxi',	'initial',	NULL,	NULL,	NULL),
('CN',	'150',	'Fujian',	'initial',	NULL,	NULL,	NULL),
('CN',	'160',	'Hunan',	'initial',	NULL,	NULL,	NULL),
('CN',	'170',	'Hubei',	'initial',	NULL,	NULL,	NULL),
('CN',	'180',	'Henan',	'initial',	NULL,	NULL,	NULL),
('CN',	'190',	'Guangdong',	'initial',	NULL,	NULL,	NULL),
('CN',	'200',	'Hainan',	'initial',	NULL,	NULL,	NULL),
('CN',	'210',	'Guangxi',	'initial',	NULL,	NULL,	NULL),
('CN',	'220',	'Guizhou',	'initial',	NULL,	NULL,	NULL),
('CN',	'230',	'Sichuan',	'initial',	NULL,	NULL,	NULL),
('CN',	'240',	'Yunnan',	'initial',	NULL,	NULL,	NULL),
('CN',	'250',	'Shaanxi',	'initial',	NULL,	NULL,	NULL),
('CN',	'260',	'Gansu',	'initial',	NULL,	NULL,	NULL),
('CN',	'270',	'Ningxia',	'initial',	NULL,	NULL,	NULL),
('CN',	'280',	'Qinghai',	'initial',	NULL,	NULL,	NULL),
('CN',	'290',	'Xinjiang',	'initial',	NULL,	NULL,	NULL),
('CN',	'300',	'Xizang',	'initial',	NULL,	NULL,	NULL),
('CN',	'320',	'Chong Qing',	'initial',	NULL,	NULL,	NULL),
('CN',	'TW',	'Taiwan',	'initial',	NULL,	NULL,	NULL),
('CO',	'05',	'ANTIOQUIA',	'initial',	NULL,	NULL,	NULL),
('CO',	'08',	'ATLANTICO',	'initial',	NULL,	NULL,	NULL),
('CO',	'11',	'BOGOTA',	'initial',	NULL,	NULL,	NULL),
('CO',	'13',	'BOLIVAR',	'initial',	NULL,	NULL,	NULL),
('CO',	'15',	'BOYACA',	'initial',	NULL,	NULL,	NULL),
('CO',	'17',	'CALDAS',	'initial',	NULL,	NULL,	NULL),
('CO',	'18',	'CAQUETA',	'initial',	NULL,	NULL,	NULL),
('CO',	'19',	'CAUCA',	'initial',	NULL,	NULL,	NULL),
('CO',	'20',	'CESAR',	'initial',	NULL,	NULL,	NULL),
('CO',	'23',	'CORDOBA',	'initial',	NULL,	NULL,	NULL),
('CO',	'25',	'CUNDINAMARCA',	'initial',	NULL,	NULL,	NULL),
('CO',	'27',	'CHOCO',	'initial',	NULL,	NULL,	NULL),
('CO',	'41',	'HUILA',	'initial',	NULL,	NULL,	NULL),
('CO',	'44',	'LA GUAJIRA',	'initial',	NULL,	NULL,	NULL),
('CO',	'47',	'MAGDALENA',	'initial',	NULL,	NULL,	NULL),
('CO',	'50',	'META',	'initial',	NULL,	NULL,	NULL),
('CO',	'52',	'NARINO',	'initial',	NULL,	NULL,	NULL),
('CO',	'54',	'NORTE SANTANDER',	'initial',	NULL,	NULL,	NULL),
('CO',	'63',	'QUINDIO',	'initial',	NULL,	NULL,	NULL),
('CO',	'66',	'RISARALDA',	'initial',	NULL,	NULL,	NULL),
('CO',	'68',	'SANTANDER',	'initial',	NULL,	NULL,	NULL),
('CO',	'70',	'SUCRE',	'initial',	NULL,	NULL,	NULL),
('CO',	'73',	'TOLIMA',	'initial',	NULL,	NULL,	NULL),
('CO',	'76',	'VALLE',	'initial',	NULL,	NULL,	NULL),
('CO',	'81',	'ARAUCA',	'initial',	NULL,	NULL,	NULL),
('CO',	'85',	'CASANARE',	'initial',	NULL,	NULL,	NULL),
('CO',	'86',	'PUTUMAYO',	'initial',	NULL,	NULL,	NULL),
('CO',	'88',	'SAN ANDRES',	'initial',	NULL,	NULL,	NULL),
('CO',	'91',	'AMAZONAS',	'initial',	NULL,	NULL,	NULL),
('CO',	'94',	'GUAINIA',	'initial',	NULL,	NULL,	NULL),
('CO',	'95',	'GUAVIARE',	'initial',	NULL,	NULL,	NULL),
('CO',	'97',	'VAUPES',	'initial',	NULL,	NULL,	NULL),
('CO',	'99',	'VICHADA',	'initial',	NULL,	NULL,	NULL),
('CZ',	'11',	'Praha',	'initial',	NULL,	NULL,	NULL),
('CZ',	'21',	'Stredocesky',	'initial',	NULL,	NULL,	NULL),
('CZ',	'31',	'Jihocesky',	'initial',	NULL,	NULL,	NULL),
('CZ',	'32',	'Plzensky',	'initial',	NULL,	NULL,	NULL),
('CZ',	'41',	'Karlovarsky',	'initial',	NULL,	NULL,	NULL),
('CZ',	'42',	'Ustecky',	'initial',	NULL,	NULL,	NULL),
('CZ',	'51',	'Liberecky',	'initial',	NULL,	NULL,	NULL),
('CZ',	'52',	'Kralovehradecky',	'initial',	NULL,	NULL,	NULL),
('CZ',	'53',	'Pardubicky',	'initial',	NULL,	NULL,	NULL),
('CZ',	'63',	'Vysocina',	'initial',	NULL,	NULL,	NULL),
('CZ',	'64',	'Jihomoravsky',	'initial',	NULL,	NULL,	NULL),
('CZ',	'71',	'Olomoucky',	'initial',	NULL,	NULL,	NULL),
('CZ',	'72',	'Zlinsky',	'initial',	NULL,	NULL,	NULL),
('CZ',	'81',	'Moravskoslezsky',	'initial',	NULL,	NULL,	NULL),
('DE',	'01',	'Schleswig-Holstein',	'initial',	NULL,	NULL,	NULL),
('DE',	'02',	'Hamburg',	'initial',	NULL,	NULL,	NULL),
('DE',	'03',	'Lower Saxony',	'initial',	NULL,	NULL,	NULL),
('DE',	'04',	'Bremen',	'initial',	NULL,	NULL,	NULL),
('DE',	'05',	'Nrth Rhine Westfalia',	'initial',	NULL,	NULL,	NULL),
('DE',	'06',	'Hessen',	'initial',	NULL,	NULL,	NULL),
('DE',	'07',	'Rhineland Palatinate',	'initial',	NULL,	NULL,	NULL),
('DE',	'08',	'Baden-Wurttemberg',	'initial',	NULL,	NULL,	NULL),
('DE',	'09',	'Bavaria',	'initial',	NULL,	NULL,	NULL),
('DE',	'10',	'Saarland',	'initial',	NULL,	NULL,	NULL),
('DE',	'11',	'Berlin',	'initial',	NULL,	NULL,	NULL),
('DE',	'12',	'Brandenburg',	'initial',	NULL,	NULL,	NULL),
('DE',	'13',	'Mecklenburg-Vorpomm.',	'initial',	NULL,	NULL,	NULL),
('DE',	'14',	'Saxony',	'initial',	NULL,	NULL,	NULL),
('DE',	'15',	'Saxony-Anhalt',	'initial',	NULL,	NULL,	NULL),
('DE',	'16',	'Thuringia',	'initial',	NULL,	NULL,	NULL),
('DK',	'001',	'Danish Capital Reg.',	'initial',	NULL,	NULL,	NULL),
('DK',	'002',	'Central Jutland',	'initial',	NULL,	NULL,	NULL),
('DK',	'003',	'North Jutland',	'initial',	NULL,	NULL,	NULL),
('DK',	'004',	'Zealand',	'initial',	NULL,	NULL,	NULL),
('DK',	'005',	'South Denmark',	'initial',	NULL,	NULL,	NULL),
('EG',	'01',	'Cairo',	'initial',	NULL,	NULL,	NULL),
('EG',	'02',	'Giza',	'initial',	NULL,	NULL,	NULL),
('EG',	'03',	'Al Sharqia',	'initial',	NULL,	NULL,	NULL),
('EG',	'04',	'Dakahlia',	'initial',	NULL,	NULL,	NULL),
('EG',	'05',	'Beheira',	'initial',	NULL,	NULL,	NULL),
('EG',	'06',	'Qalyubia',	'initial',	NULL,	NULL,	NULL),
('EG',	'07',	'Minya',	'initial',	NULL,	NULL,	NULL),
('EG',	'08',	'Alexandria',	'initial',	NULL,	NULL,	NULL),
('EG',	'09',	'Gharbia',	'initial',	NULL,	NULL,	NULL),
('EG',	'10',	'Sohag',	'initial',	NULL,	NULL,	NULL),
('EG',	'11',	'Asyut',	'initial',	NULL,	NULL,	NULL),
('EG',	'12',	'Monufia',	'initial',	NULL,	NULL,	NULL),
('EG',	'13',	'Qena',	'initial',	NULL,	NULL,	NULL),
('EG',	'14',	'Kafr El-Sheikh',	'initial',	NULL,	NULL,	NULL),
('EG',	'15',	'Faiyum',	'initial',	NULL,	NULL,	NULL),
('EG',	'16',	'Beni Suef',	'initial',	NULL,	NULL,	NULL),
('EG',	'17',	'Aswan',	'initial',	NULL,	NULL,	NULL),
('EG',	'18',	'Damietta',	'initial',	NULL,	NULL,	NULL),
('EG',	'19',	'Ismailia',	'initial',	NULL,	NULL,	NULL),
('EG',	'20',	'Port Said',	'initial',	NULL,	NULL,	NULL),
('EG',	'21',	'Suez',	'initial',	NULL,	NULL,	NULL),
('EG',	'22',	'Luxor',	'initial',	NULL,	NULL,	NULL),
('EG',	'23',	'North Sinai',	'initial',	NULL,	NULL,	NULL),
('EG',	'24',	'Matruh',	'initial',	NULL,	NULL,	NULL),
('EG',	'25',	'Red Sea',	'initial',	NULL,	NULL,	NULL),
('EG',	'26',	'New Valley',	'initial',	NULL,	NULL,	NULL),
('EG',	'27',	'South Sinai',	'initial',	NULL,	NULL,	NULL),
('ES',	'01',	'Alava',	'initial',	NULL,	NULL,	NULL),
('ES',	'02',	'Albacete',	'initial',	NULL,	NULL,	NULL),
('ES',	'03',	'Alicante',	'initial',	NULL,	NULL,	NULL),
('ES',	'04',	'Almeria',	'initial',	NULL,	NULL,	NULL),
('ES',	'05',	'Avila',	'initial',	NULL,	NULL,	NULL),
('ES',	'06',	'Badajoz',	'initial',	NULL,	NULL,	NULL),
('ES',	'07',	'Baleares',	'initial',	NULL,	NULL,	NULL),
('ES',	'08',	'Barcelona',	'initial',	NULL,	NULL,	NULL),
('ES',	'09',	'Burgos',	'initial',	NULL,	NULL,	NULL),
('ES',	'10',	'Caceres',	'initial',	NULL,	NULL,	NULL),
('ES',	'11',	'Cadiz',	'initial',	NULL,	NULL,	NULL),
('ES',	'12',	'Castellon',	'initial',	NULL,	NULL,	NULL),
('ES',	'13',	'Ciudad Real',	'initial',	NULL,	NULL,	NULL),
('ES',	'14',	'Cordoba',	'initial',	NULL,	NULL,	NULL),
('ES',	'15',	'La Coruna',	'initial',	NULL,	NULL,	NULL),
('ES',	'16',	'Cuenca',	'initial',	NULL,	NULL,	NULL),
('ES',	'17',	'Gerona',	'initial',	NULL,	NULL,	NULL),
('ES',	'18',	'Granada',	'initial',	NULL,	NULL,	NULL),
('ES',	'19',	'Guadalajara',	'initial',	NULL,	NULL,	NULL),
('ES',	'20',	'Guipuzcoa',	'initial',	NULL,	NULL,	NULL),
('ES',	'21',	'Huelva',	'initial',	NULL,	NULL,	NULL),
('ES',	'22',	'Huesca',	'initial',	NULL,	NULL,	NULL),
('ES',	'23',	'Jaen',	'initial',	NULL,	NULL,	NULL),
('ES',	'24',	'Leon',	'initial',	NULL,	NULL,	NULL),
('ES',	'25',	'Lerida',	'initial',	NULL,	NULL,	NULL),
('ES',	'26',	'La Rioja',	'initial',	NULL,	NULL,	NULL),
('ES',	'27',	'Lugo',	'initial',	NULL,	NULL,	NULL),
('ES',	'28',	'Madrid',	'initial',	NULL,	NULL,	NULL),
('ES',	'29',	'Malaga',	'initial',	NULL,	NULL,	NULL),
('ES',	'30',	'Murcia',	'initial',	NULL,	NULL,	NULL),
('ES',	'31',	'Navarra',	'initial',	NULL,	NULL,	NULL),
('ES',	'32',	'Orense',	'initial',	NULL,	NULL,	NULL),
('ES',	'33',	'Asturias',	'initial',	NULL,	NULL,	NULL),
('ES',	'34',	'Palencia',	'initial',	NULL,	NULL,	NULL),
('ES',	'35',	'Las Palmas',	'initial',	NULL,	NULL,	NULL),
('ES',	'36',	'Pontevedra',	'initial',	NULL,	NULL,	NULL),
('ES',	'37',	'Salamanca',	'initial',	NULL,	NULL,	NULL),
('ES',	'38',	'Sta. Cruz Tenerife',	'initial',	NULL,	NULL,	NULL),
('ES',	'39',	'Cantabria',	'initial',	NULL,	NULL,	NULL),
('ES',	'40',	'Segovia',	'initial',	NULL,	NULL,	NULL),
('ES',	'41',	'Sevilla',	'initial',	NULL,	NULL,	NULL),
('ES',	'42',	'Soria',	'initial',	NULL,	NULL,	NULL),
('ES',	'43',	'Tarragona',	'initial',	NULL,	NULL,	NULL),
('ES',	'44',	'Teruel',	'initial',	NULL,	NULL,	NULL),
('ES',	'45',	'Toledo',	'initial',	NULL,	NULL,	NULL),
('ES',	'46',	'Valencia',	'initial',	NULL,	NULL,	NULL),
('ES',	'47',	'Valladolid',	'initial',	NULL,	NULL,	NULL),
('ES',	'48',	'Vizcaya',	'initial',	NULL,	NULL,	NULL),
('ES',	'49',	'Zamora',	'initial',	NULL,	NULL,	NULL),
('ES',	'50',	'Zaragoza',	'initial',	NULL,	NULL,	NULL),
('FI',	'001',	'Ahvenanmaa',	'initial',	NULL,	NULL,	NULL),
('FI',	'002',	'Southern Finnland',	'initial',	NULL,	NULL,	NULL),
('FI',	'003',	'Eastern Finnland',	'initial',	NULL,	NULL,	NULL),
('FI',	'004',	'Lappi',	'initial',	NULL,	NULL,	NULL),
('FI',	'005',	'Western Finnland',	'initial',	NULL,	NULL,	NULL),
('FI',	'006',	'Oulu',	'initial',	NULL,	NULL,	NULL),
('FR',	'01',	'Ain',	'initial',	NULL,	NULL,	NULL),
('FR',	'02',	'Aisne',	'initial',	NULL,	NULL,	NULL),
('FR',	'03',	'Allier',	'initial',	NULL,	NULL,	NULL),
('FR',	'04',	'Alpes (Hte-Provence)',	'initial',	NULL,	NULL,	NULL),
('FR',	'05',	'Alpes (Hautes)',	'initial',	NULL,	NULL,	NULL),
('FR',	'06',	'Alpes-Maritimes',	'initial',	NULL,	NULL,	NULL),
('FR',	'07',	'Ardeche',	'initial',	NULL,	NULL,	NULL),
('FR',	'08',	'Ardennes',	'initial',	NULL,	NULL,	NULL),
('FR',	'09',	'Ariege',	'initial',	NULL,	NULL,	NULL),
('FR',	'10',	'Aube',	'initial',	NULL,	NULL,	NULL),
('FR',	'11',	'Aude',	'initial',	NULL,	NULL,	NULL),
('FR',	'12',	'Aveyron',	'initial',	NULL,	NULL,	NULL),
('FR',	'13',	'Bouches-du-Rhone',	'initial',	NULL,	NULL,	NULL),
('FR',	'14',	'Calvados',	'initial',	NULL,	NULL,	NULL),
('FR',	'15',	'Cantal',	'initial',	NULL,	NULL,	NULL),
('FR',	'16',	'Charente',	'initial',	NULL,	NULL,	NULL),
('FR',	'17',	'Charente-Maritime',	'initial',	NULL,	NULL,	NULL),
('FR',	'18',	'Cher',	'initial',	NULL,	NULL,	NULL),
('FR',	'19',	'Correze',	'initial',	NULL,	NULL,	NULL),
('FR',	'21',	'Cote-d''Or',	'initial',	NULL,	NULL,	NULL),
('FR',	'22',	'Cotes-d''Armor',	'initial',	NULL,	NULL,	NULL),
('FR',	'23',	'Creuse',	'initial',	NULL,	NULL,	NULL),
('FR',	'24',	'Dordogne',	'initial',	NULL,	NULL,	NULL),
('FR',	'25',	'Doubs',	'initial',	NULL,	NULL,	NULL),
('FR',	'26',	'Drome',	'initial',	NULL,	NULL,	NULL),
('FR',	'27',	'Eure',	'initial',	NULL,	NULL,	NULL),
('FR',	'28',	'Eure-et-Loir',	'initial',	NULL,	NULL,	NULL),
('FR',	'29',	'Finistere',	'initial',	NULL,	NULL,	NULL),
('FR',	'2A',	'Corse-du-Sud',	'initial',	NULL,	NULL,	NULL),
('FR',	'2B',	'Corse-du-Nord',	'initial',	NULL,	NULL,	NULL),
('FR',	'30',	'Gard',	'initial',	NULL,	NULL,	NULL),
('FR',	'31',	'Garonne (Haute)',	'initial',	NULL,	NULL,	NULL),
('FR',	'32',	'Gers',	'initial',	NULL,	NULL,	NULL),
('FR',	'33',	'Gironde',	'initial',	NULL,	NULL,	NULL),
('FR',	'34',	'Herault',	'initial',	NULL,	NULL,	NULL),
('FR',	'35',	'Ille-et-Vilaine',	'initial',	NULL,	NULL,	NULL),
('FR',	'36',	'Indre',	'initial',	NULL,	NULL,	NULL),
('FR',	'37',	'Indre-et-Loire',	'initial',	NULL,	NULL,	NULL),
('FR',	'38',	'Isere',	'initial',	NULL,	NULL,	NULL),
('FR',	'39',	'Jura',	'initial',	NULL,	NULL,	NULL),
('FR',	'40',	'Landes',	'initial',	NULL,	NULL,	NULL),
('FR',	'41',	'Loir-et-Cher',	'initial',	NULL,	NULL,	NULL),
('FR',	'42',	'Loire',	'initial',	NULL,	NULL,	NULL),
('FR',	'43',	'Loire (Haute)',	'initial',	NULL,	NULL,	NULL),
('FR',	'44',	'Loire-Atlantique',	'initial',	NULL,	NULL,	NULL),
('FR',	'45',	'Loiret',	'initial',	NULL,	NULL,	NULL),
('FR',	'46',	'Lot',	'initial',	NULL,	NULL,	NULL),
('FR',	'47',	'Lot-et-Garonne',	'initial',	NULL,	NULL,	NULL),
('FR',	'48',	'Lozere',	'initial',	NULL,	NULL,	NULL),
('FR',	'49',	'Maine-et-Loire',	'initial',	NULL,	NULL,	NULL),
('FR',	'50',	'Manche',	'initial',	NULL,	NULL,	NULL),
('FR',	'51',	'Marne',	'initial',	NULL,	NULL,	NULL),
('FR',	'52',	'Marne (Haute)',	'initial',	NULL,	NULL,	NULL),
('FR',	'53',	'Mayenne',	'initial',	NULL,	NULL,	NULL),
('FR',	'54',	'Meurthe-et-Moselle',	'initial',	NULL,	NULL,	NULL),
('FR',	'55',	'Meuse',	'initial',	NULL,	NULL,	NULL),
('FR',	'56',	'Morbihan',	'initial',	NULL,	NULL,	NULL),
('FR',	'57',	'Moselle',	'initial',	NULL,	NULL,	NULL),
('FR',	'58',	'Nievre',	'initial',	NULL,	NULL,	NULL),
('FR',	'59',	'Nord',	'initial',	NULL,	NULL,	NULL),
('FR',	'60',	'Oise',	'initial',	NULL,	NULL,	NULL),
('FR',	'61',	'Orne',	'initial',	NULL,	NULL,	NULL),
('FR',	'62',	'Pas-de-Calais',	'initial',	NULL,	NULL,	NULL),
('FR',	'63',	'Puy-de-Dome',	'initial',	NULL,	NULL,	NULL),
('FR',	'64',	'Pyrenees-Atlantiques',	'initial',	NULL,	NULL,	NULL),
('FR',	'65',	'Pyrenees (Hautes)',	'initial',	NULL,	NULL,	NULL),
('FR',	'66',	'Pyrenees-Orientales',	'initial',	NULL,	NULL,	NULL),
('FR',	'67',	'Bas-Rhin',	'initial',	NULL,	NULL,	NULL),
('FR',	'68',	'Haut-Rhin',	'initial',	NULL,	NULL,	NULL),
('FR',	'69',	'Rhone',	'initial',	NULL,	NULL,	NULL),
('FR',	'70',	'Saone (Haute)',	'initial',	NULL,	NULL,	NULL),
('FR',	'71',	'Saone-et-Loire',	'initial',	NULL,	NULL,	NULL),
('FR',	'72',	'Sarthe',	'initial',	NULL,	NULL,	NULL),
('FR',	'73',	'Savoie',	'initial',	NULL,	NULL,	NULL),
('FR',	'74',	'Savoie (Haute)',	'initial',	NULL,	NULL,	NULL),
('FR',	'75',	'Paris',	'initial',	NULL,	NULL,	NULL),
('FR',	'76',	'Seine-Maritime',	'initial',	NULL,	NULL,	NULL),
('FR',	'77',	'Seine-et-Marne',	'initial',	NULL,	NULL,	NULL),
('FR',	'78',	'Yvelines',	'initial',	NULL,	NULL,	NULL),
('FR',	'79',	'Sevres (Deux)',	'initial',	NULL,	NULL,	NULL),
('FR',	'80',	'Somme',	'initial',	NULL,	NULL,	NULL),
('FR',	'81',	'Tarn',	'initial',	NULL,	NULL,	NULL),
('FR',	'82',	'Tarn-et-Garonne',	'initial',	NULL,	NULL,	NULL),
('FR',	'83',	'Var',	'initial',	NULL,	NULL,	NULL),
('FR',	'84',	'Vaucluse',	'initial',	NULL,	NULL,	NULL),
('FR',	'85',	'Vendee',	'initial',	NULL,	NULL,	NULL),
('FR',	'86',	'Vienne',	'initial',	NULL,	NULL,	NULL),
('FR',	'87',	'Vienne (Haute)',	'initial',	NULL,	NULL,	NULL),
('FR',	'88',	'Vosges',	'initial',	NULL,	NULL,	NULL),
('FR',	'89',	'Yonne',	'initial',	NULL,	NULL,	NULL),
('FR',	'90',	'Territ.-de-Belfort',	'initial',	NULL,	NULL,	NULL),
('FR',	'91',	'Essonne',	'initial',	NULL,	NULL,	NULL),
('FR',	'92',	'Hauts-de-Seine',	'initial',	NULL,	NULL,	NULL),
('FR',	'93',	'Seine-Saint-Denis',	'initial',	NULL,	NULL,	NULL),
('FR',	'94',	'Val-de-Marne',	'initial',	NULL,	NULL,	NULL),
('FR',	'95',	'Val-d''Oise',	'initial',	NULL,	NULL,	NULL),
('FR',	'97',	'D.O.M.-T.O.M.',	'initial',	NULL,	NULL,	NULL),
('FR',	'971',	'Guadeloupe',	'initial',	NULL,	NULL,	NULL),
('FR',	'972',	'Martinique',	'initial',	NULL,	NULL,	NULL),
('FR',	'973',	'Guyane',	'initial',	NULL,	NULL,	NULL),
('FR',	'974',	'Reunion',	'initial',	NULL,	NULL,	NULL),
('FR',	'975',	'Saint-Pierre-et-Miq.',	'initial',	NULL,	NULL,	NULL),
('FR',	'976',	'Wallis-et-Futuna',	'initial',	NULL,	NULL,	NULL),
('FR',	'99',	'Hors-France',	'initial',	NULL,	NULL,	NULL),
('GB',	'AB',	'Aberdeenshire',	'initial',	NULL,	NULL,	NULL),
('GB',	'AG',	'Argyllshire',	'initial',	NULL,	NULL,	NULL),
('GB',	'AL',	'Anglesey',	'initial',	NULL,	NULL,	NULL),
('GB',	'AM',	'Armagh',	'initial',	NULL,	NULL,	NULL),
('GB',	'AN',	'Angus/Forfarshire',	'initial',	NULL,	NULL,	NULL),
('GB',	'AT',	'Antrim',	'initial',	NULL,	NULL,	NULL),
('GB',	'AY',	'Ayrshire',	'initial',	NULL,	NULL,	NULL),
('GB',	'BE',	'Bedfordshire',	'initial',	NULL,	NULL,	NULL),
('GB',	'BF',	'Banffshire',	'initial',	NULL,	NULL,	NULL),
('GB',	'BK',	'Berkshire',	'initial',	NULL,	NULL,	NULL),
('GB',	'BR',	'Brecknockshire',	'initial',	NULL,	NULL,	NULL),
('GB',	'BS',	'Bath&NthEstSomerset',	'initial',	NULL,	NULL,	NULL),
('GB',	'BT',	'Buteshire',	'initial',	NULL,	NULL,	NULL),
('GB',	'BU',	'Buckinghamshire',	'initial',	NULL,	NULL,	NULL),
('GB',	'BW',	'Berwickshire',	'initial',	NULL,	NULL,	NULL),
('GB',	'CA',	'Cambridgeshire',	'initial',	NULL,	NULL,	NULL),
('GB',	'CB',	'Carmarthenshire',	'initial',	NULL,	NULL,	NULL),
('GB',	'CD',	'Cardiganshire',	'initial',	NULL,	NULL,	NULL),
('GB',	'CF',	'Caernarfonshire',	'initial',	NULL,	NULL,	NULL),
('GB',	'CH',	'Cheshire',	'initial',	NULL,	NULL,	NULL),
('GB',	'CM',	'Cromartyshire',	'initial',	NULL,	NULL,	NULL),
('GB',	'CN',	'Clackmannanshire',	'initial',	NULL,	NULL,	NULL),
('GB',	'CO',	'Cornwall',	'initial',	NULL,	NULL,	NULL),
('GB',	'CT',	'Caithness',	'initial',	NULL,	NULL,	NULL),
('GB',	'CU',	'Cumberland',	'initial',	NULL,	NULL,	NULL),
('GB',	'DB',	'Derbyshire',	'initial',	NULL,	NULL,	NULL),
('GB',	'DD',	'Denbighshire',	'initial',	NULL,	NULL,	NULL),
('GB',	'DF',	'Dumfriesshire',	'initial',	NULL,	NULL,	NULL),
('GB',	'DN',	'Down',	'initial',	NULL,	NULL,	NULL),
('GB',	'DO',	'Dorset',	'initial',	NULL,	NULL,	NULL),
('GB',	'DT',	'Dunbartonshire',	'initial',	NULL,	NULL,	NULL),
('GB',	'DU',	'Durham',	'initial',	NULL,	NULL,	NULL),
('GB',	'DV',	'Devon',	'initial',	NULL,	NULL,	NULL),
('GB',	'EL',	'East Lothian',	'initial',	NULL,	NULL,	NULL),
('GB',	'ES',	'Essex',	'initial',	NULL,	NULL,	NULL),
('GB',	'FI',	'Fife',	'initial',	NULL,	NULL,	NULL),
('GB',	'FL',	'Flintshire',	'initial',	NULL,	NULL,	NULL),
('GB',	'FM',	'Fermanagh',	'initial',	NULL,	NULL,	NULL),
('GB',	'GL',	'Gloucestershire',	'initial',	NULL,	NULL,	NULL),
('GB',	'HA',	'Hampshire',	'initial',	NULL,	NULL,	NULL),
('GB',	'HT',	'Hertfordshire',	'initial',	NULL,	NULL,	NULL),
('GB',	'HU',	'Huntingdonshire',	'initial',	NULL,	NULL,	NULL),
('GB',	'HW',	'Hereford and Worcs.',	'initial',	NULL,	NULL,	NULL),
('GB',	'IN',	'Invernesshire',	'initial',	NULL,	NULL,	NULL),
('GB',	'IW',	'Isle of Wight',	'initial',	NULL,	NULL,	NULL),
('GB',	'KE',	'Kent',	'initial',	NULL,	NULL,	NULL),
('GB',	'KI',	'Kincardineshire',	'initial',	NULL,	NULL,	NULL),
('GB',	'KK',	'Kirkcudbrightshire',	'initial',	NULL,	NULL,	NULL),
('GB',	'KN',	'Kinross-shire',	'initial',	NULL,	NULL,	NULL),
('GB',	'LA',	'Lancashire',	'initial',	NULL,	NULL,	NULL),
('GB',	'LD',	'Londonderry',	'initial',	NULL,	NULL,	NULL),
('GB',	'LE',	'Leicestershire',	'initial',	NULL,	NULL,	NULL),
('GB',	'LI',	'Lincolnshire',	'initial',	NULL,	NULL,	NULL),
('GB',	'LN',	'Lanarkshire',	'initial',	NULL,	NULL,	NULL),
('GB',	'MD',	'Midlothian',	'initial',	NULL,	NULL,	NULL),
('GB',	'ME',	'Merioneth',	'initial',	NULL,	NULL,	NULL),
('GB',	'MG',	'Mid Glamorgan',	'initial',	NULL,	NULL,	NULL),
('GB',	'MM',	'Monmouthshire',	'initial',	NULL,	NULL,	NULL),
('GB',	'MR',	'Morayshire',	'initial',	NULL,	NULL,	NULL),
('GB',	'MT',	'Montgomeryshire',	'initial',	NULL,	NULL,	NULL),
('GB',	'MX',	'Middlesex',	'initial',	NULL,	NULL,	NULL),
('GB',	'NH',	'Northamptonshire',	'initial',	NULL,	NULL,	NULL),
('GB',	'NK',	'Norfolk',	'initial',	NULL,	NULL,	NULL),
('GB',	'NR',	'Nairnshire',	'initial',	NULL,	NULL,	NULL),
('GB',	'NT',	'Nottinghamshire',	'initial',	NULL,	NULL,	NULL),
('GB',	'NU',	'Northumberland',	'initial',	NULL,	NULL,	NULL),
('GB',	'OR',	'Orkney',	'initial',	NULL,	NULL,	NULL),
('GB',	'OX',	'Oxfordshire',	'initial',	NULL,	NULL,	NULL),
('GB',	'PE',	'Peeblesshire',	'initial',	NULL,	NULL,	NULL),
('GB',	'PM',	'Pembrokeshire',	'initial',	NULL,	NULL,	NULL),
('GB',	'PR',	'Perthshire',	'initial',	NULL,	NULL,	NULL),
('GB',	'RA',	'Radnorshire',	'initial',	NULL,	NULL,	NULL),
('GB',	'RE',	'Renfrewshire',	'initial',	NULL,	NULL,	NULL),
('GB',	'RO',	'Ross-shire',	'initial',	NULL,	NULL,	NULL),
('GB',	'RU',	'Rutland',	'initial',	NULL,	NULL,	NULL),
('GB',	'RX',	'Roxburghshire',	'initial',	NULL,	NULL,	NULL),
('GB',	'SE',	'East Sussex',	'initial',	NULL,	NULL,	NULL),
('GB',	'SF',	'Selkirkshire',	'initial',	NULL,	NULL,	NULL),
('GB',	'SG',	'South Glamorgan',	'initial',	NULL,	NULL,	NULL),
('GB',	'SH',	'Shropshire',	'initial',	NULL,	NULL,	NULL),
('GB',	'SK',	'Suffolk',	'initial',	NULL,	NULL,	NULL),
('GB',	'SL',	'Shetland',	'initial',	NULL,	NULL,	NULL),
('GB',	'SO',	'Somerset',	'initial',	NULL,	NULL,	NULL),
('GB',	'ST',	'Staffordshire',	'initial',	NULL,	NULL,	NULL),
('GB',	'SU',	'Sutherland',	'initial',	NULL,	NULL,	NULL),
('GB',	'SV',	'Stirlingshire',	'initial',	NULL,	NULL,	NULL),
('GB',	'SW',	'West Sussex',	'initial',	NULL,	NULL,	NULL),
('GB',	'SY',	'Surrey',	'initial',	NULL,	NULL,	NULL),
('GB',	'TY',	'Tyrone',	'initial',	NULL,	NULL,	NULL),
('GB',	'WA',	'Warwickshire',	'initial',	NULL,	NULL,	NULL),
('GB',	'WC',	'Worcestershire',	'initial',	NULL,	NULL,	NULL),
('GB',	'WE',	'Westmorland',	'initial',	NULL,	NULL,	NULL),
('GB',	'WG',	'West Glamorgan',	'initial',	NULL,	NULL,	NULL),
('GB',	'WI',	'Wiltshire',	'initial',	NULL,	NULL,	NULL),
('GB',	'WK',	'West Lothian',	'initial',	NULL,	NULL,	NULL),
('GB',	'WT',	'Wigtownshire',	'initial',	NULL,	NULL,	NULL),
('GB',	'YN',	'North Yorkshire',	'initial',	NULL,	NULL,	NULL),
('GB',	'YS',	'South Yorkshire',	'initial',	NULL,	NULL,	NULL),
('GB',	'YW',	'West Yorkshire',	'initial',	NULL,	NULL,	NULL),
('GR',	'01',	'Aitolia kai Akarnan.',	'initial',	NULL,	NULL,	NULL),
('GR',	'02',	'Akhaia',	'initial',	NULL,	NULL,	NULL),
('GR',	'03',	'Argolis',	'initial',	NULL,	NULL,	NULL),
('GR',	'04',	'Arkadhia',	'initial',	NULL,	NULL,	NULL),
('GR',	'05',	'Arta',	'initial',	NULL,	NULL,	NULL),
('GR',	'06',	'Attiki',	'initial',	NULL,	NULL,	NULL),
('GR',	'07',	'Dhodhekanisos',	'initial',	NULL,	NULL,	NULL),
('GR',	'08',	'Dhrama',	'initial',	NULL,	NULL,	NULL),
('GR',	'09',	'Evritania',	'initial',	NULL,	NULL,	NULL),
('GR',	'10',	'Evros',	'initial',	NULL,	NULL,	NULL),
('GR',	'11',	'Evvoia',	'initial',	NULL,	NULL,	NULL),
('GR',	'12',	'Florina',	'initial',	NULL,	NULL,	NULL),
('GR',	'13',	'Fokis',	'initial',	NULL,	NULL,	NULL),
('GR',	'14',	'Fthiotis',	'initial',	NULL,	NULL,	NULL),
('GR',	'15',	'Grevena',	'initial',	NULL,	NULL,	NULL),
('GR',	'16',	'Ilia',	'initial',	NULL,	NULL,	NULL),
('GR',	'17',	'Imathia',	'initial',	NULL,	NULL,	NULL),
('GR',	'18',	'Ioannina',	'initial',	NULL,	NULL,	NULL),
('GR',	'19',	'Iraklion',	'initial',	NULL,	NULL,	NULL),
('GR',	'20',	'Kardhitsa',	'initial',	NULL,	NULL,	NULL),
('GR',	'21',	'Kastoria',	'initial',	NULL,	NULL,	NULL),
('GR',	'22',	'Kavala',	'initial',	NULL,	NULL,	NULL),
('GR',	'23',	'Kefallinia',	'initial',	NULL,	NULL,	NULL),
('GR',	'24',	'Kerkira',	'initial',	NULL,	NULL,	NULL),
('GR',	'25',	'Khalkidhiki',	'initial',	NULL,	NULL,	NULL),
('GR',	'26',	'Khania',	'initial',	NULL,	NULL,	NULL),
('GR',	'27',	'Khios',	'initial',	NULL,	NULL,	NULL),
('GR',	'28',	'Kikladhes',	'initial',	NULL,	NULL,	NULL),
('GR',	'29',	'Kilkis',	'initial',	NULL,	NULL,	NULL),
('GR',	'30',	'Korinthia',	'initial',	NULL,	NULL,	NULL),
('GR',	'31',	'Kozani',	'initial',	NULL,	NULL,	NULL),
('GR',	'32',	'Lakonia',	'initial',	NULL,	NULL,	NULL),
('GR',	'33',	'Larisa',	'initial',	NULL,	NULL,	NULL),
('GR',	'34',	'Lasithi',	'initial',	NULL,	NULL,	NULL),
('GR',	'35',	'Lesvos',	'initial',	NULL,	NULL,	NULL),
('GR',	'36',	'Levkas',	'initial',	NULL,	NULL,	NULL),
('GR',	'37',	'Magnisia',	'initial',	NULL,	NULL,	NULL),
('GR',	'38',	'Messinia',	'initial',	NULL,	NULL,	NULL),
('GR',	'39',	'Pella',	'initial',	NULL,	NULL,	NULL),
('GR',	'40',	'Pieria',	'initial',	NULL,	NULL,	NULL),
('GR',	'41',	'Piraievs',	'initial',	NULL,	NULL,	NULL),
('GR',	'42',	'Preveza',	'initial',	NULL,	NULL,	NULL),
('GR',	'43',	'Rethimni',	'initial',	NULL,	NULL,	NULL),
('GR',	'44',	'Rodhopi',	'initial',	NULL,	NULL,	NULL),
('GR',	'45',	'Samos',	'initial',	NULL,	NULL,	NULL),
('GR',	'46',	'Serrai',	'initial',	NULL,	NULL,	NULL),
('GR',	'47',	'Thesprotia',	'initial',	NULL,	NULL,	NULL),
('GR',	'48',	'Thessaloniki',	'initial',	NULL,	NULL,	NULL),
('GR',	'49',	'Trikala',	'initial',	NULL,	NULL,	NULL),
('GR',	'50',	'Voiotia',	'initial',	NULL,	NULL,	NULL),
('GR',	'51',	'Xanthi',	'initial',	NULL,	NULL,	NULL),
('GR',	'52',	'Zakinthos',	'initial',	NULL,	NULL,	NULL),
('HK',	'HK',	'Hong Kong Island',	'initial',	NULL,	NULL,	NULL),
('HK',	'KLN',	'Kowloon',	'initial',	NULL,	NULL,	NULL),
('HK',	'NT',	'New Territories',	'initial',	NULL,	NULL,	NULL),
('HR',	'A00',	'Zagrebacka',	'initial',	NULL,	NULL,	NULL),
('HR',	'B00',	'Krapinsko-zagorska',	'initial',	NULL,	NULL,	NULL),
('HR',	'C00',	'Sisacko-moslavacka',	'initial',	NULL,	NULL,	NULL),
('HR',	'D00',	'Karlovacka',	'initial',	NULL,	NULL,	NULL),
('HR',	'E00',	'Varazdinska',	'initial',	NULL,	NULL,	NULL),
('HR',	'F00',	'Koprivnicko-krizevac',	'initial',	NULL,	NULL,	NULL),
('HR',	'G00',	'Bjelovarsko-bilogors',	'initial',	NULL,	NULL,	NULL),
('HR',	'H00',	'Rijecko-goranska',	'initial',	NULL,	NULL,	NULL),
('HR',	'I00',	'Licko-senjska',	'initial',	NULL,	NULL,	NULL),
('HR',	'J00',	'Viroviticko-podravac',	'initial',	NULL,	NULL,	NULL),
('HR',	'K00',	'Pozesko-slavonska',	'initial',	NULL,	NULL,	NULL),
('HR',	'L00',	'Slavonskobrodska',	'initial',	NULL,	NULL,	NULL),
('HR',	'M00',	'Zadarska',	'initial',	NULL,	NULL,	NULL),
('HR',	'N00',	'Osjecko-baranjska',	'initial',	NULL,	NULL,	NULL),
('HR',	'O00',	'Sibensko-kninska',	'initial',	NULL,	NULL,	NULL),
('HR',	'P00',	'Vukovarsko-srijemska',	'initial',	NULL,	NULL,	NULL),
('HR',	'R00',	'Splitsko-dalmatinska',	'initial',	NULL,	NULL,	NULL),
('HR',	'S00',	'Istarska',	'initial',	NULL,	NULL,	NULL),
('HR',	'T00',	'Dubrovacko-neretvans',	'initial',	NULL,	NULL,	NULL),
('HR',	'U00',	'Medjimurska',	'initial',	NULL,	NULL,	NULL),
('HR',	'V00',	'Zagreb',	'initial',	NULL,	NULL,	NULL),
('HU',	'01',	'Bacs-Kiskun',	'initial',	NULL,	NULL,	NULL),
('HU',	'02',	'Baranya',	'initial',	NULL,	NULL,	NULL),
('HU',	'03',	'Bekes',	'initial',	NULL,	NULL,	NULL),
('HU',	'04',	'Bekescsaba',	'initial',	NULL,	NULL,	NULL),
('HU',	'05',	'Borsod-Abauj-Zemplen',	'initial',	NULL,	NULL,	NULL),
('HU',	'06',	'Budapest',	'initial',	NULL,	NULL,	NULL),
('HU',	'07',	'Csongrad',	'initial',	NULL,	NULL,	NULL),
('HU',	'08',	'Debrecen',	'initial',	NULL,	NULL,	NULL),
('HU',	'09',	'Dunaujvaros',	'initial',	NULL,	NULL,	NULL),
('HU',	'10',	'Eger',	'initial',	NULL,	NULL,	NULL),
('HU',	'11',	'Fejer',	'initial',	NULL,	NULL,	NULL),
('HU',	'12',	'Gyor',	'initial',	NULL,	NULL,	NULL),
('HU',	'13',	'Gyor-Moson-Sopron',	'initial',	NULL,	NULL,	NULL),
('HU',	'14',	'Hajdu-Bihar',	'initial',	NULL,	NULL,	NULL),
('HU',	'15',	'Heves',	'initial',	NULL,	NULL,	NULL),
('HU',	'16',	'Hodmezovasarhely',	'initial',	NULL,	NULL,	NULL),
('HU',	'17',	'Jasz-Nagykun-Szolnok',	'initial',	NULL,	NULL,	NULL),
('HU',	'18',	'Kaposvar',	'initial',	NULL,	NULL,	NULL),
('HU',	'19',	'Kecskemet',	'initial',	NULL,	NULL,	NULL),
('HU',	'20',	'Komarom-Esztergom',	'initial',	NULL,	NULL,	NULL),
('HU',	'21',	'Miskolc',	'initial',	NULL,	NULL,	NULL),
('HU',	'22',	'Nagykanizsa',	'initial',	NULL,	NULL,	NULL),
('HU',	'23',	'Nograd',	'initial',	NULL,	NULL,	NULL),
('HU',	'24',	'Nyiregyhaza',	'initial',	NULL,	NULL,	NULL),
('HU',	'25',	'Pecs',	'initial',	NULL,	NULL,	NULL),
('HU',	'26',	'Pest',	'initial',	NULL,	NULL,	NULL),
('HU',	'27',	'Somogy',	'initial',	NULL,	NULL,	NULL),
('HU',	'28',	'Sopron',	'initial',	NULL,	NULL,	NULL),
('HU',	'29',	'Szabolcs-Szat.-Bereg',	'initial',	NULL,	NULL,	NULL),
('HU',	'30',	'Szeged',	'initial',	NULL,	NULL,	NULL),
('HU',	'31',	'Szekesfehervar',	'initial',	NULL,	NULL,	NULL),
('HU',	'32',	'Szolnok',	'initial',	NULL,	NULL,	NULL),
('HU',	'33',	'Szombathely',	'initial',	NULL,	NULL,	NULL),
('HU',	'34',	'Tatabanya',	'initial',	NULL,	NULL,	NULL),
('HU',	'35',	'Tolna',	'initial',	NULL,	NULL,	NULL),
('HU',	'36',	'Vas',	'initial',	NULL,	NULL,	NULL),
('HU',	'37',	'Veszprem',	'initial',	NULL,	NULL,	NULL),
('HU',	'38',	'Zala',	'initial',	NULL,	NULL,	NULL),
('HU',	'39',	'Zalaegerszeg',	'initial',	NULL,	NULL,	NULL),
('ID',	'01',	'DKI Jakarta Jakarta',	'initial',	NULL,	NULL,	NULL),
('ID',	'02',	'Jawa Barat West Java',	'initial',	NULL,	NULL,	NULL),
('ID',	'03',	'Jawa Tengah Central',	'initial',	NULL,	NULL,	NULL),
('ID',	'04',	'Jawa Timur East Java',	'initial',	NULL,	NULL,	NULL),
('ID',	'05',	'DI Yogyakarta Yogyak',	'initial',	NULL,	NULL,	NULL),
('ID',	'07',	'Sumatera Utara North',	'initial',	NULL,	NULL,	NULL),
('ID',	'08',	'Sumatera Barat West',	'initial',	NULL,	NULL,	NULL),
('ID',	'09',	'Riau Riau',	'initial',	NULL,	NULL,	NULL),
('ID',	'10',	'Jambi Jambi',	'initial',	NULL,	NULL,	NULL),
('ID',	'11',	'Sumatera Selatan Sou',	'initial',	NULL,	NULL,	NULL),
('ID',	'12',	'Bengkulu Bengkulu',	'initial',	NULL,	NULL,	NULL),
('ID',	'13',	'Lampung Lampung',	'initial',	NULL,	NULL,	NULL),
('ID',	'14',	'Kalimantan Selatan S',	'initial',	NULL,	NULL,	NULL),
('ID',	'15',	'Kalimantan Barat Wes',	'initial',	NULL,	NULL,	NULL),
('ID',	'16',	'Kalimantan Tengah Ce',	'initial',	NULL,	NULL,	NULL),
('ID',	'17',	'Kalimantan Timur Eas',	'initial',	NULL,	NULL,	NULL),
('ID',	'18',	'Sulawesi Selatan Sou',	'initial',	NULL,	NULL,	NULL),
('ID',	'19',	'Sulawesi Tenggara So',	'initial',	NULL,	NULL,	NULL),
('ID',	'20',	'Sulawesi Tengah Cent',	'initial',	NULL,	NULL,	NULL),
('ID',	'21',	'Sulawesi Utara North',	'initial',	NULL,	NULL,	NULL),
('ID',	'22',	'Bali Bali',	'initial',	NULL,	NULL,	NULL),
('ID',	'23',	'Nusa Tenggara Barat',	'initial',	NULL,	NULL,	NULL),
('ID',	'24',	'Nusa Tenggara Timur',	'initial',	NULL,	NULL,	NULL),
('ID',	'25',	'Maluku Maluku',	'initial',	NULL,	NULL,	NULL),
('ID',	'26',	'Papua Papua',	'initial',	NULL,	NULL,	NULL),
('ID',	'27',	'Banten',	'initial',	NULL,	NULL,	NULL),
('ID',	'28',	'Kep. Bangka Belitung',	'initial',	NULL,	NULL,	NULL),
('ID',	'29',	'Kepulauan Riau Riau',	'initial',	NULL,	NULL,	NULL),
('ID',	'30',	'Kalimantan Utara Nor',	'initial',	NULL,	NULL,	NULL),
('ID',	'31',	'Gorontalo Gorontalo',	'initial',	NULL,	NULL,	NULL),
('ID',	'32',	'Sulawesi Barat West',	'initial',	NULL,	NULL,	NULL),
('ID',	'33',	'Maluku Utara North',	'initial',	NULL,	NULL,	NULL),
('ID',	'34',	'Papua Barat West Pap',	'initial',	NULL,	NULL,	NULL),
('IE',	'C',	'Cork',	'initial',	NULL,	NULL,	NULL),
('IE',	'CE',	'Clare',	'initial',	NULL,	NULL,	NULL),
('IE',	'CN',	'Cavan',	'initial',	NULL,	NULL,	NULL),
('IE',	'CW',	'Carlow',	'initial',	NULL,	NULL,	NULL),
('IE',	'D',	'Dublin',	'initial',	NULL,	NULL,	NULL),
('IE',	'DL',	'Donegal',	'initial',	NULL,	NULL,	NULL),
('IE',	'G',	'Galway',	'initial',	NULL,	NULL,	NULL),
('IE',	'KE',	'Kildare',	'initial',	NULL,	NULL,	NULL),
('IE',	'KK',	'Kilkenny',	'initial',	NULL,	NULL,	NULL),
('IE',	'KY',	'Kerry',	'initial',	NULL,	NULL,	NULL),
('IE',	'LD',	'Longford',	'initial',	NULL,	NULL,	NULL),
('IE',	'LH',	'Louth',	'initial',	NULL,	NULL,	NULL),
('IE',	'LK',	'Limerick',	'initial',	NULL,	NULL,	NULL),
('IE',	'LM',	'Leitrim',	'initial',	NULL,	NULL,	NULL),
('IE',	'LS',	'Laois',	'initial',	NULL,	NULL,	NULL),
('IE',	'MH',	'Meath',	'initial',	NULL,	NULL,	NULL),
('IE',	'MN',	'Monaghan',	'initial',	NULL,	NULL,	NULL),
('IE',	'MO',	'Mayo',	'initial',	NULL,	NULL,	NULL),
('IE',	'OY',	'Offaly',	'initial',	NULL,	NULL,	NULL),
('IE',	'RN',	'Roscommon',	'initial',	NULL,	NULL,	NULL),
('IE',	'SO',	'Sligo',	'initial',	NULL,	NULL,	NULL),
('IE',	'TA',	'Tipperary',	'initial',	NULL,	NULL,	NULL),
('IE',	'WD',	'Waterford',	'initial',	NULL,	NULL,	NULL),
('IE',	'WH',	'Westmeath',	'initial',	NULL,	NULL,	NULL),
('IE',	'WW',	'Wicklow',	'initial',	NULL,	NULL,	NULL),
('IE',	'WX',	'Wexford',	'initial',	NULL,	NULL,	NULL),
('IL',	'01',	'Central',	'initial',	NULL,	NULL,	NULL),
('IL',	'02',	'Haifa',	'initial',	NULL,	NULL,	NULL),
('IL',	'03',	'Jerusalem',	'initial',	NULL,	NULL,	NULL),
('IL',	'04',	'Northern',	'initial',	NULL,	NULL,	NULL),
('IL',	'05',	'Southern',	'initial',	NULL,	NULL,	NULL),
('IL',	'06',	'Tel Aviv',	'initial',	NULL,	NULL,	NULL),
('IN',	'01',	'Andhra Pradesh',	'initial',	NULL,	NULL,	NULL),
('IN',	'02',	'Arunachal Pradesh',	'initial',	NULL,	NULL,	NULL),
('IN',	'03',	'Assam',	'initial',	NULL,	NULL,	NULL),
('IN',	'04',	'Bihar',	'initial',	NULL,	NULL,	NULL),
('IN',	'05',	'Goa',	'initial',	NULL,	NULL,	NULL),
('IN',	'06',	'Gujarat',	'initial',	NULL,	NULL,	NULL),
('IN',	'07',	'Haryana',	'initial',	NULL,	NULL,	NULL),
('IN',	'08',	'Himachal Pradesh',	'initial',	NULL,	NULL,	NULL),
('IN',	'09',	'Jammu and Kashmir',	'initial',	NULL,	NULL,	NULL),
('IN',	'10',	'Karnataka',	'initial',	NULL,	NULL,	NULL),
('IN',	'11',	'Kerala',	'initial',	NULL,	NULL,	NULL),
('IN',	'12',	'Madhya Pradesh',	'initial',	NULL,	NULL,	NULL),
('IN',	'13',	'Maharashtra',	'initial',	NULL,	NULL,	NULL),
('IN',	'14',	'Manipur',	'initial',	NULL,	NULL,	NULL),
('IN',	'15',	'Meghalaya',	'initial',	NULL,	NULL,	NULL),
('IN',	'16',	'Mizoram',	'initial',	NULL,	NULL,	NULL),
('IN',	'17',	'Nagaland',	'initial',	NULL,	NULL,	NULL),
('IN',	'18',	'Orissa',	'initial',	NULL,	NULL,	NULL),
('IN',	'19',	'Punjab',	'initial',	NULL,	NULL,	NULL),
('IN',	'20',	'Rajasthan',	'initial',	NULL,	NULL,	NULL),
('IN',	'21',	'Sikkim',	'initial',	NULL,	NULL,	NULL),
('IN',	'22',	'Tamil Nadu',	'initial',	NULL,	NULL,	NULL),
('IN',	'23',	'Tripura',	'initial',	NULL,	NULL,	NULL),
('IN',	'24',	'Uttar Pradesh',	'initial',	NULL,	NULL,	NULL),
('IN',	'25',	'West Bengal',	'initial',	NULL,	NULL,	NULL),
('IN',	'26',	'Andaman and Nico.Is.',	'initial',	NULL,	NULL,	NULL),
('IN',	'27',	'Chandigarh',	'initial',	NULL,	NULL,	NULL),
('IN',	'28',	'Dadra and Nagar Hav.',	'initial',	NULL,	NULL,	NULL),
('IN',	'29',	'Daman and Diu',	'initial',	NULL,	NULL,	NULL),
('IN',	'30',	'Delhi',	'initial',	NULL,	NULL,	NULL),
('IN',	'31',	'Lakshadweep',	'initial',	NULL,	NULL,	NULL),
('IN',	'32',	'Puducherry',	'initial',	NULL,	NULL,	NULL),
('IN',	'33',	'Chhattisgarh',	'initial',	NULL,	NULL,	NULL),
('IN',	'34',	'Jharkhand',	'initial',	NULL,	NULL,	NULL),
('IN',	'35',	'Uttarakhand',	'initial',	NULL,	NULL,	NULL),
('IT',	'AG',	'Agriento',	'initial',	NULL,	NULL,	NULL),
('IT',	'AL',	'Alessandria',	'initial',	NULL,	NULL,	NULL),
('IT',	'AN',	'Ancona',	'initial',	NULL,	NULL,	NULL),
('IT',	'AO',	'Aosta',	'initial',	NULL,	NULL,	NULL),
('IT',	'AP',	'Ascoli Piceno',	'initial',	NULL,	NULL,	NULL),
('IT',	'AQ',	'L''Aquila',	'initial',	NULL,	NULL,	NULL),
('IT',	'AR',	'Arezzo',	'initial',	NULL,	NULL,	NULL),
('IT',	'AT',	'Asti',	'initial',	NULL,	NULL,	NULL),
('IT',	'AV',	'Avellino',	'initial',	NULL,	NULL,	NULL),
('IT',	'BA',	'Bari',	'initial',	NULL,	NULL,	NULL),
('IT',	'BG',	'Bergamo',	'initial',	NULL,	NULL,	NULL),
('IT',	'BI',	'Biella',	'initial',	NULL,	NULL,	NULL),
('IT',	'BL',	'Belluno',	'initial',	NULL,	NULL,	NULL),
('IT',	'BN',	'Benevento',	'initial',	NULL,	NULL,	NULL),
('IT',	'BO',	'Bologna',	'initial',	NULL,	NULL,	NULL),
('IT',	'BR',	'Brindisi',	'initial',	NULL,	NULL,	NULL),
('IT',	'BS',	'Brescia',	'initial',	NULL,	NULL,	NULL),
('IT',	'BT',	'Barletta-Andria-Tr.',	'initial',	NULL,	NULL,	NULL),
('IT',	'BZ',	'Bolzano',	'initial',	NULL,	NULL,	NULL),
('IT',	'CA',	'Cagliari',	'initial',	NULL,	NULL,	NULL),
('IT',	'CB',	'Campobasso',	'initial',	NULL,	NULL,	NULL),
('IT',	'CE',	'Caserta',	'initial',	NULL,	NULL,	NULL),
('IT',	'CH',	'Chieti',	'initial',	NULL,	NULL,	NULL),
('IT',	'CI',	'Carbonia-Iglesias',	'initial',	NULL,	NULL,	NULL),
('IT',	'CL',	'Caltanisetta',	'initial',	NULL,	NULL,	NULL),
('IT',	'CN',	'Cuneo',	'initial',	NULL,	NULL,	NULL),
('IT',	'CO',	'Como',	'initial',	NULL,	NULL,	NULL),
('IT',	'CR',	'Cremona',	'initial',	NULL,	NULL,	NULL),
('IT',	'CS',	'Cosenza',	'initial',	NULL,	NULL,	NULL),
('IT',	'CT',	'Catania',	'initial',	NULL,	NULL,	NULL),
('IT',	'CZ',	'Catanzaro',	'initial',	NULL,	NULL,	NULL),
('IT',	'EE',	'Stati Esteri',	'initial',	NULL,	NULL,	NULL),
('IT',	'EN',	'Enna',	'initial',	NULL,	NULL,	NULL),
('IT',	'FC',	'Forli-Cesana',	'initial',	NULL,	NULL,	NULL),
('IT',	'FE',	'Ferrara',	'initial',	NULL,	NULL,	NULL),
('IT',	'FG',	'Foggia',	'initial',	NULL,	NULL,	NULL),
('IT',	'FI',	'Florence',	'initial',	NULL,	NULL,	NULL),
('IT',	'FM',	'Fermo',	'initial',	NULL,	NULL,	NULL),
('IT',	'FR',	'Frosinone',	'initial',	NULL,	NULL,	NULL),
('IT',	'FU',	'Fiume',	'initial',	NULL,	NULL,	NULL),
('IT',	'GE',	'Genova',	'initial',	NULL,	NULL,	NULL),
('IT',	'GO',	'Gorizia',	'initial',	NULL,	NULL,	NULL),
('IT',	'GR',	'Grosseto',	'initial',	NULL,	NULL,	NULL),
('IT',	'IM',	'Imperia',	'initial',	NULL,	NULL,	NULL),
('IT',	'IS',	'Isernia',	'initial',	NULL,	NULL,	NULL),
('IT',	'KR',	'Crotone',	'initial',	NULL,	NULL,	NULL),
('IT',	'LC',	'Lecco',	'initial',	NULL,	NULL,	NULL),
('IT',	'LE',	'Lecce',	'initial',	NULL,	NULL,	NULL),
('IT',	'LI',	'Livorno',	'initial',	NULL,	NULL,	NULL),
('IT',	'LO',	'Lodi',	'initial',	NULL,	NULL,	NULL),
('IT',	'LT',	'Latina',	'initial',	NULL,	NULL,	NULL),
('IT',	'LU',	'Lucca',	'initial',	NULL,	NULL,	NULL),
('IT',	'MB',	'Monza e Brianza',	'initial',	NULL,	NULL,	NULL),
('IT',	'MC',	'Macerata',	'initial',	NULL,	NULL,	NULL),
('IT',	'ME',	'Messina',	'initial',	NULL,	NULL,	NULL),
('IT',	'MI',	'Milan',	'initial',	NULL,	NULL,	NULL),
('IT',	'MN',	'Mantova',	'initial',	NULL,	NULL,	NULL),
('IT',	'MO',	'Modena',	'initial',	NULL,	NULL,	NULL),
('IT',	'MS',	'Massa Carrara',	'initial',	NULL,	NULL,	NULL),
('IT',	'MT',	'Matera',	'initial',	NULL,	NULL,	NULL),
('IT',	'NA',	'Naples',	'initial',	NULL,	NULL,	NULL),
('IT',	'NO',	'Novara',	'initial',	NULL,	NULL,	NULL),
('IT',	'NU',	'Nuoro',	'initial',	NULL,	NULL,	NULL),
('IT',	'OG',	'Ogliastra',	'initial',	NULL,	NULL,	NULL),
('IT',	'OR',	'Oristano',	'initial',	NULL,	NULL,	NULL),
('IT',	'OT',	'Olbia-Tempio',	'initial',	NULL,	NULL,	NULL),
('IT',	'PA',	'Palermo',	'initial',	NULL,	NULL,	NULL),
('IT',	'PC',	'Piacenza',	'initial',	NULL,	NULL,	NULL),
('IT',	'PD',	'Padova',	'initial',	NULL,	NULL,	NULL),
('IT',	'PE',	'Pescara',	'initial',	NULL,	NULL,	NULL),
('IT',	'PG',	'Perugia',	'initial',	NULL,	NULL,	NULL),
('IT',	'PI',	'Pisa',	'initial',	NULL,	NULL,	NULL),
('IT',	'PL',	'Pola',	'initial',	NULL,	NULL,	NULL),
('IT',	'PN',	'Pordenone',	'initial',	NULL,	NULL,	NULL),
('IT',	'PO',	'Prato',	'initial',	NULL,	NULL,	NULL),
('IT',	'PR',	'Parma',	'initial',	NULL,	NULL,	NULL),
('IT',	'PT',	'Pistoia',	'initial',	NULL,	NULL,	NULL),
('IT',	'PU',	'Pesaro-Urbino',	'initial',	NULL,	NULL,	NULL),
('IT',	'PV',	'Pavia',	'initial',	NULL,	NULL,	NULL),
('IT',	'PZ',	'Potenza',	'initial',	NULL,	NULL,	NULL),
('IT',	'RA',	'Ravenna',	'initial',	NULL,	NULL,	NULL),
('IT',	'RC',	'Reggio Calabria',	'initial',	NULL,	NULL,	NULL),
('IT',	'RE',	'Reggio Emilia',	'initial',	NULL,	NULL,	NULL),
('IT',	'RG',	'Ragusa',	'initial',	NULL,	NULL,	NULL),
('IT',	'RI',	'Rieti',	'initial',	NULL,	NULL,	NULL),
('IT',	'RM',	'Rome',	'initial',	NULL,	NULL,	NULL),
('IT',	'RN',	'Rimini',	'initial',	NULL,	NULL,	NULL),
('IT',	'RO',	'Rovigo',	'initial',	NULL,	NULL,	NULL),
('IT',	'SA',	'Salerno',	'initial',	NULL,	NULL,	NULL),
('IT',	'SI',	'Siena',	'initial',	NULL,	NULL,	NULL),
('IT',	'SO',	'Sondrio',	'initial',	NULL,	NULL,	NULL),
('IT',	'SP',	'La Spezia',	'initial',	NULL,	NULL,	NULL),
('IT',	'SR',	'Siracusa',	'initial',	NULL,	NULL,	NULL),
('IT',	'SS',	'Sassari',	'initial',	NULL,	NULL,	NULL),
('IT',	'SV',	'Savona',	'initial',	NULL,	NULL,	NULL),
('IT',	'TA',	'Taranto',	'initial',	NULL,	NULL,	NULL),
('IT',	'TE',	'Teramo',	'initial',	NULL,	NULL,	NULL),
('IT',	'TN',	'Trento',	'initial',	NULL,	NULL,	NULL),
('IT',	'TO',	'Turin',	'initial',	NULL,	NULL,	NULL),
('IT',	'TP',	'Trapani',	'initial',	NULL,	NULL,	NULL),
('IT',	'TR',	'Terni',	'initial',	NULL,	NULL,	NULL),
('IT',	'TS',	'Trieste',	'initial',	NULL,	NULL,	NULL),
('IT',	'TV',	'Treviso',	'initial',	NULL,	NULL,	NULL),
('IT',	'UD',	'Udine',	'initial',	NULL,	NULL,	NULL),
('IT',	'VA',	'Varese',	'initial',	NULL,	NULL,	NULL),
('IT',	'VB',	'Verbano-Cusio-Ossola',	'initial',	NULL,	NULL,	NULL),
('IT',	'VC',	'Vercelli',	'initial',	NULL,	NULL,	NULL),
('IT',	'VE',	'Venice',	'initial',	NULL,	NULL,	NULL),
('IT',	'VI',	'Vicenza',	'initial',	NULL,	NULL,	NULL),
('IT',	'VR',	'Verona',	'initial',	NULL,	NULL,	NULL),
('IT',	'VS',	'Medio Campidano',	'initial',	NULL,	NULL,	NULL),
('IT',	'VT',	'Viterbo',	'initial',	NULL,	NULL,	NULL),
('IT',	'VV',	'Vibo Valentia',	'initial',	NULL,	NULL,	NULL),
('IT',	'ZA',	'Zara',	'initial',	NULL,	NULL,	NULL),
('JP',	'01',	'Hokkaido',	'initial',	NULL,	NULL,	NULL),
('JP',	'02',	'Aomori',	'initial',	NULL,	NULL,	NULL),
('JP',	'03',	'Iwate',	'initial',	NULL,	NULL,	NULL),
('JP',	'04',	'Miyagi',	'initial',	NULL,	NULL,	NULL),
('JP',	'05',	'Akita',	'initial',	NULL,	NULL,	NULL),
('JP',	'06',	'Yamagata',	'initial',	NULL,	NULL,	NULL),
('JP',	'07',	'Fukushima',	'initial',	NULL,	NULL,	NULL),
('JP',	'08',	'Ibaraki',	'initial',	NULL,	NULL,	NULL),
('JP',	'09',	'Tochigi',	'initial',	NULL,	NULL,	NULL),
('JP',	'10',	'Gunma',	'initial',	NULL,	NULL,	NULL),
('JP',	'11',	'Saitama',	'initial',	NULL,	NULL,	NULL),
('JP',	'12',	'Chiba',	'initial',	NULL,	NULL,	NULL),
('JP',	'13',	'Tokyo',	'initial',	NULL,	NULL,	NULL),
('JP',	'14',	'Kanagawa',	'initial',	NULL,	NULL,	NULL),
('JP',	'15',	'Niigata',	'initial',	NULL,	NULL,	NULL),
('JP',	'16',	'Toyama',	'initial',	NULL,	NULL,	NULL),
('JP',	'17',	'Ishikawa',	'initial',	NULL,	NULL,	NULL),
('JP',	'18',	'Fukui',	'initial',	NULL,	NULL,	NULL),
('JP',	'19',	'Yamanashi',	'initial',	NULL,	NULL,	NULL),
('JP',	'20',	'Nagano',	'initial',	NULL,	NULL,	NULL),
('JP',	'21',	'Gifu',	'initial',	NULL,	NULL,	NULL),
('JP',	'22',	'Shizuoka',	'initial',	NULL,	NULL,	NULL),
('JP',	'23',	'Aichi',	'initial',	NULL,	NULL,	NULL),
('JP',	'24',	'Mie',	'initial',	NULL,	NULL,	NULL),
('JP',	'25',	'Shiga',	'initial',	NULL,	NULL,	NULL),
('JP',	'26',	'Kyoto',	'initial',	NULL,	NULL,	NULL),
('JP',	'27',	'Osaka',	'initial',	NULL,	NULL,	NULL),
('JP',	'28',	'Hyogo',	'initial',	NULL,	NULL,	NULL),
('JP',	'29',	'Nara',	'initial',	NULL,	NULL,	NULL),
('JP',	'30',	'Wakayama',	'initial',	NULL,	NULL,	NULL),
('JP',	'31',	'Tottori',	'initial',	NULL,	NULL,	NULL),
('JP',	'32',	'Shimane',	'initial',	NULL,	NULL,	NULL),
('JP',	'33',	'Okayama',	'initial',	NULL,	NULL,	NULL),
('JP',	'34',	'Hiroshima',	'initial',	NULL,	NULL,	NULL),
('JP',	'35',	'Yamaguchi',	'initial',	NULL,	NULL,	NULL),
('JP',	'36',	'Tokushima',	'initial',	NULL,	NULL,	NULL),
('JP',	'37',	'Kagawa',	'initial',	NULL,	NULL,	NULL),
('JP',	'38',	'Ehime',	'initial',	NULL,	NULL,	NULL),
('JP',	'39',	'Kochi',	'initial',	NULL,	NULL,	NULL),
('JP',	'40',	'Fukuoka',	'initial',	NULL,	NULL,	NULL),
('JP',	'41',	'Saga',	'initial',	NULL,	NULL,	NULL),
('JP',	'42',	'Nagasaki',	'initial',	NULL,	NULL,	NULL),
('JP',	'43',	'Kumamoto',	'initial',	NULL,	NULL,	NULL),
('JP',	'44',	'Oita',	'initial',	NULL,	NULL,	NULL),
('JP',	'45',	'Miyazaki',	'initial',	NULL,	NULL,	NULL),
('JP',	'46',	'Kagoshima',	'initial',	NULL,	NULL,	NULL),
('JP',	'47',	'Okinawa',	'initial',	NULL,	NULL,	NULL),
('KR',	'01',	'Jeju',	'initial',	NULL,	NULL,	NULL),
('KR',	'02',	'Jeolla buk do',	'initial',	NULL,	NULL,	NULL),
('KR',	'03',	'Jeolla nam do',	'initial',	NULL,	NULL,	NULL),
('KR',	'04',	'Chungcheong buk do',	'initial',	NULL,	NULL,	NULL),
('KR',	'05',	'Chungcheong nam do',	'initial',	NULL,	NULL,	NULL),
('KR',	'06',	'Incheon',	'initial',	NULL,	NULL,	NULL),
('KR',	'07',	'Gangwon do',	'initial',	NULL,	NULL,	NULL),
('KR',	'08',	'Gwangju',	'initial',	NULL,	NULL,	NULL),
('KR',	'09',	'Gyeonggi do',	'initial',	NULL,	NULL,	NULL),
('KR',	'10',	'Gyeongsang buk do',	'initial',	NULL,	NULL,	NULL),
('KR',	'11',	'Gyeongsang nam do',	'initial',	NULL,	NULL,	NULL),
('KR',	'12',	'Busan',	'initial',	NULL,	NULL,	NULL),
('KR',	'13',	'Seoul',	'initial',	NULL,	NULL,	NULL),
('KR',	'14',	'Daegu',	'initial',	NULL,	NULL,	NULL),
('KR',	'15',	'Daejeon',	'initial',	NULL,	NULL,	NULL),
('KR',	'16',	'Ulsan',	'initial',	NULL,	NULL,	NULL),
('KR',	'17',	'Sejong',	'initial',	NULL,	NULL,	NULL),
('KW',	'KW',	'Kuwait City',	'initial',	NULL,	NULL,	NULL),
('KZ',	'00',	'Almatynskaia',	'initial',	NULL,	NULL,	NULL),
('KZ',	'01',	'Kostanaiskaia',	'initial',	NULL,	NULL,	NULL),
('KZ',	'02',	'Severo-Kazakhstansk',	'initial',	NULL,	NULL,	NULL),
('KZ',	'03',	'Pavlodarskaia',	'initial',	NULL,	NULL,	NULL),
('KZ',	'04',	'Akmolinskaia',	'initial',	NULL,	NULL,	NULL),
('KZ',	'05',	'Aktubinskaia',	'initial',	NULL,	NULL,	NULL),
('KZ',	'06',	'Atyrauskaia',	'initial',	NULL,	NULL,	NULL),
('KZ',	'07',	'Zapadno-Kazakhst',	'initial',	NULL,	NULL,	NULL),
('KZ',	'08',	'Mangystayskaia',	'initial',	NULL,	NULL,	NULL),
('KZ',	'09',	'Karagandinskaia',	'initial',	NULL,	NULL,	NULL),
('KZ',	'10',	'Vostochno-Kazakhstan',	'initial',	NULL,	NULL,	NULL),
('KZ',	'11',	'Gambilskaia',	'initial',	NULL,	NULL,	NULL),
('KZ',	'12',	'Kyzilordinskaia',	'initial',	NULL,	NULL,	NULL),
('MX',	'AGS',	'Aguascalientes',	'initial',	NULL,	NULL,	NULL),
('MX',	'BC',	'Baja California',	'initial',	NULL,	NULL,	NULL),
('MX',	'BCS',	'Baja California S',	'initial',	NULL,	NULL,	NULL),
('MX',	'CHI',	'Chihuahua',	'initial',	NULL,	NULL,	NULL),
('MX',	'CHS',	'Chiapas',	'initial',	NULL,	NULL,	NULL),
('MX',	'CMP',	'Campeche',	'initial',	NULL,	NULL,	NULL),
('MX',	'COA',	'Coahuila',	'initial',	NULL,	NULL,	NULL),
('MX',	'COL',	'Colima',	'initial',	NULL,	NULL,	NULL),
('MX',	'DF',	'Distrito Federal',	'initial',	NULL,	NULL,	NULL),
('MX',	'DGO',	'Durango',	'initial',	NULL,	NULL,	NULL),
('MX',	'GRO',	'Guerrero',	'initial',	NULL,	NULL,	NULL),
('MX',	'GTO',	'Guanajuato',	'initial',	NULL,	NULL,	NULL),
('MX',	'HGO',	'Hidalgo',	'initial',	NULL,	NULL,	NULL),
('MX',	'JAL',	'Jalisco',	'initial',	NULL,	NULL,	NULL),
('MX',	'MCH',	'Michoacan',	'initial',	NULL,	NULL,	NULL),
('MX',	'MEX',	'Estado de Mexico',	'initial',	NULL,	NULL,	NULL),
('MX',	'MOR',	'Morelos',	'initial',	NULL,	NULL,	NULL),
('MX',	'NAY',	'Nayarit',	'initial',	NULL,	NULL,	NULL),
('MX',	'NL',	'Nuevo Leon',	'initial',	NULL,	NULL,	NULL),
('MX',	'OAX',	'Oaxaca',	'initial',	NULL,	NULL,	NULL),
('MX',	'PUE',	'Puebla',	'initial',	NULL,	NULL,	NULL),
('MX',	'QR',	'Quintana Roo',	'initial',	NULL,	NULL,	NULL),
('MX',	'QRO',	'Queretaro',	'initial',	NULL,	NULL,	NULL),
('MX',	'SIN',	'Sinaloa',	'initial',	NULL,	NULL,	NULL),
('MX',	'SLP',	'San Luis Potosi',	'initial',	NULL,	NULL,	NULL),
('MX',	'SON',	'Sonora',	'initial',	NULL,	NULL,	NULL),
('MX',	'TAB',	'Tabasco',	'initial',	NULL,	NULL,	NULL),
('MX',	'TLX',	'Tlaxcala',	'initial',	NULL,	NULL,	NULL),
('MX',	'TMS',	'Tamaulipas',	'initial',	NULL,	NULL,	NULL),
('MX',	'VER',	'Veracruz',	'initial',	NULL,	NULL,	NULL),
('MX',	'YUC',	'Yucatan',	'initial',	NULL,	NULL,	NULL),
('MX',	'ZAC',	'Zacatecas',	'initial',	NULL,	NULL,	NULL),
('MY',	'JOH',	'Johor',	'initial',	NULL,	NULL,	NULL),
('MY',	'KED',	'Kedah',	'initial',	NULL,	NULL,	NULL),
('MY',	'KEL',	'Kelantan',	'initial',	NULL,	NULL,	NULL),
('MY',	'KUL',	'Kuala Lumpur',	'initial',	NULL,	NULL,	NULL),
('MY',	'LAB',	'Labuan',	'initial',	NULL,	NULL,	NULL),
('MY',	'MEL',	'Melaka',	'initial',	NULL,	NULL,	NULL),
('MY',	'PAH',	'Pahang',	'initial',	NULL,	NULL,	NULL),
('MY',	'PEL',	'Perlis',	'initial',	NULL,	NULL,	NULL),
('MY',	'PER',	'Perak',	'initial',	NULL,	NULL,	NULL),
('MY',	'PIN',	'Pulau Pinang',	'initial',	NULL,	NULL,	NULL),
('MY',	'PSK',	'Wil. Persekutuan',	'initial',	NULL,	NULL,	NULL),
('MY',	'SAB',	'Sabah',	'initial',	NULL,	NULL,	NULL),
('MY',	'SAR',	'Sarawak',	'initial',	NULL,	NULL,	NULL),
('MY',	'SEL',	'Selangor',	'initial',	NULL,	NULL,	NULL),
('MY',	'SER',	'Negeri Sembilan',	'initial',	NULL,	NULL,	NULL),
('MY',	'TRE',	'Trengganu',	'initial',	NULL,	NULL,	NULL),
('MY',	'WPP',	'W.P Putrajaya',	'initial',	NULL,	NULL,	NULL),
('NL',	'01',	'Drenthe',	'initial',	NULL,	NULL,	NULL),
('NL',	'02',	'Flevoland',	'initial',	NULL,	NULL,	NULL),
('NL',	'03',	'Friesland',	'initial',	NULL,	NULL,	NULL),
('NL',	'04',	'Gelderland',	'initial',	NULL,	NULL,	NULL),
('NL',	'05',	'Groningen',	'initial',	NULL,	NULL,	NULL),
('NL',	'06',	'Limburg',	'initial',	NULL,	NULL,	NULL),
('NL',	'07',	'Noord-Brabant',	'initial',	NULL,	NULL,	NULL),
('NL',	'08',	'Noord-Holland',	'initial',	NULL,	NULL,	NULL),
('NL',	'09',	'Overijssel',	'initial',	NULL,	NULL,	NULL),
('NL',	'10',	'Utrecht',	'initial',	NULL,	NULL,	NULL),
('NL',	'11',	'Zeeland',	'initial',	NULL,	NULL,	NULL),
('NL',	'12',	'Zuid-Holland',	'initial',	NULL,	NULL,	NULL),
('NO',	'01',	'Ostfold County',	'initial',	NULL,	NULL,	NULL),
('NO',	'02',	'Akershus County',	'initial',	NULL,	NULL,	NULL),
('NO',	'03',	'Oslo',	'initial',	NULL,	NULL,	NULL),
('NO',	'04',	'Hedmark County',	'initial',	NULL,	NULL,	NULL),
('NO',	'05',	'Oppland County',	'initial',	NULL,	NULL,	NULL),
('NO',	'06',	'Buskerud County',	'initial',	NULL,	NULL,	NULL),
('NO',	'07',	'Vestfold County',	'initial',	NULL,	NULL,	NULL),
('NO',	'08',	'Telemark County',	'initial',	NULL,	NULL,	NULL),
('NO',	'09',	'Aust-Agder County',	'initial',	NULL,	NULL,	NULL),
('NO',	'10',	'Vest-Agder County',	'initial',	NULL,	NULL,	NULL),
('NO',	'11',	'Rogaland County',	'initial',	NULL,	NULL,	NULL),
('NO',	'12',	'Hordaland County',	'initial',	NULL,	NULL,	NULL),
('NO',	'14',	'Sogn og Fjordane C.',	'initial',	NULL,	NULL,	NULL),
('NO',	'15',	'More og Romsdal C.',	'initial',	NULL,	NULL,	NULL),
('NO',	'16',	'Sor-Trondelag County',	'initial',	NULL,	NULL,	NULL),
('NO',	'17',	'Nord-Trondelag Cnty',	'initial',	NULL,	NULL,	NULL),
('NO',	'18',	'Nordland County',	'initial',	NULL,	NULL,	NULL),
('NO',	'19',	'Troms County',	'initial',	NULL,	NULL,	NULL),
('NO',	'20',	'Finnmark County',	'initial',	NULL,	NULL,	NULL),
('NZ',	'AKL',	'Auckland',	'initial',	NULL,	NULL,	NULL),
('NZ',	'BOP',	'Bay of Plenty',	'initial',	NULL,	NULL,	NULL),
('NZ',	'CAN',	'Canterbury',	'initial',	NULL,	NULL,	NULL),
('NZ',	'HAB',	'Hawke´s Bay',	'initial',	NULL,	NULL,	NULL),
('NZ',	'MAN',	'Manawatu-Wanganui',	'initial',	NULL,	NULL,	NULL),
('NZ',	'NTL',	'Northland',	'initial',	NULL,	NULL,	NULL),
('NZ',	'OTA',	'Otago',	'initial',	NULL,	NULL,	NULL),
('NZ',	'STL',	'Southland',	'initial',	NULL,	NULL,	NULL),
('NZ',	'TAR',	'Taranaki',	'initial',	NULL,	NULL,	NULL),
('NZ',	'WAI',	'Waikato',	'initial',	NULL,	NULL,	NULL),
('NZ',	'WEC',	'West Coast',	'initial',	NULL,	NULL,	NULL),
('NZ',	'WLG',	'Wellington',	'initial',	NULL,	NULL,	NULL),
('PE',	'01',	'Tumbes',	'initial',	NULL,	NULL,	NULL),
('PE',	'02',	'Piura',	'initial',	NULL,	NULL,	NULL),
('PE',	'03',	'Lambayeque',	'initial',	NULL,	NULL,	NULL),
('PE',	'04',	'La Libertad',	'initial',	NULL,	NULL,	NULL),
('PE',	'05',	'Ancash',	'initial',	NULL,	NULL,	NULL),
('PE',	'06',	'Lima y Callao',	'initial',	NULL,	NULL,	NULL),
('PE',	'07',	'Ica',	'initial',	NULL,	NULL,	NULL),
('PE',	'08',	'Arequipa',	'initial',	NULL,	NULL,	NULL),
('PE',	'09',	'Moquegua',	'initial',	NULL,	NULL,	NULL),
('PE',	'10',	'Tacna',	'initial',	NULL,	NULL,	NULL),
('PE',	'11',	'Amazon',	'initial',	NULL,	NULL,	NULL),
('PE',	'12',	'Cajamarca',	'initial',	NULL,	NULL,	NULL),
('PE',	'13',	'San Martin',	'initial',	NULL,	NULL,	NULL),
('PE',	'14',	'Huanuco',	'initial',	NULL,	NULL,	NULL),
('PE',	'15',	'Pasco',	'initial',	NULL,	NULL,	NULL),
('PE',	'16',	'Junin',	'initial',	NULL,	NULL,	NULL),
('PE',	'17',	'Huancavelica',	'initial',	NULL,	NULL,	NULL),
('PE',	'18',	'Ayacucho',	'initial',	NULL,	NULL,	NULL),
('PE',	'19',	'Apurimac',	'initial',	NULL,	NULL,	NULL),
('PE',	'20',	'Cuzco',	'initial',	NULL,	NULL,	NULL),
('PE',	'21',	'Puno',	'initial',	NULL,	NULL,	NULL),
('PE',	'22',	'Loreto',	'initial',	NULL,	NULL,	NULL),
('PE',	'23',	'Ucayali',	'initial',	NULL,	NULL,	NULL),
('PE',	'24',	'Madre de Dios',	'initial',	NULL,	NULL,	NULL),
('PH',	'01',	'Ilocos',	'initial',	NULL,	NULL,	NULL),
('PH',	'02',	'Cagayan Valley',	'initial',	NULL,	NULL,	NULL),
('PH',	'03',	'Central Luzon',	'initial',	NULL,	NULL,	NULL),
('PH',	'04',	'South Luzon',	'initial',	NULL,	NULL,	NULL),
('PH',	'05',	'Bicol',	'initial',	NULL,	NULL,	NULL),
('PH',	'06',	'West Visayas',	'initial',	NULL,	NULL,	NULL),
('PH',	'07',	'Central Visayas',	'initial',	NULL,	NULL,	NULL),
('PH',	'08',	'Eastern Visayas',	'initial',	NULL,	NULL,	NULL),
('PH',	'09',	'Western Mindanao',	'initial',	NULL,	NULL,	NULL),
('PH',	'10',	'Northern Mindanao',	'initial',	NULL,	NULL,	NULL),
('PH',	'11',	'Central Mindanao',	'initial',	NULL,	NULL,	NULL),
('PH',	'12',	'South Mindanao',	'initial',	NULL,	NULL,	NULL),
('PL',	'DSL',	'Dolnoslaskie',	'initial',	NULL,	NULL,	NULL),
('PL',	'K-P',	'Kujawsko-Pomorskie',	'initial',	NULL,	NULL,	NULL),
('PL',	'LBL',	'Lubelskie',	'initial',	NULL,	NULL,	NULL),
('PL',	'LBS',	'Lubuskie',	'initial',	NULL,	NULL,	NULL),
('PL',	'LDZ',	'Lodzkie',	'initial',	NULL,	NULL,	NULL),
('PL',	'MAL',	'Malopolskie',	'initial',	NULL,	NULL,	NULL),
('PL',	'MAZ',	'Mazowieckie',	'initial',	NULL,	NULL,	NULL),
('PL',	'OPO',	'Opolskie',	'initial',	NULL,	NULL,	NULL),
('PL',	'PDK',	'Podkarpackie',	'initial',	NULL,	NULL,	NULL),
('PL',	'PDL',	'Podlaskie',	'initial',	NULL,	NULL,	NULL),
('PL',	'POM',	'Pomorskie',	'initial',	NULL,	NULL,	NULL),
('PL',	'SLS',	'Slaskie',	'initial',	NULL,	NULL,	NULL),
('PL',	'SWK',	'Swietokrzyskie',	'initial',	NULL,	NULL,	NULL),
('PL',	'W-M',	'Warminsko-mazurskie',	'initial',	NULL,	NULL,	NULL),
('PL',	'WLK',	'Wielkopolskie',	'initial',	NULL,	NULL,	NULL),
('PL',	'Z-P',	'Zachodnio-Pomorskie',	'initial',	NULL,	NULL,	NULL),
('PT',	'10',	'Minho-Lima',	'initial',	NULL,	NULL,	NULL),
('PT',	'11',	'Cavado',	'initial',	NULL,	NULL,	NULL),
('PT',	'12',	'Ave',	'initial',	NULL,	NULL,	NULL),
('PT',	'13',	'Grande Porto',	'initial',	NULL,	NULL,	NULL),
('PT',	'14',	'Tamega',	'initial',	NULL,	NULL,	NULL),
('PT',	'15',	'Entre Douro e Vouga',	'initial',	NULL,	NULL,	NULL),
('PT',	'16',	'Douro',	'initial',	NULL,	NULL,	NULL),
('PT',	'17',	'Alto Tras-os-Montes',	'initial',	NULL,	NULL,	NULL),
('PT',	'20',	'Baixo Vouga',	'initial',	NULL,	NULL,	NULL),
('PT',	'21',	'Baixo Mondego',	'initial',	NULL,	NULL,	NULL),
('PT',	'22',	'Pinhal Litoral',	'initial',	NULL,	NULL,	NULL),
('PT',	'23',	'Pinhal Interior N.',	'initial',	NULL,	NULL,	NULL),
('PT',	'24',	'Pinhal Interior Sul',	'initial',	NULL,	NULL,	NULL),
('PT',	'25',	'Dao-Lafoes',	'initial',	NULL,	NULL,	NULL),
('PT',	'26',	'Serra da Estrela',	'initial',	NULL,	NULL,	NULL),
('PT',	'27',	'Beira Interior Norte',	'initial',	NULL,	NULL,	NULL),
('PT',	'28',	'Beira Interior Sul',	'initial',	NULL,	NULL,	NULL),
('PT',	'29',	'Cova da Beira',	'initial',	NULL,	NULL,	NULL),
('PT',	'30',	'Oeste',	'initial',	NULL,	NULL,	NULL),
('PT',	'31',	'Grande Lisboa',	'initial',	NULL,	NULL,	NULL),
('PT',	'32',	'Peninsula de Setubal',	'initial',	NULL,	NULL,	NULL),
('PT',	'33',	'Medio Tejo',	'initial',	NULL,	NULL,	NULL),
('PT',	'34',	'Leziria do Tejo',	'initial',	NULL,	NULL,	NULL),
('PT',	'40',	'Alentejo Litoral',	'initial',	NULL,	NULL,	NULL),
('PT',	'41',	'Alto Alentejo',	'initial',	NULL,	NULL,	NULL),
('PT',	'42',	'Alentejo Central',	'initial',	NULL,	NULL,	NULL),
('PT',	'43',	'Baixo Alentejo',	'initial',	NULL,	NULL,	NULL),
('PT',	'50',	'Algarve',	'initial',	NULL,	NULL,	NULL),
('PT',	'60',	'Reg. Aut. dos Açores',	'initial',	NULL,	NULL,	NULL),
('PT',	'70',	'Reg. Aut. da Madeira',	'initial',	NULL,	NULL,	NULL),
('QA',	'001',	'Doha',	'initial',	NULL,	NULL,	NULL),
('QA',	'002',	'The north',	'initial',	NULL,	NULL,	NULL),
('QA',	'003',	'The west coast',	'initial',	NULL,	NULL,	NULL),
('QA',	'004',	'The south',	'initial',	NULL,	NULL,	NULL),
('RO',	'01',	'Alba',	'initial',	NULL,	NULL,	NULL),
('RO',	'02',	'Arad',	'initial',	NULL,	NULL,	NULL),
('RO',	'03',	'Arges',	'initial',	NULL,	NULL,	NULL),
('RO',	'04',	'Bacau',	'initial',	NULL,	NULL,	NULL),
('RO',	'05',	'Bihor',	'initial',	NULL,	NULL,	NULL),
('RO',	'06',	'Bistrita-Nasaud',	'initial',	NULL,	NULL,	NULL),
('RO',	'07',	'Botosani',	'initial',	NULL,	NULL,	NULL),
('RO',	'08',	'Braila',	'initial',	NULL,	NULL,	NULL),
('RO',	'09',	'Brasov',	'initial',	NULL,	NULL,	NULL),
('RO',	'10',	'Bucuresti',	'initial',	NULL,	NULL,	NULL),
('RO',	'11',	'Buzau',	'initial',	NULL,	NULL,	NULL),
('RO',	'12',	'Calarasi',	'initial',	NULL,	NULL,	NULL),
('RO',	'13',	'Caras-Severin',	'initial',	NULL,	NULL,	NULL),
('RO',	'14',	'Cluj',	'initial',	NULL,	NULL,	NULL),
('RO',	'15',	'Constanta',	'initial',	NULL,	NULL,	NULL),
('RO',	'16',	'Covasna',	'initial',	NULL,	NULL,	NULL),
('RO',	'17',	'Dimbovita',	'initial',	NULL,	NULL,	NULL),
('RO',	'18',	'Dolj',	'initial',	NULL,	NULL,	NULL),
('RO',	'19',	'Galati',	'initial',	NULL,	NULL,	NULL),
('RO',	'20',	'Gorj',	'initial',	NULL,	NULL,	NULL),
('RO',	'21',	'Giurgiu',	'initial',	NULL,	NULL,	NULL),
('RO',	'22',	'Harghita',	'initial',	NULL,	NULL,	NULL),
('RO',	'23',	'Hunedoara',	'initial',	NULL,	NULL,	NULL),
('RO',	'24',	'Ialomita',	'initial',	NULL,	NULL,	NULL),
('RO',	'25',	'Iasi',	'initial',	NULL,	NULL,	NULL),
('RO',	'26',	'Ilfov',	'initial',	NULL,	NULL,	NULL),
('RO',	'27',	'Maramures',	'initial',	NULL,	NULL,	NULL),
('RO',	'28',	'Mehedinti',	'initial',	NULL,	NULL,	NULL),
('RO',	'29',	'Mures',	'initial',	NULL,	NULL,	NULL),
('RO',	'30',	'Neamt',	'initial',	NULL,	NULL,	NULL),
('RO',	'31',	'Olt',	'initial',	NULL,	NULL,	NULL),
('RO',	'32',	'Prahova',	'initial',	NULL,	NULL,	NULL),
('RO',	'33',	'Salaj',	'initial',	NULL,	NULL,	NULL),
('RO',	'34',	'Satu Mare',	'initial',	NULL,	NULL,	NULL),
('RO',	'35',	'Sibiu',	'initial',	NULL,	NULL,	NULL),
('RO',	'36',	'Suceava',	'initial',	NULL,	NULL,	NULL),
('RO',	'37',	'Teleorman',	'initial',	NULL,	NULL,	NULL),
('RO',	'38',	'Timis',	'initial',	NULL,	NULL,	NULL),
('RO',	'39',	'Tulcea',	'initial',	NULL,	NULL,	NULL),
('RO',	'40',	'Vaslui',	'initial',	NULL,	NULL,	NULL),
('RO',	'41',	'Vilcea',	'initial',	NULL,	NULL,	NULL),
('RO',	'42',	'Vrancea',	'initial',	NULL,	NULL,	NULL),
('RU',	'01',	'Adigeja Republic',	'initial',	NULL,	NULL,	NULL),
('RU',	'02',	'Highlands-Altay Rep.',	'initial',	NULL,	NULL,	NULL),
('RU',	'03',	'Republ.of Bashkortos',	'initial',	NULL,	NULL,	NULL),
('RU',	'04',	'Buryat Republic',	'initial',	NULL,	NULL,	NULL),
('RU',	'05',	'Dagestan Republic',	'initial',	NULL,	NULL,	NULL),
('RU',	'06',	'Ingushetija Republic',	'initial',	NULL,	NULL,	NULL),
('RU',	'07',	'Kabardino-Balkar.Rep',	'initial',	NULL,	NULL,	NULL),
('RU',	'08',	'Kalmyk Republic',	'initial',	NULL,	NULL,	NULL),
('RU',	'09',	'Karach.-Cherkessk Re',	'initial',	NULL,	NULL,	NULL),
('RU',	'10',	'Karelian Republic',	'initial',	NULL,	NULL,	NULL),
('RU',	'11',	'Komi Republic',	'initial',	NULL,	NULL,	NULL),
('RU',	'12',	'Marijskaya Republic',	'initial',	NULL,	NULL,	NULL),
('RU',	'13',	'Mordovian Republic',	'initial',	NULL,	NULL,	NULL),
('RU',	'14',	'Yakutiya-Saha Rrepub',	'initial',	NULL,	NULL,	NULL),
('RU',	'15',	'North-Osetiya Republ',	'initial',	NULL,	NULL,	NULL),
('RU',	'16',	'Tatarstan Republic',	'initial',	NULL,	NULL,	NULL),
('RU',	'17',	'Tuva Republic',	'initial',	NULL,	NULL,	NULL),
('RU',	'18',	'The Udmurt Republic',	'initial',	NULL,	NULL,	NULL),
('RU',	'19',	'Chakassky Republic',	'initial',	NULL,	NULL,	NULL),
('RU',	'20',	'Chechenskaya Republ.',	'initial',	NULL,	NULL,	NULL),
('RU',	'21',	'Chuvash Republic',	'initial',	NULL,	NULL,	NULL),
('RU',	'22',	'Altay Territory',	'initial',	NULL,	NULL,	NULL),
('RU',	'23',	'Krasnodar Territory',	'initial',	NULL,	NULL,	NULL),
('RU',	'24',	'Krasnoyarsk Territor',	'initial',	NULL,	NULL,	NULL),
('RU',	'25',	'Primorye Territory',	'initial',	NULL,	NULL,	NULL),
('RU',	'26',	'Stavropol Territory',	'initial',	NULL,	NULL,	NULL),
('RU',	'27',	'Khabarovsk Territory',	'initial',	NULL,	NULL,	NULL),
('RU',	'28',	'The Amur Area',	'initial',	NULL,	NULL,	NULL),
('RU',	'29',	'The Arkhangelsk Area',	'initial',	NULL,	NULL,	NULL),
('RU',	'30',	'The Astrakhan Area',	'initial',	NULL,	NULL,	NULL),
('RU',	'31',	'The Belgorod Area',	'initial',	NULL,	NULL,	NULL),
('RU',	'32',	'The Bryansk Area',	'initial',	NULL,	NULL,	NULL),
('RU',	'33',	'The Vladimir Area',	'initial',	NULL,	NULL,	NULL),
('RU',	'34',	'The Volgograd Area',	'initial',	NULL,	NULL,	NULL),
('RU',	'35',	'The Vologda Area',	'initial',	NULL,	NULL,	NULL),
('RU',	'36',	'The Voronezh Area',	'initial',	NULL,	NULL,	NULL),
('RU',	'37',	'The Ivanovo Area',	'initial',	NULL,	NULL,	NULL),
('RU',	'38',	'The Irkutsk Area',	'initial',	NULL,	NULL,	NULL),
('RU',	'39',	'The Kaliningrad Area',	'initial',	NULL,	NULL,	NULL),
('RU',	'40',	'The Kaluga Area',	'initial',	NULL,	NULL,	NULL),
('RU',	'41',	'Kamchatka Territory',	'initial',	NULL,	NULL,	NULL),
('RU',	'42',	'The Kemerovo Area',	'initial',	NULL,	NULL,	NULL),
('RU',	'43',	'The Kirov Area',	'initial',	NULL,	NULL,	NULL),
('RU',	'44',	'The Kostroma Area',	'initial',	NULL,	NULL,	NULL),
('RU',	'45',	'The Kurgan Area',	'initial',	NULL,	NULL,	NULL),
('RU',	'46',	'The Kursk Area',	'initial',	NULL,	NULL,	NULL),
('RU',	'47',	'The Leningrad Area',	'initial',	NULL,	NULL,	NULL),
('RU',	'48',	'The Lipetsk Area',	'initial',	NULL,	NULL,	NULL),
('RU',	'49',	'The Magadan Area',	'initial',	NULL,	NULL,	NULL),
('RU',	'50',	'The Moscow Area',	'initial',	NULL,	NULL,	NULL),
('RU',	'51',	'The Murmansk Area',	'initial',	NULL,	NULL,	NULL),
('RU',	'52',	'The Nizhniy Novgorod',	'initial',	NULL,	NULL,	NULL),
('RU',	'53',	'The Novgorod Area',	'initial',	NULL,	NULL,	NULL),
('RU',	'54',	'The Novosibirsk Area',	'initial',	NULL,	NULL,	NULL),
('RU',	'55',	'The Omsk Area',	'initial',	NULL,	NULL,	NULL),
('RU',	'56',	'The Orenburg Area',	'initial',	NULL,	NULL,	NULL),
('RU',	'57',	'The Oryol Area',	'initial',	NULL,	NULL,	NULL),
('RU',	'58',	'The Penza Area',	'initial',	NULL,	NULL,	NULL),
('RU',	'59',	'Perm Territory',	'initial',	NULL,	NULL,	NULL),
('RU',	'60',	'The Pskov Area',	'initial',	NULL,	NULL,	NULL),
('RU',	'61',	'The Rostov Area',	'initial',	NULL,	NULL,	NULL),
('RU',	'62',	'The Ryazan Area',	'initial',	NULL,	NULL,	NULL),
('RU',	'63',	'The Samara Area',	'initial',	NULL,	NULL,	NULL),
('RU',	'64',	'The Saratov Area',	'initial',	NULL,	NULL,	NULL),
('RU',	'65',	'The Sakhalin Area',	'initial',	NULL,	NULL,	NULL),
('RU',	'66',	'The Sverdlovsk Area',	'initial',	NULL,	NULL,	NULL),
('RU',	'67',	'The Smolensk Area',	'initial',	NULL,	NULL,	NULL),
('RU',	'68',	'The Tambov Area',	'initial',	NULL,	NULL,	NULL),
('RU',	'69',	'The Tver Area',	'initial',	NULL,	NULL,	NULL),
('RU',	'70',	'The Tomsk Area',	'initial',	NULL,	NULL,	NULL),
('RU',	'71',	'The Tula Area',	'initial',	NULL,	NULL,	NULL),
('RU',	'72',	'The Tyumen Area',	'initial',	NULL,	NULL,	NULL),
('RU',	'73',	'The Ulyanovsk Area',	'initial',	NULL,	NULL,	NULL),
('RU',	'74',	'The Chelyabinsk Area',	'initial',	NULL,	NULL,	NULL),
('RU',	'75',	'Zabaykalsk Territory',	'initial',	NULL,	NULL,	NULL),
('RU',	'76',	'The Yaroslavl Area',	'initial',	NULL,	NULL,	NULL),
('RU',	'77',	'c.Moscow',	'initial',	NULL,	NULL,	NULL),
('RU',	'78',	'c.St-Peterburg',	'initial',	NULL,	NULL,	NULL),
('RU',	'79',	'The Jewish Auton.are',	'initial',	NULL,	NULL,	NULL),
('RU',	'80',	'Aginsk Buryat Aut.di',	'initial',	NULL,	NULL,	NULL),
('RU',	'81',	'Komy Permjats.Aut.di',	'initial',	NULL,	NULL,	NULL),
('RU',	'82',	'Korjacs Auton.distri',	'initial',	NULL,	NULL,	NULL),
('RU',	'83',	'Nenekchky Auton.dist',	'initial',	NULL,	NULL,	NULL),
('RU',	'84',	'The Taymir Auton.dis',	'initial',	NULL,	NULL,	NULL),
('RU',	'85',	'Ust-Ordinsky Buryat',	'initial',	NULL,	NULL,	NULL),
('RU',	'86',	'Chanti-Mansyjsky Aut',	'initial',	NULL,	NULL,	NULL),
('RU',	'87',	'Chukotka Auton. dist',	'initial',	NULL,	NULL,	NULL),
('RU',	'88',	'Evensky Auton.distri',	'initial',	NULL,	NULL,	NULL),
('RU',	'89',	'Jamalo-Nenekchky Aut',	'initial',	NULL,	NULL,	NULL),
('SE',	'001',	'Blekinge County',	'initial',	NULL,	NULL,	NULL),
('SE',	'002',	'Dalarnas County',	'initial',	NULL,	NULL,	NULL),
('SE',	'003',	'Gotland County',	'initial',	NULL,	NULL,	NULL),
('SE',	'004',	'Gaevleborg County',	'initial',	NULL,	NULL,	NULL),
('SE',	'005',	'Halland County',	'initial',	NULL,	NULL,	NULL),
('SE',	'006',	'Jaemtland County',	'initial',	NULL,	NULL,	NULL),
('SE',	'007',	'Joenkoeping County',	'initial',	NULL,	NULL,	NULL),
('SE',	'008',	'Kalmar County',	'initial',	NULL,	NULL,	NULL),
('SE',	'009',	'Kronoberg County',	'initial',	NULL,	NULL,	NULL),
('SE',	'010',	'Norrbotten County',	'initial',	NULL,	NULL,	NULL),
('SE',	'011',	'Skaane County',	'initial',	NULL,	NULL,	NULL),
('SE',	'012',	'Stockholm County',	'initial',	NULL,	NULL,	NULL),
('SE',	'013',	'Soedermanland County',	'initial',	NULL,	NULL,	NULL),
('SE',	'014',	'Uppsala County',	'initial',	NULL,	NULL,	NULL),
('SE',	'015',	'Vaermland County',	'initial',	NULL,	NULL,	NULL),
('SE',	'016',	'Vaesterbotten County',	'initial',	NULL,	NULL,	NULL),
('SE',	'017',	'Vaesternorrland Cnty',	'initial',	NULL,	NULL,	NULL),
('SE',	'018',	'Vaestmanland County',	'initial',	NULL,	NULL,	NULL),
('SE',	'019',	'Vaestra Goetaland C.',	'initial',	NULL,	NULL,	NULL),
('SE',	'020',	'Oerebro County',	'initial',	NULL,	NULL,	NULL),
('SE',	'021',	'Oestergoetland Cnty',	'initial',	NULL,	NULL,	NULL),
('SG',	'SG',	'Singapore',	'initial',	NULL,	NULL,	NULL),
('SI',	'001',	'Ajdov¹èina',	'initial',	NULL,	NULL,	NULL),
('SI',	'002',	'Beltinci',	'initial',	NULL,	NULL,	NULL),
('SI',	'003',	'Bled',	'initial',	NULL,	NULL,	NULL),
('SI',	'004',	'Bohinj',	'initial',	NULL,	NULL,	NULL),
('SI',	'005',	'Borovnica',	'initial',	NULL,	NULL,	NULL),
('SI',	'006',	'Bovec',	'initial',	NULL,	NULL,	NULL),
('SI',	'007',	'Brda',	'initial',	NULL,	NULL,	NULL),
('SI',	'008',	'Brezovica',	'initial',	NULL,	NULL,	NULL),
('SI',	'009',	'Bre¾ice',	'initial',	NULL,	NULL,	NULL),
('SI',	'01',	'Ajdovscina',	'initial',	NULL,	NULL,	NULL),
('SI',	'010',	'Ti¹ina',	'initial',	NULL,	NULL,	NULL),
('SI',	'011',	'Celje',	'initial',	NULL,	NULL,	NULL),
('SI',	'012',	'Cerklje na Gorenjske',	'initial',	NULL,	NULL,	NULL),
('SI',	'013',	'Cerknica',	'initial',	NULL,	NULL,	NULL),
('SI',	'014',	'Cerkno',	'initial',	NULL,	NULL,	NULL),
('SI',	'015',	'Èren¹ovci',	'initial',	NULL,	NULL,	NULL),
('SI',	'016',	'Èrna na Koro¹kem',	'initial',	NULL,	NULL,	NULL),
('SI',	'017',	'Èrnomelj',	'initial',	NULL,	NULL,	NULL),
('SI',	'018',	'Destrnik',	'initial',	NULL,	NULL,	NULL),
('SI',	'019',	'Divaèa',	'initial',	NULL,	NULL,	NULL),
('SI',	'02',	'Brezice',	'initial',	NULL,	NULL,	NULL),
('SI',	'020',	'Dobrepolje',	'initial',	NULL,	NULL,	NULL),
('SI',	'021',	'Dobrova-Polhov Grade',	'initial',	NULL,	NULL,	NULL),
('SI',	'022',	'Dol pri Ljubljani',	'initial',	NULL,	NULL,	NULL),
('SI',	'023',	'Dom¾ale',	'initial',	NULL,	NULL,	NULL),
('SI',	'024',	'Dornava',	'initial',	NULL,	NULL,	NULL),
('SI',	'025',	'Dravograd',	'initial',	NULL,	NULL,	NULL),
('SI',	'026',	'Duplek',	'initial',	NULL,	NULL,	NULL),
('SI',	'027',	'Gorenja vas-Poljane',	'initial',	NULL,	NULL,	NULL),
('SI',	'028',	'Gori¹nica',	'initial',	NULL,	NULL,	NULL),
('SI',	'029',	'Gornja Radgona',	'initial',	NULL,	NULL,	NULL),
('SI',	'03',	'Celje',	'initial',	NULL,	NULL,	NULL),
('SI',	'030',	'Gornji Grad',	'initial',	NULL,	NULL,	NULL),
('SI',	'031',	'Gornji Petrovci',	'initial',	NULL,	NULL,	NULL),
('SI',	'032',	'Grosuplje',	'initial',	NULL,	NULL,	NULL),
('SI',	'033',	'©alovci',	'initial',	NULL,	NULL,	NULL),
('SI',	'034',	'Hrastnik',	'initial',	NULL,	NULL,	NULL),
('SI',	'035',	'Hrpelje-Kozina',	'initial',	NULL,	NULL,	NULL),
('SI',	'036',	'Idrija',	'initial',	NULL,	NULL,	NULL),
('SI',	'037',	'Ig',	'initial',	NULL,	NULL,	NULL),
('SI',	'038',	'Ilirska Bistrica',	'initial',	NULL,	NULL,	NULL),
('SI',	'039',	'Ivanèna Gorica',	'initial',	NULL,	NULL,	NULL),
('SI',	'04',	'Cerknica',	'initial',	NULL,	NULL,	NULL),
('SI',	'040',	'Izola - Isola',	'initial',	NULL,	NULL,	NULL),
('SI',	'041',	'Jesenice',	'initial',	NULL,	NULL,	NULL),
('SI',	'042',	'Jur¹inci',	'initial',	NULL,	NULL,	NULL),
('SI',	'043',	'Kamnik',	'initial',	NULL,	NULL,	NULL),
('SI',	'044',	'Kanal',	'initial',	NULL,	NULL,	NULL),
('SI',	'045',	'Kidrièevo',	'initial',	NULL,	NULL,	NULL),
('SI',	'046',	'Kobarid',	'initial',	NULL,	NULL,	NULL),
('SI',	'047',	'Kobilje',	'initial',	NULL,	NULL,	NULL),
('SI',	'048',	'Koèevje',	'initial',	NULL,	NULL,	NULL),
('SI',	'049',	'Komen',	'initial',	NULL,	NULL,	NULL),
('SI',	'05',	'Crnomelj',	'initial',	NULL,	NULL,	NULL),
('SI',	'050',	'Koper - Capodistria',	'initial',	NULL,	NULL,	NULL),
('SI',	'051',	'Kozje',	'initial',	NULL,	NULL,	NULL),
('SI',	'052',	'Kranj',	'initial',	NULL,	NULL,	NULL),
('SI',	'053',	'Kranjska Gora',	'initial',	NULL,	NULL,	NULL),
('SI',	'054',	'Kr¹ko',	'initial',	NULL,	NULL,	NULL),
('SI',	'055',	'Kungota',	'initial',	NULL,	NULL,	NULL),
('SI',	'056',	'Kuzma',	'initial',	NULL,	NULL,	NULL),
('SI',	'057',	'La¹ko',	'initial',	NULL,	NULL,	NULL),
('SI',	'058',	'Lenart',	'initial',	NULL,	NULL,	NULL),
('SI',	'059',	'Lendava - Lendva',	'initial',	NULL,	NULL,	NULL),
('SI',	'06',	'Dravograd',	'initial',	NULL,	NULL,	NULL),
('SI',	'060',	'Litija',	'initial',	NULL,	NULL,	NULL),
('SI',	'061',	'Ljubljana',	'initial',	NULL,	NULL,	NULL),
('SI',	'062',	'Ljubno',	'initial',	NULL,	NULL,	NULL),
('SI',	'063',	'Ljutomer',	'initial',	NULL,	NULL,	NULL),
('SI',	'064',	'Logatec',	'initial',	NULL,	NULL,	NULL),
('SI',	'065',	'Lo¹ka dolina',	'initial',	NULL,	NULL,	NULL),
('SI',	'066',	'Lo¹ki Potok',	'initial',	NULL,	NULL,	NULL),
('SI',	'067',	'Luèe',	'initial',	NULL,	NULL,	NULL),
('SI',	'068',	'Lukovica',	'initial',	NULL,	NULL,	NULL),
('SI',	'069',	'Maj¹perk',	'initial',	NULL,	NULL,	NULL),
('SI',	'07',	'Gornja Radgona',	'initial',	NULL,	NULL,	NULL),
('SI',	'070',	'Maribor',	'initial',	NULL,	NULL,	NULL),
('SI',	'071',	'Medvode',	'initial',	NULL,	NULL,	NULL),
('SI',	'072',	'Menge¹',	'initial',	NULL,	NULL,	NULL),
('SI',	'073',	'Metlika',	'initial',	NULL,	NULL,	NULL),
('SI',	'074',	'Me¾ica',	'initial',	NULL,	NULL,	NULL),
('SI',	'075',	'Miren-Kostanjevica',	'initial',	NULL,	NULL,	NULL),
('SI',	'076',	'Mislinja',	'initial',	NULL,	NULL,	NULL),
('SI',	'077',	'Moravèe',	'initial',	NULL,	NULL,	NULL),
('SI',	'078',	'Moravske Toplice',	'initial',	NULL,	NULL,	NULL),
('SI',	'079',	'Mozirje',	'initial',	NULL,	NULL,	NULL),
('SI',	'08',	'Grosuplje',	'initial',	NULL,	NULL,	NULL),
('SI',	'080',	'Murska Sobota',	'initial',	NULL,	NULL,	NULL),
('SI',	'081',	'Muta',	'initial',	NULL,	NULL,	NULL),
('SI',	'082',	'Naklo',	'initial',	NULL,	NULL,	NULL),
('SI',	'083',	'Nazarje',	'initial',	NULL,	NULL,	NULL),
('SI',	'084',	'Nova Gorica',	'initial',	NULL,	NULL,	NULL),
('SI',	'085',	'Novo mesto',	'initial',	NULL,	NULL,	NULL),
('SI',	'086',	'Odranci',	'initial',	NULL,	NULL,	NULL),
('SI',	'087',	'Ormo¾',	'initial',	NULL,	NULL,	NULL),
('SI',	'088',	'Osilnica',	'initial',	NULL,	NULL,	NULL),
('SI',	'089',	'Pesnica',	'initial',	NULL,	NULL,	NULL),
('SI',	'09',	'Hrastnik Lasko',	'initial',	NULL,	NULL,	NULL),
('SI',	'090',	'Piran - Pirano',	'initial',	NULL,	NULL,	NULL),
('SI',	'091',	'Pivka',	'initial',	NULL,	NULL,	NULL),
('SI',	'092',	'Podèetrtek',	'initial',	NULL,	NULL,	NULL),
('SI',	'093',	'Podvelka',	'initial',	NULL,	NULL,	NULL),
('SI',	'094',	'Postojna',	'initial',	NULL,	NULL,	NULL),
('SI',	'095',	'Preddvor',	'initial',	NULL,	NULL,	NULL),
('SI',	'096',	'Ptuj',	'initial',	NULL,	NULL,	NULL),
('SI',	'097',	'Puconci',	'initial',	NULL,	NULL,	NULL),
('SI',	'098',	'Raèe-Fram',	'initial',	NULL,	NULL,	NULL),
('SI',	'099',	'Radeèe',	'initial',	NULL,	NULL,	NULL),
('SI',	'10',	'Idrija',	'initial',	NULL,	NULL,	NULL),
('SI',	'100',	'Radenci',	'initial',	NULL,	NULL,	NULL),
('SI',	'101',	'Radlje ob Dravi',	'initial',	NULL,	NULL,	NULL),
('SI',	'102',	'Radovljica',	'initial',	NULL,	NULL,	NULL),
('SI',	'103',	'Ravne na Koro¹kem',	'initial',	NULL,	NULL,	NULL),
('SI',	'104',	'Ribnica',	'initial',	NULL,	NULL,	NULL),
('SI',	'105',	'Roga¹ovci',	'initial',	NULL,	NULL,	NULL),
('SI',	'106',	'Roga¹ka Slatina',	'initial',	NULL,	NULL,	NULL),
('SI',	'107',	'Rogatec',	'initial',	NULL,	NULL,	NULL),
('SI',	'108',	'Ru¹e',	'initial',	NULL,	NULL,	NULL),
('SI',	'109',	'Semiè',	'initial',	NULL,	NULL,	NULL),
('SI',	'11',	'Ilirska Bistrica',	'initial',	NULL,	NULL,	NULL),
('SI',	'110',	'Sevnica',	'initial',	NULL,	NULL,	NULL),
('SI',	'111',	'Se¾ana',	'initial',	NULL,	NULL,	NULL),
('SI',	'112',	'Slovenj Gradec',	'initial',	NULL,	NULL,	NULL),
('SI',	'113',	'Slovenska Bistrica',	'initial',	NULL,	NULL,	NULL),
('SI',	'114',	'Slovenske Konjice',	'initial',	NULL,	NULL,	NULL),
('SI',	'115',	'Star¹e',	'initial',	NULL,	NULL,	NULL),
('SI',	'116',	'Sveti Jurij',	'initial',	NULL,	NULL,	NULL),
('SI',	'117',	'©enèur',	'initial',	NULL,	NULL,	NULL),
('SI',	'118',	'©entilj',	'initial',	NULL,	NULL,	NULL),
('SI',	'119',	'©entjernej',	'initial',	NULL,	NULL,	NULL),
('SI',	'12',	'Izola',	'initial',	NULL,	NULL,	NULL),
('SI',	'120',	'©entjur pri Celju',	'initial',	NULL,	NULL,	NULL),
('SI',	'121',	'©kocjan',	'initial',	NULL,	NULL,	NULL),
('SI',	'122',	'©kofja Loka',	'initial',	NULL,	NULL,	NULL),
('SI',	'123',	'©kofljica',	'initial',	NULL,	NULL,	NULL),
('SI',	'124',	'©marje pri Jel¹ah',	'initial',	NULL,	NULL,	NULL),
('SI',	'125',	'©martno ob Paki',	'initial',	NULL,	NULL,	NULL),
('SI',	'126',	'©o¹tanj',	'initial',	NULL,	NULL,	NULL),
('SI',	'127',	'©tore',	'initial',	NULL,	NULL,	NULL),
('SI',	'128',	'Tolmin',	'initial',	NULL,	NULL,	NULL),
('SI',	'129',	'Trbovlje',	'initial',	NULL,	NULL,	NULL),
('SI',	'13',	'Jesenice',	'initial',	NULL,	NULL,	NULL),
('SI',	'130',	'Trebnje',	'initial',	NULL,	NULL,	NULL),
('SI',	'131',	'Tr¾iè',	'initial',	NULL,	NULL,	NULL),
('SI',	'132',	'Turni¹èe',	'initial',	NULL,	NULL,	NULL),
('SI',	'133',	'Velenje',	'initial',	NULL,	NULL,	NULL),
('SI',	'134',	'Velike La¹èe',	'initial',	NULL,	NULL,	NULL),
('SI',	'135',	'Videm',	'initial',	NULL,	NULL,	NULL),
('SI',	'136',	'Vipava',	'initial',	NULL,	NULL,	NULL),
('SI',	'137',	'Vitanje',	'initial',	NULL,	NULL,	NULL),
('SI',	'138',	'Vodice',	'initial',	NULL,	NULL,	NULL),
('SI',	'139',	'Vojnik',	'initial',	NULL,	NULL,	NULL),
('SI',	'14',	'Kamnik',	'initial',	NULL,	NULL,	NULL),
('SI',	'140',	'Vrhnika',	'initial',	NULL,	NULL,	NULL),
('SI',	'141',	'Vuzenica',	'initial',	NULL,	NULL,	NULL),
('SI',	'142',	'Zagorje ob Savi',	'initial',	NULL,	NULL,	NULL),
('SI',	'143',	'Zavrè',	'initial',	NULL,	NULL,	NULL),
('SI',	'144',	'Zreèe',	'initial',	NULL,	NULL,	NULL),
('SI',	'146',	'®elezniki',	'initial',	NULL,	NULL,	NULL),
('SI',	'147',	'®iri',	'initial',	NULL,	NULL,	NULL),
('SI',	'148',	'Benedikt',	'initial',	NULL,	NULL,	NULL),
('SI',	'149',	'Bistrica ob Sotli',	'initial',	NULL,	NULL,	NULL),
('SI',	'15',	'Kocevje',	'initial',	NULL,	NULL,	NULL),
('SI',	'150',	'Bloke',	'initial',	NULL,	NULL,	NULL),
('SI',	'151',	'Braslovèe',	'initial',	NULL,	NULL,	NULL),
('SI',	'152',	'Cankova',	'initial',	NULL,	NULL,	NULL),
('SI',	'153',	'Cerkvenjak',	'initial',	NULL,	NULL,	NULL),
('SI',	'154',	'Dobje',	'initial',	NULL,	NULL,	NULL),
('SI',	'155',	'Dobrna',	'initial',	NULL,	NULL,	NULL),
('SI',	'156',	'Dobrovnik - Dobronak',	'initial',	NULL,	NULL,	NULL),
('SI',	'157',	'Dolenjske Toplice',	'initial',	NULL,	NULL,	NULL),
('SI',	'158',	'Grad',	'initial',	NULL,	NULL,	NULL),
('SI',	'159',	'Hajdina',	'initial',	NULL,	NULL,	NULL),
('SI',	'16',	'Koper',	'initial',	NULL,	NULL,	NULL),
('SI',	'160',	'Hoèe-Slivnica',	'initial',	NULL,	NULL,	NULL),
('SI',	'161',	'Hodo¹ - Hodos',	'initial',	NULL,	NULL,	NULL),
('SI',	'162',	'Horjul',	'initial',	NULL,	NULL,	NULL),
('SI',	'163',	'Jezersko',	'initial',	NULL,	NULL,	NULL),
('SI',	'164',	'Komenda',	'initial',	NULL,	NULL,	NULL),
('SI',	'165',	'Kostel',	'initial',	NULL,	NULL,	NULL),
('SI',	'166',	'Kri¾evci',	'initial',	NULL,	NULL,	NULL),
('SI',	'167',	'Lovrenc na Pohorju',	'initial',	NULL,	NULL,	NULL),
('SI',	'168',	'Markovci',	'initial',	NULL,	NULL,	NULL),
('SI',	'169',	'Miklav¾ na Dravskem',	'initial',	NULL,	NULL,	NULL),
('SI',	'17',	'Kranj',	'initial',	NULL,	NULL,	NULL),
('SI',	'170',	'Mirna Peè',	'initial',	NULL,	NULL,	NULL),
('SI',	'171',	'Oplotnica',	'initial',	NULL,	NULL,	NULL),
('SI',	'172',	'Podlehnik',	'initial',	NULL,	NULL,	NULL),
('SI',	'173',	'Polzela',	'initial',	NULL,	NULL,	NULL),
('SI',	'174',	'Prebold',	'initial',	NULL,	NULL,	NULL),
('SI',	'175',	'Prevalje',	'initial',	NULL,	NULL,	NULL),
('SI',	'176',	'Razkri¾je',	'initial',	NULL,	NULL,	NULL),
('SI',	'177',	'Ribnica na Pohorju',	'initial',	NULL,	NULL,	NULL),
('SI',	'178',	'Selnica ob Dravi',	'initial',	NULL,	NULL,	NULL),
('SI',	'179',	'Sodra¾ica',	'initial',	NULL,	NULL,	NULL),
('SI',	'18',	'Krsko',	'initial',	NULL,	NULL,	NULL),
('SI',	'180',	'Solèava',	'initial',	NULL,	NULL,	NULL),
('SI',	'181',	'Sveta Ana',	'initial',	NULL,	NULL,	NULL),
('SI',	'182',	'Sveti Andra¾ v Slov.',	'initial',	NULL,	NULL,	NULL),
('SI',	'183',	'©empeter - Vrtojba',	'initial',	NULL,	NULL,	NULL),
('SI',	'184',	'Tabor',	'initial',	NULL,	NULL,	NULL),
('SI',	'185',	'Trnovska vas',	'initial',	NULL,	NULL,	NULL),
('SI',	'186',	'Trzin',	'initial',	NULL,	NULL,	NULL),
('SI',	'187',	'Velika Polana',	'initial',	NULL,	NULL,	NULL),
('SI',	'188',	'Ver¾ej',	'initial',	NULL,	NULL,	NULL),
('SI',	'189',	'Vransko',	'initial',	NULL,	NULL,	NULL),
('SI',	'19',	'Lenart',	'initial',	NULL,	NULL,	NULL),
('SI',	'190',	'®alec',	'initial',	NULL,	NULL,	NULL),
('SI',	'191',	'®etale',	'initial',	NULL,	NULL,	NULL),
('SI',	'192',	'®irovnica',	'initial',	NULL,	NULL,	NULL),
('SI',	'193',	'®u¾emberk',	'initial',	NULL,	NULL,	NULL),
('SI',	'20',	'Lendava',	'initial',	NULL,	NULL,	NULL),
('SI',	'21',	'Litija',	'initial',	NULL,	NULL,	NULL),
('SI',	'22',	'Ljubljana-Bezigrad',	'initial',	NULL,	NULL,	NULL),
('SI',	'23',	'Ljubljana-Center',	'initial',	NULL,	NULL,	NULL),
('SI',	'24',	'Ljubljana-Moste-Polj',	'initial',	NULL,	NULL,	NULL),
('SI',	'25',	'Ljubljana-Siska',	'initial',	NULL,	NULL,	NULL),
('SI',	'26',	'Ljubljana-Vic-Rudnik',	'initial',	NULL,	NULL,	NULL),
('SI',	'27',	'Ljutomer',	'initial',	NULL,	NULL,	NULL),
('SI',	'28',	'Logatec',	'initial',	NULL,	NULL,	NULL),
('SI',	'29',	'Maribor',	'initial',	NULL,	NULL,	NULL),
('SI',	'30',	'Metlika',	'initial',	NULL,	NULL,	NULL),
('SI',	'31',	'Mozirje',	'initial',	NULL,	NULL,	NULL),
('SI',	'32',	'Murska Sobota',	'initial',	NULL,	NULL,	NULL),
('SI',	'33',	'Nova Gorica',	'initial',	NULL,	NULL,	NULL),
('SI',	'34',	'Novo Mesto',	'initial',	NULL,	NULL,	NULL),
('SI',	'35',	'Ormoz',	'initial',	NULL,	NULL,	NULL),
('SI',	'36',	'Pesnica',	'initial',	NULL,	NULL,	NULL),
('SI',	'37',	'Piran',	'initial',	NULL,	NULL,	NULL),
('SI',	'38',	'Postojna',	'initial',	NULL,	NULL,	NULL),
('SI',	'39',	'Ptuj',	'initial',	NULL,	NULL,	NULL),
('SI',	'40',	'Radlje Ob Dravi',	'initial',	NULL,	NULL,	NULL),
('SI',	'41',	'Radovljica',	'initial',	NULL,	NULL,	NULL),
('SI',	'42',	'Ravne Na Koroskem',	'initial',	NULL,	NULL,	NULL),
('SI',	'43',	'Ribnica',	'initial',	NULL,	NULL,	NULL),
('SI',	'44',	'Ruse',	'initial',	NULL,	NULL,	NULL),
('SI',	'45',	'Sentjur Pri Celju',	'initial',	NULL,	NULL,	NULL),
('SI',	'46',	'Sevnica',	'initial',	NULL,	NULL,	NULL),
('SI',	'47',	'Sezana',	'initial',	NULL,	NULL,	NULL),
('SI',	'48',	'Skofja Loka',	'initial',	NULL,	NULL,	NULL),
('SI',	'49',	'Slovenj Gradec',	'initial',	NULL,	NULL,	NULL),
('SI',	'50',	'Slovenska Bistrica',	'initial',	NULL,	NULL,	NULL),
('SI',	'51',	'Slovenske Konjice',	'initial',	NULL,	NULL,	NULL),
('SI',	'52',	'Smarje Pri Jelsah',	'initial',	NULL,	NULL,	NULL),
('SI',	'53',	'Tolmin',	'initial',	NULL,	NULL,	NULL),
('SI',	'54',	'Trbovlje',	'initial',	NULL,	NULL,	NULL),
('SI',	'55',	'Trebnje',	'initial',	NULL,	NULL,	NULL),
('SI',	'56',	'Trzic',	'initial',	NULL,	NULL,	NULL),
('SI',	'57',	'Velenje',	'initial',	NULL,	NULL,	NULL),
('SI',	'58',	'Vrhnika',	'initial',	NULL,	NULL,	NULL),
('SI',	'59',	'Zagorje Ob Savi',	'initial',	NULL,	NULL,	NULL),
('SI',	'60',	'Zalec',	'initial',	NULL,	NULL,	NULL),
('TH',	'01',	'Amnat Charoen',	'initial',	NULL,	NULL,	NULL),
('TH',	'02',	'Ang Thong',	'initial',	NULL,	NULL,	NULL),
('TH',	'03',	'Buriram',	'initial',	NULL,	NULL,	NULL),
('TH',	'04',	'Chachoengsao',	'initial',	NULL,	NULL,	NULL),
('TH',	'05',	'Chai Nat',	'initial',	NULL,	NULL,	NULL),
('TH',	'06',	'Chaiyaphum',	'initial',	NULL,	NULL,	NULL),
('TH',	'07',	'Chanthaburi',	'initial',	NULL,	NULL,	NULL),
('TH',	'08',	'Chiang Mai',	'initial',	NULL,	NULL,	NULL),
('TH',	'09',	'Chiang Rai',	'initial',	NULL,	NULL,	NULL),
('TH',	'10',	'Chon Buri',	'initial',	NULL,	NULL,	NULL),
('TH',	'11',	'Chumphon',	'initial',	NULL,	NULL,	NULL),
('TH',	'12',	'Kalasin',	'initial',	NULL,	NULL,	NULL),
('TH',	'13',	'Kamphaeng Phet',	'initial',	NULL,	NULL,	NULL),
('TH',	'14',	'Kanchanaburi',	'initial',	NULL,	NULL,	NULL),
('TH',	'15',	'Khon Kaen',	'initial',	NULL,	NULL,	NULL),
('TH',	'16',	'Krabi',	'initial',	NULL,	NULL,	NULL),
('TH',	'17',	'Krung Thep',	'initial',	NULL,	NULL,	NULL),
('TH',	'18',	'Mahanakhon',	'initial',	NULL,	NULL,	NULL),
('TH',	'19',	'Lampang',	'initial',	NULL,	NULL,	NULL),
('TH',	'20',	'Lamphun',	'initial',	NULL,	NULL,	NULL),
('TH',	'21',	'Loei',	'initial',	NULL,	NULL,	NULL),
('TH',	'22',	'Lop Buri',	'initial',	NULL,	NULL,	NULL),
('TH',	'23',	'Mae Hong Son',	'initial',	NULL,	NULL,	NULL),
('TH',	'24',	'Maha Sarakham',	'initial',	NULL,	NULL,	NULL),
('TH',	'25',	'Mukdahan',	'initial',	NULL,	NULL,	NULL),
('TH',	'26',	'Nakhon Nayok',	'initial',	NULL,	NULL,	NULL),
('TH',	'27',	'Nakhon Pathom',	'initial',	NULL,	NULL,	NULL),
('TH',	'28',	'Nakhon Phanom',	'initial',	NULL,	NULL,	NULL),
('TH',	'29',	'Nakhon Ratchasima',	'initial',	NULL,	NULL,	NULL),
('TH',	'30',	'Nakhon Sawan',	'initial',	NULL,	NULL,	NULL),
('TH',	'31',	'Nakhon Si Thammarat',	'initial',	NULL,	NULL,	NULL),
('TH',	'32',	'Nan',	'initial',	NULL,	NULL,	NULL),
('TH',	'33',	'Narathiwat',	'initial',	NULL,	NULL,	NULL),
('TH',	'34',	'Nong Bua Lamphu',	'initial',	NULL,	NULL,	NULL),
('TH',	'35',	'Nong Khai',	'initial',	NULL,	NULL,	NULL),
('TH',	'36',	'Nonthaburi',	'initial',	NULL,	NULL,	NULL),
('TH',	'37',	'Pathum Thani',	'initial',	NULL,	NULL,	NULL),
('TH',	'38',	'Pattani',	'initial',	NULL,	NULL,	NULL),
('TH',	'39',	'Phangnga',	'initial',	NULL,	NULL,	NULL),
('TH',	'40',	'Phatthalung',	'initial',	NULL,	NULL,	NULL),
('TH',	'41',	'Phayao',	'initial',	NULL,	NULL,	NULL),
('TH',	'42',	'Phetchabun',	'initial',	NULL,	NULL,	NULL),
('TH',	'43',	'Phetchaburi',	'initial',	NULL,	NULL,	NULL),
('TH',	'44',	'Phichit',	'initial',	NULL,	NULL,	NULL),
('TH',	'45',	'Phitsanulok',	'initial',	NULL,	NULL,	NULL),
('TH',	'46',	'Phra Nakhon Si Ayut.',	'initial',	NULL,	NULL,	NULL),
('TH',	'47',	'Phrae',	'initial',	NULL,	NULL,	NULL),
('TH',	'48',	'Phuket',	'initial',	NULL,	NULL,	NULL),
('TH',	'49',	'Prachin Buri',	'initial',	NULL,	NULL,	NULL),
('TH',	'50',	'Bueng Kan',	'initial',	NULL,	NULL,	NULL),
('TR',	'01',	'Adana',	'initial',	NULL,	NULL,	NULL),
('TR',	'02',	'Adiyaman',	'initial',	NULL,	NULL,	NULL),
('TR',	'03',	'Afyon',	'initial',	NULL,	NULL,	NULL),
('TR',	'04',	'Agri',	'initial',	NULL,	NULL,	NULL),
('TR',	'05',	'Amasya',	'initial',	NULL,	NULL,	NULL),
('TR',	'06',	'Ankara',	'initial',	NULL,	NULL,	NULL),
('TR',	'07',	'Antalya',	'initial',	NULL,	NULL,	NULL),
('TR',	'08',	'Artvin',	'initial',	NULL,	NULL,	NULL),
('TR',	'09',	'Aydin',	'initial',	NULL,	NULL,	NULL),
('TR',	'10',	'Balikesir',	'initial',	NULL,	NULL,	NULL),
('TR',	'11',	'Bilecik',	'initial',	NULL,	NULL,	NULL),
('TR',	'12',	'Bingöl',	'initial',	NULL,	NULL,	NULL),
('TR',	'13',	'Bitlis',	'initial',	NULL,	NULL,	NULL),
('TR',	'14',	'Bolu',	'initial',	NULL,	NULL,	NULL),
('TR',	'15',	'Burdur',	'initial',	NULL,	NULL,	NULL),
('TR',	'16',	'Bursa',	'initial',	NULL,	NULL,	NULL),
('TR',	'17',	'Canakkale',	'initial',	NULL,	NULL,	NULL),
('TR',	'18',	'Cankiri',	'initial',	NULL,	NULL,	NULL),
('TR',	'19',	'Corum',	'initial',	NULL,	NULL,	NULL),
('TR',	'20',	'Denizli',	'initial',	NULL,	NULL,	NULL),
('TR',	'21',	'Diyarbakir',	'initial',	NULL,	NULL,	NULL),
('TR',	'22',	'Edirne',	'initial',	NULL,	NULL,	NULL),
('TR',	'23',	'Elazig',	'initial',	NULL,	NULL,	NULL),
('TR',	'24',	'Erzincan',	'initial',	NULL,	NULL,	NULL),
('TR',	'25',	'Erzurum',	'initial',	NULL,	NULL,	NULL),
('TR',	'26',	'Eskisehir',	'initial',	NULL,	NULL,	NULL),
('TR',	'27',	'Gaziantep',	'initial',	NULL,	NULL,	NULL),
('TR',	'28',	'Giresun',	'initial',	NULL,	NULL,	NULL),
('TR',	'29',	'Guemueshane',	'initial',	NULL,	NULL,	NULL),
('TR',	'30',	'Hakkari',	'initial',	NULL,	NULL,	NULL),
('TR',	'31',	'Hatay',	'initial',	NULL,	NULL,	NULL),
('TR',	'32',	'Isparta',	'initial',	NULL,	NULL,	NULL),
('TR',	'33',	'Icel',	'initial',	NULL,	NULL,	NULL),
('TR',	'34',	'Istanbul',	'initial',	NULL,	NULL,	NULL),
('TR',	'35',	'Izmir',	'initial',	NULL,	NULL,	NULL),
('TR',	'36',	'Kars',	'initial',	NULL,	NULL,	NULL),
('TR',	'37',	'Kastamonu',	'initial',	NULL,	NULL,	NULL),
('TR',	'38',	'Kayseri',	'initial',	NULL,	NULL,	NULL),
('TR',	'39',	'Kirklareli',	'initial',	NULL,	NULL,	NULL),
('TR',	'40',	'Kirshehir',	'initial',	NULL,	NULL,	NULL),
('TR',	'41',	'Kocaeli',	'initial',	NULL,	NULL,	NULL),
('TR',	'42',	'Konya',	'initial',	NULL,	NULL,	NULL),
('TR',	'43',	'Kuetahya',	'initial',	NULL,	NULL,	NULL),
('TR',	'44',	'Malatya',	'initial',	NULL,	NULL,	NULL),
('TR',	'45',	'Manisa',	'initial',	NULL,	NULL,	NULL),
('TR',	'46',	'K.Marash',	'initial',	NULL,	NULL,	NULL),
('TR',	'47',	'Mardin',	'initial',	NULL,	NULL,	NULL),
('TR',	'48',	'Mugla',	'initial',	NULL,	NULL,	NULL),
('TR',	'49',	'Mush',	'initial',	NULL,	NULL,	NULL),
('TR',	'50',	'Nevshehir',	'initial',	NULL,	NULL,	NULL),
('TR',	'51',	'Nigde',	'initial',	NULL,	NULL,	NULL),
('TR',	'52',	'Ordu',	'initial',	NULL,	NULL,	NULL),
('TR',	'53',	'Rize',	'initial',	NULL,	NULL,	NULL),
('TR',	'54',	'Sakarya',	'initial',	NULL,	NULL,	NULL),
('TR',	'55',	'Samsun',	'initial',	NULL,	NULL,	NULL),
('TR',	'56',	'Siirt',	'initial',	NULL,	NULL,	NULL),
('TR',	'57',	'Sinop',	'initial',	NULL,	NULL,	NULL),
('TR',	'58',	'Sivas',	'initial',	NULL,	NULL,	NULL),
('TR',	'59',	'Tekirdag',	'initial',	NULL,	NULL,	NULL),
('TR',	'60',	'Tokat',	'initial',	NULL,	NULL,	NULL),
('TR',	'61',	'Trabzon',	'initial',	NULL,	NULL,	NULL),
('TR',	'62',	'Tunceli',	'initial',	NULL,	NULL,	NULL),
('TR',	'63',	'Shanliurfa',	'initial',	NULL,	NULL,	NULL),
('TR',	'64',	'Ushak',	'initial',	NULL,	NULL,	NULL),
('TR',	'65',	'Van',	'initial',	NULL,	NULL,	NULL),
('TR',	'66',	'Yozgat',	'initial',	NULL,	NULL,	NULL),
('TR',	'67',	'Zonguldak',	'initial',	NULL,	NULL,	NULL),
('TR',	'68',	'Aksaray',	'initial',	NULL,	NULL,	NULL),
('TR',	'69',	'Bayburt',	'initial',	NULL,	NULL,	NULL),
('TR',	'70',	'Karaman',	'initial',	NULL,	NULL,	NULL),
('TR',	'71',	'Kirikkale',	'initial',	NULL,	NULL,	NULL),
('TR',	'72',	'Batman',	'initial',	NULL,	NULL,	NULL),
('TR',	'73',	'Shirnak',	'initial',	NULL,	NULL,	NULL),
('TR',	'74',	'Bartin',	'initial',	NULL,	NULL,	NULL),
('TR',	'75',	'Ardahan',	'initial',	NULL,	NULL,	NULL),
('TR',	'76',	'Igdir',	'initial',	NULL,	NULL,	NULL),
('TR',	'77',	'Yalova',	'initial',	NULL,	NULL,	NULL),
('TW',	'FJN',	'Fu-chien',	'initial',	NULL,	NULL,	NULL),
('TW',	'KSH',	'Kao-hsiung',	'initial',	NULL,	NULL,	NULL),
('TW',	'TPE',	'T´ai-pei',	'initial',	NULL,	NULL,	NULL),
('TW',	'TWN',	'Taiwan',	'initial',	NULL,	NULL,	NULL),
('UA',	'CHG',	'Chernigivs''ka',	'initial',	NULL,	NULL,	NULL),
('UA',	'CHR',	'Cherkas''ka',	'initial',	NULL,	NULL,	NULL),
('UA',	'CHV',	'Chernovits''ka',	'initial',	NULL,	NULL,	NULL),
('UA',	'DNP',	'Dnipropetrovs''ka',	'initial',	NULL,	NULL,	NULL),
('UA',	'DON',	'Donets''ka',	'initial',	NULL,	NULL,	NULL),
('UA',	'HAR',	'Harkivs''ka',	'initial',	NULL,	NULL,	NULL),
('UA',	'HML',	'Hmel''nits''ka',	'initial',	NULL,	NULL,	NULL),
('UA',	'HRS',	'Hersons''ka',	'initial',	NULL,	NULL,	NULL),
('UA',	'IVF',	'Ivano-Frankivs''ka',	'initial',	NULL,	NULL,	NULL),
('UA',	'KIE',	'Kievs''ka',	'initial',	NULL,	NULL,	NULL),
('UA',	'KIR',	'Kirovograds''ka',	'initial',	NULL,	NULL,	NULL),
('UA',	'KRM',	'Respublika Krim',	'initial',	NULL,	NULL,	NULL),
('UA',	'L''V',	'L''vivsbka',	'initial',	NULL,	NULL,	NULL),
('UA',	'LUG',	'Lugans''ka',	'initial',	NULL,	NULL,	NULL),
('UA',	'MIK',	'Mikolaivs''ka',	'initial',	NULL,	NULL,	NULL),
('UA',	'M_K',	'm.Kiev',	'initial',	NULL,	NULL,	NULL),
('UA',	'M_S',	'm.Sevastopil''',	'initial',	NULL,	NULL,	NULL),
('UA',	'ODS',	'Odes''ka',	'initial',	NULL,	NULL,	NULL),
('UA',	'POL',	'Poltavs''ka',	'initial',	NULL,	NULL,	NULL),
('UA',	'RIV',	'Rivnens''ka',	'initial',	NULL,	NULL,	NULL),
('UA',	'SUM',	'Sums''ka',	'initial',	NULL,	NULL,	NULL),
('UA',	'TER',	'Ternopil''s''ka',	'initial',	NULL,	NULL,	NULL),
('UA',	'VIN',	'Vinnits''ka',	'initial',	NULL,	NULL,	NULL),
('UA',	'VOL',	'Volins''ka',	'initial',	NULL,	NULL,	NULL),
('UA',	'ZAK',	'Zakarpats''ka',	'initial',	NULL,	NULL,	NULL),
('UA',	'ZAP',	'Zaporiz''ka',	'initial',	NULL,	NULL,	NULL),
('UA',	'ZHI',	'Zhitomirs''ka',	'initial',	NULL,	NULL,	NULL),
('US',	'AK',	'Alaska',	'initial',	NULL,	NULL,	NULL),
('US',	'AL',	'Alabama',	'initial',	NULL,	NULL,	NULL),
('US',	'AR',	'Arkansas',	'initial',	NULL,	NULL,	NULL),
('US',	'AS',	'American Samoa',	'initial',	NULL,	NULL,	NULL),
('US',	'AZ',	'Arizona',	'initial',	NULL,	NULL,	NULL),
('US',	'CA',	'California',	'initial',	NULL,	NULL,	NULL),
('US',	'CO',	'Colorado',	'initial',	NULL,	NULL,	NULL),
('US',	'CT',	'Connecticut',	'initial',	NULL,	NULL,	NULL),
('US',	'DC',	'District of Columbia',	'initial',	NULL,	NULL,	NULL),
('US',	'DE',	'Delaware',	'initial',	NULL,	NULL,	NULL),
('US',	'FL',	'Florida',	'initial',	NULL,	NULL,	NULL),
('US',	'GA',	'Georgia',	'initial',	NULL,	NULL,	NULL),
('US',	'GU',	'Guam',	'initial',	NULL,	NULL,	NULL),
('US',	'HI',	'Hawaii',	'initial',	NULL,	NULL,	NULL),
('US',	'IA',	'Iowa',	'initial',	NULL,	NULL,	NULL),
('US',	'ID',	'Idaho',	'initial',	NULL,	NULL,	NULL),
('US',	'IL',	'Illinois',	'initial',	NULL,	NULL,	NULL),
('US',	'IN',	'Indiana',	'initial',	NULL,	NULL,	NULL),
('US',	'KS',	'Kansas',	'initial',	NULL,	NULL,	NULL),
('US',	'KY',	'Kentucky',	'initial',	NULL,	NULL,	NULL),
('US',	'LA',	'Louisiana',	'initial',	NULL,	NULL,	NULL),
('US',	'MA',	'Massachusetts',	'initial',	NULL,	NULL,	NULL),
('US',	'MD',	'Maryland',	'initial',	NULL,	NULL,	NULL),
('US',	'ME',	'Maine',	'initial',	NULL,	NULL,	NULL),
('US',	'MI',	'Michigan',	'initial',	NULL,	NULL,	NULL),
('US',	'MN',	'Minnesota',	'initial',	NULL,	NULL,	NULL),
('US',	'MO',	'Missouri',	'initial',	NULL,	NULL,	NULL),
('US',	'MP',	'Northern Mariana Isl',	'initial',	NULL,	NULL,	NULL),
('US',	'MS',	'Mississippi',	'initial',	NULL,	NULL,	NULL),
('US',	'MT',	'Montana',	'initial',	NULL,	NULL,	NULL),
('US',	'NC',	'North Carolina',	'initial',	NULL,	NULL,	NULL),
('US',	'ND',	'North Dakota',	'initial',	NULL,	NULL,	NULL),
('US',	'NE',	'Nebraska',	'initial',	NULL,	NULL,	NULL),
('US',	'NH',	'New Hampshire',	'initial',	NULL,	NULL,	NULL),
('US',	'NJ',	'New Jersey',	'initial',	NULL,	NULL,	NULL),
('US',	'NM',	'New Mexico',	'initial',	NULL,	NULL,	NULL),
('US',	'NV',	'Nevada',	'initial',	NULL,	NULL,	NULL),
('US',	'NY',	'New York',	'initial',	NULL,	NULL,	NULL),
('US',	'OH',	'Ohio',	'initial',	NULL,	NULL,	NULL),
('US',	'OK',	'Oklahoma',	'initial',	NULL,	NULL,	NULL),
('US',	'OR',	'Oregon',	'initial',	NULL,	NULL,	NULL),
('US',	'PA',	'Pennsylvania',	'initial',	NULL,	NULL,	NULL),
('US',	'PR',	'Puerto Rico',	'initial',	NULL,	NULL,	NULL),
('US',	'RI',	'Rhode Island',	'initial',	NULL,	NULL,	NULL),
('US',	'SC',	'South Carolina',	'initial',	NULL,	NULL,	NULL),
('US',	'SD',	'South Dakota',	'initial',	NULL,	NULL,	NULL),
('US',	'TN',	'Tennessee',	'initial',	NULL,	NULL,	NULL),
('US',	'TX',	'Texas',	'initial',	NULL,	NULL,	NULL),
('US',	'UT',	'Utah',	'initial',	NULL,	NULL,	NULL),
('US',	'VA',	'Virginia',	'initial',	NULL,	NULL,	NULL),
('US',	'VI',	'Virgin Islands',	'initial',	NULL,	NULL,	NULL),
('US',	'VT',	'Vermont',	'initial',	NULL,	NULL,	NULL),
('US',	'WA',	'Washington',	'initial',	NULL,	NULL,	NULL),
('US',	'WI',	'Wisconsin',	'initial',	NULL,	NULL,	NULL),
('US',	'WV',	'West Virginia',	'initial',	NULL,	NULL,	NULL),
('US',	'WY',	'Wyoming',	'initial',	NULL,	NULL,	NULL),
('VE',	'AMA',	'Amazon',	'initial',	NULL,	NULL,	NULL),
('VE',	'ANZ',	'Anzoategui',	'initial',	NULL,	NULL,	NULL),
('VE',	'APU',	'Apure',	'initial',	NULL,	NULL,	NULL),
('VE',	'ARA',	'Aragua',	'initial',	NULL,	NULL,	NULL),
('VE',	'BAR',	'Barinas',	'initial',	NULL,	NULL,	NULL),
('VE',	'BOL',	'Bolivar',	'initial',	NULL,	NULL,	NULL),
('VE',	'CAR',	'Carabobo',	'initial',	NULL,	NULL,	NULL),
('VE',	'COJ',	'Cojedes',	'initial',	NULL,	NULL,	NULL),
('VE',	'DA',	'Delta Amacuro',	'initial',	NULL,	NULL,	NULL),
('VE',	'DF',	'Distrito Federal',	'initial',	NULL,	NULL,	NULL),
('VE',	'FAL',	'Falcon',	'initial',	NULL,	NULL,	NULL),
('VE',	'GUA',	'Guarico',	'initial',	NULL,	NULL,	NULL),
('VE',	'LAR',	'Lara',	'initial',	NULL,	NULL,	NULL),
('VE',	'MER',	'Merida',	'initial',	NULL,	NULL,	NULL),
('VE',	'MIR',	'Miranda',	'initial',	NULL,	NULL,	NULL),
('VE',	'MON',	'Monagas',	'initial',	NULL,	NULL,	NULL),
('VE',	'NE',	'Nueva Esparta',	'initial',	NULL,	NULL,	NULL),
('VE',	'POR',	'Portuguesa',	'initial',	NULL,	NULL,	NULL),
('VE',	'SUC',	'Sucre',	'initial',	NULL,	NULL,	NULL),
('VE',	'TAC',	'Tachira',	'initial',	NULL,	NULL,	NULL),
('VE',	'TRU',	'Trujillo',	'initial',	NULL,	NULL,	NULL),
('VE',	'VAR',	'Vargas',	'initial',	NULL,	NULL,	NULL),
('VE',	'YAR',	'Yaracuy',	'initial',	NULL,	NULL,	NULL),
('VE',	'ZUL',	'Zulia',	'initial',	NULL,	NULL,	NULL),
('ZA',	'EC',	'Eastern Cape',	'initial',	NULL,	NULL,	NULL),
('ZA',	'FS',	'Free State',	'initial',	NULL,	NULL,	NULL),
('ZA',	'GP',	'Gauteng',	'initial',	NULL,	NULL,	NULL),
('ZA',	'LP',	'Limpopo',	'initial',	NULL,	NULL,	NULL),
('ZA',	'MP',	'Mpumalanga',	'initial',	NULL,	NULL,	NULL),
('ZA',	'NC',	'Northern Cape',	'initial',	NULL,	NULL,	NULL),
('ZA',	'NW',	'Northwest',	'initial',	NULL,	NULL,	NULL),
('ZA',	'WC',	'Western Cape',	'initial',	NULL,	NULL,	NULL),
('ZA',	'ZN',	'KwaZulu-Natal',	'initial',	NULL,	NULL,	NULL),
('ID',	'06',	'DI Aceh Aceh',	'initial',	NULL,	NULL,	NULL);

DROP TABLE IF EXISTS "ref_purchase_groups";
DROP SEQUENCE IF EXISTS ref_purchase_groups_id_seq;
CREATE SEQUENCE ref_purchase_groups_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "vendormgt"."ref_purchase_groups" (
    "id" bigint DEFAULT nextval('ref_purchase_groups_id_seq') NOT NULL,
    "group_code" character(3) NOT NULL,
    "description" character varying(50) NOT NULL,
    "created_by" character varying(255) DEFAULT 'initial' NOT NULL,
    "created_at" timestamp(0),
    "updated_at" timestamp(0),
    "deleted_at" timestamp(0),
    CONSTRAINT "ref_purchase_groups_pkey" PRIMARY KEY ("id")
) WITH (oids = false);

COMMENT ON COLUMN "vendormgt"."ref_purchase_groups"."created_by" IS 'Define row who user created';


DROP TABLE IF EXISTS "ref_purchase_orgs";
DROP SEQUENCE IF EXISTS ref_purchase_orgs_id_seq;
CREATE SEQUENCE ref_purchase_orgs_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "vendormgt"."ref_purchase_orgs" (
    "id" bigint DEFAULT nextval('ref_purchase_orgs_id_seq') NOT NULL,
    "org_code" character varying(4) NOT NULL,
    "description" character varying(50) NOT NULL,
    "created_by" character varying(255) DEFAULT 'initial' NOT NULL,
    "created_at" timestamp(0),
    "updated_at" timestamp(0),
    "deleted_at" timestamp(0),
    CONSTRAINT "ref_purchase_orgs_pkey" PRIMARY KEY ("id")
) WITH (oids = false);

COMMENT ON COLUMN "vendormgt"."ref_purchase_orgs"."created_by" IS 'Define row who user created';


DROP TABLE IF EXISTS "ref_statuss";
DROP SEQUENCE IF EXISTS ref_statuss_id_seq;
CREATE SEQUENCE ref_statuss_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 2147483647 START 1 CACHE 1;

CREATE TABLE "vendormgt"."ref_statuss" (
    "id" integer DEFAULT nextval('ref_statuss_id_seq') NOT NULL,
    "status" character varying(255) NOT NULL,
    "description" character varying(255),
    "deleteflg" boolean DEFAULT false NOT NULL,
    "created_at" timestamp(0) DEFAULT '2020-05-18 03:47:39' NOT NULL,
    "created_by" character varying(255) DEFAULT 'initial' NOT NULL,
    "updated_at" timestamp(0),
    "updated_by" character varying(255),
    CONSTRAINT "ref_statuss_pkey" PRIMARY KEY ("id"),
    CONSTRAINT "ref_statuss_status_unique" UNIQUE ("status")
) WITH (oids = false);

COMMENT ON COLUMN "vendormgt"."ref_statuss"."deleteflg" IS 'Define row active';

COMMENT ON COLUMN "vendormgt"."ref_statuss"."created_at" IS 'Define time when row has been created';

COMMENT ON COLUMN "vendormgt"."ref_statuss"."created_by" IS 'Define row who user created';

COMMENT ON COLUMN "vendormgt"."ref_statuss"."updated_at" IS 'Define time when row has been updated';

COMMENT ON COLUMN "vendormgt"."ref_statuss"."updated_by" IS 'Define row who user updated';


DROP TABLE IF EXISTS "ref_sub_districts";
CREATE TABLE "vendormgt"."ref_sub_districts" (
    "country_code" character varying(255) NOT NULL,
    "region_code" character varying(255) NOT NULL,
    "city_code" character varying(255) NOT NULL,
    "district_code" character varying(255) NOT NULL,
    "district_description" character varying(255) NOT NULL,
    "created_by" character varying(255) DEFAULT 'initial' NOT NULL,
    "created_at" timestamp(0),
    "updated_at" timestamp(0),
    "deleted_at" timestamp(0),
    CONSTRAINT "ref_sub_districts_pkey" PRIMARY KEY ("district_code")
) WITH (oids = false);

COMMENT ON COLUMN "vendormgt"."ref_sub_districts"."created_by" IS 'Define row who user created';


DROP TABLE IF EXISTS "role_has_permissions";
CREATE TABLE "vendormgt"."role_has_permissions" (
    "permission_id" bigint NOT NULL,
    "role_id" bigint NOT NULL,
    CONSTRAINT "role_has_permissions_pkey" PRIMARY KEY ("permission_id", "role_id"),
    CONSTRAINT "role_has_permissions_permission_id_foreign" FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE NOT DEFERRABLE,
    CONSTRAINT "role_has_permissions_role_id_foreign" FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE NOT DEFERRABLE
) WITH (oids = false);


DROP TABLE IF EXISTS "roles";
DROP SEQUENCE IF EXISTS roles_id_seq;
CREATE SEQUENCE roles_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "vendormgt"."roles" (
    "id" bigint DEFAULT nextval('roles_id_seq') NOT NULL,
    "name" character varying(255) NOT NULL,
    "guard_name" character varying(255) NOT NULL,
    "created_at" timestamp(0),
    "updated_at" timestamp(0),
    CONSTRAINT "roles_pkey" PRIMARY KEY ("id")
) WITH (oids = false);


DROP TABLE IF EXISTS "sessions";
CREATE TABLE "vendormgt"."sessions" (
    "id" character varying(255) NOT NULL,
    "user_id" bigint,
    "ip_address" character varying(45),
    "user_agent" text,
    "payload" text NOT NULL,
    "last_activity" integer NOT NULL,
    CONSTRAINT "sessions_id_unique" UNIQUE ("id")
) WITH (oids = false);


DROP TABLE IF EXISTS "tender_aanwijzings";
DROP SEQUENCE IF EXISTS tender_aanwijzings_id_seq;
CREATE SEQUENCE tender_aanwijzings_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "vendormgt"."tender_aanwijzings" (
    "id" bigint DEFAULT nextval('tender_aanwijzings_id_seq') NOT NULL,
    "tender_number" character varying(32),
    "event_name" character varying(255),
    "venue" character varying(255),
    "event_start" timestamp(0),
    "event_end" timestamp(0),
    "note" character varying(255),
    "status" character varying(255) DEFAULT 'DRAFT',
    "result_attachment" character varying(255),
    "result_description" character varying(255),
    "created_by" character varying(255),
    "updated_by" character varying(255),
    "deleted_by" character varying(255),
    "created_at" timestamp(0),
    "updated_at" timestamp(0),
    "deleted_at" timestamp(0),
    CONSTRAINT "tender_aanwijzings_pkey" PRIMARY KEY ("id")
) WITH (oids = false);

CREATE INDEX "tender_aanwijzings_tender_number_index" ON "vendormgt"."tender_aanwijzings" USING btree ("tender_number");


DROP TABLE IF EXISTS "tender_general_documents";
DROP SEQUENCE IF EXISTS tender_general_documents_id_seq;
CREATE SEQUENCE tender_general_documents_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "vendormgt"."tender_general_documents" (
    "id" bigint DEFAULT nextval('tender_general_documents_id_seq') NOT NULL,
    "tender_number" character varying(32),
    "document_name" character varying(255),
    "description" character varying(255),
    "attachment" character varying(255),
    "created_by" character varying(255),
    "updated_by" character varying(255),
    "deleted_by" character varying(255),
    "created_at" timestamp(0),
    "updated_at" timestamp(0),
    "deleted_at" timestamp(0),
    CONSTRAINT "tender_general_documents_pkey" PRIMARY KEY ("id")
) WITH (oids = false);

CREATE INDEX "tender_general_documents_tender_number_index" ON "vendormgt"."tender_general_documents" USING btree ("tender_number");


DROP TABLE IF EXISTS "tender_internal_documents";
DROP SEQUENCE IF EXISTS tender_internal_documents_id_seq;
CREATE SEQUENCE tender_internal_documents_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "vendormgt"."tender_internal_documents" (
    "id" bigint DEFAULT nextval('tender_internal_documents_id_seq') NOT NULL,
    "tender_number" character varying(32),
    "document_name" character varying(255),
    "description" character varying(255),
    "attachment" character varying(255),
    "created_by" character varying(255),
    "updated_by" character varying(255),
    "deleted_by" character varying(255),
    "created_at" timestamp(0),
    "updated_at" timestamp(0),
    "deleted_at" timestamp(0),
    CONSTRAINT "tender_internal_documents_pkey" PRIMARY KEY ("id")
) WITH (oids = false);

CREATE INDEX "tender_internal_documents_tender_number_index" ON "vendormgt"."tender_internal_documents" USING btree ("tender_number");


DROP TABLE IF EXISTS "tender_items";
DROP SEQUENCE IF EXISTS tender_items_id_seq;
CREATE SEQUENCE tender_items_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "vendormgt"."tender_items" (
    "id" bigint DEFAULT nextval('tender_items_id_seq') NOT NULL,
    "tender_number" character varying(32) NOT NULL,
    "number" character varying(32) NOT NULL,
    "line_number" character varying(32) NOT NULL,
    "product_code" character varying(32),
    "product_group_code" character varying(32),
    "description" character varying(256),
    "purch_group_code" character varying(32),
    "purch_group_name" character varying(32),
    "qty" numeric(19,3),
    "uom" character varying(32),
    "est_unit_price" numeric(19,2),
    "price_unit" integer DEFAULT '1',
    "currency_code" character varying(8),
    "subtotal" numeric(19,2),
    "state" character varying(32),
    "expected_delivery_date" timestamp(0),
    "transfer_date" timestamp(0),
    "account_assignment" character varying(32),
    "item_category" character varying(1),
    "gl_account" character varying(32),
    "cost_code" character varying(32),
    "requisitioner" character varying(32),
    "requisitioner_desc" character varying(80),
    "tracking_number" character varying(80),
    "request_date" date,
    "certification" character varying(255),
    "material_status" character varying(255),
    "plant" character varying(32),
    "plant_name" character varying(64),
    "storage_loc" character varying(32),
    "storage_loc_name" character varying(64),
    "qty_ordered" numeric(19,3),
    "cost_desc" character varying(255),
    "overall_limit" character varying(255),
    "expected_limit" character varying(255),
    "deleteflg" character varying(4),
    "created_by" character varying(255),
    "updated_by" character varying(255),
    "deleted_by" character varying(255),
    "created_at" timestamp(0),
    "updated_at" timestamp(0),
    "deleted_at" timestamp(0),
    CONSTRAINT "tender_items_pkey" PRIMARY KEY ("id")
) WITH (oids = false);

CREATE INDEX "tender_items_number_index" ON "vendormgt"."tender_items" USING btree ("number");

CREATE INDEX "tender_items_tender_number_index" ON "vendormgt"."tender_items" USING btree ("tender_number");

COMMENT ON COLUMN "vendormgt"."tender_items"."subtotal" IS 'qty * est_unit_price';


DROP TABLE IF EXISTS "tender_parameters";
DROP SEQUENCE IF EXISTS tender_parameters_id_seq;
CREATE SEQUENCE tender_parameters_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "vendormgt"."tender_parameters" (
    "id" bigint DEFAULT nextval('tender_parameters_id_seq') NOT NULL,
    "tender_number" character varying(32),
    "title" character varying(255) NOT NULL,
    "purchase_org_id" bigint,
    "purchase_group_id" bigint,
    "location" character varying(255),
    "incoterm" character varying(32),
    "tender_method" character varying(32),
    "buyer" character varying(255),
    "prequalification" integer,
    "eauction" integer,
    "submission_method" character varying(32),
    "evaluation_method" character varying(32),
    "bid_bond" character varying(32),
    "winning_method" character varying(32),
    "validity_quotation" integer,
    "visibility_bid_document" character varying(32),
    "aanwijzing" integer,
    "tkdn" character varying(32),
    "tkdn_option" integer,
    "down_payment" integer,
    "down_payment_percentage" integer,
    "retention" integer,
    "retention_percentage" integer,
    "scope_of_work" character varying(255),
    "note_to_vendor" character varying(255),
    "note" character varying(255),
    "plant_id" integer,
    "status" character varying(255),
    "workflow_status" character varying(255),
    "workflow_values" character varying(255),
    "retender_from" character varying(32),
    "created_by" character varying(255),
    "updated_by" character varying(255),
    "deleted_by" character varying(255),
    "created_at" timestamp(0),
    "updated_at" timestamp(0),
    "deleted_at" timestamp(0),
    CONSTRAINT "tender_parameters_pkey" PRIMARY KEY ("id"),
    CONSTRAINT "tender_parameters_tender_number_unique" UNIQUE ("tender_number")
) WITH (oids = false);


DROP TABLE IF EXISTS "tender_workflows";
DROP SEQUENCE IF EXISTS tender_workflows_id_seq;
CREATE SEQUENCE tender_workflows_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "vendormgt"."tender_workflows" (
    "id" bigint DEFAULT nextval('tender_workflows_id_seq') NOT NULL,
    "tender_number" character varying(32),
    "status" character varying(32),
    "workflow_status" character varying(32),
    "page" character varying(32),
    "user" character varying(64) DEFAULT 'any',
    "sequence" integer,
    "is_done" integer DEFAULT '0',
    "created_at" timestamp(0),
    "updated_at" timestamp(0),
    "deleted_at" timestamp(0),
    CONSTRAINT "tender_workflows_pkey" PRIMARY KEY ("id")
) WITH (oids = false);

CREATE INDEX "tender_workflows_tender_number_index" ON "vendormgt"."tender_workflows" USING btree ("tender_number");


DROP TABLE IF EXISTS "user_extensions";
DROP SEQUENCE IF EXISTS user_extensions_id_seq;
CREATE SEQUENCE user_extensions_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "vendormgt"."user_extensions" (
    "id" bigint DEFAULT nextval('user_extensions_id_seq') NOT NULL,
    "created_at" timestamp(0),
    "updated_at" timestamp(0),
    CONSTRAINT "user_extensions_pkey" PRIMARY KEY ("id")
) WITH (oids = false);


DROP TABLE IF EXISTS "users";
DROP SEQUENCE IF EXISTS users_id_seq;
CREATE SEQUENCE users_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "vendormgt"."users" (
    "id" bigint DEFAULT nextval('users_id_seq') NOT NULL,
    "name" character varying(255) NOT NULL,
    "userid" character varying(255) NOT NULL,
    "user_type" character varying(255),
    "ref_id" bigint,
    "email" character varying(255) NOT NULL,
    "email_verified_at" timestamp(0),
    "password" character varying(255) NOT NULL,
    "remember_token" character varying(100),
    "created_at" timestamp(0),
    "updated_at" timestamp(0),
    CONSTRAINT "users_email_unique" UNIQUE ("email"),
    CONSTRAINT "users_pkey" PRIMARY KEY ("id"),
    CONSTRAINT "users_userid_unique" UNIQUE ("userid")
) WITH (oids = false);


DROP VIEW IF EXISTS "v_candidates";
CREATE TABLE "v_candidates" ("id" bigint, "vendor_group" character varying(100), "vendor_name" character varying(255), "company_type_id" bigint, "company_type" character varying(20), "purchase_org_id" bigint, "purchase_org_code" character varying(4), "purchase_org_description" character varying(50), "president_director" character varying(200), "address_1" character varying(255), "address_2" character varying(255), "address_3" character varying(255), "address_4" character varying(255), "address_5" character varying(255), "country_code" character varying(200), "country" character varying(255), "region_code" character varying(200), "province" character varying(255), "city_code" character varying(200), "city" character varying(255), "district_code" character varying(200), "sub_district" character varying(255), "house_number" character varying(20), "postal_code" character varying(20), "phone_number" character varying(20), "fax_number" character varying(20), "company_email" character varying(255), "company_site" character varying(255), "pic_full_name" character varying(200), "pic_mobile_number" character varying(20), "pic_email" character varying(255), "tender_ref_number" character varying(255), "pkp_number" character varying(100), "pkp_attachment" character varying(255), "tin_number" character varying(100), "tin_attachment" character varying(255), "idcard_number" character varying(100), "idcard_attachment" character varying(255), "identification_type" character varying(100), "pkp_type" character varying(100), "non_pkp_number" character varying(100), "vendor_code" character varying(16), "business_partner_code" character varying(255), "sap_vendor_code" character varying(255), "already_exist_sap" boolean, "created_by" character varying(255), "created_at" timestamp(0), "updated_at" timestamp(0), "deleted_at" timestamp(0));


DROP VIEW IF EXISTS "v_vendors";
CREATE TABLE "v_vendors" ("id" bigint, "vendor_group" character varying(100), "vendor_name" character varying(255), "company_type_id" bigint, "company_type" character varying(20), "purchase_org_id" bigint, "purchase_org_code" character varying(4), "purchase_org_description" character varying(50), "president_director" character varying(200), "address_1" character varying(255), "address_2" character varying(255), "address_3" character varying(255), "address_4" character varying(255), "address_5" character varying(255), "country_code" character varying(200), "country" character varying(255), "region_code" character varying(200), "province" character varying(255), "city_code" character varying(200), "city" character varying(255), "district_code" character varying(200), "sub_district" character varying(255), "house_number" character varying(20), "postal_code" character varying(20), "phone_number" character varying(20), "fax_number" character varying(20), "company_email" character varying(255), "company_site" character varying(255), "pic_full_name" character varying(200), "pic_mobile_number" character varying(20), "pic_email" character varying(255), "tender_ref_number" character varying(255), "pkp_number" character varying(100), "pkp_attachment" character varying(255), "tin_number" character varying(100), "tin_attachment" character varying(255), "idcard_number" character varying(100), "idcard_attachment" character varying(255), "identification_type" character varying(100), "pkp_type" character varying(100), "non_pkp_number" character varying(100), "vendor_code" character varying(16), "business_partner_code" character varying(255), "sap_vendor_code" character varying(255), "already_exist_sap" boolean, "created_by" character varying(255), "created_at" timestamp(0), "updated_at" timestamp(0), "deleted_at" timestamp(0));


DROP TABLE IF EXISTS "vendor_approvals";
DROP SEQUENCE IF EXISTS vendor_approvals_id_seq;
CREATE SEQUENCE vendor_approvals_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "vendormgt"."vendor_approvals" (
    "id" bigint DEFAULT nextval('vendor_approvals_id_seq') NOT NULL,
    "vendor_id" bigint NOT NULL,
    "as_position" character varying(255) NOT NULL,
    "approver" character varying(255) NOT NULL,
    "sequence_level" integer DEFAULT '0' NOT NULL,
    "is_done" boolean DEFAULT false NOT NULL,
    "created_by" character varying(255) DEFAULT 'initial' NOT NULL,
    "created_at" timestamp(0),
    "updated_at" timestamp(0),
    "deleted_at" timestamp(0),
    CONSTRAINT "vendor_approvals_pkey" PRIMARY KEY ("id")
) WITH (oids = false);

COMMENT ON COLUMN "vendormgt"."vendor_approvals"."created_by" IS 'Define row who user created';


DROP TABLE IF EXISTS "vendor_history_statuses";
DROP SEQUENCE IF EXISTS vendor_history_statuses_id_seq;
CREATE SEQUENCE vendor_history_statuses_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "vendormgt"."vendor_history_statuses" (
    "id" bigint DEFAULT nextval('vendor_history_statuses_id_seq') NOT NULL,
    "vendor_id" bigint NOT NULL,
    "status" character varying(255) NOT NULL,
    "description" character varying(255),
    "version" character varying(255),
    "remarks" character varying(255),
    "created_by" character varying(255) DEFAULT 'initial' NOT NULL,
    "created_at" timestamp(0),
    "updated_at" timestamp(0),
    "deleted_at" timestamp(0),
    CONSTRAINT "vendor_history_statuses_pkey" PRIMARY KEY ("id")
) WITH (oids = false);

COMMENT ON COLUMN "vendormgt"."vendor_history_statuses"."created_by" IS 'Define row who user created';


DROP TABLE IF EXISTS "vendor_profile_bank_accounts";
DROP SEQUENCE IF EXISTS vendor_profile_bank_accounts_id_seq;
CREATE SEQUENCE vendor_profile_bank_accounts_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "vendormgt"."vendor_profile_bank_accounts" (
    "id" bigint DEFAULT nextval('vendor_profile_bank_accounts_id_seq') NOT NULL,
    "vendor_profile_id" bigint NOT NULL,
    "account_holder_name" character varying(255) NOT NULL,
    "account_number" character varying(255) NOT NULL,
    "currency" character varying(255) NOT NULL,
    "bank_name" character varying(255) NOT NULL,
    "bank_address" character varying(255) NOT NULL,
    "bank_statement_letter" character varying(255),
    "parent_id" bigint DEFAULT '0',
    "is_finished" boolean DEFAULT false NOT NULL,
    "is_submitted" boolean DEFAULT false NOT NULL,
    "is_current_data" boolean DEFAULT false NOT NULL,
    "created_by" character varying(255) DEFAULT 'initial' NOT NULL,
    "created_at" timestamp(0),
    "updated_at" timestamp(0),
    "deleted_at" timestamp(0),
    CONSTRAINT "vendor_profile_bank_accounts_pkey" PRIMARY KEY ("id")
) WITH (oids = false);

COMMENT ON COLUMN "vendormgt"."vendor_profile_bank_accounts"."is_finished" IS 'Define row status is finish changes';

COMMENT ON COLUMN "vendormgt"."vendor_profile_bank_accounts"."is_submitted" IS 'Define row status is submit to admin';

COMMENT ON COLUMN "vendormgt"."vendor_profile_bank_accounts"."is_current_data" IS 'Define row status is current data';

COMMENT ON COLUMN "vendormgt"."vendor_profile_bank_accounts"."created_by" IS 'Define row who user created';


DROP TABLE IF EXISTS "vendor_profile_bodbocs";
DROP SEQUENCE IF EXISTS vendor_profile_bodbocs_id_seq;
CREATE SEQUENCE vendor_profile_bodbocs_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "vendormgt"."vendor_profile_bodbocs" (
    "id" bigint DEFAULT nextval('vendor_profile_bodbocs_id_seq') NOT NULL,
    "vendor_profile_id" bigint NOT NULL,
    "board_type" character varying(255) NOT NULL,
    "is_person_company_shareholder" boolean DEFAULT true NOT NULL,
    "full_name" character varying(255) NOT NULL,
    "nationality" character varying(255),
    "position" character varying(255),
    "email" character varying(255),
    "phone_number" character varying(255),
    "company_head" boolean DEFAULT true NOT NULL,
    "parent_id" bigint DEFAULT '0',
    "is_finished" boolean DEFAULT false NOT NULL,
    "is_submitted" boolean DEFAULT false NOT NULL,
    "is_current_data" boolean DEFAULT false NOT NULL,
    "created_by" character varying(255) DEFAULT 'initial' NOT NULL,
    "created_at" timestamp(0),
    "updated_at" timestamp(0),
    "deleted_at" timestamp(0),
    CONSTRAINT "vendor_profile_bodbocs_pkey" PRIMARY KEY ("id")
) WITH (oids = false);

COMMENT ON COLUMN "vendormgt"."vendor_profile_bodbocs"."is_finished" IS 'Define row status is finish changes';

COMMENT ON COLUMN "vendormgt"."vendor_profile_bodbocs"."is_submitted" IS 'Define row status is submit to admin';

COMMENT ON COLUMN "vendormgt"."vendor_profile_bodbocs"."is_current_data" IS 'Define row status is current data';

COMMENT ON COLUMN "vendormgt"."vendor_profile_bodbocs"."created_by" IS 'Define row who user created';


DROP TABLE IF EXISTS "vendor_profile_business_permits";
DROP SEQUENCE IF EXISTS vendor_profile_business_permits_id_seq;
CREATE SEQUENCE vendor_profile_business_permits_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "vendormgt"."vendor_profile_business_permits" (
    "id" bigint DEFAULT nextval('vendor_profile_business_permits_id_seq') NOT NULL,
    "vendor_profile_id" bigint NOT NULL,
    "business_permit_type" character varying(255) NOT NULL,
    "business_class" character varying(255) NOT NULL,
    "business_permit_number" character varying(255) NOT NULL,
    "valid_from_date" date NOT NULL,
    "valid_thru_date" date NOT NULL,
    "issued_by" character varying(255) NOT NULL,
    "attachment" character varying(255) NOT NULL,
    "parent_id" bigint DEFAULT '0',
    "is_finished" boolean DEFAULT false NOT NULL,
    "is_submitted" boolean DEFAULT false NOT NULL,
    "is_current_data" boolean DEFAULT false NOT NULL,
    "created_by" character varying(255) DEFAULT 'initial' NOT NULL,
    "created_at" timestamp(0),
    "updated_at" timestamp(0),
    "deleted_at" timestamp(0),
    CONSTRAINT "vendor_profile_business_permits_pkey" PRIMARY KEY ("id")
) WITH (oids = false);

COMMENT ON COLUMN "vendormgt"."vendor_profile_business_permits"."is_finished" IS 'Define row status is finish changes';

COMMENT ON COLUMN "vendormgt"."vendor_profile_business_permits"."is_submitted" IS 'Define row status is submit to admin';

COMMENT ON COLUMN "vendormgt"."vendor_profile_business_permits"."is_current_data" IS 'Define row status is current data';

COMMENT ON COLUMN "vendormgt"."vendor_profile_business_permits"."created_by" IS 'Define row who user created';


DROP TABLE IF EXISTS "vendor_profile_certifications";
DROP SEQUENCE IF EXISTS vendor_profile_certifications_id_seq;
CREATE SEQUENCE vendor_profile_certifications_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "vendormgt"."vendor_profile_certifications" (
    "id" bigint DEFAULT nextval('vendor_profile_certifications_id_seq') NOT NULL,
    "vendor_profile_id" bigint NOT NULL,
    "certification_type" character varying(255) NOT NULL,
    "description" character varying(255) NOT NULL,
    "valid_from_date" date NOT NULL,
    "valid_thru_date" date NOT NULL,
    "attachment" character varying(255) NOT NULL,
    "parent_id" bigint DEFAULT '0',
    "is_finished" boolean DEFAULT false NOT NULL,
    "is_submitted" boolean DEFAULT false NOT NULL,
    "is_current_data" boolean DEFAULT false NOT NULL,
    "created_by" character varying(255) DEFAULT 'initial' NOT NULL,
    "created_at" timestamp(0),
    "updated_at" timestamp(0),
    "deleted_at" timestamp(0),
    CONSTRAINT "vendor_profile_certifications_pkey" PRIMARY KEY ("id")
) WITH (oids = false);

COMMENT ON COLUMN "vendormgt"."vendor_profile_certifications"."is_finished" IS 'Define row status is finish changes';

COMMENT ON COLUMN "vendormgt"."vendor_profile_certifications"."is_submitted" IS 'Define row status is submit to admin';

COMMENT ON COLUMN "vendormgt"."vendor_profile_certifications"."is_current_data" IS 'Define row status is current data';

COMMENT ON COLUMN "vendormgt"."vendor_profile_certifications"."created_by" IS 'Define row who user created';


DROP TABLE IF EXISTS "vendor_profile_competencies";
DROP SEQUENCE IF EXISTS vendor_profile_competencies_id_seq;
CREATE SEQUENCE vendor_profile_competencies_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "vendormgt"."vendor_profile_competencies" (
    "id" bigint DEFAULT nextval('vendor_profile_competencies_id_seq') NOT NULL,
    "vendor_profile_id" bigint NOT NULL,
    "classification" character varying(255) NOT NULL,
    "sub_classification" character varying(255) NOT NULL,
    "detail_competency" character varying(255) NOT NULL,
    "vendor_type" character varying(255) NOT NULL,
    "attachment" character varying(255) NOT NULL,
    "parent_id" bigint DEFAULT '0',
    "is_finished" boolean DEFAULT false NOT NULL,
    "is_submitted" boolean DEFAULT false NOT NULL,
    "is_current_data" boolean DEFAULT false NOT NULL,
    "created_by" character varying(255) DEFAULT 'initial' NOT NULL,
    "created_at" timestamp(0),
    "updated_at" timestamp(0),
    "deleted_at" timestamp(0),
    CONSTRAINT "vendor_profile_competencies_pkey" PRIMARY KEY ("id")
) WITH (oids = false);

COMMENT ON COLUMN "vendormgt"."vendor_profile_competencies"."is_finished" IS 'Define row status is finish changes';

COMMENT ON COLUMN "vendormgt"."vendor_profile_competencies"."is_submitted" IS 'Define row status is submit to admin';

COMMENT ON COLUMN "vendormgt"."vendor_profile_competencies"."is_current_data" IS 'Define row status is current data';

COMMENT ON COLUMN "vendormgt"."vendor_profile_competencies"."created_by" IS 'Define row who user created';


DROP TABLE IF EXISTS "vendor_profile_deeds";
DROP SEQUENCE IF EXISTS vendor_profile_deeds_id_seq;
CREATE SEQUENCE vendor_profile_deeds_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "vendormgt"."vendor_profile_deeds" (
    "id" bigint DEFAULT nextval('vendor_profile_deeds_id_seq') NOT NULL,
    "vendor_profile_id" bigint NOT NULL,
    "deed_type" character varying(255) NOT NULL,
    "deed_number" character varying(255) NOT NULL,
    "deed_date" date NOT NULL,
    "notary_name" character varying(255) NOT NULL,
    "sk_menkumham_number" character varying(255) NOT NULL,
    "sk_menkumham_date" date NOT NULL,
    "attachment" character varying(255) NOT NULL,
    "parent_id" bigint DEFAULT '0',
    "is_finished" boolean DEFAULT false NOT NULL,
    "is_submitted" boolean DEFAULT false NOT NULL,
    "is_current_data" boolean DEFAULT false NOT NULL,
    "created_by" character varying(255) DEFAULT 'initial' NOT NULL,
    "created_at" timestamp(0),
    "updated_at" timestamp(0),
    "deleted_at" timestamp(0),
    CONSTRAINT "vendor_profile_deeds_pkey" PRIMARY KEY ("id")
) WITH (oids = false);

COMMENT ON COLUMN "vendormgt"."vendor_profile_deeds"."is_finished" IS 'Define row status is finish changes';

COMMENT ON COLUMN "vendormgt"."vendor_profile_deeds"."is_submitted" IS 'Define row status is submit to admin';

COMMENT ON COLUMN "vendormgt"."vendor_profile_deeds"."is_current_data" IS 'Define row status is current data';

COMMENT ON COLUMN "vendormgt"."vendor_profile_deeds"."created_by" IS 'Define row who user created';


DROP TABLE IF EXISTS "vendor_profile_detail_statuses";
DROP SEQUENCE IF EXISTS vendor_profile_detail_statuses_id_seq;
CREATE SEQUENCE vendor_profile_detail_statuses_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "vendormgt"."vendor_profile_detail_statuses" (
    "id" bigint DEFAULT nextval('vendor_profile_detail_statuses_id_seq') NOT NULL,
    "vendor_profile_id" bigint NOT NULL,
    "general_status" character varying(255) DEFAULT 'none' NOT NULL,
    "deed_status" character varying(255) DEFAULT 'none' NOT NULL,
    "shareholder_status" character varying(255) DEFAULT 'none' NOT NULL,
    "bodboc_status" character varying(255) DEFAULT 'none' NOT NULL,
    "businesspermit_status" character varying(255) DEFAULT 'none' NOT NULL,
    "pic_status" character varying(255) DEFAULT 'none' NOT NULL,
    "equipment_status" character varying(255) DEFAULT 'none' NOT NULL,
    "expert_status" character varying(255) DEFAULT 'none' NOT NULL,
    "certification_status" character varying(255) DEFAULT 'none' NOT NULL,
    "scopeofsupply_status" character varying(255) DEFAULT 'none' NOT NULL,
    "experience_status" character varying(255) DEFAULT 'none' NOT NULL,
    "bankaccount_status" character varying(255) DEFAULT 'none' NOT NULL,
    "financial_status" character varying(255) DEFAULT 'none' NOT NULL,
    "tax_status" character varying(255) DEFAULT 'none' NOT NULL,
    "is_submitted" boolean DEFAULT false NOT NULL,
    "created_by" character varying(255) DEFAULT 'initial' NOT NULL,
    "created_at" timestamp(0),
    "updated_at" timestamp(0),
    "deleted_at" timestamp(0),
    "is_revised" boolean DEFAULT false NOT NULL,
    CONSTRAINT "vendor_profile_detail_statuses_pkey" PRIMARY KEY ("id")
) WITH (oids = false);

COMMENT ON COLUMN "vendormgt"."vendor_profile_detail_statuses"."is_submitted" IS 'Define row who user created';

COMMENT ON COLUMN "vendormgt"."vendor_profile_detail_statuses"."created_by" IS 'Define row who user created';


DROP TABLE IF EXISTS "vendor_profile_experience";
DROP SEQUENCE IF EXISTS vendor_profile_experience_id_seq;
CREATE SEQUENCE vendor_profile_experience_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "vendormgt"."vendor_profile_experience" (
    "id" bigint DEFAULT nextval('vendor_profile_experience_id_seq') NOT NULL,
    "vendor_profile_id" bigint NOT NULL,
    "classification" character varying(255) NOT NULL,
    "sub_classification" character varying(255) NOT NULL,
    "project_name" character varying(255) NOT NULL,
    "project_location" character varying(255) NOT NULL,
    "contract_owner" character varying(255) NOT NULL,
    "address" character varying(255),
    "country" character varying(255),
    "province" character varying(255),
    "city" character varying(255),
    "sub_district" character varying(255),
    "postal_code" character varying(255),
    "contact_person" character varying(255),
    "phone_number" character varying(255),
    "contract_number" character varying(255) NOT NULL,
    "valid_from_date" character varying(255) NOT NULL,
    "valid_thru_date" character varying(255) NOT NULL,
    "currency" character varying(255),
    "contract_value" character varying(255),
    "bast_wan_date" character varying(255),
    "bast_wan_number" character varying(255),
    "bast_wan_attachment" character varying(255),
    "parent_id" bigint DEFAULT '0',
    "is_finished" boolean DEFAULT false NOT NULL,
    "is_submitted" boolean DEFAULT false NOT NULL,
    "is_current_data" boolean DEFAULT false NOT NULL,
    "created_by" character varying(255) DEFAULT 'initial' NOT NULL,
    "created_at" timestamp(0),
    "updated_at" timestamp(0),
    "deleted_at" timestamp(0),
    CONSTRAINT "vendor_profile_experience_pkey" PRIMARY KEY ("id")
) WITH (oids = false);

COMMENT ON COLUMN "vendormgt"."vendor_profile_experience"."is_finished" IS 'Define row status is finish changes';

COMMENT ON COLUMN "vendormgt"."vendor_profile_experience"."is_submitted" IS 'Define row status is submit to admin';

COMMENT ON COLUMN "vendormgt"."vendor_profile_experience"."is_current_data" IS 'Define row status is current data';

COMMENT ON COLUMN "vendormgt"."vendor_profile_experience"."created_by" IS 'Define row who user created';


DROP TABLE IF EXISTS "vendor_profile_experts";
DROP SEQUENCE IF EXISTS vendor_profile_experts_id_seq;
CREATE SEQUENCE vendor_profile_experts_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "vendormgt"."vendor_profile_experts" (
    "id" bigint DEFAULT nextval('vendor_profile_experts_id_seq') NOT NULL,
    "vendor_profile_id" bigint NOT NULL,
    "full_name" character varying(255),
    "date_of_birth" date,
    "education" character varying(255),
    "university" character varying(255),
    "experts_university" character varying(255),
    "major" character varying(255),
    "ktp_number" character varying(255),
    "address" text,
    "job_experience" character varying(255),
    "years_experience" integer,
    "certification_number" character varying(255),
    "parent_id" bigint DEFAULT '0',
    "is_finished" boolean DEFAULT false NOT NULL,
    "is_submitted" boolean DEFAULT false NOT NULL,
    "is_current_data" boolean DEFAULT false NOT NULL,
    "created_by" character varying(255) DEFAULT 'initial' NOT NULL,
    "created_at" timestamp(0),
    "updated_at" timestamp(0),
    "deleted_at" timestamp(0),
    CONSTRAINT "vendor_profile_experts_pkey" PRIMARY KEY ("id")
) WITH (oids = false);

COMMENT ON COLUMN "vendormgt"."vendor_profile_experts"."is_finished" IS 'Define row status is finish changes';

COMMENT ON COLUMN "vendormgt"."vendor_profile_experts"."is_submitted" IS 'Define row status is submit to admin';

COMMENT ON COLUMN "vendormgt"."vendor_profile_experts"."is_current_data" IS 'Define row status is current data';

COMMENT ON COLUMN "vendormgt"."vendor_profile_experts"."created_by" IS 'Define row who user created';


DROP TABLE IF EXISTS "vendor_profile_financials";
DROP SEQUENCE IF EXISTS vendor_profile_financials_id_seq;
CREATE SEQUENCE vendor_profile_financials_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "vendormgt"."vendor_profile_financials" (
    "id" bigint DEFAULT nextval('vendor_profile_financials_id_seq') NOT NULL,
    "vendor_profile_id" bigint NOT NULL,
    "financial_statement_date" date NOT NULL,
    "public_accountant_full_name" character varying(255) NOT NULL,
    "audit" character varying(255) NOT NULL,
    "financial_statement_year" character varying(4) NOT NULL,
    "valid_thru_date" date NOT NULL,
    "financial_statement_attachment" character varying(255),
    "currency" character varying(255),
    "cash" numeric(18,2) NOT NULL,
    "bank" numeric(18,2),
    "short_term_investments" numeric(18,2),
    "long_term_investments" numeric(18,2),
    "total_receivables" numeric(18,2),
    "inventories" numeric(18,2),
    "work_in_progress" numeric(18,2) NOT NULL,
    "total_current_assets" numeric(18,2) NOT NULL,
    "equipment_and_machinery" numeric(18,2),
    "fixed_inventories" numeric(18,2),
    "buildings" numeric(18,2),
    "lands" numeric(18,2),
    "total_fixed_assets" numeric(18,2),
    "other_assets" numeric(18,2),
    "incoming_dept" numeric(18,2),
    "taxes_payable" numeric(18,2),
    "other_payable" numeric(18,2),
    "total_short_term_debt" numeric(18,2),
    "long_term_payables" numeric(18,2),
    "net_worth" numeric(18,2),
    "total_assets" numeric(18,2),
    "total_liabilities" numeric(18,2),
    "net_worth_exclude_land_building" numeric(18,2),
    "annual_revenue" numeric(18,2),
    "business_class" character varying(255),
    "parent_id" bigint DEFAULT '0',
    "is_finished" boolean DEFAULT false NOT NULL,
    "is_submitted" boolean DEFAULT false NOT NULL,
    "is_current_data" boolean DEFAULT false NOT NULL,
    "created_by" character varying(255) DEFAULT 'initial' NOT NULL,
    "created_at" timestamp(0),
    "updated_at" timestamp(0),
    "deleted_at" timestamp(0),
    CONSTRAINT "vendor_profile_financials_pkey" PRIMARY KEY ("id")
) WITH (oids = false);

COMMENT ON COLUMN "vendormgt"."vendor_profile_financials"."is_finished" IS 'Define row status is finish changes';

COMMENT ON COLUMN "vendormgt"."vendor_profile_financials"."is_submitted" IS 'Define row status is submit to admin';

COMMENT ON COLUMN "vendormgt"."vendor_profile_financials"."is_current_data" IS 'Define row status is current data';

COMMENT ON COLUMN "vendormgt"."vendor_profile_financials"."created_by" IS 'Define row who user created';


DROP TABLE IF EXISTS "vendor_profile_generals";
DROP SEQUENCE IF EXISTS vendor_profile_generals_id_seq;
CREATE SEQUENCE vendor_profile_generals_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "vendormgt"."vendor_profile_generals" (
    "id" bigint DEFAULT nextval('vendor_profile_generals_id_seq') NOT NULL,
    "vendor_profile_id" bigint NOT NULL,
    "company_name" character varying(255) NOT NULL,
    "company_type_id" bigint NOT NULL,
    "location_category" character varying(255),
    "country" character varying(200) NOT NULL,
    "province" character varying(200) NOT NULL,
    "city" character varying(200) NOT NULL,
    "sub_district" character varying(200) NOT NULL,
    "postal_code" character varying(20) NOT NULL,
    "address_1" character varying(255) NOT NULL,
    "phone_number" character varying(20) NOT NULL,
    "fax_number" character varying(20) NOT NULL,
    "website" character varying(255),
    "company_email" character varying(100) NOT NULL,
    "parent_id" bigint DEFAULT '0',
    "primary_data" boolean DEFAULT false NOT NULL,
    "is_finished" boolean DEFAULT false NOT NULL,
    "is_submitted" boolean DEFAULT false NOT NULL,
    "is_current_data" boolean DEFAULT false NOT NULL,
    "created_by" character varying(255) DEFAULT 'initial' NOT NULL,
    "created_at" timestamp(0),
    "updated_at" timestamp(0),
    "deleted_at" timestamp(0),
    "address_2" character varying(255) NOT NULL,
    "address_3" character varying(255) NOT NULL,
    "address_4" character varying(255) NOT NULL,
    "address_5" character varying(255) NOT NULL,
    CONSTRAINT "vendor_profile_generals_pkey" PRIMARY KEY ("id")
) WITH (oids = false);

COMMENT ON COLUMN "vendormgt"."vendor_profile_generals"."primary_data" IS 'Define row to show vendor profile info';

COMMENT ON COLUMN "vendormgt"."vendor_profile_generals"."is_finished" IS 'Define row status is finish changes';

COMMENT ON COLUMN "vendormgt"."vendor_profile_generals"."is_submitted" IS 'Define row status is submit to admin';

COMMENT ON COLUMN "vendormgt"."vendor_profile_generals"."is_current_data" IS 'Define row status is current data';

COMMENT ON COLUMN "vendormgt"."vendor_profile_generals"."created_by" IS 'Define row who user created';


DROP TABLE IF EXISTS "vendor_profile_pics";
DROP SEQUENCE IF EXISTS vendor_profile_pics_id_seq;
CREATE SEQUENCE vendor_profile_pics_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "vendormgt"."vendor_profile_pics" (
    "id" bigint DEFAULT nextval('vendor_profile_pics_id_seq') NOT NULL,
    "vendor_profile_id" bigint NOT NULL,
    "username" character varying(8) NOT NULL,
    "full_name" character varying(255) NOT NULL,
    "email" character varying(255) NOT NULL,
    "phone" character varying(255),
    "primary_data" boolean DEFAULT false NOT NULL,
    "parent_id" bigint DEFAULT '0',
    "is_finished" boolean DEFAULT false NOT NULL,
    "is_submitted" boolean DEFAULT false NOT NULL,
    "is_current_data" boolean DEFAULT false NOT NULL,
    "created_by" character varying(255) DEFAULT 'initial' NOT NULL,
    "created_at" timestamp(0),
    "updated_at" timestamp(0),
    "deleted_at" timestamp(0),
    CONSTRAINT "vendor_profile_pics_pkey" PRIMARY KEY ("id")
) WITH (oids = false);

COMMENT ON COLUMN "vendormgt"."vendor_profile_pics"."is_finished" IS 'Define row status is finish changes';

COMMENT ON COLUMN "vendormgt"."vendor_profile_pics"."is_submitted" IS 'Define row status is submit to admin';

COMMENT ON COLUMN "vendormgt"."vendor_profile_pics"."is_current_data" IS 'Define row status is current data';

COMMENT ON COLUMN "vendormgt"."vendor_profile_pics"."created_by" IS 'Define row who user created';


DROP TABLE IF EXISTS "vendor_profile_shareholders";
DROP SEQUENCE IF EXISTS vendor_profile_shareholders_id_seq;
CREATE SEQUENCE vendor_profile_shareholders_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "vendormgt"."vendor_profile_shareholders" (
    "id" bigint DEFAULT nextval('vendor_profile_shareholders_id_seq') NOT NULL,
    "vendor_profile_id" bigint NOT NULL,
    "full_name" character varying(255) NOT NULL,
    "nationality" character varying(255) NOT NULL,
    "share_percentage" numeric(5,2) NOT NULL,
    "email" character varying(255),
    "ktp_number" character varying(255),
    "ktp_attachment" character varying(255),
    "parent_id" bigint DEFAULT '0',
    "is_finished" boolean DEFAULT false NOT NULL,
    "is_submitted" boolean DEFAULT false NOT NULL,
    "is_current_data" boolean DEFAULT false NOT NULL,
    "created_by" character varying(255) DEFAULT 'initial' NOT NULL,
    "created_at" timestamp(0),
    "updated_at" timestamp(0),
    "deleted_at" timestamp(0),
    CONSTRAINT "vendor_profile_shareholders_pkey" PRIMARY KEY ("id")
) WITH (oids = false);

COMMENT ON COLUMN "vendormgt"."vendor_profile_shareholders"."is_finished" IS 'Define row status is finish changes';

COMMENT ON COLUMN "vendormgt"."vendor_profile_shareholders"."is_submitted" IS 'Define row status is submit to admin';

COMMENT ON COLUMN "vendormgt"."vendor_profile_shareholders"."is_current_data" IS 'Define row status is current data';

COMMENT ON COLUMN "vendormgt"."vendor_profile_shareholders"."created_by" IS 'Define row who user created';


DROP TABLE IF EXISTS "vendor_profile_taxes";
DROP SEQUENCE IF EXISTS vendor_profile_taxes_id_seq;
CREATE SEQUENCE vendor_profile_taxes_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "vendormgt"."vendor_profile_taxes" (
    "id" bigint DEFAULT nextval('vendor_profile_taxes_id_seq') NOT NULL,
    "vendor_profile_id" bigint NOT NULL,
    "tax_document_type" character varying(255) NOT NULL,
    "tax_document_number" character varying(255) NOT NULL,
    "issued_date" date NOT NULL,
    "tax_document_attachment" character varying(255) NOT NULL,
    "parent_id" bigint DEFAULT '0',
    "is_finished" boolean DEFAULT false NOT NULL,
    "is_submitted" boolean DEFAULT false NOT NULL,
    "is_current_data" boolean DEFAULT false NOT NULL,
    "created_by" character varying(255) DEFAULT 'initial' NOT NULL,
    "created_at" timestamp(0),
    "updated_at" timestamp(0),
    "deleted_at" timestamp(0),
    CONSTRAINT "vendor_profile_taxes_pkey" PRIMARY KEY ("id")
) WITH (oids = false);

COMMENT ON COLUMN "vendormgt"."vendor_profile_taxes"."is_finished" IS 'Define row status is finish changes';

COMMENT ON COLUMN "vendormgt"."vendor_profile_taxes"."is_submitted" IS 'Define row status is submit to admin';

COMMENT ON COLUMN "vendormgt"."vendor_profile_taxes"."is_current_data" IS 'Define row status is current data';

COMMENT ON COLUMN "vendormgt"."vendor_profile_taxes"."created_by" IS 'Define row who user created';


DROP TABLE IF EXISTS "vendor_profile_tools";
DROP SEQUENCE IF EXISTS vendor_profile_tools_id_seq;
CREATE SEQUENCE vendor_profile_tools_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "vendormgt"."vendor_profile_tools" (
    "id" bigint DEFAULT nextval('vendor_profile_tools_id_seq') NOT NULL,
    "vendor_profile_id" bigint NOT NULL,
    "equipment_type" character varying(255) NOT NULL,
    "total_qty" integer NOT NULL,
    "measurement" character varying(255) NOT NULL,
    "brand" character varying(255) NOT NULL,
    "condition" character varying(255) NOT NULL,
    "location" character varying(255) NOT NULL,
    "manufacturing_date" date NOT NULL,
    "ownership" character varying(255) NOT NULL,
    "parent_id" bigint DEFAULT '0',
    "is_finished" boolean DEFAULT false NOT NULL,
    "is_submitted" boolean DEFAULT false NOT NULL,
    "is_current_data" boolean DEFAULT false NOT NULL,
    "created_by" character varying(255) DEFAULT 'initial' NOT NULL,
    "created_at" timestamp(0),
    "updated_at" timestamp(0),
    "deleted_at" timestamp(0),
    CONSTRAINT "vendor_profile_tools_pkey" PRIMARY KEY ("id")
) WITH (oids = false);

COMMENT ON COLUMN "vendormgt"."vendor_profile_tools"."is_finished" IS 'Define row status is finish changes';

COMMENT ON COLUMN "vendormgt"."vendor_profile_tools"."is_submitted" IS 'Define row status is submit to admin';

COMMENT ON COLUMN "vendormgt"."vendor_profile_tools"."is_current_data" IS 'Define row status is current data';

COMMENT ON COLUMN "vendormgt"."vendor_profile_tools"."created_by" IS 'Define row who user created';


DROP TABLE IF EXISTS "vendor_profiles";
DROP SEQUENCE IF EXISTS vendor_profiles_id_seq;
CREATE SEQUENCE vendor_profiles_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "vendormgt"."vendor_profiles" (
    "id" bigint DEFAULT nextval('vendor_profiles_id_seq') NOT NULL,
    "vendor_id" bigint NOT NULL,
    "company_name" character varying(255) NOT NULL,
    "company_type" character varying(100) NOT NULL,
    "company_category" character varying(255),
    "company_status" character varying(255),
    "active_skl_number" character varying(255),
    "active_skl_attachment" character varying(255),
    "company_warning" character varying(255),
    "created_by" character varying(255) DEFAULT 'initial' NOT NULL,
    "created_at" timestamp(0),
    "updated_at" timestamp(0),
    "deleted_at" timestamp(0),
    CONSTRAINT "vendor_profiles_pkey" PRIMARY KEY ("id")
) WITH (oids = false);

COMMENT ON COLUMN "vendormgt"."vendor_profiles"."created_by" IS 'Define row who user created';


DROP TABLE IF EXISTS "vendor_sanction_histories";
DROP SEQUENCE IF EXISTS vendor_sanction_histories_id_seq;
CREATE SEQUENCE vendor_sanction_histories_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "vendormgt"."vendor_sanction_histories" (
    "id" bigint DEFAULT nextval('vendor_sanction_histories_id_seq') NOT NULL,
    "vendor_profile_id" bigint NOT NULL,
    "vendor_sanction_id" bigint NOT NULL,
    "username" character varying(32),
    "role" character varying(255) NOT NULL,
    "activity" character varying(64) NOT NULL,
    "status" character varying(32) NOT NULL,
    "comments" character varying(255) NOT NULL,
    "pic" character varying(32),
    "activity_date" timestamp(0) NOT NULL,
    "created_at" timestamp(0),
    "updated_at" timestamp(0),
    CONSTRAINT "vendor_sanction_histories_pkey" PRIMARY KEY ("id")
) WITH (oids = false);


DROP TABLE IF EXISTS "vendor_sanctions";
DROP SEQUENCE IF EXISTS vendor_sanctions_id_seq;
CREATE SEQUENCE vendor_sanctions_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "vendormgt"."vendor_sanctions" (
    "id" bigint DEFAULT nextval('vendor_sanctions_id_seq') NOT NULL,
    "vendor_profile_id" bigint NOT NULL,
    "sanction_type" character varying(32) NOT NULL,
    "valid_from_date" date NOT NULL,
    "valid_thru_date" date NOT NULL,
    "letter_number" character varying(32) NOT NULL,
    "description" character varying(255) NOT NULL,
    "attachment" character varying(255),
    "status" character varying(16),
    "created_by" character varying(32),
    "updated_by" character varying(32),
    "created_at" timestamp(0),
    "updated_at" timestamp(0),
    "deleted_at" timestamp(0),
    CONSTRAINT "vendor_sanctions_pkey" PRIMARY KEY ("id")
) WITH (oids = false);


DROP TABLE IF EXISTS "vendor_workflows";
DROP SEQUENCE IF EXISTS vendor_workflows_id_seq;
CREATE SEQUENCE vendor_workflows_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "vendormgt"."vendor_workflows" (
    "id" bigint DEFAULT nextval('vendor_workflows_id_seq') NOT NULL,
    "vendor_id" bigint NOT NULL,
    "activity" character varying(255) NOT NULL,
    "remarks" character varying(255),
    "started_at" timestamp(0),
    "finished_at" timestamp(0),
    "created_by" character varying(255) DEFAULT 'system' NOT NULL,
    "created_at" timestamp(0),
    "updated_at" timestamp(0),
    "deleted_at" timestamp(0),
    CONSTRAINT "vendor_workflows_pkey" PRIMARY KEY ("id")
) WITH (oids = false);

CREATE INDEX "vendor_workflows_vendor_id_created_by_index" ON "vendormgt"."vendor_workflows" USING btree ("vendor_id", "created_by");

COMMENT ON COLUMN "vendormgt"."vendor_workflows"."created_by" IS 'Define row who user created';


DROP TABLE IF EXISTS "vendors";
DROP SEQUENCE IF EXISTS vendors_id_seq;
CREATE SEQUENCE vendors_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "vendormgt"."vendors" (
    "id" bigint DEFAULT nextval('vendors_id_seq') NOT NULL,
    "vendor_name" character varying(255) NOT NULL,
    "company_type_id" bigint NOT NULL,
    "purchase_org_id" bigint NOT NULL,
    "president_director" character varying(200) NOT NULL,
    "address_1" character varying(255) NOT NULL,
    "address_2" character varying(255),
    "address_3" character varying(255),
    "address_4" character varying(255),
    "address_5" character varying(255),
    "country" character varying(200) NOT NULL,
    "province" character varying(200) NOT NULL,
    "city" character varying(200) NOT NULL,
    "sub_district" character varying(200) NOT NULL,
    "house_number" character varying(20),
    "postal_code" character varying(20) NOT NULL,
    "phone_number" character varying(20) NOT NULL,
    "fax_number" character varying(20),
    "company_email" character varying(255),
    "company_site" character varying(255),
    "pic_full_name" character varying(200) NOT NULL,
    "pic_mobile_number" character varying(20) NOT NULL,
    "pic_email" character varying(255) NOT NULL,
    "tender_ref_number" character varying(255),
    "pkp_number" character varying(100),
    "pkp_attachment" character varying(255),
    "tin_number" character varying(100),
    "tin_attachment" character varying(255),
    "vendor_code" character varying(16),
    "business_partner_code" character varying(255),
    "sap_vendor_code" character varying(255),
    "already_exist_sap" boolean DEFAULT false,
    "created_by" character varying(255) DEFAULT 'applicant' NOT NULL,
    "created_at" timestamp(0),
    "updated_at" timestamp(0),
    "deleted_at" timestamp(0),
    "idcard_number" character varying(100),
    "idcard_attachment" character varying(255),
    "identification_type" character varying(100),
    "pkp_type" character varying(100),
    "non_pkp_number" character varying(100),
    "vendor_group" character varying(100) DEFAULT 'local',
    CONSTRAINT "vendors_pkey" PRIMARY KEY ("id")
) WITH (oids = false);

COMMENT ON COLUMN "vendormgt"."vendors"."created_by" IS 'Define row who user created';


DROP TABLE IF EXISTS "v_candidates";
CREATE VIEW "v_candidates" AS SELECT vendors.id,
    vendors.vendor_group,
    vendors.vendor_name,
    vendors.company_type_id,
    ref_company_types.company_type,
    vendors.purchase_org_id,
    ref_purchase_orgs.org_code AS purchase_org_code,
    ref_purchase_orgs.description AS purchase_org_description,
    vendors.president_director,
    vendors.address_1,
    vendors.address_2,
    vendors.address_3,
    vendors.address_4,
    vendors.address_5,
    vendors.country AS country_code,
    ref_countries.country_description AS country,
    vendors.province AS region_code,
    ref_provinces.region_description AS province,
    vendors.city AS city_code,
    ref_cities.city_description AS city,
    vendors.sub_district AS district_code,
    ref_sub_districts.district_description AS sub_district,
    vendors.house_number,
    vendors.postal_code,
    vendors.phone_number,
    vendors.fax_number,
    vendors.company_email,
    vendors.company_site,
    vendors.pic_full_name,
    vendors.pic_mobile_number,
    vendors.pic_email,
    vendors.tender_ref_number,
    vendors.pkp_number,
    vendors.pkp_attachment,
    vendors.tin_number,
    vendors.tin_attachment,
    vendors.idcard_number,
    vendors.idcard_attachment,
    vendors.identification_type,
    vendors.pkp_type,
    vendors.non_pkp_number,
    vendors.vendor_code,
    vendors.business_partner_code,
    vendors.sap_vendor_code,
    vendors.already_exist_sap,
    vendors.created_by,
    vendors.created_at,
    vendors.updated_at,
    vendors.deleted_at
   FROM ((((((vendors
     JOIN ref_company_types ON (((ref_company_types.id = vendors.company_type_id) AND (ref_company_types.deleted_at IS NULL))))
     JOIN ref_purchase_orgs ON (((ref_purchase_orgs.id = vendors.purchase_org_id) AND (ref_purchase_orgs.deleted_at IS NULL))))
     JOIN ref_countries ON ((((ref_countries.country_code)::text = (vendors.country)::text) AND (ref_countries.deleted_at IS NULL))))
     JOIN ref_provinces ON ((((ref_provinces.country_code)::text = (vendors.country)::text) AND ((ref_provinces.region_code)::text = (vendors.province)::text) AND (ref_provinces.deleted_at IS NULL))))
     JOIN ref_cities ON ((((ref_cities.country_code)::text = (vendors.country)::text) AND ((ref_cities.region_code)::text = (vendors.province)::text) AND ((ref_cities.city_code)::text = (vendors.city)::text) AND (ref_cities.deleted_at IS NULL))))
     JOIN ref_sub_districts ON ((((ref_sub_districts.country_code)::text = (vendors.country)::text) AND ((ref_sub_districts.region_code)::text = (vendors.province)::text) AND ((ref_sub_districts.city_code)::text = (vendors.city)::text) AND ((ref_sub_districts.district_code)::text = (vendors.sub_district)::text) AND (ref_sub_districts.deleted_at IS NULL))));

DROP TABLE IF EXISTS "v_vendors";
CREATE VIEW "v_vendors" AS SELECT vendors.id,
    vendors.vendor_group,
    vendors.vendor_name,
    vendors.company_type_id,
    ref_company_types.company_type,
    vendors.purchase_org_id,
    ref_purchase_orgs.org_code AS purchase_org_code,
    ref_purchase_orgs.description AS purchase_org_description,
    vendors.president_director,
    vendors.address_1,
    vendors.address_2,
    vendors.address_3,
    vendors.address_4,
    vendors.address_5,
    vendors.country AS country_code,
    ref_countries.country_description AS country,
    vendors.province AS region_code,
    ref_provinces.region_description AS province,
    vendors.city AS city_code,
    ref_cities.city_description AS city,
    vendors.sub_district AS district_code,
    ref_sub_districts.district_description AS sub_district,
    vendors.house_number,
    vendors.postal_code,
    vendors.phone_number,
    vendors.fax_number,
    vendors.company_email,
    vendors.company_site,
    vendors.pic_full_name,
    vendors.pic_mobile_number,
    vendors.pic_email,
    vendors.tender_ref_number,
    vendors.pkp_number,
    vendors.pkp_attachment,
    vendors.tin_number,
    vendors.tin_attachment,
    vendors.idcard_number,
    vendors.idcard_attachment,
    vendors.identification_type,
    vendors.pkp_type,
    vendors.non_pkp_number,
    vendors.vendor_code,
    vendors.business_partner_code,
    vendors.sap_vendor_code,
    vendors.already_exist_sap,
    vendors.created_by,
    vendors.created_at,
    vendors.updated_at,
    vendors.deleted_at
   FROM ((((((vendors
     JOIN ref_company_types ON (((ref_company_types.id = vendors.company_type_id) AND (ref_company_types.deleted_at IS NULL))))
     JOIN ref_purchase_orgs ON (((ref_purchase_orgs.id = vendors.purchase_org_id) AND (ref_purchase_orgs.deleted_at IS NULL))))
     JOIN ref_countries ON ((((ref_countries.country_code)::text = (vendors.country)::text) AND (ref_countries.deleted_at IS NULL))))
     JOIN ref_provinces ON ((((ref_provinces.country_code)::text = (vendors.country)::text) AND ((ref_provinces.region_code)::text = (vendors.province)::text) AND (ref_provinces.deleted_at IS NULL))))
     JOIN ref_cities ON ((((ref_cities.country_code)::text = (vendors.country)::text) AND ((ref_cities.region_code)::text = (vendors.province)::text) AND ((ref_cities.city_code)::text = (vendors.city)::text) AND (ref_cities.deleted_at IS NULL))))
     JOIN ref_sub_districts ON ((((ref_sub_districts.country_code)::text = (vendors.country)::text) AND ((ref_sub_districts.region_code)::text = (vendors.province)::text) AND ((ref_sub_districts.city_code)::text = (vendors.city)::text) AND ((ref_sub_districts.district_code)::text = (vendors.sub_district)::text) AND (ref_sub_districts.deleted_at IS NULL))));

-- 2020-06-10 10:29:55.267135+07
