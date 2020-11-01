-- ========================================================================
-- MODIFY QUERY 2020-05-13
-- ========================================================================
ALTER TABLE vendormgt.applicant_general_administrations
    ADD COLUMN parent_id BIGINT NOT NULL DEFAULT 0;

COMMENT ON COLUMN vendormgt.applicant_general_administrations.parent_id
    IS 'Define row is child from other row';
	
ALTER TABLE vendormgt.applicant_company_profiles
     ADD COLUMN parent_id BIGINT NOT NULL DEFAULT 0;

COMMENT ON COLUMN vendormgt.applicant_company_profiles.parent_id
    IS 'Define row is child from other row';
	
ALTER TABLE vendormgt.applicant_deeds
     ADD COLUMN parent_id BIGINT NOT NULL DEFAULT 0;

COMMENT ON COLUMN vendormgt.applicant_deeds.parent_id
    IS 'Define row is child from other row';
	
ALTER TABLE vendormgt.applicant_shareholders
     ADD COLUMN parent_id BIGINT NOT NULL DEFAULT 0;

COMMENT ON COLUMN vendormgt.applicant_shareholders.parent_id
    IS 'Define row is child from other row';

ALTER TABLE vendormgt.applicant_general_administrations
    ADD COLUMN is_finished boolean NOT NULL DEFAULT False;

COMMENT ON COLUMN vendormgt.applicant_general_administrations.is_finished
    IS 'Already finish in editing form';
	
ALTER TABLE vendormgt.applicant_general_administrations
    ADD COLUMN is_current_data boolean NOT NULL DEFAULT False;

COMMENT ON COLUMN vendormgt.applicant_general_administrations.is_current_data
    IS 'Define data is current_data';

ALTER TABLE vendormgt.applicant_company_profiles
    ADD COLUMN is_finished boolean NOT NULL DEFAULT False;

COMMENT ON COLUMN vendormgt.applicant_company_profiles.is_finished
    IS 'Already finish in editing form';
	
ALTER TABLE vendormgt.applicant_company_profiles
    ADD COLUMN is_current_data boolean NOT NULL DEFAULT False;

COMMENT ON COLUMN vendormgt.applicant_company_profiles.is_current_data
    IS 'Define data is current_data';
	
ALTER TABLE vendormgt.applicant_deeds
    ADD COLUMN is_finished boolean NOT NULL DEFAULT False;

COMMENT ON COLUMN vendormgt.applicant_deeds.is_finished
    IS 'Already finish in editing form';
	
ALTER TABLE vendormgt.applicant_deeds
    ADD COLUMN is_current_data boolean NOT NULL DEFAULT False;

COMMENT ON COLUMN vendormgt.applicant_deeds.is_current_data
    IS 'Define data is current_data';
	
ALTER TABLE vendormgt.applicant_shareholders
    ADD COLUMN is_finished boolean NOT NULL DEFAULT False;

COMMENT ON COLUMN vendormgt.applicant_shareholders.is_finished
    IS 'Already finish in editing form';
	
ALTER TABLE vendormgt.applicant_shareholders
    ADD COLUMN is_current_data boolean NOT NULL DEFAULT False;

COMMENT ON COLUMN vendormgt.applicant_shareholders.is_current_data
    IS 'Define data is current_data';
	
ALTER TABLE vendormgt.applicant_general_administrations
    ADD COLUMN is_submmited boolean NOT NULL DEFAULT False;

COMMENT ON COLUMN vendormgt.applicant_general_administrations.is_submmited
    IS 'Define data has submitted';

ALTER TABLE vendormgt.applicant_general_administrations
    RENAME COLUMN branch_name TO company_name;
	
ALTER TABLE vendormgt.applicant_general_administrations
    RENAME COLUMN is_submmited TO is_submitted;

ALTER TABLE vendormgt.applicant_general_administrations
    ADD COLUMN company_type_id bigint NOT NULL DEFAULT 1;

ALTER TABLE vendormgt.applicant_deeds
    ADD COLUMN is_submitted boolean NOT NULL DEFAULT False;
	
COMMENT ON COLUMN vendormgt.applicant_deeds.is_submitted
    IS 'Define data has submitted';

ALTER TABLE vendormgt.applicant_shareholders
    ADD COLUMN is_submitted boolean NOT NULL DEFAULT False;

COMMENT ON COLUMN vendormgt.applicant_shareholders.is_submitted
    IS 'Define data has submitted';
	
ALTER TABLE vendormgt.applicant_bod_bocs
    ADD COLUMN is_finished boolean NOT NULL DEFAULT False;

COMMENT ON COLUMN vendormgt.applicant_bod_bocs.is_finished
    IS 'Already finish in editing form';
	
ALTER TABLE vendormgt.applicant_bod_bocs
    ADD COLUMN is_current_data boolean NOT NULL DEFAULT False;

COMMENT ON COLUMN vendormgt.applicant_bod_bocs.is_current_data
    IS 'Define data is current_data';
	
ALTER TABLE vendormgt.applicant_bod_bocs
     ADD COLUMN parent_id BIGINT NOT NULL DEFAULT 0;

COMMENT ON COLUMN vendormgt.applicant_bod_bocs.parent_id
    IS 'Define row is child from other row';

ALTER TABLE vendormgt.applicant_bod_bocs
    ADD COLUMN is_submitted boolean NOT NULL DEFAULT False;

COMMENT ON COLUMN vendormgt.applicant_bod_bocs.is_submitted
    IS 'Define data has submitted';
	
-- View: vendormgt.v_profile_checklist

-- DROP VIEW vendormgt.v_profile_checklist;

CREATE OR REPLACE VIEW vendormgt.v_profile_checklist
 AS
 SELECT a1.applicant_id,
    count(a1.is_finished) AS general_has_finish,
    COALESCE(a2.has_not_finish, 0::bigint) AS general_not_finish,
    COALESCE(b1.has_finish, 0::bigint) AS deed_has_finish,
    COALESCE(b2.has_not_finish, 0::bigint) AS deed_has_not_finish,
    COALESCE(c1.has_finish, 0::bigint) AS shareholder_has_finish,
    COALESCE(c2.has_not_finish, 0::bigint) AS shareholder_has_not_finish,
    COALESCE(d1.has_finish, 0::bigint) AS bodboc_has_finish,
    COALESCE(d2.has_not_finish, 0::bigint) AS bodboc_has_not_finish
   FROM vendormgt.applicant_general_administrations a1
     LEFT JOIN ( SELECT applicant_general_administrations.applicant_id,
            count(applicant_general_administrations.is_finished) AS has_not_finish
           FROM vendormgt.applicant_general_administrations
          WHERE applicant_general_administrations.is_finished IS FALSE
          GROUP BY applicant_general_administrations.applicant_id, applicant_general_administrations.is_finished) a2 ON a2.applicant_id = a1.applicant_id
     LEFT JOIN ( SELECT applicant_deeds.applicant_id,
            count(applicant_deeds.is_finished) AS has_finish
           FROM vendormgt.applicant_deeds
          WHERE applicant_deeds.is_finished IS TRUE
          GROUP BY applicant_deeds.applicant_id, applicant_deeds.is_finished) b1 ON b1.applicant_id = a1.applicant_id
     LEFT JOIN ( SELECT applicant_deeds.applicant_id,
            count(applicant_deeds.is_finished) AS has_not_finish
           FROM vendormgt.applicant_deeds
          WHERE applicant_deeds.is_finished IS FALSE
          GROUP BY applicant_deeds.applicant_id, applicant_deeds.is_finished) b2 ON b2.applicant_id = a1.applicant_id
     LEFT JOIN ( SELECT applicant_shareholders.applicant_id,
            count(applicant_shareholders.is_finished) AS has_finish
           FROM vendormgt.applicant_shareholders
          WHERE applicant_shareholders.is_finished IS TRUE
          GROUP BY applicant_shareholders.applicant_id, applicant_shareholders.is_finished) c1 ON c1.applicant_id = a1.applicant_id
     LEFT JOIN ( SELECT applicant_shareholders.applicant_id,
            count(applicant_shareholders.is_finished) AS has_not_finish
           FROM vendormgt.applicant_shareholders
          WHERE applicant_shareholders.is_finished IS FALSE
          GROUP BY applicant_shareholders.applicant_id, applicant_shareholders.is_finished) c2 ON c2.applicant_id = a1.applicant_id
     LEFT JOIN ( SELECT applicant_bod_bocs.applicant_id,
            count(applicant_bod_bocs.is_finished) AS has_finish
           FROM vendormgt.applicant_bod_bocs
          WHERE applicant_bod_bocs.is_finished IS TRUE
          GROUP BY applicant_bod_bocs.applicant_id, applicant_bod_bocs.is_finished) d1 ON d1.applicant_id = a1.applicant_id
     LEFT JOIN ( SELECT applicant_bod_bocs.applicant_id,
            count(applicant_bod_bocs.is_finished) AS has_not_finish
           FROM vendormgt.applicant_bod_bocs
          WHERE applicant_bod_bocs.is_finished IS FALSE
          GROUP BY applicant_bod_bocs.applicant_id, applicant_bod_bocs.is_finished) d2 ON d2.applicant_id = a1.applicant_id
  WHERE a1.is_finished IS TRUE
  GROUP BY a1.applicant_id, a1.is_finished, a2.has_not_finish, b1.has_finish, b2.has_not_finish, c1.has_finish, c2.has_not_finish, d1.has_finish, d2.has_not_finish;

ALTER TABLE vendormgt.v_profile_checklist
    OWNER TO postgres;

GRANT ALL ON TABLE vendormgt.v_profile_checklist TO postgres WITH GRANT OPTION;