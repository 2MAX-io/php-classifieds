INSERT INTO page (slug, title, content, enabled) VALUES ('terms-and-conditions', 'Terms and Conditions', 'Terms and Conditions...', 1);
INSERT INTO page (slug, title, content, enabled) VALUES ('privacy-policy', 'Privacy Policy', 'Privacy Policy...', 1);
INSERT INTO page (slug, title, content, enabled) VALUES ('rejection-reasons', 'Common rejection reasons', 'Rejection reasons', 1);
INSERT INTO page (slug, title, content, enabled) VALUES ('advertisement', 'Advertisement', 'Advertisement...', 1);
INSERT INTO page (slug, title, content, enabled) VALUES ('contact', 'Contact us', 'Contact...', 1);

UPDATE setting SET name = 'linkTermsConditions', value = 'terms-and-conditions', last_update_date = '2010-01-01 00:00:00' WHERE name = 'linkTermsConditions';
UPDATE setting SET name = 'linkPrivacyPolicy', value = 'privacy-policy', last_update_date = '2010-01-01 00:00:00' WHERE name = 'linkPrivacyPolicy';
UPDATE setting SET name = 'linkRejectionReason', value = 'rejection-reasons', last_update_date = '2010-01-01 00:00:00' WHERE name = 'linkRejectionReason';
UPDATE setting SET name = 'linkContact', value = 'contact', last_update_date = '2010-01-01 00:00:00' WHERE name = 'linkContact';
UPDATE setting SET name = 'linkAdvertisement', value = 'advertisement', last_update_date = '2010-01-01 00:00:00' WHERE name = 'linkAdvertisement';
