INSERT INTO category (id, parent_id, name, slug, sort, lft, rgt, lvl, picture) VALUES (1, null, 'Main Category', 'main-category', 0, 1, 210, 0, '');

INSERT INTO admin (id, email, roles, enabled, password) VALUES (1, 'admin@bb77.pl', '["ROLE_ADMIN"]', 1, '$argon2i$v=19$m=1024,t=2,p=2$b1dDVGFyTXA2dzIwRjBBYw$iVHv+0AUNqsfMUZrKNzBQMuhSyprcSS8MZKLLUlWZjk');
