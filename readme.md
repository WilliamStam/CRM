# working with "data" columns

INSERT INTO products VALUES(NULL, 'Blouse', 17, 15, '{"colour": "white"}');

UPDATE products SET attr = JSON_REPLACE(attr, '$.colour', 'red') WHERE name = 'Blouse';





# add contraint to column
ALTER TABLE `fields_companies` ADD CONSTRAINT json CHECK(`data` IS NULL OR JSON_VALID(`data`));


# adding the columns
ALTER TABLE `products` ADD `attr_colour` VARCHAR(32) AS (JSON_VALUE(`attr`, '$.colour'));

# creating an index on the column
CREATE INDEX products_attr_colour_ix ON products(`attr_colour`);